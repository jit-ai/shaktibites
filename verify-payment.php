<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
    exit;
}

$paymentId  = $_POST['razorpay_payment_id'] ?? '';
$orderId    = $_POST['razorpay_order_id'] ?? '';
$signature  = $_POST['razorpay_signature'] ?? '';

if ($paymentId === '' || $orderId === '' || $signature === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing payment details.']);
    exit;
}

$pending = $_SESSION['razorpay_pending'] ?? null;
unset($_SESSION['razorpay_pending']);

if (!is_array($pending) || ($pending['amount'] ?? null) === null) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Your checkout session has expired. Please place the order again.']);
    exit;
}

// Verify the payment signature: HMAC-SHA256(order_id + payment_id) against the secret
$expectedSignature = hash_hmac('sha256', $orderId . $paymentId, RAZORPAY_KEY_SECRET);
if (!hash_equals($expectedSignature, $signature)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Payment signature verification failed.']);
    exit;
}

try {
    $pdo = getPDO();

    // If the customer asked to create an account, do it now (only after payment succeeds)
    if (($pending['account_type'] ?? 'guest') === 'create') {
        $billing = $pending['billing'];
        $pErr = validateAccountCreation($pending['password'] ?? '', $pending['confirm'] ?? '');
        if ($pErr !== null) {
            throw new RuntimeException($pErr);
        }
        $name = $billing['first_name'] . ' ' . $billing['last_name'];
        $userId = createUser($pdo, $billing['email'], $name, $pending['password'], $billing['phone'], $billing['address']);
        $_SESSION['user_id']    = $userId;
        $_SESSION['user_name']  = $name;
        $_SESSION['user_email'] = $billing['email'];
        $_SESSION['is_admin']   = 0;
    }

    $order = createOrder($pdo, $pending['billing'], 'online', 'completed', $paymentId);
    echo json_encode([
        'status'       => 'success',
        'order_number' => $order['order_number'],
        'total'        => $order['total'],
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    exit;
}
