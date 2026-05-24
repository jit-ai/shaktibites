<?php
$page_title = 'Checkout';
include 'includes/header.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Product data (same as in product.php)
$products = [
    1 => ['name' => 'Peanut Jaggery Power Bites', 'price' => 249],
    2 => ['name' => 'Almond Cacao Power Bites', 'price' => 279],
    3 => ['name' => 'Dry Fruit Cardamom Bites', 'price' => 299]
];
?>

<!-- ===== CHECKOUT HERO ===== -->
<section class="checkout-hero">
  <div class="container">
    <h1 class="checkout-hero-title">Checkout</h1>
  </div>
</section>

<!-- ===== CHECKOUT SECTION ===== -->
<section class="checkout-section">
  <div class="container">
    <div class="checkout-grid">

      <!-- Checkout Form -->
      <div class="checkout-form-col">
        <div class="billing-details-card">
          <h3 class="checkout-section-title"><i class="bi bi-geo-alt-fill"></i> Billing Details</h3>
          <form action="order-complete.php" method="post" class="checkout-form">
            <div class="form-row">
              <div class="form-group">
                <label for="first-name">First Name *</label>
                <input type="text" id="first-name" name="first_name" placeholder="Enter your first name" required>
              </div>
              <div class="form-group">
                <label for="last-name">Last Name *</label>
                <input type="text" id="last-name" name="last_name" placeholder="Enter your last name" required>
              </div>
            </div>

            <div class="form-group">
              <label for="address">Address *</label>
              <textarea id="address" name="address" placeholder="Street address, apartment, suite, etc." rows="2" required></textarea>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="city">City *</label>
                <input type="text" id="city" name="city" placeholder="City" required>
              </div>
              <div class="form-group">
                <label for="state">State *</label>
                <input type="text" id="state" name="state" placeholder="State" required>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="pincode">Pincode *</label>
                <input type="text" id="pincode" name="pincode" placeholder="6-digit pincode" required>
              </div>
              <div class="form-group">
                <label for="phone">Phone *</label>
                <input type="tel" id="phone" name="phone" placeholder="10-digit mobile number" required>
              </div>
            </div>

            <div class="form-group">
              <label for="email">Email Address *</label>
              <input type="email" id="email" name="email" placeholder="your@email.com" required>
            </div>

            <h3 class="checkout-section-title"><i class="bi bi-credit-card-2-front"></i> Payment Method</h3>
            <div class="payment-options">
              <label class="payment-option">
                <input type="radio" name="payment" value="cod" checked>
                <span><i class="bi bi-cash-stack"></i> Cash on Delivery</span>
              </label>
              <label class="payment-option">
                <input type="radio" name="payment" value="online">
                <span><i class="bi bi-credit-card"></i> Online Payment (UPI/Card/Netbanking)</span>
              </label>
            </div>

            <button type="submit" class="btn btn-place-order">
              <i class="bi bi-lock-fill"></i> Place Order
            </button>
          </form>
        </div>
      </div>

<!-- Order Summary -->
<div class="checkout-summary-col">
    <h3>Your Order Summary</h3>
    <div class="checkout-items">
        <?php
        $subtotal = 0;
        foreach ($_SESSION['cart'] as $productId => $quantity) {
            $productName = '';
            $productPrice = 0;
            switch($productId) {
                case 1:
                    $productName = 'Peanut Jaggery Power Bites';
                    $productPrice = 249;
                    break;
                case 2:
                    $productName = 'Almond Cacao Power Bites';
                    $productPrice = 279;
                    break;
                case 3:
                    $productName = 'Dry Fruit Cardamom Bites';
                    $productPrice = 299;
                    break;
            }
            if ($productName) {
                $itemTotal = $productPrice * $quantity;
                $subtotal += $itemTotal;
                ?>
                <div class="checkout-item">
                    <span><?php echo htmlspecialchars($productName); ?> × <?php echo $quantity; ?></span>
                    <span>₹<?php echo $itemTotal; ?></span>
                </div>
                <?php
            }
        }
        ?>
    </div>
    <hr>
    <div class="checkout-summary-row">
        <span>Subtotal</span>
        <span>₹<?php echo number_format($subtotal, 0); ?></span>
    </div>
    <div class="checkout-summary-row">
        <span>Shipping</span>
        <span style="color: var(--green);">Free</span>
    </div>
    <div class="checkout-summary-row">
        <span>Tax (GST)</span>
        <span>₹<?php echo number_format($subtotal * 0.05, 0); ?></span>
    </div>
    <hr>
    <div class="checkout-total-row">
        <strong>Total</strong>
        <strong style="color: var(--orange);">₹<?php echo number_format($subtotal * 1.05, 0); ?></strong>
    </div>

    <div class="checkout-guarantee">
        <i class="bi bi-shield-check"></i>
        <span>100% Secure Checkout</span>
    </div>
</div>

    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>