<?php
require_once __DIR__ . '/checkout-pricing.php';
$builderPrepaid = shakti_checkout_pricing([1 => 1], [1 => ['price' => 1000, 'boxes' => $builderBoxes]], 'online')['prepaid_discount'];
?>
<section class="combo-builder combo-builder-page" id="comboBuilder" aria-labelledby="builderTitle">
  <form method="post" action="add-custom-combo" id="comboBuilderForm">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['combo_token'], ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="boxes" id="builderBoxes" value="<?php echo $builderBoxes; ?>">
    <div class="builder-body">
      <a class="builder-back" href="combo">&#8592; All combos</a>
      <h1 id="builderTitle">Build Your <?php echo htmlspecialchars($builderPlan['title'], ENT_QUOTES, 'UTF-8'); ?> Combo</h1>
      <div class="builder-intro"><div><h3 id="builderOffer">Choose any <?php echo $builderBoxes; ?> boxes and save up to &#8377;<?php echo $builderPlan['saving']; ?> (<?php echo $builderPlan['discount']; ?>% OFF)</h3><p>Pick your favourite flavours. You can mix and match.</p></div><span class="builder-box-badge" id="builderBadge"><?php echo $builderBoxes; ?> Boxes</span></div>
      <div class="builder-products">
        <?php foreach (shakti_builder_products() as $flavourId => $flavour): ?>
          <article class="builder-product" data-flavour="<?php echo $flavourId; ?>" data-price="<?php echo $flavour['price']; ?>">
            <img src="assets/images/<?php echo htmlspecialchars($flavour['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($flavour['name'], ENT_QUOTES, 'UTF-8'); ?>">
            <h3><?php echo htmlspecialchars($flavour['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <p class="builder-protein">&#10086; 10g Protein / Bite</p>
            <p class="builder-unit-price">&#8377;<?php echo $flavour['price']; ?>/box</p>
            <div class="builder-stepper"><button type="button" data-change="-1" aria-label="Remove one <?php echo htmlspecialchars($flavour['name'], ENT_QUOTES, 'UTF-8'); ?> box">&minus;</button><input type="number" min="0" max="<?php echo $builderBoxes; ?>" step="1" name="flavours[<?php echo $flavourId; ?>]" value="0" aria-label="<?php echo htmlspecialchars($flavour['name'], ENT_QUOTES, 'UTF-8'); ?> quantity"><button type="button" data-change="1" aria-label="Add one <?php echo htmlspecialchars($flavour['name'], ENT_QUOTES, 'UTF-8'); ?> box">+</button></div>
          </article>
        <?php endforeach; ?>
      </div>
      <div class="builder-selection"><div><h3>Your Combo</h3><p id="builderCount" role="status" aria-live="polite"></p><div id="builderSelected" class="builder-selected"></div></div><div class="builder-nutrition"><p><i class="bi bi-lightning" aria-hidden="true"></i> <span id="builderBites"></span> Energy Bites</p><p><i class="bi bi-heart" aria-hidden="true"></i> <span id="builderProtein"></span>g Protein</p></div></div>
    </div>
    <div class="builder-bottom">
      <div class="builder-price-row"><button type="button" class="builder-clear">Clear Selection</button><div class="builder-totals"><p><span id="builderSubtotalLabel"></span><s id="builderSubtotal"></s></p><span id="builderSaving" class="builder-saving"></span><p class="builder-total"><strong>Combo Price</strong><strong id="builderTotal"></strong></p><div class="builder-prepaid" id="builderPrepaid" data-discount="<?php echo $builderPrepaid; ?>"><span>&#10003; Extra &#8377;<?php echo $builderPrepaid; ?> OFF on prepaid orders</span><strong id="builderPrepaidTotal" aria-live="polite">&mdash;</strong></div></div></div>

      <p id="builderMessage" class="builder-message"></p>
      <button type="submit" class="builder-add"><span>Add Combo to Cart &#8594;</span><span id="builderButtonTotal"></span></button>
    </div>
  </form>
</section>
