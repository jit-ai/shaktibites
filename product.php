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
  'image' => 'product1.PNG',
  'label' => 'Everyday Energy',
  'accent' => 'peanut',
  'short' => 'Clean, steady energy in every bite.',
  'slug' => 'peanut-jaggery-power-bites',
  'ingredients' => ['Real ingredients', 'Natural sweetness', 'Plant protein', 'No chemicals', 'Made in India'],
  'benefits' => ['10g protein per laddoo', 'No refined sugar', 'Naturally energising', 'Made in India'],
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
$comboImage = $id === 1 ? 'Peanut.png' : $displayImage;
$heroHeadline = [
  1 => '10g Protein Bites That Give You Real Energy. No Sugar Crash.',
  2 => '10g Protein Chocolate Bites That Actually Taste Amazing.',
  3 => '10g Protein Bites Made with Rich Dry Fruits & Clean Energy. Royal Taste.',
][$id];
$socialHeading = $id === 3 ? 'Don&apos;t Take Our Words for it..' : 'Real People. Real Results';
$momentsHeading = [
  1 => 'Perfect For When You Need Real Energy',
  2 => 'Perfect For',
  3 => 'Perfect For Your Daily Energy Moments',
][$id];
$comparison = $id === 1 ? ['Sugar spike & crash', 'Empty calories', 'Artificial sweetness', 'Makes you feel heavy'] : ($id === 2 ? ['Sugar-loaded sweets', 'Artificial chocolate', 'Refined sugar', 'Energy crash'] : ['Processed mithai', 'Added preservatives', 'Empty calories', 'Makes you feel sluggish']);
$momentIcons = $id === 3
  ? ['bi-flower1', 'bi-gift-fill', 'bi-car-front-fill']
  : ($id === 2 ? ['bi-activity', 'bi-cup-hot-fill', 'bi-car-front-fill'] : ['bi-activity', 'bi-briefcase-fill', 'bi-car-front-fill']);
$momentTitles = $id === 2
  ? ['Pre Post Workout', 'Office Snacks', 'Travel Energy']
  : ($id === 3 ? ['Pre Post Workout', 'Office Energy Boost', 'Travel / On the Go'] : ['Pre Post Workout', 'Office Energy Boost', 'Travel / On the Go']);
$momentCopy = $id === 2
  ? ['Fuel your fitness routine', 'Beat the work slump', 'Healthy on-the-go energy']
  : ['Fuel your workouts without a sugar crash', 'Beat 4PM fatigue with a better snack', 'Clean energy for long days & travel'];
$ingredientNote = $id === 3
  ? ['No preservatives', 'No artificial sweeteners', 'No refined sugar']
  : [];
include 'includes/header.php';
?>

