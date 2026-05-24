<div class="product-card">
    <div class="product-image">
        <img src="assets/images/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
    </div>
    <div class="product-info">
        <h3><?php echo $product['name']; ?></h3>
        <p><?php echo $product['description']; ?></p>
        <div class="price">$<?php echo $product['price']; ?></div>
        <a href="shop.php" class="btn">Add to Cart</a>
    </div>
</div>