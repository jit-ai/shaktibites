<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/catalog.php';

/**
 * MySQL driver error codes that mean "the connection failed", not "the query was
 * wrong". Shared hosting drops idle connections, restarts MySQL and caps the
 * number of concurrent connections per user, so any of these can appear on a
 * request that worked moments earlier.
 */
const DB_TRANSIENT_ERRORS = [
    1040, // Too many connections
    2002, // Can't connect to server
    2003, // Can't connect (timeout)
    2005, // Unknown host
    2006, // MySQL server has gone away
    2013, // Lost connection during query
    2026, // SSL connection error
    2055, // Lost connection, system error
    1290, // Read-only host
];

function db_is_transient(Throwable $e): bool
{
    if (!$e instanceof PDOException) {
        return false;
    }
    $code = isset($e->errorInfo[1]) ? (int) $e->errorInfo[1] : 0;
    return in_array($code, DB_TRANSIENT_ERRORS, true);
}

/**
 * A PDO connection that repairs itself.
 *
 * Shared hosting makes the database drop connections: MySQL restarts, the host
 * caps concurrent connections per user, and idle sessions are reaped. The result
 * is an intermittent "Database connection error" on a site that was working a
 * moment ago. When a query fails for one of those reasons the connection is
 * rebuilt once and the statement is retried, so a transient blip never reaches
 * the visitor.
 *
 * Writes are never replayed automatically. If a write dies partway through, the
 * server may already have applied it, so retrying could duplicate a row. Those
 * failures are surfaced with a clear message instead.
 */
class ResilientPDO extends PDO
{
    private string $dsn;
    private ?string $dbUser;
    private ?string $dbPass;
    private array $pdoOptions;

    public function __construct(string $dsn, ?string $user = null, ?string $pass = null, ?array $options = null)
    {
        $this->dsn = $dsn;
        $this->dbUser = $user;
        $this->dbPass = $pass;
        $this->pdoOptions = $options ?? [];
        parent::__construct($dsn, $user, $pass, $this->pdoOptions);
    }

    /**
     * Drop the dead handle and open a fresh connection in its place. A short
     * pause helps when the cause is a connection burst rather than a dead
     * socket, because the host needs a moment to free a slot.
     */
    public function reconnect(): void
    {
        if (isset($this->pdoOptions[PDO::ATTR_TIMEOUT])) {
            usleep(250000);
        }
        parent::__construct($this->dsn, $this->dbUser, $this->dbPass, $this->pdoOptions);
    }

    /**
     * Reconnecting can itself hit a full connection table, so the rebuilt
     * handle gets the same single retry as the statement that triggered it.
     */
    private function reconnectOrFail(PDOException $original): void
    {
        try {
            $this->reconnect();
        } catch (PDOException $retryError) {
            throw $retryError;
        }
    }

    private static function isReadStatement(string $sql): bool
    {
        $sql = ltrim($sql);
        // Strip a leading comment before deciding what the statement does.
        if (strpos($sql, '/*') === 0) {
            $end = strpos($sql, '*/');
            $sql = $end === false ? $sql : ltrim(substr($sql, $end + 2));
        }
        foreach (['SELECT', 'SHOW', 'DESCRIBE', 'DESC', 'EXPLAIN', 'WITH'] as $keyword) {
            if (stripos($sql, $keyword) === 0) {
                return true;
            }
        }
        return false;
    }

    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): PDOStatement|false
    {
        try {
            return parent::query($query, $fetchMode, ...$fetchModeArgs);
        } catch (PDOException $e) {
            if (!db_is_transient($e) || !self::isReadStatement($query)) {
                throw $e;
            }
            $this->reconnectOrFail($e);
            return parent::query($query, $fetchMode, ...$fetchModeArgs);
        }
    }

    /**
     * prepare() only parses the statement, so retrying it cannot duplicate an
     * effect. The caller's execute() then runs against a live connection.
     */
    public function prepare(string $query, array $options = []): PDOStatement|false
    {
        try {
            return parent::prepare($query, $options);
        } catch (PDOException $e) {
            if (!db_is_transient($e)) {
                throw $e;
            }
            $this->reconnectOrFail($e);
            return parent::prepare($query, $options);
        }
    }

    public function exec(string $statement): int|false
    {
        try {
            return parent::exec($statement);
        } catch (PDOException $e) {
            // Replaying a write is not safe, so make the cause obvious instead.
            if (db_is_transient($e)) {
                throw new DBUnavailableException(
                    'The database connection dropped during a write. Please try again.',
                    0,
                    $e
                );
            }
            throw $e;
        }
    }
}

