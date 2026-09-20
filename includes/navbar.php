<?php
$cartProducts = [
  1 => ['name' => 'Peanut Jaggery Power Bites', 'price' => 249, 'image' => 'product1.PNG'],
  2 => ['name' => 'Almond Cacao Power Bites', 'price' => 279, 'image' => 'product2.PNG'],
  3 => ['name' => 'Dry Fruit Cardamom Bites', 'price' => 299, 'image' => 'product3.PNG'],
];
$cartCount = 0;
$cartSubtotal = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $productId => $quantity) {
    $quantity = max(0, (int) $quantity);
    $cartCount += $quantity;
    if (isset($cartProducts[$productId])) {
      $cartSubtotal += $cartProducts[$productId]['price'] * $quantity;
    }
  }
}
?>
<nav class="sb-navbar navbar navbar-expand-lg navbar-light">
  <div class="container">

    <!-- Brand Logo -->
    <a class="navbar-brand sb-brand" href="./">
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
          <a class="nav-link sb-nav-link <?php echo ($page_title === 'Home') ? 'active' : ''; ?>" href="./">HOME</a>
        </li>
        <li class="nav-item">
          <a class="nav-link sb-nav-link <?php echo ($page_title === 'Shop') ? 'active' : ''; ?>" href="shop">SHOP</a>
        </li>
        <li class="nav-item">
          <a class="nav-link sb-nav-link <?php echo ($page_title === 'Combo') ? 'active' : ''; ?>" href="combo">COMBO</a>
        </li>
        <li class="nav-item">
          <a class="nav-link sb-nav-link <?php echo ($page_title === 'About') ? 'active' : ''; ?>" href="about">ABOUT</a>
        </li>
        <li class="nav-item">
          <a class="nav-link sb-nav-link <?php echo ($page_title === 'Contact') ? 'active' : ''; ?>" href="contact">CONTACT</a>
        </li>
        <!-- Cart Icon -->
        <li class="nav-item dropdown cart-dropdown-wrap">
          <a class="nav-link sb-nav-link position-relative dropdown-toggle" href="cart" id="cartDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="View cart preview">
            <i class="bi bi-cart"></i>
            <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger<?php echo $cartCount === 0 ? ' d-none' : ''; ?>">
              <?php echo $cartCount; ?>
            </span>
          </a>
          <div class="dropdown-menu dropdown-menu-end cart-dropdown-menu" aria-labelledby="cartDropdown">
            <div class="cart-dropdown-heading">
              <span>Your Bag</span>
              <small><?php echo $cartCount; ?> item<?php echo $cartCount === 1 ? '' : 's'; ?></small>
            </div>
            <?php if ($cartCount === 0): ?>
              <div class="cart-dropdown-empty">
                <i class="bi bi-bag-heart"></i>
                <p>Your bag is empty.</p>
                <a href="shop" class="btn btn-preview-shop">Explore Products</a>
              </div>
            <?php else: ?>
              <div class="cart-dropdown-items">
                <?php foreach ($_SESSION['cart'] as $productId => $quantity): ?>
                  <?php if (isset($cartProducts[$productId]) && (int) $quantity > 0): ?>
                    <?php $product = $cartProducts[$productId]; ?>
                    <div class="cart-preview-item">
                      <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                      <div>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p>Qty: <?php echo (int) $quantity; ?> &times; &#8377;<?php echo number_format($product['price']); ?></p>
                      </div>
                      <strong>&#8377;<?php echo number_format($product['price'] * (int) $quantity); ?></strong>
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
              <div class="cart-preview-total">
                <span>Subtotal</span>
                <strong>&#8377;<?php echo number_format($cartSubtotal); ?></strong>
              </div>
              <p class="cart-preview-note"><i class="bi bi-truck"></i> Free delivery on orders above &#8377;499</p>
              <div class="cart-dropdown-actions">
                <a href="cart" class="btn btn-preview-secondary">View Cart</a>
                <a href="checkout" class="btn btn-preview-primary">Checkout <i class="bi bi-arrow-right"></i></a>
              </div>
            <?php endif; ?>
          </div>
        </li>
        <!-- User Menu -->
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle sb-nav-link account-nav-link" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Open account menu">
              <i class="bi bi-person-circle"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
              <?php if ($_SESSION['is_admin']): ?>
                <li><a class="dropdown-item" href="admin/dashboard"><i class="bi bi-speedometer2"></i> Admin Dashboard</a></li>
                <li><hr class="dropdown-divider"></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="profile"><i class="bi bi-person"></i> My Profile</a></li>
              <li><a class="dropdown-item" href="orders"><i class="bi bi-box-seam"></i> My Orders</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="logout"><i class="bi bi-box-arrow-left"></i> Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link sb-nav-link account-nav-link" href="login" aria-label="Login or create an account"><i class="bi bi-person-circle"></i></a>
          </li>
        <?php endif; ?>
      </ul>
    </div>

  </div>
</nav>
