<?php
declare(strict_types=1);

session_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed.']);
    exit;
}

if (!isset($_SESSION['cart_token']) || !hash_equals($_SESSION['cart_token'], (string) ($_POST['token'] ?? ''))) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'message' => 'Your cart session has expired. Please refresh the page.']);
    exit;
}

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
$catalog = [1 => 249, 2 => 279, 3 => 299];

if (!$productId || !isset($catalog[$productId]) || $quantity === false || $quantity < 0) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'Invalid cart update.']);
    exit;
}

$_SESSION['cart'] = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];
if ($quantity === 0) {
    unset($_SESSION['cart'][$productId]);
} else {
    $_SESSION['cart'][$productId] = min($quantity, 99);
}

$cartCount = 0;
$subtotal = 0;
foreach ($_SESSION['cart'] as $id => $qty) {
    if (isset($catalog[$id])) {
        $cartCount += (int) $qty;
        $subtotal += $catalog[$id] * (int) $qty;
    }
}

echo json_encode([
    'ok' => true,
    'cart_count' => $cartCount,
    'subtotal' => $subtotal,
    'tax' => (int) round($subtotal * 0.05),
    'total' => (int) round($subtotal * 1.05),
]);
