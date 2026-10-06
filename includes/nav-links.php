<?php
/**
 * Shared navigation items.
 *
 * The identical list is rendered twice: once for the desktop bar and once
 * inside the mobile right-hand drawer, so the two can never drift apart.
 * Callers set $sbNavVariant to 'mobile' before including this file.
 *
 * @var string $sbNavVariant 'mobile' for the drawer, anything else for desktop.
 */
$sbNavVariant = (isset($sbNavVariant) && $sbNavVariant === 'mobile') ? 'mobile' : 'desktop';
$sbIsMobile = $sbNavVariant === 'mobile';
$sbNavIdSuffix = $sbIsMobile ? 'Mobile' : 'Desktop';
$sbCartMenuId = 'cartDropdown' . $sbNavIdSuffix;
$sbAccountMenuId = 'accountDropdown' . $sbNavIdSuffix;
$sbNavListClass = $sbIsMobile ? 'sb-drawer-nav' : 'navbar-nav gap-lg-2';
$sbNavActive = static function (string $page) use ($page_title): string {
    return ($page_title === $page) ? ' active' : '';
};
?>
<ul class="<?php echo htmlspecialchars($sbNavListClass, ENT_QUOTES, 'UTF-8'); ?>">

  <li class="nav-item">
    <a class="nav-link sb-nav-link<?php echo $sbNavActive('Home'); ?>" href="./"><?php echo $sbIsMobile ? 'Home' : 'HOME'; ?><?php if ($sbIsMobile): ?><i class="bi bi-chevron-right sb-drawer-chevron" aria-hidden="true"></i><?php endif; ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link sb-nav-link<?php echo $sbNavActive('Shop'); ?>" href="shop"><?php echo $sbIsMobile ? 'Shop' : 'SHOP'; ?><?php if ($sbIsMobile): ?><i class="bi bi-chevron-right sb-drawer-chevron" aria-hidden="true"></i><?php endif; ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link sb-nav-link<?php echo $sbNavActive('Combo'); ?>" href="combo"><?php echo $sbIsMobile ? 'Combo' : 'COMBO'; ?><?php if ($sbIsMobile): ?><i class="bi bi-chevron-right sb-drawer-chevron" aria-hidden="true"></i><?php endif; ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link sb-nav-link<?php echo $sbNavActive('About'); ?>" href="about"><?php echo $sbIsMobile ? 'About' : 'ABOUT'; ?><?php if ($sbIsMobile): ?><i class="bi bi-chevron-right sb-drawer-chevron" aria-hidden="true"></i><?php endif; ?></a>
  </li>
  <li class="nav-item">
    <a class="nav-link sb-nav-link<?php echo $sbNavActive('Contact'); ?>" href="contact"><?php echo $sbIsMobile ? 'Contact' : 'CONTACT'; ?><?php if ($sbIsMobile): ?><i class="bi bi-chevron-right sb-drawer-chevron" aria-hidden="true"></i><?php endif; ?></a>
  </li>

  <li class="nav-item dropdown cart-dropdown-wrap sb-drawer-bag">
    <a class="nav-link sb-nav-link position-relative dropdown-toggle<?php echo $sbIsMobile ? ' sb-drawer-toggle-row' : ''; ?>" href="cart" id="<?php echo $sbCartMenuId; ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false"<?php echo $sbIsMobile ? '' : ' aria-label="View cart preview"'; ?>>
      <?php if ($sbIsMobile): ?>
        <span class="sb-drawer-row-label"><i class="bi bi-bag" aria-hidden="true"></i> My Bag</span>
        <span class="sb-drawer-row-meta"><?php echo $cartCount; ?> item<?php echo $cartCount === 1 ? '' : 's'; ?></span>
      <?php else: ?>
        <i class="bi bi-cart" aria-hidden="true"></i>
        <span id="cart-badge<?php echo $sbNavIdSuffix; ?>" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger<?php echo $cartCount === 0 ? ' d-none' : ''; ?>">
          <?php echo $cartCount; ?>
        </span>
      <?php endif; ?>
    </a>
    <div class="dropdown-menu dropdown-menu-end cart-dropdown-menu<?php echo $sbIsMobile ? ' sb-drawer-dropdown' : ''; ?>" aria-labelledby="<?php echo $sbCartMenuId; ?>">
      <div class="cart-dropdown-heading">
        <span>Your Bag</span>
        <small><?php echo $cartCount; ?> item<?php echo $cartCount === 1 ? '' : 's'; ?></small>
      </div>
      <?php if ($cartCount === 0): ?>
        <div class="cart-dropdown-empty">
          <i class="bi bi-bag-heart" aria-hidden="true"></i>
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
        <p class="cart-preview-note"><i class="bi bi-truck" aria-hidden="true"></i> Free delivery on orders above &#8377;499</p>
        <div class="cart-dropdown-actions">
          <a href="cart" class="btn btn-preview-secondary">View Cart</a>
          <a href="checkout" class="btn btn-preview-primary">Checkout <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
        </div>
      <?php endif; ?>
    </div>
  </li>

  <?php if (isset($_SESSION['user_id'])): ?>
    <li class="nav-item dropdown sb-drawer-account">
      <a class="nav-link dropdown-toggle sb-nav-link account-nav-link<?php echo $sbIsMobile ? ' sb-drawer-toggle-row' : ''; ?>" href="#" id="<?php echo $sbAccountMenuId; ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false"<?php echo $sbIsMobile ? '' : ' aria-label="Open account menu"'; ?>>
        <?php if ($sbIsMobile): ?>
          <span class="sb-drawer-row-label"><i class="bi bi-person-circle" aria-hidden="true"></i> My Account</span>
          <span class="sb-drawer-row-meta"><?php echo htmlspecialchars((string) ($_SESSION['user_name'] ?? 'Account')); ?></span>
        <?php else: ?>
          <i class="bi bi-person-circle" aria-hidden="true"></i>
        <?php endif; ?>
      </a>
      <ul class="dropdown-menu dropdown-menu-end sb-drawer-dropdown" aria-labelledby="<?php echo $sbAccountMenuId; ?>">
        <?php if (!empty($_SESSION['is_admin'])): ?>
          <li><a class="dropdown-item" href="admin/dashboard"><i class="bi bi-speedometer2" aria-hidden="true"></i> Admin Dashboard</a></li>
          <li><hr class="dropdown-divider"></li>
        <?php endif; ?>
        <li><a class="dropdown-item" href="profile"><i class="bi bi-person" aria-hidden="true"></i> My Profile</a></li>
        <li><a class="dropdown-item" href="orders"><i class="bi bi-box-seam" aria-hidden="true"></i> My Orders</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="logout"><i class="bi bi-box-arrow-left" aria-hidden="true"></i> Logout</a></li>
      </ul>
    </li>
  <?php else: ?>
    <li class="nav-item sb-drawer-account">
      <a class="nav-link sb-nav-link account-nav-link<?php echo $sbIsMobile ? ' sb-drawer-toggle-row' : ''; ?>" href="login"<?php echo $sbIsMobile ? '' : ' aria-label="Login or create an account"'; ?>>
        <?php if ($sbIsMobile): ?>
          <span class="sb-drawer-row-label"><i class="bi bi-person-circle" aria-hidden="true"></i> Login / Sign Up</span>
          <i class="bi bi-chevron-right sb-drawer-chevron" aria-hidden="true"></i>
        <?php else: ?>
          <i class="bi bi-person-circle" aria-hidden="true"></i>
        <?php endif; ?>
      </a>
    </li>
  <?php endif; ?>
</ul>
