<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
        die("Database connection error: " . htmlspecialchars($e->getMessage()));
    }
}

// Redirect if not logged in or not admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
        $userId = (int)$_POST['user_id'];
        // Prevent deleting yourself
        if ($userId != $_SESSION['user_id']) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
        }
        header('Location: users.php');
        exit;
    }
    
    if (isset($_POST['toggle_status']) && isset($_POST['user_id'])) {
        $userId = (int)$_POST['user_id'];
        // Get current status
        $stmt = $pdo->prepare("SELECT is_active FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if ($user) {
            $newStatus = $user['is_active'] ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ?");
            $stmt->execute([$newStatus, $userId]);
        }
        header('Location: users.php');
        exit;
    }
}

// Get users with pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get total count for pagination
$totalStmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $totalStmt->fetchColumn();
$totalPages = ceil($totalUsers / $limit);

// Get users
$usersStmt = $pdo->prepare("
    SELECT id, name, email, phone, is_admin, is_active, created_at 
    FROM users 
    ORDER BY created_at DESC 
    LIMIT ? OFFSET ?
");
$usersStmt->execute([$limit, $offset]);
$users = $usersStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Shakti Bites Admin</title>
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
            <a href="users.php" class="list-group-item list-group-item-action active">
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
            <div class="row mb-4">
                <div class="col">
                    <h2 class="h4">Manage Users</h2>
                </div>
                <div class="col-auto">
                    <!-- Add User Button (placeholder for modal) -->
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus me-1"></i> Add User
                    </button>
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
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Joined</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($users)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center py-4">No users found</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($users as $user): ?>
                                                <tr>
                                                    <td><?php echo $user['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo ($user['is_admin'] ? 'danger' : 'primary'); ?>">
                                                            <?php echo $user['is_admin'] ? 'Admin' : 'User'; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <form method="POST" action="" class="d-inline">
                                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                            <button type="submit" name="toggle_status" class="btn btn-sm btn-<?php echo $user['is_active'] ? 'outline-success' : 'outline-secondary'; ?>">
                                                                <i class="bi bi-<?php echo $user['is_active'] ? 'check-circle' : 'x-circle'; ?>"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <!-- Prevent deleting yourself -->
                                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                                <form method="POST" action="" class="d-inline">
                                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                                    <button type="submit" name="delete_user" class="btn btn-sm btn-outline-danger" 
                                                                            onclick="return confirm('Are you sure you want to delete this user?');">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </form>
                                                            <?php else: ?>
                                                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            <?php endif; ?>
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
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo max(1, $page - 1); ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo min($totalPages, $page + 1); ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
    <!-- /#page-content-wrapper -->
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                Active Account
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </form>
            </div>
        </div>
    </div>
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
    
    // Add user form submission (placeholder - would need actual PHP endpoint)
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        // In a real implementation, this would submit to a PHP script
        alert('User added successfully! (This is a demo - form not connected to backend)');
        const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
        modal.hide();
        this.reset();
    });
</script>
</body>
</html>