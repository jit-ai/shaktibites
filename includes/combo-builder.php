<?php
function shakti_builder_products(): array {
    $products = shakti_catalog();
    return [2 => $products[2], 1 => $products[1], 3 => $products[3]];
}
function shakti_builder_plans(): array {
    $plans = [3 => ['title'=>'Starter','discount'=>8,'saving'=>91,'tone'=>'starter','tag'=>'Good to Start'],6 => ['title'=>'Popular','discount'=>15,'saving'=>341,'tone'=>'popular','tag'=>'Most Popular'],9 => ['title'=>'Value','discount'=>20,'saving'=>682,'tone'=>'value','tag'=>'Best Value']];
    $highest = max(array_column(shakti_builder_products(), 'price'));
    foreach ($plans as $boxes => &$plan) $plan['saving'] = (int) round($highest * $boxes - round($highest * $boxes * (100 - $plan['discount']) / 100));
    unset($plan);
    return $plans;
}
function shakti_build_combo(int $boxes, array $selection): array {
    $plans = shakti_builder_plans();
    if (!isset($plans[$boxes])) throw new InvalidArgumentException('Choose a 3, 6 or 9 box combo.');
    $products = shakti_builder_products(); $subtotal = 0; $count = 0; $parts = []; $names = [];
    foreach ($selection as $id => $qty) {
        if (!isset($products[$id]) || !is_int($qty) || $qty < 0 || $qty > $boxes) throw new InvalidArgumentException('Invalid flavour selection.');
        if (!$qty) continue;
        $count += $qty; $subtotal += $products[$id]['price'] * $qty;
        $parts[$id] = $qty; $names[] = $products[$id]['name'] . ' x ' . $qty;
    }
    if ($count !== $boxes) throw new InvalidArgumentException('Please select exactly ' . $boxes . ' boxes.');
    return ['name'=>$boxes . ' Box Custom Combo (' . implode(', ', $names) . ')','price'=>(int) round($subtotal * (100-$plans[$boxes]['discount'])/100),'original_price'=>$subtotal,'discount_percent'=>$plans[$boxes]['discount'],'image'=>'Combo-product-' . $boxes . '.png','type'=>'custom_combo','slug'=>'custom-combo','selection'=>$parts,'boxes'=>$boxes];
}
