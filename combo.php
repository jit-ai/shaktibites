<?php
$page_title = 'Combos';
include 'includes/header.php';
?>

<!-- ===== COMBO HERO ===== -->
<section class="combo-hero">
  <div class="container">
    <h1 class="combo-hero-title">Save More with Combos</h1>
    <p class="combo-hero-sub">Best value. More protein. More savings. Perfect for sharing or stocking up!</p>
  </div>
</section>

<!-- ===== COMBO LISTINGS ===== -->
<section class="combo-section">
  <div class="container">
    <h2 class="section-title text-center">Choose Your Perfect Combo</h2>
    <p class="section-sub text-center">All combos include free shipping across India</p>
    <div class="combo-grid">

      <div class="combo-card">
        <div class="combo-top-label">STARTER COMBO</div>
        <div class="combo-images">
          <img src="assets/images/product1.PNG" alt="Peanut Jaggery" class="combo-prod-img-full">
        </div>
        <div class="combo-name">STARTER PRO COMBO</div>
        <div class="combo-qty">3 BOXES</div>
        <div class="combo-note">Try all three flavours</div>
        <div class="combo-price">
          <span class="price-old">₹699</span>
          <span class="price-new">₹549</span>
        </div>
        <a href="#" class="btn btn-buy">Buy Now</a>
      </div>

      <div class="combo-card best-value-card">
        <div class="combo-best-badge">BEST VALUE</div>
        <div class="combo-top-label" style="color:var(--orange);font-weight:800;">BEST VALUE</div>
        <div class="combo-images">
          <img src="assets/images/product1.PNG" alt="Peanut Jaggery" class="combo-prod-img-full">
        </div>
        <div class="combo-name">BEST VALUE COMBO</div>
        <div class="combo-qty">6 BOXES</div>
        <div class="combo-note" style="color:var(--orange);font-weight:600;">Only ₹16 per pack</div>
        <div class="combo-price">
          <span class="price-old">₹1494</span>
          <span class="price-new">₹1199</span>
        </div>
        <a href="#" class="btn btn-buy">Buy Now</a>
      </div>

      <div class="combo-card">
        <div class="combo-top-label">POWER COMBO</div>
        <div class="combo-images">
          <img src="assets/images/product2.PNG" alt="Almond Cacao" class="combo-prod-img-full">
        </div>
        <div class="combo-name">HIGH PROTEIN COMBO</div>
        <div class="combo-qty">4 BOXES</div>
        <div class="combo-note">For Fitness Lovers</div>
        <div class="combo-price">
          <span class="price-old">₹747</span>
          <span class="price-new">₹699</span>
        </div>
        <a href="#" class="btn btn-buy">Buy Now</a>
      </div>

    </div>
  </div>
</section>

<!-- ===== WHY CHOOSE COMBO ===== -->
<section class="why-combo-section">
  <div class="container">
    <h2 class="section-title text-center">Why Choose Our Combos?</h2>
    <p class="section-sub text-center">Smart savings for smart snacking</p>
    <div class="why-combo-grid">
      <div class="why-combo-card">
        <div class="why-combo-icon">
          <i class="bi bi-currency-rupee"></i>
        </div>
        <h5>Save Up to 25%</h5>
        <p>Get premium protein laddoos at discounted prices when you buy in bundles.</p>
      </div>
      <div class="why-combo-card">
        <div class="why-combo-icon">
          <i class="bi bi-truck"></i>
        </div>
        <h5>Free Shipping</h5>
        <p>All combos include free PAN India delivery. No hidden charges.</p>
      </div>
      <div class="why-combo-card">
        <div class="why-combo-icon">
          <i class="bi bi-shield-check"></i>
        </div>
        <h5>Satisfaction Guaranteed</h5>
        <p>Love it or we'll make it right. 100% happiness promise.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== FAQ SECTION ===== -->
<section class="faq-section">
  <div class="container">
    <h2 class="section-title text-center">Frequently Asked Questions</h2>
    <div class="faq-grid">
      <div class="faq-item">
        <h5>How long do the laddoos stay fresh?</h5>
        <p>Our protein laddoos stay fresh for 30 days from the date of manufacture when stored in a cool, dry place.</p>
      </div>
      <div class="faq-item">
        <h5>Can I customize my combo?</h5>
        <p>Currently, combos are pre-curated for optimal value. You can shop individual products from our shop page.</p>
      </div>
      <div class="faq-item">
        <h5>What is your return policy?</h5>
        <p>We offer easy returns within 7 days of delivery if you're not satisfied with the product quality.</p>
      </div>
      <div class="faq-item">
        <h5>Do you offer corporate gifting?</h5>
        <p>Yes! Contact us for bulk orders and corporate gifting options with custom packaging.</p>
      </div>
    </div>
  </div>
</section>

<!-- ===== CTA SECTION ===== -->
<section class="combo-cta-section">
  <div class="container text-center">
    <h2 class="combo-cta-title">Ready to Stock Up?</h2>
    <p class="combo-cta-text">Get your favorite protein laddoos delivered to your doorstep.</p>
    <a href="shop.php" class="btn btn-cta">View Individual Products</a>
  </div>
</section>

<?php include 'includes/footer.php'; ?>