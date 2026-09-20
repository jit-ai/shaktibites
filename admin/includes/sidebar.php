<?php
$currentPage = basename(parse_url($_SERVER['PHP_SELF'] ?? '/', PHP_URL_PATH), '.php');
?>
<!-- Sidebar -->
<div class="bg-white" id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-4">
        <img src="../assets/images/logo.PNG" alt="Shakti Bites Logo" style="max-height: 40px; margin-bottom: 5px;">
        <h4 class="text-uppercase mb-0">Admin Panel</h4>
    </div>
    <div class="list-group list-group-flush">
        <a href="dashboard" class="list-group-item list-group-item-action <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
        <a href="users" class="list-group-item list-group-item-action <?php echo $currentPage === 'users' ? 'active' : ''; ?>">
            <i class="bi bi-people me-2"></i>Users
        </a>
        <a href="products" class="list-group-item list-group-item-action <?php echo $currentPage === 'products' || $currentPage === 'product-add' || $currentPage === 'product-edit' ? 'active' : ''; ?>">
            <i class="bi bi-box me-2"></i>Products
        </a>
        <a href="categories" class="list-group-item list-group-item-action <?php echo $currentPage === 'categories' ? 'active' : ''; ?>">
            <i class="bi bi-tag me-2"></i>Categories
        </a>
        <a href="orders" class="list-group-item list-group-item-action <?php echo $currentPage === 'orders' || $currentPage === 'order-details' ? 'active' : ''; ?>">
            <i class="bi bi-boxes me-2"></i>Orders
        </a>
    </div>
</div>
<!-- /#sidebar-wrapper -->
