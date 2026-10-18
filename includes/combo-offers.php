<?php
require_once __DIR__ . '/catalog.php';
require_once __DIR__ . '/combo-builder.php';
require_once __DIR__ . '/checkout-pricing.php';
$bundleHeadingTag = isset($bundleHeadingTag) && $bundleHeadingTag === 'h2' ? 'h2' : 'h1';
$comboOffers = [];
foreach (shakti_builder_plans() as $boxes => $plan) {
    $comboOffers[$boxes] = ['boxes'=>$boxes,'name'=>$boxes . ' Box Combo','tone'=>$plan['tone'],'tag'=>$plan['tag'],'saving'=>$plan['saving'],'image'=>'Combo-product-' . $boxes . '.png','alt'=>$boxes . ' box combo','benefits'=>['Pick any flavours','Fresh batch & free shipping']];
}
$maxComboSaving = max(array_column($comboOffers, 'saving'));
?>
  <section class="bundle-intro" aria-labelledby="bundle-title">
    <div class="container">
      <<?php echo $bundleHeadingTag; ?> id="bundle-title">Save More When You Stock Up</<?php echo $bundleHeadingTag; ?>>
      <p class="bundle-subtitle">More protein. More savings. Fewer reorders.</p>
      <p class="bundle-promo"><span aria-hidden="true">&#128293;</span> Limited launch combo pricing &mdash; ending soon</p>
      <span class="bundle-savings">Up to &#8377;<?php echo number_format($maxComboSaving); ?> OFF on combos</span>
    </div>
  </section>

  <section class="bundle-offers" id="combo-offers" aria-label="Choose your combo">
    <div class="container bundle-grid">
      <?php foreach ($comboOffers as $offerId => $offer): ?>
        <article class="bundle-card bundle-card--<?php echo htmlspecialchars($offer['tone'], ENT_QUOTES, 'UTF-8'); ?>">
          <div class="bundle-tag"><?php echo $offer['tone'] === 'popular' ? 'MOST POPULAR' : htmlspecialchars($offer['tag'], ENT_QUOTES, 'UTF-8'); ?></div>
          <div class="bundle-card-body">
            <h2><?php echo htmlspecialchars($offer['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <div class="bundle-rule" aria-hidden="true"></div>
            <p class="bundle-saving">Save up to &#8377;<?php echo number_format($offer['saving']); ?></p>
            <div class="bundle-photo"><img src="assets/images/<?php echo htmlspecialchars($offer['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($offer['alt'], ENT_QUOTES, 'UTF-8'); ?>"></div>
            <ul class="bundle-features">
              <li>Pick any flavours</li>
              <li><?php echo $offer['boxes'] * 6; ?> protein laddus</li>
              <li>Free delivery &middot; No refined sugar</li>
            </ul>
            <p class="bundle-saving bundle-saving-bottom">Save up to &#8377;<?php echo number_format($offer['saving']); ?> on this combo</p>
<?php $offerPrepaid = shakti_checkout_pricing([1 => 1], [1 => ['price' => 1000, 'boxes' => $offer['boxes']]], 'online')['prepaid_discount']; ?>
            <p class="bundle-prepaid">&#10003; Extra &#8377;<?php echo number_format($offerPrepaid); ?> off on prepaid</p>
            <a class="bundle-button" href="combo/<?php echo [3 => 'starter', 6 => 'popular', 9 => 'value'][$offer['boxes']]; ?>">Build Your Combo</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

