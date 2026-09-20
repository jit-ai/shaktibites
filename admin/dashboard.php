<?php
session_start();
include '../includes/config.php';

$host = 'localhost';
$db   = 'shakti_bites';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    if (strpos($e->getMessage(), "Unknown database") !== false) {
        header('Location: ../setup');
        exit;
    } else {
        die("Database connection error. Please contact administrator.");
    }
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login');
    exit;
}

$stats = [];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
$stats['total_users'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
$stats['total_products'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) as count FROM orders");
$stats['total_orders'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'completed'");
$result = $stmt->fetch();
$stats['total_revenue'] = $result['total'] ? number_format($result['total'], 2) : '0.00';

$recentOrdersStmt = $pdo->query("
    SELECT o.id, o.order_number, o.total_amount, o.order_status, o.created_at, 
           u.name as user_name
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 5
");
$recentOrders = $recentOrdersStmt->fetchAll();

include 'includes/header.php';
?>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="admin-card stats-card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="text-uppercase fw-bold">Total Users</h6>
                    </div>
                    <div class="icon">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <div class="display-4 fw-bold"><?php echo number_format($stats['total_users']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="admin-card stats-card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="text-uppercase fw-bold">Total Products</h6>
                    </div>
                    <div class="icon">
                        <i class="bi bi-box"></i>
                    </div>
                </div>
                <div class="display-4 fw-bold"><?php echo $stats['total_products']; ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="admin-card stats-card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="text-uppercase fw-bold">Total Orders</h6>
                    </div>
                    <div class="icon">
                        <i class="bi bi-boxes"></i>
                    </div>
                </div>
                <div class="display-4 fw-bold"><?php echo number_format($stats['total_orders']); ?></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="admin-card stats-card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <h6 class="text-uppercase fw-bold">Total Revenue</h6>
                    </div>
                    <div class="icon">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
                <div class="display-4 fw-bold">₹<?php echo $stats['total_revenue']; ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-xl-8">
        <div class="admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Recent Orders</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle admin-table admin-table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentOrders)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">No orders found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($order['order_number']); ?></td>
                                        <td><?php echo htmlspecialchars($order['user_name'] ?? 'Guest'); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge <?php 
                                                echo (($order['order_status'] == 'completed') ? 'bg-success' : 
                                                    (($order['order_status'] == 'processing') ? 'bg-info' : 
                                                    (($order['order_status'] == 'pending') ? 'bg-warning' : 'bg-secondary')));
                                            ?>">
                                                <?php echo ucfirst($order['order_status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">System Status</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <p class="mb-1">Database Connection</p>
                        <p class="fs-5 text-success"><i class="bi bi-check-circle me-1"></i> Connected</p>
                    </div>
                    <div class="col-6 text-end">
                        <p class="mb-1">PHP Version</p>
                        <p class="fs-5"><?php echo phpversion(); ?></p>
                    </div>
                </div>
                <hr class="my-3">
                <div class="row">
                    <div class="col-6">
                        <p class="mb-1">MySQL Version</p>
                        <p class="fs-5">
                            <?php
                            $version = $pdo->query("SELECT VERSION()")->fetchColumn();
                            echo htmlspecialchars($version);
                            ?>
                        </p>
                    </div>
                    <div class="col-6 text-end">
                        <p class="mb-1">Server Time</p>
                        <p class="fs-5"><?php echo date('M d, Y H:i:s'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
