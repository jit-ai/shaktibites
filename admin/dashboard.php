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
    // If database doesn't exist, redirect to setup
    if (strpos($e->getMessage(), "Unknown database") !== false) {
        header('Location: ../setup.php');
        exit;
    } else {
        // For other PDO errors, show a generic error (don't expose details in production)
        die("Database connection error. Please contact administrator.");
    }
}

// Redirect if not logged in or not admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

// Get stats for dashboard
$stats = [];

// Total users
$stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
$stats['total_users'] = $stmt->fetchColumn();

// Total products
$stmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE is_active = 1");
$stats['total_products'] = $stmt->fetchColumn();

// Total orders
$stmt = $pdo->query("SELECT COUNT(*) as count FROM orders");
$stats['total_orders'] = $stmt->fetchColumn();

// Total revenue (completed orders)
$stmt = $pdo->query("SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'completed'");
$result = $stmt->fetch();
$stats['total_revenue'] = $result['total'] ? number_format($result['total'], 2) : '0.00';

// Recent orders
$recentOrdersStmt = $pdo->query("
    SELECT o.id, o.order_number, o.total_amount, o.order_status, o.created_at, 
           u.name as user_name
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 5
");
$recentOrders = $recentOrdersStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Shakti Bites</title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Admin panel customizations to match website theme */
        :root {
            --admin-sidebar-width: 260px;
            --admin-header-height: 60px;
        }
        
        /* Sidebar styling */
        #sidebar-wrapper {
            width: var(--admin-sidebar-width);
            background: var(--white);
            border-right: 1px solid rgba(0,0,0,0.07);
            height: 100vh;
            position: fixed;
            top: var(--admin-header-height);
            left: 0;
            z-index: 1000;
            transition: transform 0.25s ease-in-out;
        }
        
        #sidebar-wrapper .sidebar-heading {
            background: var(--orange);
            color: var(--white);
            text-align: center;
        }
        
        #sidebar-wrapper .list-group-item {
            border: none;
            border-left: 3px solid transparent;
            padding: 1rem 1.5rem;
            margin: 0.5rem 1rem;
            border-radius: var(--radius-card);
            background: var(--cream);
            font-weight: 600;
            color: var(--text);
            transition: all 0.2s ease;
        }
        
        #sidebar-wrapper .list-group-item:hover {
            background: var(--orange);
            color: var(--white);
            border-left-color: var(--orange-dark);
            transform: translateX(5px);
        }
        
        #sidebar-wrapper .list-group-item.active {
            background: var(--orange);
            color: var(--white);
            border-left-color: var(--orange-dark);
            font-weight: 700;
        }
        
        /* Header styling */
        #page-content-wrapper {
            margin-left: var(--admin-sidebar-width);
            min-height: 100vh;
            transition: margin 0.25s ease-in-out;
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
        
        /* Card styling to match website */
        .card {
            border: none;
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }
        
        .card-header {
            background: var(--cream2);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            border-top-left-radius: var(--radius-card);
            border-top-right-radius: var(--radius-card);
            font-weight: 700;
            color: var(--text);
            padding: 1rem 1.5rem;
        }
        
        /* Stats cards */
        .card.bg-primary.text-white {
            background: var(--orange) !important;
        }
        
        .card.bg-success.text-white {
            background: var(--green) !important;
        }
        
        .card.bg-info.text-white {
            background: var(--orange-dark) !important;
        }
        
        .card.bg-warning.text-white {
            background: var(--orange-light) !important;
        }
        
        /* Button styling */
        .btn-primary {
            background: var(--orange);
            border: none;
            font-weight: 600;
            border-radius: var(--radius-pill);
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            background: var(--orange-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(224, 123, 42, 0.3);
        }
        
        .btn-outline-primary {
            color: var(--orange);
            border-color: var(--orange);
            border-radius: var(--radius-pill);
            font-weight: 600;
        }
        
        .btn-outline-primary:hover {
            background: var(--orange);
            color: var(--white);
        }
        
        .btn-outline-success {
            color: var(--green);
            border-color: var(--green);
            border-radius: var(--radius-pill);
        }
        
        .btn-outline-success:hover {
            background: var(--green);
            color: var(--white);
        }
        
        .btn-outline-danger {
            color: var(--red-x);
            border-color: var(--red-x);
            border-radius: var(--radius-pill);
        }
        
        .btn-outline-danger:hover {
            background: var(--red-x);
            color: var(--white);
        }
        
        /* Badge styling */
        .badge {
            font-weight: 600;
            padding: 0.5em 0.8em;
            border-radius: var(--radius-pill);
        }
        
        .bg-success {
            background-color: var(--green) !important;
        }
        
        .bg-info {
            background-color: var(--orange-dark) !important;
        }
        
        .bg-warning {
            background-color: var(--orange-light) !important;
        }
        
        .bg-secondary {
            background-color: var(--text-muted) !important;
        }
        
        /* Table styling */
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            background-color: var(--cream2);
            font-weight: 700;
            color: var(--text);
            border-bottom: 2px solid rgba(0,0,0,0.1);
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .table-hover tbody tr:hover {
            background-color: var(--cream);
        }
        
        /* Responsive adjustments */
        @media (max-width: 991px) {
            #sidebar-wrapper {
                transform: translateX(-100%);
            }
            
            #sidebar-wrapper.toggled {
                transform: translateX(0);
            }
            
            #page-content-wrapper {
                margin-left: 0;
            }
            
            #page-content-wrapper .navbar {
                left: 0;
            }
        }
        
        /* Sidebar toggle button */
        #menu-toggle {
            background: var(--orange);
            color: var(--white);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        #menu-toggle:hover {
            background: var(--orange-dark);
            transform: scale(1.1);
        }
        
        /* Dropdown menu */
        .dropdown-menu {
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-md);
            border: none;
            margin-top: 0.5rem;
        }
        
        .dropdown-item {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background: var(--orange);
            color: var(--white);
        }
        
        .dropdown-divider {
            border-top: 1px solid rgba(0,0,0,0.1);
            margin: 0.5rem 0;
        }
        
        /* Custom scrollbar for sidebar */
        #sidebar-wrapper::-webkit-scrollbar {
            width: 6px;
        }
        
        #sidebar-wrapper::-webkit-scrollbar-track {
            background: var(--cream);
        }
        
        #sidebar-wrapper::-webkit-scrollbar-thumb {
            background: var(--orange);
            border-radius: 3px;
        }
        
        #sidebar-wrapper::-webkit-scrollbar-thumb:hover {
            background: var(--orange-dark);
        }
    </style>
