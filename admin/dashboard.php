<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

try {
    $pdo = getPDO();
} catch (\Throwable $e) {
    if ($e instanceof \PDOException
        && strpos($e->getMessage(), 'Unknown database') !== false) {
        header('Location: ../setup');
        exit;
    }
    die(db_connection_error_message($e));
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
$stats['total_revenue'] = $result['total'] ? (float) $result['total'] : 0.0;

$recentOrdersStmt = $pdo->query("
    SELECT o.id, o.order_number, o.total_amount, o.order_status, o.payment_method, o.created_at,
           u.name as user_name
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 5
");
$recentOrders = $recentOrdersStmt->fetchAll();

include 'includes/header.php';
?>

<div class="admin-page-header">
    <div>
        <p class="admin-eyebrow">Overview</p>
        <h1 class="admin-page-title">Dashboard</h1>
        <p class="admin-page-subtitle">Store performance at a glance, plus the latest activity.</p>
    </div>
    <div class="admin-page-actions">
        <a href="orders" class="btn btn-outline-admin-primary">
            <i class="bi bi-receipt me-1"></i> View Orders
        </a>
        <a href="product-add" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Product
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="admin-stat">
            <span class="admin-stat-rail" aria-hidden="true"></span>
            <div class="admin-stat-top">
                <p class="admin-stat-label">Total Users</p>
                <span class="admin-stat-icon"><i class="bi bi-people"></i></span>
            </div>
            <div class="admin-stat-value"><?php echo number_format((int) $stats['total_users']); ?></div>
            <p class="admin-stat-note">Registered accounts</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="admin-stat is-success">
            <span class="admin-stat-rail" aria-hidden="true"></span>
            <div class="admin-stat-top">
                <p class="admin-stat-label">Active Products</p>
                <span class="admin-stat-icon"><i class="bi bi-box"></i></span>
            </div>
            <div class="admin-stat-value"><?php echo number_format((int) $stats['total_products']); ?></div>
            <p class="admin-stat-note">Boxes and combo packs</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="admin-stat is-info">
            <span class="admin-stat-rail" aria-hidden="true"></span>
            <div class="admin-stat-top">
                <p class="admin-stat-label">Total Orders</p>
                <span class="admin-stat-icon"><i class="bi bi-receipt"></i></span>
            </div>
            <div class="admin-stat-value"><?php echo number_format((int) $stats['total_orders']); ?></div>
            <p class="admin-stat-note">All time</p>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="admin-stat is-warning">
            <span class="admin-stat-rail" aria-hidden="true"></span>
            <div class="admin-stat-top">
                <p class="admin-stat-label">Revenue Collected</p>
                <span class="admin-stat-icon"><i class="bi bi-cash-stack"></i></span>
            </div>
            <div class="admin-stat-value">₹<?php echo number_format($stats['total_revenue'], 2); ?></div>
            <p class="admin-stat-note">Paid orders only</p>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-8">
        <div class="admin-card mb-0">
            <div class="card-header">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="orders" class="btn btn-sm btn-outline-admin-primary">View all</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle admin-table admin-table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Placed</th>
                                <th class="text-end">Amount</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentOrders)): ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="admin-empty">
                                            <i class="bi bi-receipt admin-empty-icon"></i>
                                            <p class="admin-empty-title">No orders yet</p>
                                            <p class="admin-empty-text">The five most recent orders will appear here.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentOrders as $order): ?>
                                    <?php $orderStatus = $order['order_status'] ?? 'pending'; ?>
                                    <tr>
                                        <td class="admin-cell-strong">#<?php echo htmlspecialchars($order['order_number']); ?></td>
                                        <td><?php echo htmlspecialchars($order['user_name'] ?? 'Guest'); ?></td>
                                        <td class="admin-cell-muted admin-nowrap"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        <td class="text-end admin-amount">₹<?php echo number_format((float) $order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="status-pill status-<?php
                                                echo $orderStatus === 'completed' ? 'success'
                                                    : ($orderStatus === 'processing' ? 'info'
                                                    : ($orderStatus === 'pending' ? 'warning' : 'muted'));
                                            ?>"><?php echo htmlspecialchars(ucfirst($orderStatus)); ?></span>
                                        </td>
                                        <td class="text-end">
                                            <a href="order-details?id=<?php echo (int) $order['id']; ?>" class="btn btn-sm btn-outline-admin-primary">
                                                <i class="bi bi-eye"></i> View
                                            </a>
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
        <div class="admin-card mb-0">
            <div class="card-header">
                <h5 class="mb-0">System Status</h5>
                <span class="status-pill status-success">Connected</span>
            </div>
            <div class="card-body">
                <dl class="mb-0">
                    <div class="admin-kv">
                        <dt>Database</dt>
                        <dd><?php echo htmlspecialchars($pdo->query("SELECT VERSION()")->fetchColumn(), ENT_QUOTES, 'UTF-8'); ?></dd>
                    </div>
                    <div class="admin-kv">
                        <dt>PHP Version</dt>
                        <dd><?php echo htmlspecialchars(phpversion(), ENT_QUOTES, 'UTF-8'); ?></dd>
                    </div>
                    <div class="admin-kv">
                        <dt>Server Time</dt>
                        <dd><?php echo date('M d, Y H:i'); ?></dd>
                    </div>
                    <div class="admin-kv">
                        <dt>Active Products</dt>
                        <dd><?php echo number_format((int) $stats['total_products']); ?></dd>
                    </div>
                    <div class="admin-kv">
                        <dt>Pending Orders</dt>
                        <dd><?php
                            echo number_format((int) $pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'pending'")->fetchColumn());
                        ?></dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>