<main class="flavour-page flavour-page--<?php echo htmlspecialchars($displayAccent, ENT_QUOTES, 'UTF-8'); ?>">
  <section class="flavour-hero">
    <div class="container flavour-hero-grid">
      <div class="flavour-pack"><img src="assets/images/<?php echo htmlspecialchars($displayImage, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?>"></div>
      <div class="flavour-copy">
        <span class="flavour-kicker"><?php echo htmlspecialchars($displayLabel, ENT_QUOTES, 'UTF-8'); ?></span>
        <h1><?php echo $heroHeadline; ?></h1>
        <p><?php echo htmlspecialchars($displayShort, ENT_QUOTES, 'UTF-8'); ?> Made with ingredients you can recognise.</p>
        <div class="flavour-price"><del>&#8377;349</del> <strong>&#8377;<?php echo number_format($displayPrice); ?></strong> <em>Save &#8377;50 today</em></div>
        <div class="flavour-rating"><span>&#9733;&#9733;&#9733;&#9733;&#9733;</span> 4.8 | 500+ Reviews</div>
        <form method="post" action="add_to_cart" class="flavour-actions"><input type="hidden" name="id" value="<?php echo $id; ?>"><input type="hidden" name="quantity" value="1"><button class="flavour-btn flavour-btn--outline" type="submit">Try First Box</button><a class="flavour-btn" href="combo">View Combo</a></form>
        <div class="flavour-bullets"><?php foreach (array_slice($displayBenefits, 0, 4) as $benefit): ?><span><i class="bi bi-check-circle-fill"></i><?php echo htmlspecialchars((string) $benefit, ENT_QUOTES, 'UTF-8'); ?></span><?php endforeach; ?></div>
      </div>
    </div>
  </section>

  <div class="flavour-trust"><div class="container"><span><i class="bi bi-truck"></i> Free Shipping</span><span><i class="bi bi-cash-stack"></i> COD Available</span><span><i class="bi bi-lock-fill"></i> Secure Checkout</span></div></div>

  <section class="flavour-ingredients"><div class="container">
    <h2>Ingredients:</h2><p>Real ingredients = real energy. No chemicals. No shortcuts.</p>
    <div class="ingredient-art"><img src="assets/images/Ingridents.png" alt="Natural ingredients"><div class="ingredient-names"><?php foreach ($displayIngredients as $ingredient): ?><span><?php echo htmlspecialchars((string) $ingredient, ENT_QUOTES, 'UTF-8'); ?></span><?php endforeach; ?></div></div>
    <?php if ($ingredientNote): ?><div class="ingredient-promises"><?php foreach ($ingredientNote as $note): ?><span><i class="bi bi-x-circle-fill"></i><?php echo htmlspecialchars($note); ?></span><?php endforeach; ?></div><?php else: ?><strong>What you see is what you eat - no hidden chemicals.</strong><?php endif; ?>
  </div></section>

  <section class="flavour-compare"><div class="container"><h2>Why Shakti Bites &gt; Regular Mithai</h2><div class="flavour-compare-grid"><div><h3>Other Snacks</h3><ul><?php foreach ($comparison as $item): ?><li><i class="bi bi-x-circle-fill"></i><?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?></li><?php endforeach; ?></ul></div><div class="flavour-compare-good"><h3><i class="bi bi-patch-check-fill"></i> Shakti Bites</h3><ul><?php foreach ($displayBenefits as $benefit): ?><li><i class="bi bi-check-circle-fill"></i><?php echo htmlspecialchars((string) $benefit, ENT_QUOTES, 'UTF-8'); ?></li><?php endforeach; ?></ul></div></div></div></section>

  <section class="flavour-proof"><div class="container"><h2><?php echo $socialHeading; ?></h2><p><?php echo $id === 3 ? 'Real People. Real Results.' : 'Don&apos;t take our word for it.'; ?></p><div class="flavour-stars">&#9733;&#9733;&#9733;&#9733;&#9733; <b>4.8 | 500+ Reviews</b></div><div class="proof-grid"><figure><img src="assets/images/Testinomial.png" alt="Happy customer"><figcaption><?php echo $id === 2 ? 'Gym people love it' : 'Perfect for my workouts - no energy crash at all'; ?></figcaption></figure><figure><img src="assets/images/Testinomial.png" alt="Happy customer"><figcaption><?php echo $id === 2 ? 'Perfect family snacks' : 'Much better than tea &amp; biscuits during office hunger'; ?></figcaption></figure><figure><img src="assets/images/Testinomial.png" alt="Happy customer"><figcaption><?php echo $id === 2 ? 'Perfect office snacks' : 'Keeps me active even during long travel days'; ?></figcaption></figure></div></div></section>

  <div class="fresh-strip"><i class="bi bi-lightning-charge-fill"></i> Selling Fast - Limited Fresh Batch Available Today</div>

  <section class="flavour-moments"><div class="container"><h2><?php echo $momentsHeading; ?></h2><div class="moment-grid"><?php foreach ($momentTitles as $index => $occasion): ?><article><img class="moment-icon-art" src="assets/images/<?php echo $index + 1; ?><?php echo $index === 2 ? ' (1)' : ''; ?>.png" alt="" aria-hidden="true"><h3><?php echo htmlspecialchars($occasion); ?></h3><p><?php echo $momentCopy[$index]; ?></p></article><?php endforeach; ?></div></div></section>

  <section class="flavour-combo"><div class="container"><h2>Want Better Value?</h2><p>Best Value. More Protein. More Savings.</p><div class="combo-banner"><div><strong>SAVE MORE WITH COMBO</strong><p>Best Value. More Protein. More Savings.</p><b>Save &#8377;300</b><small>Only &#8377;20 Per Laddoo</small><a href="combo">Get This Combo</a></div><img src="assets/images/<?php echo htmlspecialchars($comboImage, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?> combo"></div></div></section>

  <section class="flavour-final"><div class="container"><h2>Ready to Switch to<br>Clean Protein Snacks?</h2><p>&#10022; &#10022; &#10022;</p><span>Start your clean snacking today</span><form method="post" action="add_to_cart"><input type="hidden" name="id" value="<?php echo $id; ?>"><input type="hidden" name="quantity" value="1"><button name="buy_now" value="1">Order Your First Box</button></form></div></section>
</main>

<?php include 'includes/footer.php'; ?>
