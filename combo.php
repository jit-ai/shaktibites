<?php
require_once __DIR__ . '/includes/catalog.php';
require_once __DIR__ . '/includes/combo-builder.php';
$page_title = 'Combo';
include 'includes/header.php';
if (empty($_SESSION['combo_token'])) $_SESSION['combo_token'] = bin2hex(random_bytes(32));
?>

<main class="combo-redesign">
  <?php $bundleHeadingTag = 'h1'; include __DIR__ . '/includes/combo-offers.php'; ?>

  <section class="bundle-benefits" aria-labelledby="bundle-benefits-title">
    <div class="bundle-benefits-band"><h2 id="bundle-benefits-title">Why choose combo?</h2></div>
    <div class="container">
      <p class="bundle-benefits-subtitle">3 reasons our customers pick combos</p>
      <div class="bundle-benefit-list">
        <article><span class="bundle-benefit-icon" aria-hidden="true"><i class="bi bi-currency-rupee"></i></span><div><h3>Save up to &#8377;<?php echo number_format($maxComboSaving); ?></h3><p>More of your favourite bites, better combo value.</p></div></article>
        <article><span class="bundle-benefit-icon" aria-hidden="true"><i class="bi bi-truck"></i></span><div><h3>Free shipping + fresh batch</h3><p>Freshly made snacks, delivered to your door.</p></div></article>
        <article><span class="bundle-benefit-icon" aria-hidden="true"><i class="bi bi-heart"></i></span><div><h3>A flavour for everyone</h3><p>Delicious variety, perfect for the whole family.</p></div></article>
      </div>
    </div>
  </section>

  <div class="bundle-launch-strip"><span aria-hidden="true">&#9889;</span> Discover your favourite combo today</div>

  <section class="bundle-cta" aria-labelledby="bundle-cta-title">
    <div class="container">
      <h2 id="bundle-cta-title">Ready to Switch to<br>Clean Protein Snacks?</h2>
      <div class="bundle-sparkles" aria-hidden="true"><span></span> &#10022; &#10022; &#10022; <span></span></div>
      <p>Save up to &#8377;<?php echo number_format($maxComboSaving); ?> today</p>
      <a href="#combo-offers" class="bundle-button">Get Your Combo Now</a>
    </div>
  </section>

  <?php
  $videoReviewGroup = 'combo';
  include __DIR__ . '/includes/video-reviews.php';
  ?>
</main>

<?php include 'includes/footer.php'; ?>