</head>
<body>
<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div class="bg-white" id="sidebar-wrapper">
        <div class="sidebar-heading text-center py-4">
            <img src="../assets/images/logo.PNG" alt="Shakti Bites Logo" style="max-height: 40px; margin-bottom: 5px;">
            <h4 class="text-uppercase mb-0">Admin Panel</h4>
        </div>
        <div class="list-group list-group-flush">
            <a href="dashboard.php" class="list-group-item list-group-item-action active">
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
            <a href="orders.php" class="list-group-item list-group-item-action">
                <i class="bi bi-boxes me-2"></i>Orders
            </a>
        </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="d-flex align-items-center">
                <img src="../assets/images/logo.PNG" alt="Shakti Bites" height="30" class="me-2">
                <button class="btn btn-primary" id="menu-toggle">
                    <i class="bi bi-list"></i>
                </button>
            </div>
            
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../profile.php"><i class="bi bi-person"></i> My Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        
        <div class="container-fluid px-4">
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-primary text-white h-100">
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
                    <div class="card bg-success text-white h-100">
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
                    <div class="card bg-info text-white h-100">
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
                    <div class="card bg-warning text-white h-100">
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
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Recent Orders</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
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
                                                        <span class="badge bg-<?php 
                                                                echo (($order['order_status'] == 'completed') ? 'success' : 
                                                                (($order['order_status'] == 'processing') ? 'info' : 
                                                                (($order['order_status'] == 'pending') ? 'warning' : 'secondary')));
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
                    <div class="card mb-4">
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
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>

<!-- Bootstrap 5.3.3 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Menu Toggle Script -->
<script>
    const menuToggle = document.getElementById('menu-toggle');
    const sidebarWrapper = document.getElementById('sidebar-wrapper');
    const pageContentWrapper = document.getElementById('page-content-wrapper');
    
    menuToggle.addEventListener('click', function () {
        sidebarWrapper.classList.toggle('active');
        pageContentWrapper.classList.toggle('sidebar-toggled');
    });
    
    // Close sidebar when clicking outside on mobile
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