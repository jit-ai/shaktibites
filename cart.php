<?php
$page_title = 'Shopping Cart';
include 'includes/header.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (empty($_SESSION['cart_token'])) {
    $_SESSION['cart_token'] = bin2hex(random_bytes(32));
}

// Product data (same as in product.php)
$products = [
    1 => ['name' => 'Peanut Jaggery Power Bites', 'price' => 249, 'img' => 'product1.PNG'],
    2 => ['name' => 'Almond Cacao Power Bites', 'price' => 279, 'img' => 'product2.PNG'],
    3 => ['name' => 'Dry Fruit Cardamom Bites', 'price' => 299, 'img' => 'product3.PNG']
];
?>

<!-- ===== CART HERO ===== -->
<section class="cart-hero">
  <div class="container">
    <h1 class="cart-hero-title">Your Shopping Cart</h1>
  </div>
</section>

<!-- ===== CART SECTION ===== -->
<section class="cart-section">
  <div class="container">
    <div class="cart-grid">

<!-- Cart Items -->
<div class="cart-items-col">
    <?php if (empty($_SESSION['cart'])): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <?php $itemIndex = 1; ?>
        <?php foreach ($_SESSION['cart'] as $productId => $quantity): ?>
            <?php 
                $productName = '';
                $productImg = '';
                $productPrice = 0;
                switch($productId) {
                    case 1:
                        $productName = 'Peanut Jaggery Power Bites';
                        $productImg = 'product1.PNG';
                        $productPrice = 249;
                        break;
                    case 2:
                        $productName = 'Almond Cacao Power Bites';
                        $productImg = 'product2.PNG';
                        $productPrice = 279;
                        break;
                    case 3:
                        $productName = 'Dry Fruit Cardamom Bites';
                        $productImg = 'product3.PNG';
                        $productPrice = 299;
                        break;
                }
            ?>
            <div class="cart-item" data-product-id="<?php echo (int) $productId; ?>" data-price="<?php echo (int) $productPrice; ?>">
                <img src="assets/images/<?php echo $productImg; ?>" alt="<?php echo htmlspecialchars($productName); ?>" class="cart-item-image">
                <div class="cart-item-details">
                    <h5><?php echo htmlspecialchars($productName); ?></h5>
                    <p class="cart-item-price">₹<?php echo $productPrice; ?></p>
                    <div class="cart-item-quantity">
                        <button class="qty-btn" onclick="decrementCart(<?php echo $itemIndex; ?>)">-</button>
                        <span id="qty-<?php echo $itemIndex; ?>"><?php echo $quantity; ?></span>
                        <button class="qty-btn" onclick="incrementCart(<?php echo $itemIndex; ?>)">+</button>
                    </div>
                </div>
                <button class="cart-item-remove" onclick="removeItem(<?php echo $itemIndex; ?>)">×</button>
            </div>
            <?php $itemIndex++; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Cart Summary -->
<div class="cart-summary-col">
    <h4>Order Summary</h4>
    <?php 
        $subtotal = 0;
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            switch($productId) {
                case 1: $price = 249; break;
                case 2: $price = 279; break;
                case 3: $price = 299; break;
                default: $price = 0;
            }
            $subtotal += ($price * $quantity);
        }
        $tax = $subtotal * 0.05; // 5% tax
        $total = $subtotal + $tax;
    ?>
    <div class="cart-summary-row">
        <span>Subtotal</span>
        <span>₹<?php echo number_format($subtotal, 0); ?></span>
    </div>
    <div class="cart-summary-row">
        <span>Shipping</span>
        <span>Free</span>
    </div>
    <div class="cart-summary-row">
        <span>Tax</span>
        <span>₹<?php echo number_format($tax, 0); ?></span>
    </div>
    <hr>
    <div class="cart-total-row">
        <strong>Total</strong>
        <strong>₹<?php echo number_format($total, 0); ?></strong>
    </div>
    <a href="checkout" class="btn btn-checkout">Proceed to Checkout</a>
    <a href="shop" class="btn btn-continue">Continue Shopping</a>
</div>

    </div>
  </div>
</section>

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
  const rows = document.querySelectorAll('.cart-summary-row');
  rows[0].lastElementChild.textContent = formatMoney(result.subtotal);
  rows[2].lastElementChild.textContent = formatMoney(result.tax);
  document.querySelector('.cart-total-row strong:last-child').textContent = formatMoney(result.total);
  const badge = document.getElementById('cart-badge');
  if (badge) {
    badge.textContent = result.cart_count;
    badge.classList.toggle('d-none', result.cart_count === 0);
  }
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
