<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/checkout-pricing.php';
$_SESSION = [];
$products = shakti_cart_catalog();
foreach ([1 => [15, 50], 3 => [30, 50], 6 => [50, 75], 9 => [75, 100]] as $boxes => [$discount, $fee]) {
    $cart = [1 => $boxes];
    $online = shakti_checkout_pricing($cart, $products, 'online');
    $cod = shakti_checkout_pricing($cart, $products, 'cod');
    if ($online['prepaid_discount'] !== $discount || $cod['cod_fee'] !== $fee || $online['total'] != 249 * $boxes + ($boxes === 1 ? 50 : 0) - $discount || $cod['total'] != 249 * $boxes + ($boxes === 1 ? 50 : 0) + $fee) throw new Exception('Tier failed');
    $_SESSION['cart'] = $cart;
    if (collectCartItems('online')['total'] !== $online['total'] || collectCartItems('cod')['total'] !== $cod['total']) throw new Exception('Order calculation mismatch');
    echo "$boxes-box tier passed\n";
}
$_SESSION['custom_combos'][100000] = ['boxes'=>6, 'selection'=>[1=>2,2=>2,3=>2]];
$products = shakti_cart_catalog();
$custom = shakti_checkout_pricing([100000=>1],$products,'online');
if ($custom['tier'] !== 6 || $custom['prepaid_discount'] !== 50) throw new Exception('Custom combo tier');
$legacy = shakti_checkout_pricing([5=>1],$products,'online');
if ($legacy['tier'] !== 3) throw new Exception('Legacy combo tier');
$mixed = shakti_checkout_pricing([100000=>1,1=>3],$products,'cod');
if ($mixed['tier'] !== 9 || $mixed['cod_fee'] !== 100) throw new Exception('Mixed tier');
if (shakti_checkout_pricing([],$products,'cod')['total'] != 0) throw new Exception('Empty cart charged');
echo "Custom combos, legacy combos, mixed cart, empty cart passed\n";

$boundaryProducts = [1 => ['price'=>299, 'boxes'=>1]];
$below = shakti_checkout_pricing([1=>1], $boundaryProducts, 'online');
if ($below['shipping'] !== 50 || $below['shipping_remaining'] !== 200 || $below['total'] != 334) throw new Exception('299 shipping rule failed');
foreach ([498 => 50, 499 => 0, 500 => 0] as $amount => $shipping) {
    $result = shakti_checkout_pricing([1=>1], [1=>['price'=>$amount,'boxes'=>1]], 'online');
    if ($result['shipping'] !== $shipping || $result['shipping_remaining'] !== max(0,499-$amount)) throw new Exception('Shipping boundary failed');
}
echo "Shipping 299, 498, 499, 500 boundaries passed\n";