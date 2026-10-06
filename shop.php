<?php
$page_title = 'Shop';
require_once __DIR__ . '/includes/catalog.php';
$products = shakti_catalog();
include 'includes/header.php';
?>

<main class="shop-page">
  <section class="shop-intro" aria-labelledby="shop-title">
    <div class="container">
      <p class="shop-eyebrow">Real ingredients · Real taste</p>
      <h1 id="shop-title">Choose Your Perfect Protein Snack</h1>
      <p>Clean ingredients. Real taste. No sugar crash.</p>
    </div>
  </section>

  <section class="shop-picks" aria-label="Featured protein bites">
    <div class="container shop-pick-grid">
      <?php
      $picks = [
        1 => ['tag' => 'Best for Daily Use', 'button' => 'Shop Peanut', 'tone' => 'peanut'],
        2 => ['tag' => 'Most Loved', 'button' => 'Shop Almond', 'tone' => 'almond'],
        3 => ['tag' => 'Premium Pick', 'button' => 'Shop Premium', 'tone' => 'premium'],
      ];
      foreach ($picks as $id => $pick):
        $product = $products[$id];
      ?>
        <article class="shop-pick-card shop-pick-<?php echo $pick['tone']; ?>">
          <div class="shop-pick-tag"><?php echo htmlspecialchars($pick['tag']); ?></div>
          <a class="shop-pick-image" href="product/<?php echo rawurlencode($product['slug']); ?>" aria-label="View <?php echo htmlspecialchars($product['name']); ?>">
            <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
          </a>
          <div class="shop-pick-copy">
            <h2><?php echo $id === 1 ? 'Peanut Protein Bites' : ($id === 2 ? 'Almond Protein Bites' : 'Dry Fruit Protein Bites'); ?></h2>
            <p><?php echo htmlspecialchars($product['short']); ?></p>
            <a class="shop-pill" href="product/<?php echo rawurlencode($product['slug']); ?>"><?php echo htmlspecialchars($pick['button']); ?></a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="shop-comparison" aria-labelledby="comparison-title">
    <div class="container">
      <h2 id="comparison-title">Quick Comparison</h2>
      <p class="shop-section-sub">Not sure which one to choose?</p>
      <div class="shop-table-wrap">
        <table class="shop-compare-table">
          <thead><tr><th scope="col">Product</th><th scope="col">Taste</th><th scope="col">Best For</th></tr></thead>
          <tbody>
            <tr><th scope="row"><span aria-hidden="true">🥜</span> Peanut</th><td><span aria-hidden="true">🟤</span> Nutty</td><td>Daily Energy</td></tr>
            <tr><th scope="row"><span aria-hidden="true">🌰</span> Almond</th><td><span aria-hidden="true">🍫</span> Chocolate</td><td>Cravings</td></tr>
            <tr><th scope="row"><span aria-hidden="true">🌿</span> Dry Fruits</th><td><span aria-hidden="true">🌱</span> Rich</td><td>Premium Snacking</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <section class="shop-all" id="all-products" aria-labelledby="all-products-title">
    <div class="shop-band-title"><h2 id="all-products-title">All Products</h2></div>
    <div class="container shop-all-grid">
      <?php
      $allLabels = [1 => '💪 Daily Use', 2 => '🔥 Best Seller', 3 => '🎊 Premium'];
      $allButtons = [1 => 'Start Daily Pack', 2 => 'Try Chocolate Bite', 3 => 'Experience Premium'];
      foreach ($products as $id => $product):
      ?>
        <article class="shop-product-card shop-product-<?php echo (int) $id; ?>">
          <div class="shop-product-ribbon"><?php echo $allLabels[$id]; ?></div>
          <a class="shop-product-photo" href="product/<?php echo rawurlencode($product['slug']); ?>">
            <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
          </a>
          <h3><?php echo htmlspecialchars($product['name']); ?></h3>
          <div class="shop-rating"><span aria-hidden="true">✦</span><small>Clean ingredients · Made with care</small></div>
          <div class="shop-product-price"><strong>&#8377;<?php echo number_format((int) $product['price']); ?></strong><small>per box</small></div>
          <a class="shop-pill shop-product-cta" href="product/<?php echo rawurlencode($product['slug']); ?>"><?php echo $allButtons[$id]; ?></a>
          <ul>
            <?php foreach (array_slice($product['benefits'], 0, 3) as $benefit): ?><li><?php echo htmlspecialchars($benefit); ?></li><?php endforeach; ?>
          </ul>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="shop-stockup" aria-labelledby="stockup-title">
    <div class="shop-band-title"><h2 id="stockup-title">Save More When You Stock Up</h2><p>More protein bites, more to share</p></div>
    <div class="container shop-combo-grid">
      <article class="shop-combo-card">
        <h3>Starter Combo</h3>
        <a href="combo" class="shop-combo-image"><img src="assets/images/Combo-product-3.png" alt="Starter combo with three boxes"></a>
        <h4>Starter Combo</h4><p>Try a mix of our protein bites.</p>
        <ul><li>Three delicious boxes</li><li>Great for discovering your favourite</li><li>Easy to share</li></ul>
        <a href="combo" class="shop-pill shop-combo-button">Explore Combos</a>
      </article>
      <article class="shop-combo-card shop-combo-featured">
        <span class="shop-combo-badge">Popular choice</span>
        <h3>Best Value Combo</h3>
        <a href="combo" class="shop-combo-image"><img src="assets/images/Combo-product-6.png" alt="Best value combo with six boxes"></a>
        <h4>Best Value</h4><p>Stock up on nourishing snacks.</p>
        <ul><li>Six delicious boxes</li><li>Made for sharing or stocking up</li><li>Explore all three flavours</li></ul>
        <a href="combo" class="shop-pill shop-combo-button">Explore Combos</a>
      </article>
    </div>
    <a class="shop-more-combos" href="combo">Explore All Combos <span aria-hidden="true">→</span></a>
  </section>

  <section class="shop-final-cta" aria-labelledby="shop-cta-title">
    <div class="container">
      <p class="shop-eyebrow">A better snack starts here</p>
      <h2 id="shop-cta-title">Ready to Switch to<br>Clean Protein Snacks?</h2>
      <div class="shop-sparkle" aria-hidden="true">✦　✧　✦</div>
      <p>Find your new favourite bite today.</p>
      <a class="shop-pill" href="#all-products">Shop Protein Bites</a>
    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?>
