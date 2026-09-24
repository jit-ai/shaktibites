<?php
// Callers may pass the catalogue key as $productId; fall back safely for
// legacy callers that store it in the product array.
$productId = isset($productId) ? (int) $productId : (int) ($product['id'] ?? 1);
$productSlug = is_string($product['slug'] ?? null) ? $product['slug'] : 'peanut-jaggery-power-bites';
?>
<div class="product-card">
    <a class="product-card-link" href="product/<?php echo rawurlencode($productSlug); ?>" aria-label="View <?php echo htmlspecialchars($product['name']); ?> details"></a>
    <div class="product-image">
        <img src="assets/images/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
    </div>
    <div class="product-info">
        <h3><?php echo $product['name']; ?></h3>
        <p><?php echo $product['description']; ?></p>
        <div class="price">$<?php echo $product['price']; ?></div>
        <a href="product/<?php echo rawurlencode($productSlug); ?>" class="btn btn-try">View Details</a>
    </div>
</div>
