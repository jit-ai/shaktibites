<?php
/** Prepare confirmation content without transmitting messages or credentials. */
function shakti_order_confirmation(array $order): array
{
    $billing = $order['billing'];
    $name = trim($billing['first_name'] . ' ' . $billing['last_name']);
    $phone = preg_replace('/\D/', '', (string) $billing['phone']);
    if (strlen($phone) === 10) $phone = '91' . $phone;
    $money = static function ($value): string { return 'INR ' . number_format((float) $value, 2, '.', ','); };
    $items = [];
    foreach ($order['items'] as $item) {
        $items[] = $item['name'] . ' x ' . (int) $item['quantity'] . ' (' . $money($item['price'] * $item['quantity']) . ')';
    }
    $address = implode(', ', array_filter([$billing['address'], $billing['city'], $billing['state'], $billing['pincode']]));
    $cod = $order['payment_method'] === 'cod';
    $payment = $cod ? 'Cash on delivery - payment due on delivery' : 'Online payment - ' . $order['payment_status'];
    $lines = ['Hi ' . $name . ', your Shakti Bites order is confirmed.', 'Order: ' . $order['order_number'], 'Items: ' . implode('; ', $items)];
    if (isset($order['pricing'])) {
        $price = $order['pricing'];
        $lines[] = 'Shipping: ' . $money($price['shipping']);
        $lines[] = 'Prepaid discount: ' . $money($price['prepaid_discount']);
        $lines[] = 'COD fee: ' . $money($price['cod_fee']);
    }
    $lines[] = ($cod ? 'Amount due: ' : 'Total paid: ') . $money($order['total_amount']);
    $lines[] = 'Payment: ' . $payment;
    $lines[] = 'Deliver to: ' . $address;
    return ['to' => $phone, 'customer_name' => $name, 'order_number' => $order['order_number'], 'items' => implode('; ', $items), 'total' => $money($order['total_amount']), 'payment' => $payment, 'address' => $address, 'text' => implode("\n", $lines)];
}
