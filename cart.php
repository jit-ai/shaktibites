<?php
$page_title = 'Shopping Cart';
include 'includes/header.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
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
            <div class="cart-item">
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
    <a href="checkout.php" class="btn btn-checkout">Proceed to Checkout</a>
    <a href="shop.php" class="btn btn-continue">Continue Shopping</a>
</div>

    </div>
  </div>
</section>

<script>
function incrementCart(id) {
   const qty = document.getElementById('qty-' + id);
   qty.innerText = parseInt(qty.innerText) + 1;
   updateCartCount();
 }

 function decrementCart(id) {
   const qty = document.getElementById('qty-' + id);
   if (parseInt(qty.innerText) > 1) {
       qty.innerText = parseInt(qty.innerText) - 1;
       updateCartCount();
   }
 }

 function removeItem(id) {
   document.querySelector('.cart-item:nth-child(' + id + ')').remove();
   updateCartCount();
 }

 function updateCartCount() {
   let total = 0;
   document.querySelectorAll('.cart-item').forEach(item => {
       const qtyElement = item.querySelector('[id^="qty-"]');
       if (qtyElement) {
           total += parseInt(qtyElement.innerText);
       }
   });
   
   // Update cart badge in navbar
   const cartBadge = document.getElementById('cart-badge');
   if (cartBadge) {
       cartBadge.innerText = total;
       
       // Show/hide badge based on count
       if (total > 0) {
           cartBadge.style.display = 'block';
       } else {
           cartBadge.style.display = 'none';
       }
   }
 }
</script>

<?php include 'includes/footer.php'; ?>