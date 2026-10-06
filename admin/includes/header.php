<?php
/**
 * Admin panel shell: document head, topbar and the page container that the
 * sidebar is attached to. Included by every page under admin/.
 */
$currentPage = basename(parse_url($_SERVER['PHP_SELF'] ?? '/', PHP_URL_PATH), '.php');

$adminPageTitles = [
    'dashboard'     => 'Dashboard',
    'orders'        => 'Orders',
    'order-details' => 'Order Details',
    'products'      => 'Products',
    'product-add'   => 'Add Product',
    'product-edit'  => 'Edit Product',
    'users'         => 'Users',
    'categories'    => 'Categories',
];
$currentSection = $adminPageTitles[$currentPage] ?? 'Admin Panel';

$adminUserName = trim((string) ($_SESSION['user_name'] ?? 'Admin'));
$adminInitials = strtoupper(substr($adminUserName, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo htmlspecialchars($currentSection . ' · Shakti Bites Admin', ENT_QUOTES, 'UTF-8'); ?></title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Storefront theme (supplies the font faces and brand variables) -->
    <link rel="stylesheet" href="../assets/css/style.css?v=20261003-admin-redesign">
    <!-- Admin design system -->
    <link rel="stylesheet" href="css/admin-styles.css?v=20261003-admin-redesign">
</head>
<body class="admin-panel">
<!-- The sidebar is position:fixed, so it takes no part in layout here. Using
     flexbox would leave the content sized to its own content instead of the
     viewport, leaving dead space on the right of wide screens. -->
<div id="wrapper">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Page Content -->
    <div id="page-content-wrapper">
        <nav class="navbar admin-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-primary" id="menu-toggle" aria-label="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <span class="admin-topbar-context"><?php echo htmlspecialchars($currentSection, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>

            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link admin-account" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="admin-account-avatar" aria-hidden="true"><?php echo htmlspecialchars($adminInitials, ENT_QUOTES, 'UTF-8'); ?></span>
                            <?php echo htmlspecialchars($adminUserName, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end admin-dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item admin-dropdown-item" href="../shop"><i class="bi bi-shop me-2"></i> View Storefront</a></li>
                            <li><a class="dropdown-item admin-dropdown-item" href="../profile"><i class="bi bi-person me-2"></i> My Profile</a></li>
                            <li><hr class="admin-dropdown-divider"></li>
                            <li><a class="dropdown-item admin-dropdown-item" href="../logout"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container-fluid px-4 pt-4 pb-5">