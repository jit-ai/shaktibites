<?php
$page_title = 'Shop';
include 'includes/header.php';
?>

<!-- ===== SHOP HERO ===== -->
<section class="shop-hero">
  <div class="container">
    <h1 class="shop-hero-title">Shop Protein Laddoos</h1>
    <p class="shop-hero-sub">Clean ingredients. Real nutrition. Unbeatable taste.</p>
  </div>
</section>

<!-- ===== PRODUCT LISTINGS ===== -->
<section class="product-range-section">
  <div class="container">
    <h2 class="section-title text-center">Explore Our Protein Packed Range</h2>
    <p class="section-sub text-center">Real ingredients. Real nutrition. Real taste.</p>
    <div class="products-grid">

      <div class="product-card">
        <div class="prod-label label-everyday">Everyday Energy</div>
        <div class="prod-img-wrap peanut-light">
          <img src="assets/images/product1.PNG" alt="Peanut Jaggery Power Bites">
        </div>
        <div class="prod-body">
          <h4>Peanut Jaggery Power Bites</h4>
          <div class="prod-price">₹249 <span class="price-unit">/ box</span></div>
          <div class="prod-feature"><span class="dot-orange"></span>Instant Energy | No Sugar Crash</div>
          <div class="prod-feature"><span class="dot-orange"></span>10g Protein per Laddoo</div>
          <div class="prod-feature"><span class="dot-orange"></span>Peanuts • Jaggery • Dates</div>
          <div class="prod-feature"><span class="dot-orange"></span>No Refined Sugar</div>
          <a href="product.php?id=1" class="btn btn-try">View Details</a>
        </div>
      </div>

      <div class="product-card bestseller-card">
        <div class="prod-label label-bestseller">⭐ Best Seller ⭐</div>
        <div class="prod-img-wrap cacao-dark">
          <img src="assets/images/product2.PNG" alt="Almond Cacao Power Bites">
        </div>
        <div class="prod-body">
          <h4>Almond Cacao Power Bites</h4>
          <div class="prod-price">₹279 <span class="price-unit">/ box</span></div>
          <div class="prod-feature"><span class="dot-orange"></span>High Protein | Chocolate Taste</div>
          <div class="prod-feature"><span class="dot-orange"></span>10g Protein per Laddoo</div>
          <div class="prod-feature"><span class="dot-orange"></span>Almonds • Cacao • Dates</div>
          <div class="prod-feature"><span class="dot-orange"></span>100% Natural Ingredients</div>
          <a href="product.php?id=2" class="btn btn-try btn-try-filled">View Details</a>
        </div>
      </div>

      <div class="product-card">
        <div class="prod-label label-premium">Premium Pick</div>
        <div class="prod-img-wrap dryfruit-light">
          <img src="assets/images/product3.PNG" alt="Dry Fruit Cardamom Bites">
        </div>
        <div class="prod-body">
          <h4>Dry Fruit Cardamom Bites</h4>
          <div class="prod-price">₹299 <span class="price-unit">/ box</span></div>
          <div class="prod-feature"><span class="dot-orange"></span>Rich Dry Fruits | Royal Taste</div>
          <div class="prod-feature"><span class="dot-orange"></span>10g Protein per Laddoo</div>
          <div class="prod-feature"><span class="dot-orange"></span>Cashews • Almonds • Cardamom</div>
          <div class="prod-feature"><span class="dot-orange"></span>No Preservatives</div>
          <a href="product.php?id=3" class="btn btn-try">View Details</a>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ===== FEATURES SECTION ===== -->
<section class="shop-features-section">
  <div class="container">
    <h2 class="section-title text-center">Why Our Laddoos?</h2>
    <p class="section-sub text-center">Each bite delivers pure goodness</p>
    <div class="shop-features-grid">
      <div class="shop-feature-item">
        <i class="bi bi-heart-fill feature-icon"></i>
        <h5>10g Pure Protein</h5>
        <p>Plant-based protein for sustained energy without crashes.</p>
      </div>
      <div class="shop-feature-item">
        <i class="bi bi-leaf feature-icon"></i>
        <h5>100% Natural</h5>
        <p>No artificial colors, flavors, or preservatives.</p>
      </div>
      <div class="shop-feature-item">
        <i class="bi bi-globe feature-icon"></i>
        <h5>Farm to Table</h5>
        <p>Ethically sourced ingredients from trusted farms.</p>
      </div>
      <div class="shop-feature-item">
        <i class="bi bi-lightning-fill feature-icon"></i>
        <h5>Quick Energy</h5>
        <p>Perfect pre/post workout fuel or anytime snack.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== CTA SECTION ===== -->
<section class="shop-cta-section">
  <div class="container text-center">
    <h2 class="shop-cta-title">Can't Decide? Try Our Combos!</h2>
    <p class="shop-cta-text">Save up to 25% with our curated combo packs.</p>
    <a href="combo.php" class="btn btn-cta">View Combos</a>
  </div>
</section>

<?php include 'includes/footer.php'; ?>