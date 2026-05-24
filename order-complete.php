<?php
$page_title = 'Order Complete';
include 'includes/header.php';
?>

<!-- ===== ORDER COMPLETE SECTION ===== -->
<section class="order-complete-section">
  <div class="container text-center">
    <div class="order-complete-icon">
      <i class="bi bi-check-circle-fill"></i>
    </div>
    <h1 class="order-complete-title">Thank You for Your Order!</h1>
    <p class="order-complete-order">Your order has been placed successfully.</p>
    <p class="order-number">Order #SHK20260512001</p>

    <div class="order-details">
      <h4>Order Details</h4>
      <div class="order-items">
        <div class="order-item">
          <span>Peanut Jaggery Power Bites × 1</span>
          <span>₹249</span>
        </div>
        <div class="order-item">
          <span>Almond Cacao Power Bites × 2</span>
          <span>₹558</span>
        </div>
        <hr>
        <div class="order-total-row">
          <strong>Total Paid</strong>
          <strong>₹847</strong>
        </div>
      </div>

      <div class="order-info">
        <p><strong>Estimated Delivery:</strong> 3-5 Business Days</p>
        <p><strong>Payment Method:</strong> Cash on Delivery</p>
      </div>
    </div>

    <div class="order-actions">
      <a href="index.php" class="btn btn-home">Continue Shopping</a>
      <a href="#" class="btn btn-track">Track Order</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>