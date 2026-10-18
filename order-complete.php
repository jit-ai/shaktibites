<?php
$page_title = 'Order Complete';
if (session_status() === PHP_SESSION_NONE) session_start();

$order = $_SESSION['last_order'] ?? null;
if (!$order) {
    header('Location: shop');
    exit;
}
include 'includes/header.php';
$paymentMethod = $order['payment_method'] === 'cod' ? 'Cash on Delivery' : 'Online Payment (Razorpay)';
?>

<!-- ===== ORDER COMPLETE SECTION ===== -->
<section class="order-complete-section">
  <div class="container text-center">
    <div class="order-complete-icon">
      <i class="bi bi-check-circle-fill"></i>
    </div>
    <h1 class="order-complete-title">Thank You for Your Order!</h1>
    <p class="order-complete-order">Your order has been placed successfully.</p>
    <p class="order-number">Order #<?php echo htmlspecialchars($order['order_number']); ?></p>
    <?php if (($order['whatsapp']['status'] ?? '') === 'accepted'): ?>
      <p class="order-whatsapp-status" role="status">Your order confirmation has been submitted to WhatsApp for your delivery number.</p>
    <?php elseif (in_array($order['whatsapp']['status'] ?? '', ['failed', 'configuration_error'], true)): ?>
      <p class="order-whatsapp-status" role="status">Your order is confirmed. The WhatsApp message could not be submitted; your order details are shown below.</p>
    <?php endif; ?>

    <div class="order-details">
      <h4>Order Details</h4>
      <div class="order-items">
        <?php foreach ($order['items'] as $item): ?>
          <div class="order-item">
            <span><?php echo htmlspecialchars($item['name']); ?> × <?php echo (int) $item['quantity']; ?></span>
            <span>₹<?php echo number_format($item['price'] * $item['quantity'], 0); ?></span>
          </div>
        <?php endforeach; ?>
        <hr>
        <?php if (isset($order['pricing'])): ?>
          <div class="order-item"><span>Shipping</span><span><?php echo $order['pricing']['shipping'] ? '&#8377;' . number_format($order['pricing']['shipping']) : 'Free'; ?></span></div>
          <?php if ($order['pricing']['prepaid_discount']): ?><div class="order-item"><span>Prepaid discount</span><span>&minus; &#8377;<?php echo number_format($order['pricing']['prepaid_discount']); ?></span></div><?php endif; ?>
          <?php if ($order['pricing']['cod_fee']): ?><div class="order-item"><span>COD fee</span><span>&#8377;<?php echo number_format($order['pricing']['cod_fee']); ?></span></div><?php endif; ?>
        <?php endif; ?>
        <div class="order-total-row">
          <strong><?php echo $order['payment_method'] === 'cod' ? 'Amount due on delivery' : 'Total paid'; ?></strong>
          <strong>₹<?php echo number_format($order['total_amount'], 0); ?></strong>
        </div>
      </div>

      <div class="order-info">
        <p><strong>Estimated Delivery:</strong> 3-5 Business Days</p>
        <p><strong>Payment Method:</strong> <?php echo $paymentMethod; ?></p>
        <p><strong>Payment Status:</strong> <?php echo htmlspecialchars(ucfirst($order['payment_status'])); ?></p>
      </div>
    </div>

    <div class="order-actions">
      <a href="./" class="btn btn-home">Continue Shopping</a>
      <a href="orders" class="btn btn-track">Track Order</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
