<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
require_once __DIR__ . '/includes/catalog.php';
if (empty($_SESSION['cart_token'])) $_SESSION['cart_token'] = bin2hex(random_bytes(32));
$catalog = shakti_cart_catalog();
$items = [];
$count = 0;
$total = 0;
$savings = 0;
foreach ($_SESSION['cart'] ?? [] as $id => $qty) {
    $qty = (int) $qty;
    if ($qty < 1 || !isset($catalog[$id])) continue;
    $product = $catalog[$id];
    $price = (int) $product['price'];
    $original = max($price, (int) ($product['original_price'] ?? $price));
    $items[] = ['id' => (int) $id, 'name' => $product['name'], 'image' => $product['image'], 'quantity' => $qty, 'price' => $price, 'original_price' => $original, 'type' => $product['type']];
    $count += $qty;
    $total += $price * $qty;
    $savings += ($original - $price) * $qty;
}
echo json_encode(['items' => $items, 'cart_count' => $count, 'total' => $total, 'savings' => $savings, 'token' => $_SESSION['cart_token']]);
