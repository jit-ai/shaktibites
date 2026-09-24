<?php
require_once __DIR__ . '/includes/catalog.php';
$catalog = shakti_catalog();
$product = $catalog[1];
echo "Product ID: 1\n";
echo "Keys: " . implode(', ', array_keys($product)) . "\n";
echo "short: " . ($product['short'] ?? 'MISSING') . "\n";
echo "benefits: " . (is_array($product['benefits']) ? implode(', ', $product['benefits']) : 'NOT ARRAY') . "\n";
echo "accent: " . ($product['accent'] ?? 'MISSING') . "\n";
echo "image: " . ($product['image'] ?? 'MISSING') . "\n";
echo "name: " . ($product['name'] ?? 'MISSING') . "\n";
echo "price: " . ($product['price'] ?? 'MISSING') . "\n";
echo "label: " . ($product['label'] ?? 'MISSING') . "\n";
echo "ingredients: " . (is_array($product['ingredients']) ? implode(', ', $product['ingredients']) : 'NOT ARRAY') . "\n";
echo "occasion: " . (is_array($product['occasion']) ? implode(', ', $product['occasion']) : 'NOT ARRAY') . "\n";
