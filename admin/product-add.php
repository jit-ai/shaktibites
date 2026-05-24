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

// Get categories for dropdown
$categoriesStmt = $pdo->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name");
$categories = $categoriesStmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $sku = $_POST['sku'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    $label = $_POST['label'] ?? '';
    $label_class = $_POST['label_class'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $image = $_POST['image'] ?? '';
    
    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, sku, category_id, label, label_class, stock, image, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([$name, $description, $price, $sku, $category_id, $label, $label_class, $stock, $image]);
        $success = "Product added successfully!";
        header('Location: products.php');
        exit;
    } catch (Exception $e) {
        $error = "Failed to add product: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Shakti Bites Admin</title>
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
                    <h2 class="h4">Add New Product</h2>
                </div>
                <div class="col-auto">
                    <a href="products.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Products
                    </a>
                </div>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Product Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sku" class="form-label">SKU *</label>
                                <input type="text" class="form-control" id="sku" name="sku" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label">Price (₹) *</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="stock" class="form-label">Stock Quantity *</label>
                                <input type="number" class="form-control" id="stock" name="stock" min="0" value="0" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="label" class="form-label">Label (Optional)</label>
                                <input type="text" class="form-control" id="label" name="label">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="label_class" class="form-label">Label Class (Optional)</label>
                                <input type="text" class="form-control" id="label_class" name="label_class">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Image Filename</label>
                            <input type="text" class="form-control" id="image" name="image" placeholder="e.g., product1.PNG">
                        </div>
                        
                        <button type="submit" name="add_product" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i> Add Product
                        </button>
                    </form>
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