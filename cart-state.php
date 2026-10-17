<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
require_once __DIR__ . '/includes/catalog.php';
require_once __DIR__ . '/includes/checkout-pricing.php';
if (empty($_SESSION['cart_token'])) $_SESSION['cart_token'] = bin2hex(random_bytes(32));
$catalog = shakti_cart_catalog();
$items = [];
$count = 0;
$total = 0;
$savings = 0; $singleSavings = 0; $comboSavings = 0;
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
    if ($product['type'] === 'single') $singleSavings += ($original - $price) * $qty; else $comboSavings += ($original - $price) * $qty;
}
$online = shakti_checkout_pricing($_SESSION['cart'] ?? [], $catalog, 'online');
$cod = shakti_checkout_pricing($_SESSION['cart'] ?? [], $catalog, 'cod');
echo json_encode(['items'=>$items, 'cart_count'=>$count, 'total'=>$total, 'savings'=>$savings, 'single_savings'=>$singleSavings, 'combo_savings'=>$comboSavings, 'original_total'=>$total+$savings, 'online'=>$online, 'cod'=>$cod, 'token'=>$_SESSION['cart_token']]);