<?php
$page_title = 'Product Details';
include 'includes/header.php';

$products = [
    1 => ['name' => 'Peanut Jaggery Power Bites', 'price' => 249, 'desc' => 'Experience the perfect blend of roasted peanuts and jaggery in every bite. Our Peanut Jaggery Power Bites are crafted to give you instant energy without any sugar crash. Made with premium peanuts, organic jaggery, and dates, these laddoos pack a powerful punch of protein and natural goodness.', 'img' => 'product1.PNG', 'label' => 'Everyday Energy', 'label_class' => 'label-everyday'],
    2 => ['name' => 'Almond Cacao Power Bites', 'price' => 279, 'desc' => 'Indulge in the rich, chocolatey goodness of our Almond Cacao Power Bites. Made with premium almonds, raw cacao powder, and dates, these laddoos satisfy your chocolate cravings while providing the energy you need. The perfect guilt-free treat for chocolate lovers.', 'img' => 'product2.PNG', 'label' => '⭐ Best Seller ⭐', 'label_class' => 'label-bestseller'],
    3 => ['name' => 'Dry Fruit Cardamom Bites', 'price' => 299, 'desc' => 'Savor the royal taste of our Dry Fruit Cardamom Bites. Packed with premium cashews, almonds, and aromatic cardamom, these laddoos offer a luxurious snacking experience. The perfect blend of tradition and nutrition in every bite.', 'img' => 'product3.PNG', 'label' => 'Premium Pick', 'label_class' => 'label-premium']
];

$id = $_GET['id'] ?? 1;
$product = $products[$id] ?? $products[1];
?>

<!-- ===== PRODUCT HERO ===== -->
<section class="product-hero">
  <div class="container">
    <h1 class="product-hero-title"><?php echo $product['name']; ?></h1>
  </div>
</section>

<!-- ===== PRODUCT DETAIL SECTION ===== -->
<section class="product-detail-section">
  <div class="container">
    <div class="product-detail-grid">

      <!-- Product Image -->
      <div class="product-image-col">
        <div class="product-image-wrapper">
          <img src="assets/images/<?php echo $product['img']; ?>" alt="<?php echo $product['name']; ?>" class="product-main-image" id="mainImage">
        </div>
        <div class="product-thumbnails">
          <img src="assets/images/<?php echo $product['img']; ?>" alt="Thumbnail 1" class="product-thumbnail active" onclick="changeImage(this)">
          <img src="assets/images/<?php echo $product['img']; ?>" alt="Thumbnail 2" class="product-thumbnail" onclick="changeImage(this)">
          <img src="assets/images/<?php echo $product['img']; ?>" alt="Thumbnail 3" class="product-thumbnail" onclick="changeImage(this)">
        </div>
      </div>

      <!-- Product Info -->
      <div class="product-info-col">
        <span class="prod-label <?php echo $product['label_class']; ?>"><?php echo $product['label']; ?></span>
        <h2 class="product-title"><?php echo $product['name']; ?></h2>
        <div class="product-price">₹<?php echo $product['price']; ?> <span class="price-unit">/ box</span></div>
        <p class="product-description"><?php echo $product['desc']; ?></p>

        <div class="product-features">
          <div class="prod-feature"><span class="dot-orange"></span>10g Protein per Laddoo</div>
          <div class="prod-feature"><span class="dot-orange"></span>No Refined Sugar</div>
          <div class="prod-feature"><span class="dot-orange"></span>100% Natural Ingredients</div>
        </div>

        <div class="quantity-selector">
          <label for="quantity">Quantity</label>
          <div class="quantity-input-wrapper">
            <button type="button" class="qty-btn" onclick="decrementQty()">-</button>
            <input type="number" id="quantity" name="quantity" value="1" min="1" max="10">
            <button type="button" class="qty-btn" onclick="incrementQty()">+</button>
          </div>
        </div>

        <div class="product-actions">
          <form method="POST" action="add_to_cart.php" class="cart-form">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="quantity" id="quantity-input" value="1">
            <button type="submit" class="btn btn-add-cart">Add to Cart</button>
          </form>
          <a href="checkout.php" class="btn btn-buy-now">Buy Now</a>
        </div>

        <div class="product-meta">
          <p><strong>SKU:</strong> SB-PROD-<?php echo $id; ?></p>
          <p><strong>Category:</strong> Protein Laddoos</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ===== PRODUCT TABS ===== -->
<section class="product-tabs-section">
  <div class="container">
    <div class="product-tabs">
      <div class="tab-buttons">
        <button class="tab-btn active" onclick="openTab('description')">Description</button>
        <button class="tab-btn" onclick="openTab('ingredients')">Ingredients</button>
        <button class="tab-btn" onclick="openTab('reviews')">Reviews (5)</button>
      </div>
      <div class="tab-content">
        <div id="description" class="tab-pane active">
          <p><?php echo $product['desc']; ?></p>
          <p>Store in a cool, dry place. Best consumed within 30 days of opening.</p>
        </div>
        <div id="ingredients" class="tab-pane">
          <ul>
            <li>Peanuts</li>
            <li>Jaggery</li>
            <li>Dates</li>
            <li>Cardamom</li>
          </ul>
        </div>
        <div id="reviews" class="tab-pane">
          <div class="review-summary">
            <div class="stars">★★★★★</div>
            <span>5.0 out of 5 stars</span>
          </div>
          <p>Be the first to review this product!</p>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
function changeImage(element) {
   document.getElementById('mainImage').src = element.src;
   document.querySelectorAll('.product-thumbnail').forEach(thumb => thumb.classList.remove('active'));
   element.classList.add('active');
 }

 function incrementQty() {
   const qty = document.getElementById('quantity');
   if (qty.value < 10) qty.value++;
   updateQuantityInput();
 }

 function decrementQty() {
   const qty = document.getElementById('quantity');
   if (qty.value > 1) qty.value--;
   updateQuantityInput();
 }

 function updateQuantityInput() {
   document.getElementById('quantity-input').value = document.getElementById('quantity').value;
 }

 function openTab(tabName) {
   document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
   document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
   document.getElementById(tabName).classList.add('active');
   event.target.classList.add('active');
 }
</script>

<?php include 'includes/footer.php'; ?>