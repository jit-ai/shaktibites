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

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_product']) && isset($_POST['product_id'])) {
        $productId = (int)$_POST['product_id'];
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        header('Location: products.php');
        exit;
    }
    
    if (isset($_POST['toggle_status']) && isset($_POST['product_id'])) {
        $productId = (int)$_POST['product_id'];
        // Get current status
        $stmt = $pdo->prepare("SELECT is_active FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();
        
        if ($product) {
            $newStatus = $product['is_active'] ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE products SET is_active = ? WHERE id = ?");
            $stmt->execute([$newStatus, $productId]);
        }
        header('Location: products.php');
        exit;
    }
    
    if (isset($_POST['add_product'])) {
        // Handle product addition (would need file upload handling in real implementation)
        // For now, just redirect back
        header('Location: products.php');
        exit;
    }
}

// Get products with pagination and category info
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total count for pagination
$totalStmt = $pdo->query("SELECT COUNT(*) as total FROM products");
$totalProducts = $totalStmt->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

// Get products with category names
$productsStmt = $pdo->prepare("
    SELECT p.id, p.name, p.price, p.image, p.label, p.label_class, 
           p.is_active, p.stock, p.sku, p.created_at,
           c.name as category_name
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    ORDER BY p.created_at DESC 
    LIMIT ? OFFSET ?
");
$productsStmt->execute([$limit, $offset]);
$products = $productsStmt->fetchAll();

// Get categories for dropdown (for add/edit forms)
$categoriesStmt = $pdo->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name");
$categories = $categoriesStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Shakti Bites Admin</title>
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
            <a href="dashboard.php" class="list-group-item list-group-item-action">
                <i class="bi bi-speedometer2 me-2"></i>Dashboard
            </a>
            <a href="users.php" class="list-group-item list-group-item-action">
                <i class="bi bi-people me-2"></i>Users
            </a>
            <a href="products.php" class="list-group-item list-group-item-action active">
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
            <div class="row mb-4">
                <div class="col">
                    <h2 class="h4">Manage Products</h2>
                </div>
                <div class="col-auto">
                    <!-- Add Product Button -->
                    <a href="product-add.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add Product
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Product Image</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>SKU</th>
                                            <th>Stock</th>
                                            <th>Status</th>
                                            <th>Added</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($products)): ?>
                                            <tr>
                                                <td colspan="10" class="text-center py-4">No products found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($products as $product): ?>
                                                <tr>
                                                    <td><?php echo $product['id']; ?></td>
                                                    <td>
                                                        <?php if (!empty($product['image'])): ?>
                                                            <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                                 class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                                <i class="bi bi-image"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                                                    <td>₹<?php echo number_format($product['price'], 2); ?></td>
                                                    <td><?php echo htmlspecialchars($product['sku']); ?></td>
                                                    <td><?php echo $product['stock']; ?></td>
                                                    <td>
                                                        <form method="POST" action="" class="d-inline">
                                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                            <button type="submit" name="toggle_status" class="btn btn-sm btn-<?php echo $product['is_active'] ? 'outline-success' : 'outline-secondary'; ?>">
                                                                <i class="bi bi-<?php echo $product['is_active'] ? 'check-circle' : 'x-circle'; ?>"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                    <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="product-edit.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <form method="POST" action="" class="d-inline">
                                                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                                <button type="submit" name="delete_product" class="btn btn-sm btn-outline-danger" 
                                                                        onclick="return confirm('Are you sure you want to delete this product?');">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
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
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5.3.3 JS -->
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