/**
 * Thrown when the database is unreachable rather than misconfigured, so pages
 * can tell "try again in a moment" apart from "the owner must fix the settings".
 */
class DBUnavailableException extends RuntimeException
{
}

function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo instanceof ResilientPDO) {
        // A handle can go stale between requests on a long-lived worker.
        try {
            $pdo->getAttribute(PDO::ATTR_SERVER_INFO);
            return $pdo;
        } catch (Throwable $e) {
            try {
                $pdo->reconnect();
                return $pdo;
            } catch (Throwable $reconnectError) {
                $pdo = null;
            }
        }
    }

    try {
        $pdo = new ResilientPDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Real prepared statements, matching the connection the pages
                // used before they were switched over to getPDO().
                PDO::ATTR_EMULATE_PREPARES => false,
                // Fail fast instead of hanging a worker for a minute when the
                // database is unreachable.
                PDO::ATTR_TIMEOUT => 5,
            ]
        );
    } catch (PDOException $e) {
        if (db_is_transient($e)) {
            throw new DBUnavailableException(
                'The database is temporarily unavailable. Please try again.',
                0,
                $e
            );
        }
        throw $e;
    }

    return $pdo;
}

/**
 * Single wording for every page, so a visitor sees one clear message and the
 * owner gets the real reason in the error log. A dropped connection is reported
 * as temporary, because retrying a moment later normally succeeds.
 */
function db_connection_error_message(Throwable $e): string
{
    $transient = db_is_transient($e) || $e instanceof DBUnavailableException;
    error_log('[shakti-bites] DB connection failed' . ($transient ? ' (transient)' : '') . ': ' . $e->getMessage());

    if ($transient) {
        return 'We could not reach the store database. Please try again in a moment.';
    }
    return 'Database connection error. Please contact administrator.';
}

/**
 * Every item that can be ordered: single boxes and combo packs. Kept as a
 * function so the cart, checkout and order pipeline all price a line item from
 * exactly the same source.
 */
function getCartProducts(): array
{
    return shakti_cart_catalog();
}

function generateOrderNumber(PDO $pdo): string
{
    $prefix = 'SHK';
    $date = date('ymd');
    $stmt = $pdo->prepare("SELECT order_number FROM orders WHERE order_number LIKE ? ORDER BY order_number DESC LIMIT 1");
    $stmt->execute(["{$prefix}{$date}%"]);
    $last = $stmt->fetchColumn();
    $seq = $last ? ((int) substr($last, -4)) + 1 : 1;
    return $prefix . $date . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
}

function collectCartItems(): array
{
    $cart = $_SESSION['cart'] ?? [];
    $products = getCartProducts();
    $items = [];
    $subtotal = 0.0;
    foreach ($cart as $productId => $quantity) {
        $quantity = (int) $quantity;
        if ($quantity < 1) {
            continue;
        }
        if (!isset($products[$productId])) {
            throw new RuntimeException('One or more products in your cart are invalid.');
        }
        $price = (float) $products[$productId]['price'];
        $subtotal += $price * $quantity;
        $items[] = [
            'product_id' => (int) $productId,
            'name'       => $products[$productId]['name'],
            'image'      => $products[$productId]['image'],
            'type'       => $products[$productId]['type'],
            'quantity'   => $quantity,
            'price'      => $price,
        ];
    }
    if (empty($items)) {
        throw new RuntimeException('Your cart is empty.');
    }
    $tax = round($subtotal * (float) TAX_RATE, 2);
    $total = round($subtotal + $tax, 2);
    return ['items' => $items, 'subtotal' => $subtotal, 'tax' => $tax, 'total' => $total];
}

