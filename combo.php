<?php
require_once __DIR__ . '/includes/catalog.php';

$page_title = 'Combo';
include 'includes/header.php';

// Combo offers come from the shared catalogue, so the price shown here is
// exactly the price that is added to the cart and charged at checkout.
$comboOffers = shakti_combo_catalog();
?>

<main class="combo-landing">
  <section class="combo-intro" aria-labelledby="combo-title">
    <div class="container">
      <h1 id="combo-title">Save More When You Stock Up</h1>
      <p class="combo-intro-subtitle">More protein. More savings. Fewer reorders.</p>
      <p class="combo-intro-promo">🔥 Limited launch combo pricing — ending soon</p>
      <div class="combo-savings-pill">Up to &#8377;300 OFF on combos</div>
    </div>
  </section>

  <section class="combo-offers" aria-label="Combo offers">
    <div class="container combo-offer-grid">
      <?php foreach ($comboOffers as $offerId => $offer): ?>
        <article class="combo-offer-card combo-offer-<?php echo htmlspecialchars($offer['tone'], ENT_QUOTES, 'UTF-8'); ?>">
          <?php if ($offer['tone'] === 'popular'): ?><div class="combo-offer-ribbon">MOST POPULAR</div><?php else: ?><div class="combo-offer-tag"><?php echo htmlspecialchars($offer['tag']); ?></div><?php endif; ?>
          <div class="combo-offer-content">
            <h2><?php echo htmlspecialchars($offer['name']); ?></h2>
            <div class="combo-divider"></div>
            <p class="combo-offer-save">Save &#8377;<?php echo number_format($offer['saving']); ?></p>
            <a class="combo-offer-image" href="shop" aria-label="Browse snacks included in the <?php echo htmlspecialchars($offer['name']); ?>">
              <img src="assets/images/<?php echo htmlspecialchars($offer['image']); ?>" alt="<?php echo htmlspecialchars($offer['alt']); ?>">
            </a>
            <p class="combo-offer-price">&#8377;<?php echo number_format($offer['price']); ?></p>
            <p class="combo-offer-extra">Save &#8377;50</p>
            <p class="combo-offer-unit">Only &#8377;20 per laddoo</p>
            <form class="combo-offer-form" method="post" action="add_to_cart">
              <input type="hidden" name="id" value="<?php echo (int) $offerId; ?>">
              <input type="hidden" name="quantity" value="1">
              <button type="submit" class="combo-offer-button">Get Combo Deal</button>
            </form>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="combo-benefits" aria-labelledby="combo-benefits-title">
    <div class="combo-benefits-heading"><h2 id="combo-benefits-title">Why Choose Combo?</h2></div>
    <div class="container">
      <div class="combo-benefit-art"><img src="assets/images/Untitled%20design%20(37)%20(1).png" alt="Save more, fewer deliveries, family sharing, and always-ready snacks"></div>
      <div class="combo-benefit-labels" aria-hidden="true"><span>Save more<br>per box</span><span>Fewer<br>reorders</span><span>Perfect<br>for family</span><span>Always have<br>healthy snacks ready</span></div>
    </div>
  </section>

  <div class="combo-limited-strip">⚡ Only a few combo packs left today</div>

  <section class="combo-social" aria-labelledby="combo-social-title">
    <div class="container">
      <h2 id="combo-social-title">Don’t Take Our Words for It…</h2>
      <p class="combo-social-subtitle">Real People. Real Results.</p>
      <div class="combo-social-rating"><span aria-hidden="true">★★★★★</span> <strong>4.8 | 500+ Reviews</strong></div>
      <p class="combo-social-loved">Loved by 500+ customers across India</p>
      <div class="combo-social-grid">
        <article><div class="combo-social-icon combo-social-workout"><i class="bi bi-heart-pulse-fill" aria-hidden="true"></i></div><p>Perfect for my workouts — no energy crash at all</p></article>
        <article><div class="combo-social-icon combo-social-office"><i class="bi bi-cup-hot-fill" aria-hidden="true"></i></div><p>Much better than tea and biscuits during office hunger</p></article>
        <article><div class="combo-social-icon combo-social-travel"><i class="bi bi-suitcase-fill" aria-hidden="true"></i></div><p>Keeps me active even during long travel days</p></article>
      </div>
      <p class="combo-social-foot">Join snackers switching to clean energy</p>
    </div>
  </section>

  <section class="combo-final" aria-labelledby="combo-final-title">
    <div class="container">
      <h2 id="combo-final-title">Ready to Switch to<br>Clean Protein Snacks?</h2>
      <div class="combo-final-sparkle" aria-hidden="true">—　✦　✧　✦　—</div>
      <p>Save up to &#8377;300 today</p>
      <a href="shop" class="combo-final-button">Get Your Combo Now</a>
    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?>
