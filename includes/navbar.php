<?php
require_once __DIR__ . '/catalog.php';
// Singles and combo packs share the bag, so the preview is priced from the
// full orderable catalogue.
$cartProducts = shakti_cart_catalog();
$cartCount = 0;
$cartSubtotal = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $productId => $quantity) {
    $quantity = max(0, (int) $quantity);
    if ($quantity < 1 || !isset($cartProducts[$productId])) {
      continue;
    }
    $cartCount += $quantity;
    $cartSubtotal += $cartProducts[$productId]['price'] * $quantity;
  }
}
?>
<nav class="sb-navbar navbar navbar-expand-lg navbar-light">
  <div class="container">

    <!-- Brand Logo -->
    <a class="navbar-brand sb-brand" href="./">
      <img src="assets/images/logo.PNG" alt="Shakti Bites" height="40" class="d-inline-block align-middle">
    </a>

    <!-- Desktop links -->
    <div class="sb-navbar-links d-none d-lg-flex">
      <?php $sbNavVariant = 'desktop'; include __DIR__ . '/nav-links.php'; ?>
    </div>

    <!-- Mobile quick actions -->
    <div class="sb-nav-actions d-lg-none">
      <a class="sb-nav-cart" href="cart" aria-label="View cart, <?php echo $cartCount; ?> item<?php echo $cartCount === 1 ? '' : 's'; ?>">
        <i class="bi bi-cart" aria-hidden="true"></i>
        <span class="sb-nav-cart-count<?php echo $cartCount === 0 ? ' d-none' : ''; ?>"><?php echo $cartCount; ?></span>
      </a>
      <button
        class="sb-nav-toggle"
        type="button"
        data-bs-toggle="offcanvas"
        data-bs-target="#sbMobileNav"
        aria-controls="sbMobileNav"
        aria-expanded="false"
        aria-label="Open menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>

  </div>
</nav>

<!-- ===== MOBILE DRAWER (opens from the right) ===== -->
<div class="offcanvas offcanvas-end sb-drawer" tabindex="-1" id="sbMobileNav" aria-labelledby="sbDrawerTitle">
  <div class="sb-drawer-head">
    <span class="sb-drawer-title" id="sbDrawerTitle">Menu</span>
    <button type="button" class="sb-drawer-close" data-bs-dismiss="offcanvas" aria-label="Close menu">
      <i class="bi bi-x-lg" aria-hidden="true"></i>
    </button>
  </div>
  <div class="offcanvas-body sb-drawer-body">
    <?php $sbNavVariant = 'mobile'; include __DIR__ . '/nav-links.php'; ?>
    <p class="sb-drawer-note"><i class="bi bi-truck" aria-hidden="true"></i> Free shipping above &#8377;499 &middot; COD available</p>
  </div>
</div>
