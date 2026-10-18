<?php
$comboFaqs = [
    ['How many laddus come in each combo?', 'Each box has 6 laddus. A 3-box combo has 18, a 6-box combo has 36, and a 9-box combo has 54.'],
    ['Can I mix flavours in a combo?', 'Yes. Pick any flavours you like in every combo.'],
    ['How much will I save?', 'Up to ₹91 on 3 boxes, ₹341 on 6 boxes and ₹682 on 9 boxes. The exact saving depends on the flavours you pick.'],
    ['Is delivery free? Is COD available?', 'Yes, delivery is free on combos. COD is available for a small fee: ₹50 for 3 boxes, ₹75 for 6 boxes and ₹100 for 9 boxes. Pay online and save more, with no COD fee. Prepaid discounts are ₹30, ₹50 and ₹75 respectively.'],
    ['Will a big combo stay fresh?', 'Shakti Bites stay fresh for 30 days from packing. Store them in an airtight container in a cool, dry place, and refrigerate in hot or humid weather. Choose the combo size you can finish in 30 days.'],
];
$comboFaqSchema = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(static function ($faq) {
    return ['@type' => 'Question', 'name' => $faq[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq[1]]];
}, $comboFaqs)];
?>
<section class="combo-introduction" aria-labelledby="combo-introduction-title">
  <div class="container">
    <h2 id="combo-introduction-title">Protein Laddu Combo Packs for Everyday Snacking</h2>
    <p>Shakti Bites combo packs let you stock up on protein laddus and save on every order. Choose 3, 6 or 9 boxes and pick any mix of Peanut Jaggery, Almond Cacao and Dry Fruit Cardamom. Each laddu has 10g of protein and is sweetened with dates and jaggery, with no refined sugar. Each box has 6 laddus, so a 9-box combo gives you 54 laddus. The laddus stay fresh for 30 days from packing, so choose a combo size that your family can finish in that time.</p>
  </div>
</section>
<section class="combo-faq" aria-labelledby="combo-faq-title">
  <div class="container">
    <h2 id="combo-faq-title">Frequently Asked Questions</h2>
    <div class="home-faq-list">
      <?php foreach ($comboFaqs as $faq): ?>
        <details class="home-faq-item"><summary><?php echo htmlspecialchars($faq[0], ENT_QUOTES, 'UTF-8'); ?><span aria-hidden="true">+</span></summary><p><?php echo htmlspecialchars($faq[1], ENT_QUOTES, 'UTF-8'); ?></p></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<script type="application/ld+json"><?php echo json_encode($comboFaqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
