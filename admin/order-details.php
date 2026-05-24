<?php
session_start();
include '../includes/config.php';

// Initialize database connection
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
        header('Location: ../setup.php');
        exit;
    }
    die("Database connection error: " . htmlspecialchars($e->getMessage()));
}

// Redirect if not logged in or not admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

// Get order ID
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get order details with user info
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

// Get order items
$itemsStmt = $pdo->prepare("
    SELECT oi.*, p.name as product_name, p.image as product_image
    FROM order_items oi
    LEFT JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$itemsStmt->execute([$order_id]);
$orderItems = $itemsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Shakti Bites Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root {
            --admin-sidebar-width: 260px;
            --admin-header-height: 60px;
        }
        #sidebar-wrapper {
            width: var(--admin-sidebar-width);
            background: var(--white);
            border-right: 1px solid rgba(0,0,0,0.07);
            height: 100vh;
            position: fixed;
            top: var(--admin-header-height);
            left: 0;
            z-index: 1000;
        }
        #page-content-wrapper {
            margin-left: var(--admin-sidebar-width);
            min-height: 100vh;
            padding-top: var(--admin-header-height);
        }
        #page-content-wrapper .navbar {
            height: var(--admin-header-height);
            background: var(--white);
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            position: fixed;
            top: 0;
            left: var(--admin-sidebar-width);
            right: 0;
            z-index: 1050;
        }
        @media (max-width: 991px) {
            #sidebar-wrapper { transform: translateX(-100%); }
            #sidebar-wrapper.toggled { transform: translateX(0); }
            #page-content-wrapper { margin-left: 0; }
            #page-content-wrapper .navbar { left: 0; }
        }
    </style>
</head>
<body>
<div class="d-flex" id="wrapper">
    <div class="bg-white" id="sidebar-wrapper">
        <div class="sidebar-heading text-center py-4">
            <img src="../assets/images/logo.PNG" alt="Shakti Bites Logo" style="max-height: 40px; margin-bottom: 5px;">
            <h4 class="text-uppercase mb-0">Admin Panel</h4>
        </div>
        <div class="list-group list-group-flush">
            <a href="dashboard.php" class="list-group-item list-group-item-action">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            <a href="users.php" class="list-group-item list-group-item-action">
                <i class="bi bi-people me-2"></i>Users
            </a>
            <a href="products.php" class="list-group-item list-group-item-action">
                <i class="bi bi-box me-2"></i>Products
            </a>
            <a href="categories.php" class="list-group-item list-group-item-action">
                <i class="bi bi-tag me-2"></i>Categories
            </a>
            <a href="orders.php" class="list-group-item list-group-item-action active">
                <i class="bi bi-boxes me-2"></i>Orders
            </a>
        </div>
    </div>
    
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="d-flex align-items-center">
                <img src="../assets/images/logo.PNG" alt="Shakti Bites" height="30" class="me-2">
                <button class="btn btn-primary" id="menu-toggle">
                    <i class="bi bi-list"></i>
                </button>
            </div>
            
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../profile.php"><i class="bi bi-person"></i> My Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        
        <div class="container-fluid px-4">
            <div class="row mb-4">
                <div class="col">
                    <h2 class="h4">Order Details #<?php echo htmlspecialchars($order['order_number']); ?></h2>
                </div>
                <div class="col-auto">
                    <a href="orders.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Orders
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
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
                    <div class="card mb-4">
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
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($orderItems)): ?>
                        <p class="text-center text-muted">No items in this order</p>
                    <?php else: ?>
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
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
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const menuToggle = document.getElementById('menu-toggle');
    const sidebarWrapper = document.getElementById('sidebar-wrapper');
    const pageContentWrapper = document.getElementById('page-content-wrapper');
    
    menuToggle.addEventListener('click', function () {
        sidebarWrapper.classList.toggle('active');
        pageContentWrapper.classList.toggle('sidebar-toggled');
    });
    
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 991) {
            if (!sidebarWrapper.contains(event.target) && !menuToggle.contains(event.target)) {
                sidebarWrapper.classList.remove('active');
                pageContentWrapper.classList.remove('sidebar-toggled');
            }
        }
    });
</script>
</body>
</html>