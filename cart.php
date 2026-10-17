<?php
require_once __DIR__ . '/includes/catalog.php';

$page_title = 'Shopping Cart';
include 'includes/header.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (empty($_SESSION['cart_token'])) {
    $_SESSION['cart_token'] = bin2hex(random_bytes(32));
}

// Single boxes and combo packs, priced from the shared catalogue
$products = shakti_cart_catalog();
$validCart = [];
foreach ($_SESSION['cart'] as $productId => $quantity) {
    if (isset($products[$productId]) && (int) $quantity > 0) $validCart[$productId] = (int) $quantity;
}
$bagCount = array_sum($validCart);
?>

<main class="commerce-page commerce-cart">
<!-- ===== CART HERO ===== -->
<section class="cart-hero">
  <div class="container">
    <nav class="commerce-steps" aria-label="Order progress"><span class="is-current" aria-current="step">01 &nbsp; Your cart</span><span>02 &nbsp; Delivery &amp; payment</span><span>03 &nbsp; All done</span></nav>
    <div class="commerce-title-row"><div><p class="commerce-eyebrow">GOOD FOOD. GREAT CHOICES.</p><h1 class="cart-hero-title">Your cart <span id="bag-count"><?php echo $bagCount; ?></span></h1><p>A little closer to better everyday snacking.</p></div><a href="shop" class="commerce-back">Continue shopping <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div>
  </div>
</section>

<!-- ===== CART SECTION ===== -->
<section class="cart-section">
  <div class="container">
    <div class="cart-grid">

<!-- Cart Items -->
<div class="cart-items-col">
    <?php if (empty($validCart)): ?>
        <div class="commerce-empty"><div class="commerce-empty-icon"><i class="bi bi-bag-heart" aria-hidden="true"></i></div><h2>Your next good habit starts here.</h2><p>Your cart is empty. Fill it with a little real energy.</p><a href="shop" class="commerce-primary">Explore Shakti Bites <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div>
    <?php else: ?>
        <div class="commerce-delivery"><i class="bi bi-truck" aria-hidden="true"></i><div><strong>Good snacks, delivered to your door</strong><span>Free shipping on this order</span></div><i class="bi bi-check-circle-fill" aria-hidden="true"></i></div>
        <div class="cart-list-label"><span>YOUR GOODNESS BAG</span><span>QUANTITY</span></div>
        <?php $itemIndex = 1; ?>
        <?php foreach ($_SESSION['cart'] as $productId => $quantity): ?>
            <?php
                $quantity = (int) $quantity;
                // Skip anything that is no longer in the catalogue so the row
                // list and the summary always show the same line items.
                if ($quantity < 1 || !isset($products[$productId])) {
                    continue;
                }
                $productName = $products[$productId]['name'];
                $productImg = $products[$productId]['image'];
                $productPrice = (int) $products[$productId]['price'];
                $originalPrice = (int) ($products[$productId]['original_price'] ?? $productPrice);
                $itemSaving = max(0, $originalPrice - $productPrice);
            ?>
            <div class="cart-item" data-product-id="<?php echo (int) $productId; ?>" data-price="<?php echo (int) $productPrice; ?>">
                <img src="assets/images/<?php echo $productImg; ?>" alt="<?php echo htmlspecialchars($productName); ?>" class="cart-item-image">
                <div class="cart-item-details">
                    <h5><?php echo htmlspecialchars($productName); ?></h5>
                    <div class="cart-item-pricing">
                        <?php if ($itemSaving > 0): ?><s class="cart-original-price">&#8377;<?php echo number_format($originalPrice); ?></s><?php endif; ?>
                        <strong class="cart-discounted-price">&#8377;<?php echo number_format($productPrice); ?></strong>
                        <span class="cart-price-unit">each</span>
                    </div>
                    <?php if ($itemSaving > 0): ?><p class="cart-item-saving">You save &#8377;<?php echo number_format($itemSaving); ?> <?php echo $products[$productId]['type'] === 'single' ? 'per box' : 'per combo'; ?></p><?php endif; ?>
                    <div class="cart-item-quantity">
                        <button class="qty-btn" onclick="decrementCart(<?php echo $itemIndex; ?>)">-</button>
                        <span id="qty-<?php echo $itemIndex; ?>"><?php echo $quantity; ?></span>
                        <button class="qty-btn" onclick="incrementCart(<?php echo $itemIndex; ?>)">+</button>
                    </div>
                </div>
                <button class="cart-item-remove" onclick="removeItem(<?php echo $itemIndex; ?>)" aria-label="Remove <?php echo htmlspecialchars($productName, ENT_QUOTES, 'UTF-8'); ?>"><i class="bi bi-trash3" aria-hidden="true"></i></button>
            </div>
            <?php $itemIndex++; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Cart Summary -->
