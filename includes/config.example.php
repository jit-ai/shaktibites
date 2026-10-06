<?php
/**
 * Template for includes/config.php.
 *
 * Copy this file to includes/config.php on the server and fill in the values:
 *   cp includes/config.example.php includes/config.php
 *
 * includes/config.php is git-ignored, so the live password never reaches the
 * repository. Every value below can also be supplied as an environment variable,
 * which is the preferred way to configure it on Hostinger if you prefer to keep
 * the file itself out of the deployment.
 */
$env = static function (string $key, string $fallback): string {
    $value = getenv($key);
    // Only an absent variable falls back. An empty value is deliberate, which is
    // what local XAMPP needs for the root user's blank password.
    return $value === false ? $fallback : $value;
};

define('SITE_NAME', $env('SITE_NAME', 'Shakti Bites'));
define('SITE_URL', rtrim($env('SITE_URL', 'https://example.com/'), '/'));

// On Hostinger the database host is "localhost": the database lives on the same
// server as the website, so the hostname of the site is not used here.
define('DB_HOST', $env('DB_HOST', 'localhost'));
define('DB_NAME', $env('DB_NAME', 'your_database_name'));
define('DB_USER', $env('DB_USER', 'your_database_user'));
define('DB_PASS', $env('DB_PASS', 'your_database_password'));
define('DB_CHARSET', $env('DB_CHARSET', 'utf8mb4'));

// Razorpay credentials from https://dashboard.razorpay.com
// Use the live keys once the site is taking real payments.
define('RAZORPAY_KEY_ID', $env('RAZORPAY_KEY_ID', 'rzp_test_your_key_id'));
define('RAZORPAY_KEY_SECRET', $env('RAZORPAY_KEY_SECRET', 'your_key_secret'));
define('RAZORPAY_CURRENCY', 'INR');
define('TAX_RATE', 0.05);
?>