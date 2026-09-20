<?php
$currentPage = basename(parse_url($_SERVER['PHP_SELF'] ?? '/', PHP_URL_PATH), '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Admin styles -->
    <link rel="stylesheet" href="css/admin-styles.css">
</head>
<body>
<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom admin-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-primary" id="menu-toggle" aria-label="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </button>
                
            </div>

            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end admin-dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item admin-dropdown-item" href="../profile"><i class="bi bi-person me-2"></i> My Profile</a></li>
                            <li><hr class="admin-dropdown-divider"></li>
                            <li><a class="dropdown-item admin-dropdown-item" href="../logout"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container-fluid px-4 pt-4">
