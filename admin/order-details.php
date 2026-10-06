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

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
    SELECT o.*, u.name as user_name, u.email as user_email
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: orders');
    exit;
}

$itemsStmt = $pdo->prepare("
    SELECT oi.*, p.name as product_name, p.image as product_image
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$itemsStmt->execute([$order_id]);
$orderItems = $itemsStmt->fetchAll();

include 'includes/header.php';

$customerName = $order['user_name'] ?: 'Guest';
$paymentStatus = $order['payment_status'] ?? 'pending';
$orderStatus = $order['order_status'] ?? 'pending';
?>

<div class="admin-page-header">
    <div>
        <p class="admin-eyebrow">Sales</p>
        <h1 class="admin-page-title">Order #<?php echo htmlspecialchars($order['order_number']); ?></h1>
        <p class="admin-page-subtitle">Placed <?php echo date('M d, Y \a\t H:i', strtotime($order['created_at'])); ?></p>
    </div>
    <div class="admin-page-actions">
        <span class="status-pill status-<?php echo $orderStatus === 'completed' ? 'success' : ($orderStatus === 'processing' ? 'info' : ($orderStatus === 'pending' ? 'warning' : 'muted')); ?>">
            <?php echo htmlspecialchars(ucfirst($orderStatus)); ?>
        </span>
        <a href="orders" class="btn btn-outline-admin-primary">
            <i class="bi bi-arrow-left me-1"></i> Back to Orders
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="admin-card mb-0">
            <div class="card-header">
                <h5 class="mb-0">Customer &amp; Delivery</h5>
            </div>
            <div class="card-body">
                <dl class="mb-0">
                    <div class="admin-kv"><dt>Name</dt><dd><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></dd></div>
                    <div class="admin-kv"><dt>Email</dt><dd><?php echo htmlspecialchars($order['email']); ?></dd></div>
                    <div class="admin-kv"><dt>Phone</dt><dd><?php echo htmlspecialchars($order['phone']); ?></dd></div>
                    <div class="admin-kv"><dt>Account</dt><dd><?php echo htmlspecialchars($customerName); ?></dd></div>
                    <div class="admin-kv"><dt>Address</dt><dd><?php echo nl2br(htmlspecialchars($order['address'])); ?></dd></div>
                    <div class="admin-kv"><dt>City</dt><dd><?php echo htmlspecialchars($order['city']); ?></dd></div>
                    <div class="admin-kv"><dt>State</dt><dd><?php echo htmlspecialchars($order['state']); ?></dd></div>
                    <div class="admin-kv"><dt>Pincode</dt><dd><?php echo htmlspecialchars($order['pincode']); ?></dd></div>
                </dl>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="admin-card mb-0">
            <div class="card-header">
                <h5 class="mb-0">Payment</h5>
            </div>
            <div class="card-body">
                <dl class="mb-0">
                    <div class="admin-kv">
                        <dt>Method</dt>
                        <dd><?php echo htmlspecialchars(ucfirst($order['payment_method'])); ?></dd>
                    </div>
                    <div class="admin-kv">
                        <dt>Payment Status</dt>
                        <dd>
                            <span class="status-pill status-<?php echo $paymentStatus === 'completed' ? 'success' : ($paymentStatus === 'pending' ? 'warning' : 'muted'); ?>">
                                <?php echo htmlspecialchars(ucfirst($paymentStatus)); ?>
                            </span>
                        </dd>
                    </div>
                    <div class="admin-kv">
                        <dt>Order Status</dt>
                        <dd>
                            <span class="status-pill status-<?php echo $orderStatus === 'completed' ? 'success' : ($orderStatus === 'processing' ? 'info' : ($orderStatus === 'pending' ? 'warning' : 'muted')); ?>">
                                <?php echo htmlspecialchars(ucfirst($orderStatus)); ?>
                            </span>
                        </dd>
                    </div>
                    <div class="admin-kv">
                        <dt>Total Amount</dt>
                        <dd>&#8377;<?php echo number_format((float) $order['total_amount'], 2); ?></dd>
                    </div>
                    <?php if (!empty($order['razorpay_payment_id'])): ?>
                    <div class="admin-kv">
                        <dt>Razorpay ID</dt>
                        <dd style="font-size:12px;"><?php echo htmlspecialchars($order['razorpay_payment_id']); ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="admin-card">
    <div class="card-header">
        <h5 class="mb-0">Order Items</h5>
        <span class="admin-count-chip"><?php echo count($orderItems); ?> line<?php echo count($orderItems) === 1 ? '' : 's'; ?></span>
    </div>
    <div class="card-body">
        <?php if (empty($orderItems)): ?>
            <div class="admin-empty">
                <i class="bi bi-bag-x admin-empty-icon"></i>
                <p class="admin-empty-title">No items recorded</p>
                <p class="admin-empty-text">This order has no line items attached to it.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle admin-table admin-table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Image</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-end">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderItems as $item): ?>
                            <tr>
                                <td class="admin-cell-strong"><?php echo htmlspecialchars($item['product_name'] ?? 'Unknown item'); ?></td>
                                <td>
                                    <?php if (!empty($item['product_image'])): ?>
                                        <img src="../assets/images/<?php echo htmlspecialchars($item['product_image']); ?>"
                                             alt="<?php echo htmlspecialchars($item['product_name'] ?? ''); ?>"
                                             class="admin-thumb">
                                    <?php else: ?>
                                        <span class="admin-thumb-empty"><i class="bi bi-image"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end admin-amount">&#8377;<?php echo number_format((float) $item['price'], 2); ?></td>
                                <td class="text-end admin-amount"><?php echo (int) $item['quantity']; ?></td>
                                <td class="text-end admin-amount">&#8377;<?php echo number_format((float) $item['price'] * (int) $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end admin-cell-strong">Order Total</td>
                            <td class="text-end admin-amount">&#8377;<?php echo number_format((float) $order['total_amount'], 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
