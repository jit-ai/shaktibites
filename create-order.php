<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$billing = [
    'first_name' => trim($_POST['first_name'] ?? ''),
    'last_name'  => trim($_POST['last_name'] ?? ''),
    'address'    => trim($_POST['address'] ?? ''),
    'city'       => trim($_POST['city'] ?? ''),
    'state'      => trim($_POST['state'] ?? ''),
    'pincode'    => trim($_POST['pincode'] ?? ''),
    'phone'      => trim($_POST['phone'] ?? ''),
    'email'      => trim($_POST['email'] ?? ''),
];

$error = validateBilling($billing);
if ($error !== null) {
    echo json_encode(['success' => false, 'message' => $error]);
    exit;
}

try {
    $cart = collectCartItems();
} catch (RuntimeException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

$accountType = $_POST['account_type'] ?? 'guest';
$password    = $_POST['password'] ?? '';
$confirm     = $_POST['confirm_password'] ?? '';

if ($accountType === 'create') {
    $pErr = validateAccountCreation($password, $confirm);
    if ($pErr !== null) {
        echo json_encode(['success' => false, 'message' => $pErr]);
        exit;
    }
}

// Amount in paise (smallest currency unit)
$amountPaise = (int) round($cart['total'] * 100);

// Persist billing + amount + account intent in the session so verify-payment.php can complete the order
$_SESSION['razorpay_pending'] = [
    'billing'      => $billing,
    'amount'       => $cart['total'],
    'currency'     => RAZORPAY_CURRENCY,
    'account_type' => $accountType,
    'password'     => $accountType === 'create' ? $password : '',
    'confirm'      => $accountType === 'create' ? $confirm : '',
];

$data = [
    'amount'   => $amountPaise,
    'currency' => RAZORPAY_CURRENCY,
    'receipt'  => 'order_rcptid_' . uniqid(),
];

$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($response === false) {
    unset($_SESSION['razorpay_pending']);
    http_response_code(502);
    echo json_encode(['success' => false, 'message' => 'Unable to reach Razorpay. Please try again.']);
    exit;
}

$result = json_decode((string) $response, true);

if ($httpCode !== 200 || empty($result['id'])) {
    unset($_SESSION['razorpay_pending']);
    $message = $result['error']['description'] ?? 'Unable to create Razorpay order.';
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

echo json_encode([
    'success'  => true,
    'order_id' => $result['id'],
    'amount'   => $amountPaise,
    'currency' => RAZORPAY_CURRENCY,
    'key_id'   => RAZORPAY_KEY_ID,
    'name'     => SITE_NAME,
]);
