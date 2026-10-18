<?php
require_once __DIR__ . '/includes/catalog.php';
$catalog = shakti_catalog();
$slugToId = [];
foreach ($catalog as $catalogId => $catalogProduct) {
  if (is_array($catalogProduct) && isset($catalogProduct['slug']) && is_string($catalogProduct['slug'])) {
    $slugToId[$catalogProduct['slug']] = $catalogId;
  }
}
$requestedSlug = filter_input(INPUT_GET, 'product', FILTER_UNSAFE_RAW);
$id = is_string($requestedSlug) && isset($slugToId[$requestedSlug])
  ? $slugToId[$requestedSlug]
  : null;
$legacyId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id === null && $legacyId && isset($catalog[$legacyId])) {
  header('Location: product/' . rawurlencode($catalog[$legacyId]['slug']), true, 302);
  exit;
}
if ($id === null) { $id = 1; }
$productDefaults = [
  'name' => 'Shakti Bites',
  'price' => 249,
  'image' => 'Peanut-product.png',
  'label' => 'Everyday Pick',
  'accent' => 'peanut',
  'short' => 'Clean, steady energy in every bite.',
  'slug' => 'peanut-jaggery-power-bites',
  'ingredients' => ['Real ingredients', 'Natural sweetness', 'Plant protein', 'No chemicals', 'Made in India'],
  'benefits' => ['10g protein per laddu', 'No refined sugar', 'Naturally energising', 'Made in India'],
  'occasion' => ['Pre & post workout', '4 PM energy boost', 'Travel-friendly snack'],
];
// Keeps the layout intact even if an older catalogue entry is missing a field
// or has an invalid value. This also prevents malformed data from printing a
// PHP warning inside the page markup.
$catalogEntry = is_array($catalog[$id]) ? $catalog[$id] : [];
$product = array_replace($productDefaults, $catalogEntry);
foreach (['name', 'image', 'label', 'accent', 'short'] as $field) {
  $value = $product[$field] ?? null;
  if (!is_string($value) || trim($value) === '') {
    $product[$field] = $productDefaults[$field];
  }
}
if (!is_numeric($product['price'] ?? null)) {
  $product['price'] = $productDefaults['price'];
}
foreach (['ingredients', 'benefits', 'occasion'] as $field) {
  if (!is_array($product[$field] ?? null)) {
    $product[$field] = $productDefaults[$field];
  }
}
$displayName = $product['name'] ?? $productDefaults['name'];
$displayImage = $product['image'] ?? $productDefaults['image'];
$displayLabel = $product['label'] ?? $productDefaults['label'];
$displayAccent = $product['accent'] ?? $productDefaults['accent'];
$displayShort = $product['short'] ?? $productDefaults['short'];
$displayBenefits = is_array($product['benefits'] ?? null) ? $product['benefits'] : $productDefaults['benefits'];
$displayIngredients = is_array($product['ingredients'] ?? null) ? $product['ingredients'] : $productDefaults['ingredients'];
$displayPrice = is_numeric($product['price'] ?? null) ? (float) $product['price'] : $productDefaults['price'];
$page_title = 'Shop';
require_once __DIR__ . '/includes/product-page-copy.php';
require_once __DIR__ . '/includes/combo-builder.php';
$pageCopy = shakti_product_page_copy($id);
$meta_title = $pageCopy['title'];
$meta_description = $pageCopy['description'];
$originalDisplayPrice = max($displayPrice, (float) ($product['original_price'] ?? 349));
$valueCombo = shakti_build_combo(9, [$id => 9]);
$comboPerLaddu = $valueCombo['price'] / 54;
$maxComboSaving = max(array_column(shakti_builder_plans(), 'saving'));
$comboImage = $id === 1 ? 'Combo-product-9.png' : ($id === 2 ? 'Combo-product-6.png' : 'Combo-product-3.png');
$heroHeadline = $pageCopy['headline'];
$momentsHeading = 'Perfect For When You Need Real Energy';
$comparison = $pageCopy['comparison'];
$momentTitles = ['Pre & Post Workout', 'Office Energy Boost', 'Travel / On the Go'];
$momentCopy = ['A convenient bite for your active routine.', 'Your workday snack for busy afternoons.', 'A handy snack for busy days and journeys.'];
$momentIcons = [
  '<rect x="8" y="26" width="8" height="28" rx="4"/><rect x="20" y="16" width="12" height="48" rx="6"/><rect x="32" y="34" width="32" height="12"/><rect x="64" y="16" width="12" height="48" rx="6"/><rect x="80" y="26" width="8" height="28" rx="4"/>',
  '<path d="M34 18v-4a6 6 0 0 1 6-6h16a6 6 0 0 1 6 6v4" fill="none" stroke="currentColor" stroke-width="5"/><rect x="12" y="20" width="72" height="52" rx="6"/><path d="M12 35q0 11 12 11h48q12 0 12-11" fill="none" stroke="var(--moment-card)" stroke-width="3"/><rect x="39" y="36" width="18" height="23" rx="4" fill="var(--moment-card)"/><path d="m50 39-7 11h6l-3 8 10-13h-7z"/>',
  '<path d="m24 37 7-17a8 8 0 0 1 7-5h20a8 8 0 0 1 7 5l7 17M20 41l5-5h46l5 5v19H20z" fill="none" stroke="currentColor" stroke-width="5" stroke-linejoin="round"/><rect x="21" y="60" width="11" height="9" rx="3"/><rect x="64" y="60" width="11" height="9" rx="3"/><rect x="26" y="45" width="14" height="7" rx="3"/><rect x="56" y="45" width="14" height="7" rx="3"/><path d="m8 28 6 4M5 42h8M8 56l6-4m68-20 6-4m-5 14h8m-9 10 6 4" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round"/>',
];
$majorIngredients = [
  1 => ['Peanuts' => 0, 'Dates' => 2, 'Jaggery' => 4, 'Pea Protein' => 5, 'Sesame Seeds' => 10, 'Desi Ghee' => 7],
  2 => ['Peanuts' => 0, 'Almonds' => 1, 'Dates' => 2, 'Cacao Powder' => 3, 'Jaggery' => 4, 'Pea Protein' => 5, 'Cardamom' => 6, 'Desi Ghee' => 7],
  3 => ['Cashews' => 8, 'Almonds' => 1, 'Pistachio' => 9, 'Dates' => 2, 'Cardamom' => 6, 'Jaggery' => 4, 'Pea Protein' => 5, 'Desi Ghee' => 7],
][$id];
$ingredientImage = [1 => 'Peanut .JPG', 2 => 'Almond.JPG', 3 => 'Dryfruit.JPG'][$id];
include 'includes/header.php';
?>

