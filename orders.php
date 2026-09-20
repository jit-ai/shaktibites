<?php
session_start();
require_once 'includes/config.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = 'orders';
    header('Location: login');
    exit;
}

$orders = [];
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $statement = $pdo->prepare('SELECT order_number, total_amount, payment_status, order_status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC');
    $statement->execute([$_SESSION['user_id']]);
    $orders = $statement->fetchAll();
} catch (PDOException $exception) {
    // The account page stays available even before the store database is configured.
    $orders = [];
}

$page_title = 'My Orders';
include 'includes/header.php';
?>

<section class="account-hero">
  <div class="container">
    <p class="account-kicker">Account</p>
    <h1>My Orders</h1>
    <p>Track every Shakti Bites order in one place.</p>
  </div>
</section>

<section class="orders-section">
  <div class="container">
    <?php if (empty($orders)): ?>
      <div class="orders-empty">
        <i class="bi bi-bag-heart"></i>
        <h2>No orders yet</h2>
        <p>Your completed orders will appear here.</p>
        <a href="shop" class="btn btn-preview-primary">Start Shopping</a>
      </div>
    <?php else: ?>
      <div class="orders-list">
        <?php foreach ($orders as $order): ?>
          <article class="order-card">
            <div>
              <span class="order-card-label">Order number</span>
              <h2><?php echo htmlspecialchars($order['order_number']); ?></h2>
              <p><?php echo htmlspecialchars(date('d M Y', strtotime($order['created_at']))); ?></p>
            </div>
            <div><span class="order-card-label">Status</span><span class="order-status"><?php echo htmlspecialchars(ucfirst($order['order_status'])); ?></span></div>
            <div><span class="order-card-label">Total</span><strong>&#8377;<?php echo number_format((float) $order['total_amount']); ?></strong></div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
