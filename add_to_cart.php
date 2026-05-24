<?php
session_start();
include 'includes/config.php';

$id = $_POST['id'] ?? $_GET['id'] ?? 1;
$quantity = $_POST['quantity'] ?? $_GET['quantity'] ?? 1;

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// If product already in cart, update quantity
if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id] += $quantity;
} else {
    $_SESSION['cart'][$id] = $quantity;
}

// Redirect back to referring page or cart
$referer = $_SERVER['HTTP_REFERER'] ?? 'cart.php';
header('Location: ' . $referer);
exit;
?>