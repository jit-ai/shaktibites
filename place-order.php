<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['checkout_error'] = 'Invalid request method.';
    header('Location: checkout');
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
    $_SESSION['checkout_error'] = $error;
    header('Location: checkout');
    exit;
}

$accountType = $_POST['account_type'] ?? 'guest';
$password    = $_POST['password'] ?? '';
$confirm     = $_POST['confirm_password'] ?? '';

try {
    unset($_SESSION['razorpay_pending']);

    if ($accountType === 'create') {
        $pErr = validateAccountCreation($password, $confirm);
        if ($pErr !== null) {
            $_SESSION['checkout_error'] = $pErr;
            header('Location: checkout');
            exit;
        }
        $name = $billing['first_name'] . ' ' . $billing['last_name'];
        $userId = createUser(getPDO(), $billing['email'], $name, $password, $billing['phone'], $billing['address']);
        $_SESSION['user_id']   = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $billing['email'];
        $_SESSION['is_admin']  = 0;
    }

    createOrder(getPDO(), $billing, 'cod', 'pending', null);
    header('Location: order-complete');
    exit;
} catch (Throwable $e) {
    $_SESSION['checkout_error'] = $e->getMessage();
    header('Location: checkout');
    exit;
}