<div class="cart-summary-col">
    <p class="commerce-eyebrow">THE HAPPY TOTAL</p><h4>Order summary</h4>
    <?php 
        $subtotal = 0;
        $savings = 0;
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $quantity = (int) $quantity;
            if ($quantity < 1 || !isset($products[$productId])) {
                continue;
            }
            $subtotal += $products[$productId]['price'] * $quantity;
            $savings += max(0, ($products[$productId]['original_price'] ?? $products[$productId]['price']) - $products[$productId]['price']) * $quantity;
        }
        $tax = 0;
        $total = $subtotal;
    ?>
    <div class="cart-savings-summary" id="cart-savings" <?php if ($savings <= 0) echo 'hidden'; ?>><span>You save on this order</span><strong id="cart-savings-amount">&#8377;<?php echo number_format($savings); ?></strong></div>
    <div class="cart-summary-row"><span>Subtotal (after discount)</span>
        <span>₹<?php echo number_format($subtotal, 0); ?></span>
    </div>
    <div class="cart-summary-row">
        <span>Shipping</span>
        <span>Free</span>
    </div>
    <hr>
    <div class="cart-total-row">
        <strong>Total</strong>
        <strong>₹<?php echo number_format($total, 0); ?></strong>
    </div>
    <?php if ($bagCount > 0): ?><a href="checkout" class="btn btn-checkout"><i class="bi bi-lock" aria-hidden="true"></i> Checkout securely <i class="bi bi-arrow-right" aria-hidden="true"></i></a><?php endif; ?>
    <a href="shop" class="btn btn-continue">Continue Shopping</a>
    <p class="commerce-tax-note">All taxes included. No surprises at checkout.</p>
    <div class="commerce-payments"><span>UPI</span><span>VISA</span><span>Mastercard</span><span>COD</span></div>
    <div class="commerce-safe"><i class="bi bi-shield-check" aria-hidden="true"></i> Safe payments. Happy snacking.</div>
</div>

    </div>
  </div>
</section>

<section class="commerce-recommendations"><div class="container"><div class="commerce-recommend-heading"><div><p class="commerce-eyebrow">A LITTLE MORE GOODNESS</p><h2>Meet your next favourite.</h2></div><a href="shop">Shop all <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div><div class="commerce-product-grid">
<?php foreach (shakti_catalog() as $recommended): ?>
  <article class="commerce-product"><a href="product/<?php echo rawurlencode($recommended['slug']); ?>"><img src="assets/images/<?php echo htmlspecialchars($recommended['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($recommended['name'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy"><span class="commerce-product-tag"><?php echo htmlspecialchars($recommended['label']); ?></span><h3><?php echo htmlspecialchars($recommended['name']); ?></h3></a><div><strong>&#8377;<?php echo number_format($recommended['price']); ?></strong><form action="add_to_cart" method="post"><input type="hidden" name="id" value="<?php echo $recommended['id']; ?>"><input type="hidden" name="quantity" value="1"><button type="submit" aria-label="Add <?php echo htmlspecialchars($recommended['name'], ENT_QUOTES, 'UTF-8'); ?> to cart">ADD +</button></form></div></article>
<?php endforeach; ?>
</div></div></section>
<div class="commerce-benefits"><span><i class="bi bi-flower1" aria-hidden="true"></i> Real ingredients</span><span><i class="bi bi-box-seam" aria-hidden="true"></i> Packed with care</span><span><i class="bi bi-shield-check" aria-hidden="true"></i> Secure checkout</span></div>
</main>

<script>
const cartToken = '<?php echo $_SESSION['cart_token']; ?>';
const formatMoney = amount => `₹${Number(amount).toLocaleString('en-IN')}`;

async function persistCartChange(item, quantity) {
  const body = new URLSearchParams({ product_id: item.dataset.productId, quantity, token: cartToken });
  const response = await fetch('update_cart', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body
  });
  const result = await response.json();
  if (!response.ok || !result.ok) throw new Error(result.message || 'Unable to update your cart.');
  return result;
}

function updateCartSummary(result) {
  document.getElementById('bag-count').textContent = result.cart_count;
  document.getElementById('cart-savings').hidden = result.savings <= 0;
  document.getElementById('cart-savings-amount').textContent = formatMoney(result.savings);
  const rows = document.querySelectorAll('.cart-summary-row');
  rows[0].lastElementChild.textContent = formatMoney(result.subtotal);
  document.querySelector('.cart-total-row strong:last-child').textContent = formatMoney(result.total);
  document.querySelectorAll('[id^="cart-badge"], .sb-nav-cart-count').forEach(badge => {
    badge.textContent = result.cart_count;
    badge.classList.toggle('d-none', result.cart_count === 0);
  });
}

async function changeQuantity(id, quantity) {
  const item = document.querySelector(`#qty-${id}`).closest('.cart-item');
  const controls = item.querySelectorAll('button');
  controls.forEach(button => button.disabled = true);
  try {
    const result = await persistCartChange(item, quantity);
    if (quantity === 0) {
      item.remove();
      if (!document.querySelector('.cart-item')) window.location.reload();
    } else {
      document.querySelector(`#qty-${id}`).textContent = quantity;
    }
    updateCartSummary(result);
  } catch (error) {
    alert(error.message);
  } finally {
    controls.forEach(button => button.disabled = false);
  }
}

function incrementCart(id) {
  const quantity = Number(document.querySelector(`#qty-${id}`).textContent);
  changeQuantity(id, quantity + 1);
}
function decrementCart(id) {
  const quantity = Number(document.querySelector(`#qty-${id}`).textContent);
  if (quantity > 1) changeQuantity(id, quantity - 1);
}
function removeItem(id) { changeQuantity(id, 0); }
</script>

<?php include 'includes/footer.php'; ?>
