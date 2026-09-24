<?php
session_start();
require_once __DIR__ . '/includes/catalog.php';

$catalog = shakti_catalog();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT) ?: filter_input(INPUT_GET, 'quantity', FILTER_VALIDATE_INT) ?: 1;

if (!$id || !isset($catalog[$id])) {
    header('Location: shop');
    exit;
}
$quantity = max(1, min($quantity, 99));

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// If product already in cart, update quantity
if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id] = min(99, (int) $_SESSION['cart'][$id] + $quantity);
} else {
    $_SESSION['cart'][$id] = $quantity;
}

// Buy now adds the selected item first, then continues directly to checkout.
if (isset($_POST['buy_now'])) {
    header('Location: checkout');
    exit;
}

// A fixed local destination prevents an untrusted Referer redirect.
header('Location: cart');
exit;
?>
