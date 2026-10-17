<?php
require_once __DIR__ . '/checkout-pricing.php';
$drawerProducts = shakti_cart_catalog();
$drawerOnline = shakti_checkout_pricing($_SESSION['cart'] ?? [], $drawerProducts, 'online');
$drawerCod = shakti_checkout_pricing($_SESSION['cart'] ?? [], $drawerProducts, 'cod');
$drawerSingleSavings = 0;
$drawerComboSavings = 0;
foreach ($_SESSION['cart'] ?? [] as $drawerId => $drawerQty) {
    if (!isset($drawerProducts[$drawerId]) || (int) $drawerQty < 1) continue;
    $drawerProduct = $drawerProducts[$drawerId];
    $drawerSaving = max(0, ($drawerProduct['original_price'] ?? $drawerProduct['price']) - $drawerProduct['price']) * (int) $drawerQty;
    if ($drawerProduct['type'] === 'single') $drawerSingleSavings += $drawerSaving;
    else $drawerComboSavings += $drawerSaving;
}
?>
<dialog id="headerCartDrawer" class="header-cart" aria-labelledby="headerCartTitle">
  <header class="header-cart-head"><button type="button" data-cart-close aria-label="Close cart">&#8249;</button><h2 id="headerCartTitle">Your Cart</h2><span class="header-cart-heart" aria-hidden="true">&#9825;</span></header>
  <div class="header-cart-banner">Real ingredients. Real energy. Better snacking.</div>
  <div class="header-cart-scroll">
    <div class="header-cart-heading"><h3><i class="bi bi-cart3" aria-hidden="true"></i> Cart details</h3><span>Total items: <b id="headerCartCount">0</b></span></div>
    <p id="headerCartStatus" class="header-cart-status" role="status" aria-live="polite"></p>
    <div id="headerCartItems"></div>
    <div class="bag-delivery" id="bagDelivery"><strong>&#127881; You've unlocked FREE delivery</strong><div class="bag-delivery-track"><span></span></div><small>Free shipping on your order</small></div>
    <a class="header-cart-more" href="shop">Add more items <span aria-hidden="true">&#8250;</span></a>
    <a href="cart" class="bag-full-cart">View full cart</a>
    <div class="header-cart-offer">Unlock your best offer at checkout!</div>
    <section class="bag-price-summary" id="bagPriceSummary" aria-labelledby="bagPriceTitle">
      <h3 id="bagPriceTitle">Price Summary</h3>
      <dl>
        <div><dt id="bagOrderLabel">Order total (<?php echo $drawerOnline['boxes']; ?> boxes)</dt><dd id="bagOriginalTotal">&#8377;<?php echo number_format($drawerOnline['subtotal'] + $drawerSingleSavings + $drawerComboSavings); ?></dd></div>
        <div id="bagSingleRow" <?php if (!$drawerSingleSavings) echo 'hidden'; ?>><dt>Product discount</dt><dd id="bagSingleDiscount" class="bag-discount">&minus; &#8377;<?php echo number_format($drawerSingleSavings); ?></dd></div>
        <div id="bagComboRow" <?php if (!$drawerComboSavings) echo 'hidden'; ?>><dt>Combo discount</dt><dd id="bagComboDiscount" class="bag-discount">&minus; &#8377;<?php echo number_format($drawerComboSavings); ?></dd></div>
        <div><dt>Shipping</dt><dd>Free</dd></div>
        <div><dt>Prepaid discount <small>Only UPI / Card / Netbanking</small></dt><dd id="bagPrepaidDiscount" class="bag-discount">&minus; &#8377;<?php echo number_format($drawerOnline['prepaid_discount']); ?></dd></div>
        <div><dt>COD fee <small class="bag-cod-note">Prepaid discount won't apply</small></dt><dd id="bagCodFee">+ &#8377;<?php echo number_format($drawerCod['cod_fee']); ?></dd></div>
      </dl>
      <div class="bag-pay-total"><strong>To pay (online)</strong><strong id="bagOnlineTotal">&#8377;<?php echo number_format($drawerOnline['total']); ?></strong></div>
      <div class="bag-pay-total"><strong>Pay on delivery (COD)</strong><strong id="bagCodTotal">&#8377;<?php echo number_format($drawerCod['total']); ?></strong></div>
    </section>
    <div class="bag-savings" id="headerCartSaving">You save &#8377;<?php echo number_format($drawerSingleSavings + $drawerComboSavings + $drawerOnline['prepaid_discount']); ?> with online payment</div>
    <div class="bag-payment-methods"><span>VISA</span><span>Mastercard</span><span>UPI</span><span>RuPay</span><span>Netbanking</span></div>
    <p class="bag-secure">&#128274; 100% secured payments</p>
  </div>
  <footer class="header-cart-bottom">
    <div class="header-cart-bottom-row"><div><strong id="headerCartTotal">&#8377;<?php echo number_format($drawerOnline['total']); ?></strong><small id="bagCodAlternative">or &#8377;<?php echo number_format($drawerCod['total']); ?> with COD</small></div><a href="checkout" class="header-cart-continue" id="headerCartContinue">Continue <span aria-hidden="true">&#8594;</span></a></div>
  </footer>
</dialog>
<script src="assets/js/cart-drawer.js?v=<?php echo filemtime(__DIR__ . '/../assets/js/cart-drawer.js'); ?>" defer></script>
