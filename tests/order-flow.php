<?php
define('SHAKTI_CATALOG_DEFAULTS_ONLY', true);
// No database writes, payments, messages or credentials leave this test.
putenv('WHATSAPP_ENABLED=false');
require __DIR__ . '/../includes/db.php';

class FlowStatement extends PDOStatement
{
    public function __construct(private FlowPDO $connection, private string $sql) {}
    public function execute(?array $params = null): bool {
        if ($this->connection->failItems && str_contains($this->sql, 'INSERT INTO order_items')) throw new RuntimeException('Simulated item write failure');
        $this->connection->writes[] = [$this->sql, $params];
        return true;
    }
    public function fetchColumn(int $column = 0): mixed { return false; }
    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array { return [1,2,3,4,5,6]; }
}
class FlowPDO extends PDO
{
    public array $writes = [];
    public bool $transaction = false;
    public bool $committed = false;
    public bool $rolledBack = false;
    public bool $failItems = false;
    public function __construct() {}
    public function prepare(string $query, array $options = []): PDOStatement|false { return new FlowStatement($this, $query); }
    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): PDOStatement|false { return new FlowStatement($this, $query); }
    public function beginTransaction(): bool { $this->transaction = true; return true; }
    public function commit(): bool { $this->transaction = false; $this->committed = true; return true; }
    public function rollBack(): bool { $this->transaction = false; $this->rolledBack = true; return true; }
    public function inTransaction(): bool { return $this->transaction; }
    public function lastInsertId(?string $name = null): string|false { return '1'; }
}
function check(bool $condition, string $message): void { if (!$condition) throw new RuntimeException($message); }
$billing = ['first_name'=>'Test', 'last_name'=>'Customer', 'address'=>'Test street', 'city'=>'Mumbai', 'state'=>'Maharashtra', 'pincode'=>'400001', 'phone'=>'9876543210', 'email'=>'test@example.com', 'whatsapp_opt_in'=>true];

$_SESSION = ['cart'=>[1=>1]];
$pdo = new FlowPDO();
$cod = createOrder($pdo, $billing, 'cod', 'pending');
check($pdo->committed && $cod['total'] == 349 && empty($_SESSION['cart']), 'COD save, fees or cart clearing failed');
check($_SESSION['last_order']['whatsapp']['status'] === 'not_configured', 'Missing API must not block COD');
$message = shakti_order_confirmation($_SESSION['last_order']);
check($message['to'] === '919876543210' && str_contains($message['text'], 'Amount due: INR 349.00') && str_contains($message['text'], 'Peanut Jaggery') && str_contains($message['text'], 'Mumbai'), 'Confirmation data incomplete');
$payload = shakti_whatsapp_payload($_SESSION['last_order']);
check(count($payload['template']['components'][0]['parameters']) === 6, 'Template parameters mismatch');
echo "COD transaction, shipping/COD fees, confirmation payload, disabled notification passed\n";

$_SESSION = ['cart'=>[1=>1]];
$snapshot = collectCartItems('online');
$_SESSION['cart'] = [1=>2, 2=>1];
$pdo = new FlowPDO();
$paid = createOrder($pdo, $billing, 'online', 'completed', 'test_payment', $snapshot);
check($paid['total'] == 284 && $paid['items'][0]['quantity'] === 1, 'Paid cart snapshot changed');
check($_SESSION['cart'] === [1=>1,2=>1], 'New cart additions were lost');
check(str_contains(shakti_order_confirmation($_SESSION['last_order'])['text'], 'Total paid: INR 284.00'), 'Paid confirmation total wrong');
echo "Online snapshot, prepaid discount, later cart changes preserved passed\n";

$_SESSION = ['cart'=>[100000=>1], 'custom_combos'=>[100000=>['boxes'=>6,'selection'=>[1=>2,2=>2,3=>2]]]];
$pdo = new FlowPDO();
$combo = createOrder($pdo, $billing, 'cod', 'pending');
$itemCents = 0; $itemCount = 0;
foreach ($pdo->writes as [$sql,$params]) if (str_contains($sql, 'INSERT INTO order_items')) { $itemCents += (int) round($params[3] * 100); $itemCount += $params[2]; }
check($itemCount === 6 && $itemCents === 157600 && $combo['total'] == 1651, 'Combo item allocation or fees mismatch');
echo "Six-box combo order rows and exact total passed\n";

$_SESSION = ['cart'=>[1=>1]];
$pdo = new FlowPDO(); $pdo->failItems = true;
try { createOrder($pdo, $billing, 'cod', 'pending'); throw new LogicException('Expected simulated failure'); }
catch (RuntimeException $error) { check($pdo->rolledBack && !$pdo->committed && !isset($_SESSION['last_order']) && $_SESSION['cart'] === [1=>1], 'Failed order must preserve cart and suppress confirmation'); }
check(shakti_notify_whatsapp(['billing'=>['whatsapp_opt_in'=>false]])['status'] === 'not_requested', 'Consent not respected');
echo "Failed order rollback, no confirmation, opt-out passed\n";
