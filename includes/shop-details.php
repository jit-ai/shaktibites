<?php
$shopFaqs = [
    ['Which flavour should I choose first?', 'Peanut Jaggery is the everyday pick. Choose Almond Cacao if you like chocolate, and Dry Fruit Cardamom for a richer, premium taste.'],
    ['Is there 10g of protein in every flavour?', 'Yes. Each laddu has 10g of protein.'],
    ['Are combos cheaper than buying single boxes?', 'Combo builders show the applicable discount and final price for your selected boxes. Compare that total with current single-box prices to choose the best value.'],
    ['Can I mix flavours in one combo?', 'Yes. Choose any mix of Peanut Jaggery, Almond Cacao and Dry Fruit Cardamom in our 3-, 6- or 9-box combo builders. Select the required number of boxes to complete your combo.'],
    ['Do you offer free delivery?', 'Yes, delivery is free on orders of ₹499 or more. Orders below ₹499 have a ₹50 shipping fee.'],
    ['How long do they last, and how do I store them?', 'They stay fresh for 30 days from packing. Store in an airtight container in a cool, dry place, and refrigerate in hot or humid weather.'],
];
$shopFaqSchema = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(static function ($faq) {
    return ['@type' => 'Question', 'name' => $faq[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq[1]]];
}, $shopFaqs)];
?>
<section class="shop-introduction" aria-labelledby="shop-introduction-title">
  <div class="container">
    <h2 id="shop-introduction-title">Protein Laddu Online: Choose Your Flavour</h2>
    <div class="shop-introduction-copy">
      <p>Shakti Bites protein laddus come in three flavours, so you can pick the one that fits your taste. Every laddu has 10g of protein and is sweetened with dates and jaggery, with no refined sugar.</p>
      <p>Peanut Jaggery Power Bites are the everyday choice, with a nutty, jaggery-sweet taste. Almond Cacao Power Bites are for chocolate lovers who want a high protein snack. Dry Fruit Cardamom Power Bites are rich and aromatic, and a good fit for premium snacking and gifting.</p>
      <p>Each box has 6 laddus and stays fresh for 30 days from packing. Buying more than one box? Our combos help you save, and delivery is free on orders of ₹499 or more.</p>
    </div>
  </div>
</section>
<section class="shop-faq" aria-labelledby="shop-faq-title">
  <div class="container">
    <h2 id="shop-faq-title">Frequently Asked Questions</h2>
    <div class="home-faq-list">
      <?php foreach ($shopFaqs as $faq): ?>
        <details class="home-faq-item">
          <summary><?php echo htmlspecialchars($faq[0], ENT_QUOTES, 'UTF-8'); ?><span aria-hidden="true">+</span></summary>
          <p><?php echo htmlspecialchars($faq[1], ENT_QUOTES, 'UTF-8'); ?></p>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<script type="application/ld+json"><?php echo json_encode($shopFaqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
