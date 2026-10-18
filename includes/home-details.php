<?php
$homeFaqs = [
    ['How much protein is in one Shakti Bites laddu?', 'Each laddu has 10g of protein.'],
    ['What are Shakti Bites protein laddus made of?', 'They are made with pea protein, dates, jaggery and dry fruits like almonds, peanuts and cashews. The exact ingredients depend on the flavour. Full ingredients are listed on each product page.'],
    ['Do Shakti Bites have refined sugar, maida or preservatives?', 'No. They are sweetened with dates and jaggery. They have no refined sugar, no maida and no preservatives.'],
    ['When can I eat a protein laddu?', 'Anytime you want a healthy snack. Many people have it in the morning, before or after a workout, or in the evening instead of biscuits or chips.'],
    ['How many laddus come in one box, and what flavours do you have?', 'Each box has 6 laddus. We have three flavours: Peanut Jaggery, Almond Cacao and Dry Fruit Cardamom.'],
    ['Do you offer free delivery?', 'Yes, delivery is free on orders of ₹499 or more. You also save on prepaid orders: ₹15 for a single-box tier, ₹30 for 3 boxes, ₹50 for 6 boxes and ₹75 for 9 boxes.'],
    ['How long do Shakti Bites stay fresh?', 'Shakti Bites stay fresh for 30 days from the date of packing.'],
    ['How should I store Shakti Bites?', 'Store in an airtight container in a cool, dry place. Refrigerate in hot or humid weather. Best within 30 days of packing.'],
];
$homeFaqSchema = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array_map(static function ($faq) {
    return ['@type' => 'Question', 'name' => $faq[0], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq[1]]];
}, $homeFaqs)];
?>
<section class="home-introduction" aria-labelledby="home-intro-title">
  <div class="container">
    <span class="home-content-kicker">Everyday snacking, made better</span>
    <h2 id="home-intro-title">Protein Laddu &amp; Protein Bites: Healthy High Protein Snacks by Shakti Bites</h2>
    <div class="home-introduction-copy">
      <p>Shakti Bites makes protein laddu and protein bites for people who want a tasty snack with real nutrition. Each laddu has 10g of protein from pea protein. It is sweetened with dates and jaggery, so there is no refined sugar, no maida and no preservatives.</p>
      <p>Our high protein snacks are made for everyday life. Have them as a mid-morning snack, a pre-workout energy bite, or an after-school treat for kids. They are small, easy to carry and ready to eat.</p>
      <p>We offer three flavours: Peanut Jaggery Power Bites, Almond Cacao Power Bites and Dry Fruit Cardamom Power Bites. Every box has 6 laddus. Our products are lab tested, so you can trust what is on the label.</p>
      <p>If you are looking for a healthy protein laddu in India, or a better snack than biscuits and packaged chips, Shakti Bites is made for you. Order online at shaktibites.com and get free delivery on orders of ₹499 or more.</p>
    </div>
  </div>
</section>
<section class="home-faq" aria-labelledby="home-faq-title">
  <div class="container">
    <span class="home-content-kicker">A little more about your bites</span>
    <h2 id="home-faq-title">Frequently Asked Questions</h2>
    <div class="home-faq-list">
      <?php foreach ($homeFaqs as $faq): ?>
        <details class="home-faq-item">
          <summary><?php echo htmlspecialchars($faq[0], ENT_QUOTES, 'UTF-8'); ?><span aria-hidden="true">+</span></summary>
          <p><?php echo htmlspecialchars($faq[1], ENT_QUOTES, 'UTF-8'); ?></p>
        </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<script type="application/ld+json"><?php echo json_encode($homeFaqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
