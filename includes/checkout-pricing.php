<?php
/** One payment adjustment per order, based on its total number of boxes. */
function shakti_checkout_pricing(array $cart, array $products, string $payment): array
{
    $subtotal = 0;
    $boxes = 0;
    foreach ($cart as $id => $quantity) {
        $quantity = (int) $quantity;
        if ($quantity < 1 || !isset($products[$id])) continue;
        $product = $products[$id];
        $subtotal += $product['price'] * $quantity;
        $boxes += max(1, (int) ($product['boxes'] ?? 1)) * $quantity;
    }
    $tier = $boxes >= 9 ? 9 : ($boxes >= 6 ? 6 : ($boxes >= 3 ? 3 : 1));
    $discount = $boxes && $payment === 'online' ? min($subtotal, [1 => 15, 3 => 30, 6 => 50, 9 => 75][$tier]) : 0;
    $fee = $boxes && $payment === 'cod' ? [1 => 50, 3 => 50, 6 => 75, 9 => 100][$tier] : 0;
    return ['subtotal' => $subtotal, 'boxes' => $boxes, 'tier' => $tier, 'prepaid_discount' => $discount, 'cod_fee' => $fee, 'total' => round($subtotal - $discount + $fee, 2)];
}
