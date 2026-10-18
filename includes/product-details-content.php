<section class="product-story" aria-labelledby="product-story-title">
  <div class="container">
    <h2 id="product-story-title">About <?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?></h2>
    <p><?php echo htmlspecialchars($pageCopy['about'], ENT_QUOTES, 'UTF-8'); ?></p>
    <div class="product-story-grid">
      <div><h3>Taste profile</h3><p><?php echo htmlspecialchars($pageCopy['taste'], ENT_QUOTES, 'UTF-8'); ?></p></div>
      <div><h3>Ingredients &amp; allergens</h3><p><?php echo htmlspecialchars($pageCopy['allergens'], ENT_QUOTES, 'UTF-8'); ?></p><span>6 laddus per box &middot; 210g</span></div>
    </div>
  </div>
</section>
<section class="product-faq" aria-labelledby="product-faq-title">
  <div class="container">
    <h2 id="product-faq-title">Frequently Asked Questions</h2>
    <div class="home-faq-list">
      <?php foreach ($pageCopy['faqs'] as $faq): ?>
        <details class="home-faq-item"><summary><?php echo htmlspecialchars($faq[0], ENT_QUOTES, 'UTF-8'); ?><span aria-hidden="true">+</span></summary><p><?php echo htmlspecialchars($faq[1], ENT_QUOTES, 'UTF-8'); ?></p></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php
$productFaqSchema = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(static function ($faq) {
    return ['@type' => 'Question', 'name' => $faq[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq[1]]];
}, $pageCopy['faqs'])];
?>
<script type="application/ld+json"><?php echo json_encode($productFaqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
