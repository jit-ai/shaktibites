<nav class="sb-navbar navbar navbar-expand-lg navbar-light bg-white">
  <div class="container">

    <!-- Brand Logo -->
    <a class="navbar-brand sb-brand" href="index.php">
      <img src="assets/images/logo.PNG" alt="Shakti Bites" height="100" class="d-inline-block align-middle">
    </a>

    <!-- Mobile Toggle -->
    <button
      class="navbar-toggler border-0 shadow-none"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#sbNavbar"
      aria-controls="sbNavbar"
      aria-expanded="false"
      aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Nav Links -->
    <div class="collapse navbar-collapse justify-content-end" id="sbNavbar">
      <ul class="navbar-nav gap-lg-2">
        <li class="nav-item">
          <a class="nav-link sb-nav-link <?php echo ($page_title === 'Home') ? 'active' : ''; ?>" href="index.php">HOME</a>
        </li>
        <li class="nav-item">
          <a class="nav-link sb-nav-link <?php echo ($page_title === 'Shop') ? 'active' : ''; ?>" href="shop.php">SHOP</a>
        </li>
        <li class="nav-item">
          <a class="nav-link sb-nav-link <?php echo ($page_title === 'Combo') ? 'active' : ''; ?>" href="combo.php">COMBO</a>
        </li>
        <li class="nav-item">
          <a class="nav-link sb-nav-link <?php echo ($page_title === 'About') ? 'active' : ''; ?>" href="about.php">ABOUT</a>
        </li>
        <li class="nav-item">
          <a class="nav-link sb-nav-link <?php echo ($page_title === 'Contact') ? 'active' : ''; ?>" href="contact.php">CONTACT</a>
        </li>
        <!-- Cart Icon -->
        <li class="nav-item position-relative">
          <a class="nav-link sb-nav-link position-relative" href="cart.php">
            <i class="bi bi-cart"></i>
            <?php
            $cartCount = 0;
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $quantity) {
                    $cartCount += $quantity;
                }
            }
            ?>
            <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
              <?php echo $cartCount; ?>
            </span>
          </a>
        </li>
        <!-- User Menu -->
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle sb-nav-link" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle"></i>
              <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
              <?php if ($_SESSION['is_admin']): ?>
                <li><a class="dropdown-item" href="admin/dashboard.php"><i class="bi bi-speedometer2"></i> Admin Dashboard</a></li>
                <li><hr class="dropdown-divider"></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> My Profile</a></li>
              <li><a class="dropdown-item" href="orders.php"><i class="bi bi-boxes"></i> My Orders</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link sb-nav-link" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link sb-nav-link" href="register.php"><i class="bi bi-person-plus"></i> Register</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>

  </div>
</nav>