/**
 * Guarantee every catalogue entry has a `products` row, so an order line item
 * can reference a combo pack by id exactly like a single box. The storefront
 * catalogue is authoritative for names and prices, so only missing ids are
 * inserted. Runs at most once per request and never blocks checkout: if the
 * sync cannot run, the order still proceeds with whatever rows already exist.
 */
function ensureCatalogProductRows(PDO $pdo): void
{
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    try {
        $rows = shakti_catalog_product_rows();
        $ids = array_map('intval', array_keys($rows));
        $existing = $pdo->query('SELECT id FROM products WHERE id IN (' . implode(',', $ids) . ')')
            ->fetchAll(PDO::FETCH_COLUMN);
        $missing = array_diff_key($rows, array_flip(array_map('intval', $existing)));
        if (empty($missing)) {
            return;
        }

        $categoryStmt = $pdo->prepare('SELECT id FROM categories WHERE name = ? LIMIT 1');
        $insert = $pdo->prepare(
            "INSERT INTO products (id, name, description, price, image, label, label_class, category_id, is_active, stock, sku)
             VALUES (:id, :name, :description, :price, :image, :label, :labelClass, :categoryId, 1, :stock, :sku)"
        );
        foreach ($missing as $row) {
            $categoryId = null;
            if ($row['category'] !== '') {
                $categoryStmt->execute([$row['category']]);
                $categoryId = $categoryStmt->fetchColumn() ?: null;
            }
            $insert->execute([
                ':id' => $row['id'],
                ':name' => $row['name'],
                ':description' => $row['description'],
                ':price' => $row['price'],
                ':image' => $row['image'],
                ':label' => $row['label'],
                ':labelClass' => $row['label_class'],
                ':categoryId' => $categoryId,
                ':stock' => $row['stock'],
                ':sku' => $row['sku'],
            ]);
        }
    } catch (Throwable $e) {
        // Reference data sync is best effort; a failure must not block the order.
    }
}

function createOrder(PDO $pdo, array $billing, string $paymentMethod, string $paymentStatus, ?string $razorpayPaymentId = null): array
{
    $cart = collectCartItems();
    ensureCatalogProductRows($pdo);

    $orderNumber = generateOrderNumber($pdo);

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO orders
                (user_id, order_number, total_amount, payment_method, payment_status,
                 first_name, last_name, email, phone, address, city, state, pincode, razorpay_payment_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $userId = $_SESSION['user_id'] ?? null;
        $stmt->execute([
            $userId,
            $orderNumber,
            $cart['total'],
            $paymentMethod,
            $paymentStatus,
            $billing['first_name'],
            $billing['last_name'],
            $billing['email'],
            $billing['phone'],
            $billing['address'],
            $billing['city'],
            $billing['state'],
            $billing['pincode'],
            $razorpayPaymentId,
        ]);
        $orderId = (int) $pdo->lastInsertId();

        $itemStmt = $pdo->prepare(
            "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)"
        );
        foreach ($cart['items'] as $item) {
            $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
        }
        $pdo->commit();

        $_SESSION['last_order'] = [
            'order_number'   => $orderNumber,
            'total_amount'   => $cart['total'],
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentStatus,
            'items'          => $cart['items'],
            'billing'        => $billing,
        ];

        unset($_SESSION['cart']);

        return [
            'order_id'      => $orderId,
            'order_number'  => $orderNumber,
            'total'         => $cart['total'],
            'items'         => $cart['items'],
        ];
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function validateBilling(array $billing): ?string
{
    foreach (['first_name', 'last_name', 'address', 'city', 'state', 'pincode', 'phone', 'email'] as $field) {
        if (empty($billing[$field])) {
            return ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }
    if (!filter_var($billing['email'], FILTER_VALIDATE_EMAIL)) {
        return 'A valid email address is required.';
    }
    if (!preg_match('/^[0-9]{10}$/', $billing['phone'])) {
        return 'A valid 10-digit phone number is required.';
    }
    return null;
}