<main class="flavour-page flavour-page--<?php echo htmlspecialchars($displayAccent, ENT_QUOTES, 'UTF-8'); ?>">
  <section class="flavour-hero">
    <div class="container flavour-hero-grid">
      <div class="flavour-pack"><img src="assets/images/<?php echo htmlspecialchars($displayImage, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?>"></div>
      <div class="flavour-copy">
        <span class="flavour-kicker"><?php echo htmlspecialchars($displayLabel, ENT_QUOTES, 'UTF-8'); ?></span>
        <h1><?php echo $heroHeadline; ?></h1>
        <p><?php echo htmlspecialchars($pageCopy['subline'], ENT_QUOTES, 'UTF-8'); ?></p>
        <div class="flavour-price"><?php if ($originalDisplayPrice > $displayPrice): ?><del>&#8377;<?php echo number_format($originalDisplayPrice); ?></del><?php endif; ?> <strong>&#8377;<?php echo number_format($displayPrice); ?></strong><small>per box</small><?php if ($originalDisplayPrice > $displayPrice): ?><em>Save &#8377;<?php echo number_format($originalDisplayPrice - $displayPrice); ?> today</em><?php endif; ?></div>
        <div class="flavour-rating"><span>&#9733;&#9733;&#9733;&#9733;&#9733;</span> 4.8 | 500+ Reviews</div>
        <form method="post" action="add_to_cart" class="flavour-actions"><input type="hidden" name="id" value="<?php echo $id; ?>"><input type="hidden" name="quantity" value="1"><button class="flavour-btn flavour-btn--outline" type="submit">Try First Box</button><a class="flavour-btn" href="combo">Save with Combo</a></form>
        <div class="flavour-bullets"><?php foreach ($pageCopy['ticks'] as $benefit): ?><span><i class="bi bi-check-circle-fill"></i><?php echo htmlspecialchars((string) $benefit, ENT_QUOTES, 'UTF-8'); ?></span><?php endforeach; ?></div>
      </div>
    </div>
  </section>

  <div class="flavour-trust"><div class="container"><span><i class="bi bi-truck"></i> Free delivery at &#8377;499</span><span><i class="bi bi-cash-stack"></i> COD Available</span><span><i class="bi bi-lock-fill"></i> Secure Checkout</span></div></div>

  <section class="flavour-ingredients ingredient-reference" aria-labelledby="ingredients-heading">
    <h2 id="ingredients-heading" class="visually-hidden">Major Ingredients</h2>
    <img class="ingredient-reference-image" src="assets/images/<?php echo rawurlencode($ingredientImage); ?>" alt="Major Ingredients: <?php echo htmlspecialchars(implode(', ', array_keys($majorIngredients)), ENT_QUOTES, 'UTF-8'); ?>. Real ingredients = real energy. No chemicals. No shortcuts. No artificial preservatives. No refined sugar. No artificial sweeteners." width="2752" height="1536" loading="lazy" decoding="async">
  </section>

  <section class="flavour-compare"><div class="container"><h2>Why Choose Shakti Bites Over Regular Mithai?</h2><div class="flavour-compare-grid"><div><h3>Regular Mithai &amp; Packaged Snacks</h3><ul><?php foreach ($comparison as $item): ?><li><i class="bi bi-x-circle-fill"></i><?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?></li><?php endforeach; ?></ul></div><div class="flavour-compare-good"><h3><i class="bi bi-patch-check-fill"></i> Shakti Bites</h3><ul><?php foreach ($pageCopy['advantages'] as $benefit): ?><li><i class="bi bi-check-circle-fill"></i><?php echo htmlspecialchars((string) $benefit, ENT_QUOTES, 'UTF-8'); ?></li><?php endforeach; ?></ul></div></div></div></section>


  <div class="fresh-strip"><i class="bi bi-lightning-charge-fill"></i> Selling Fast - Limited Fresh Batch Available Today</div>

  <section class="flavour-moments" aria-labelledby="moments-heading">
    <div class="container">
      <h2 id="moments-heading"><?php echo $momentsHeading; ?></h2>
      <div class="moment-grid">
        <?php foreach ($momentTitles as $index => $occasion): ?>
          <article>
            <svg class="moment-symbol" viewBox="0 0 96 80" fill="currentColor" aria-hidden="true" focusable="false"><?php echo $momentIcons[$index]; ?></svg>
            <h3><?php echo htmlspecialchars($occasion, ENT_QUOTES, 'UTF-8'); ?></h3>
            <p><?php echo htmlspecialchars($momentCopy[$index], ENT_QUOTES, 'UTF-8'); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="flavour-combo"><div class="container"><h2>Save More With a Combo</h2><p>The more boxes you add, the more you save.</p><div class="combo-banner"><div><strong>SAVE MORE WITH COMBO</strong><p>Best Value. More Protein. More Savings.</p><b>Save up to &#8377;<?php echo number_format($maxComboSaving); ?></b><small>About &#8377;<?php echo number_format($comboPerLaddu); ?> per laddu with a 9-box combo of this flavour.</small><a href="combo/value">Build Your Combo</a></div><img src="assets/images/<?php echo htmlspecialchars($comboImage, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?> combo"></div></div></section>

  <?php include __DIR__ . '/includes/product-details-content.php'; ?>

  <?php
  $videoReviewGroup = $displayAccent;
  include __DIR__ . '/includes/video-reviews.php';
  ?>

  <section class="flavour-final"><div class="container"><h2>Ready to Switch to<br>Clean Protein Snacks?</h2><p>&#10022; &#10022; &#10022;</p><span>Start your clean snacking today</span><form method="post" action="add_to_cart"><input type="hidden" name="id" value="<?php echo $id; ?>"><input type="hidden" name="quantity" value="1"><button name="buy_now" value="1">Order Your First Box</button></form></div></section>
</main>

<?php
include 'includes/footer.php';
?>
