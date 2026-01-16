<?php
$baseUrl = getenv('APP_BASE_URL');
if (!$baseUrl) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseUrl = $scheme . '://' . $host;
}
$baseUrl = rtrim($baseUrl, '/');

define('STRIPE_SECRET_KEY', 'sk_test_51SqC67J6vaRj5mmwQSAM2GLdiVzzAGs93XxXcPtcqJgVUmOCiv1s68s4e3S7K4SSZosBqc7BMD93jIthFekeewWp00OLOwljZb'); // Replace with your Stripe secret key
define('STRIPE_PUBLISHABLE_KEY', 'pk_test_51SqC67J6vaRj5mmwunjR7xXXlklyEMy2oxkAv243MvpgXscVqC6cpTpttRIitKHC12NqYf8kiLLDyeRtrVKvOYNq00LwEPuC68'); // Replace with your Stripe publishable key
define('STRIPE_CURRENCY', 'usd');

define('STRIPE_SUCCESS_URL', $baseUrl . '/Makina/pagesa/success.php?session_id={CHECKOUT_SESSION_ID}');
define('STRIPE_CANCEL_URL', $baseUrl . '/Makina/pagesa/cancel.php');
define('STRIPE_MOTORCYCLE_SUCCESS_URL', $baseUrl . '/Makina/pagesa/motorcycleSuccess.php?session_id={CHECKOUT_SESSION_ID}');
define('STRIPE_MOTORCYCLE_CANCEL_URL', $baseUrl . '/Makina/pagesa/motorcycleCancel.php');
