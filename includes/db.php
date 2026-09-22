<?php
require_once __DIR__ . '/config.php';

function getPDO(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }
    return $pdo;
}

function getCartProducts(): array
{
    return [
        1 => ['name' => 'Peanut Jaggery Power Bites', 'price' => 249, 'img' => 'product1.PNG'],
        2 => ['name' => 'Almond Cacao Power Bites', 'price' => 279, 'img' => 'product2.PNG'],
        3 => ['name' => 'Dry Fruit Cardamom Bites', 'price' => 299, 'img' => 'product3.PNG'],
    ];
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
        if (!isset($products[$productId])) {
            throw new RuntimeException('One or more products in your cart are invalid.');
        }
        $price = (float) $products[$productId]['price'];
        $subtotal += $price * (int) $quantity;
        $items[] = [
            'product_id' => (int) $productId,
            'name'       => $products[$productId]['name'],
            'quantity'   => (int) $quantity,
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

function createOrder(PDO $pdo, array $billing, string $paymentMethod, string $paymentStatus, ?string $razorpayPaymentId = null): array
{
    $cart = collectCartItems();

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
