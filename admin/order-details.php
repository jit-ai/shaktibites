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
    }
    die("Database connection error: " . htmlspecialchars($e->getMessage()));
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
    die("Order not found");
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
?>

<div class="row mb-4">
    <div class="col">
        <h2 class="h4">Order Details #<?php echo htmlspecialchars($order['order_number']); ?></h2>
    </div>
    <div class="col-auto">
        <a href="orders" class="btn btn-outline-admin-primary">
            <i class="bi bi-arrow-left me-1"></i> Back to Orders
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Order Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Order Date:</strong> <?php echo date('M d, Y H:i:s', strtotime($order['created_at'])); ?></p>
                <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['user_email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
                <p><strong>City:</strong> <?php echo htmlspecialchars($order['city']); ?></p>
                <p><strong>State:</strong> <?php echo htmlspecialchars($order['state']); ?></p>
                <p><strong>Pincode:</strong> <?php echo htmlspecialchars($order['pincode']); ?></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="admin-card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Order Summary</h5>
            </div>
            <div class="card-body">
                <p><strong>Payment Method:</strong> 
                    <span class="badge bg-<?php 
                        echo $order['payment_method'] == 'cod' ? 'secondary' : 'info'; ?>">
                        <?php echo ucfirst($order['payment_method']); ?>
                    </span>
                </p>
                <p><strong>Payment Status:</strong> 
                    <span class="badge bg-<?php 
                        echo ($order['payment_status'] == 'completed') ? 'success' : 
                        (($order['payment_status'] == 'pending') ? 'warning' : 'secondary'); ?>">
                        <?php echo ucfirst($order['payment_status']); ?>
                    </span>
                </p>
                <p><strong>Order Status:</strong> 
                    <span class="badge bg-<?php 
                        echo (($order['order_status'] == 'completed') ? 'success' : 
                        (($order['order_status'] == 'processing') ? 'info' : 
                        (($order['order_status'] == 'pending') ? 'warning' : 'secondary'))); ?>">
                        <?php echo ucfirst($order['order_status']); ?>
                    </span>
                </p>
                <p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="admin-card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Order Items</h5>
    </div>
    <div class="card-body">
        <?php if (empty($orderItems)): ?>
            <p class="text-center text-muted">No items in this order</p>
        <?php else: ?>
            <table class="table table-hover align-middle admin-table admin-table-hover">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td>
                                <?php if (!empty($item['product_image'])): ?>
                                    <img src="../assets/images/<?php echo htmlspecialchars($item['product_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                         class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="bi bi-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
