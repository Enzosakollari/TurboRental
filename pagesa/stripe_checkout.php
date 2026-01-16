<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db_config.php';

if (!isset($_SESSION['id'])) {
    header('Location: /Makina/registerLogin/login.html');
    exit;
}

$userId = $_SESSION['id'];

$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (empty($products)) {
    header('Location: /Makina/pagesa/shoppingCart.php');
    exit;
}

$bookingIds = [];
$totalAmount = 0.0;

foreach ($products as $product) {
    $carId = $product['car_id'];
    $startDate = $product['start_date'];
    $endDate = $product['end_date'];
    $totalPrice = $product['total_price'];

    $stmt = $conn->prepare("INSERT INTO bookings (user_id, car_id, start_date, end_date, total_price, status, booking_date) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("iissd", $userId, $carId, $startDate, $endDate, $totalPrice);
    $stmt->execute();
    $bookingIds[] = $stmt->insert_id;
    $stmt->close();

    $totalAmount += (float)$totalPrice;
}

$payload = [
    'mode' => 'payment',
    'payment_method_types[]' => 'card',
    'success_url' => STRIPE_SUCCESS_URL,
    'cancel_url' => STRIPE_CANCEL_URL,
    'line_items[0][price_data][currency]' => STRIPE_CURRENCY,
    'line_items[0][price_data][product_data][name]' => 'Car Rental',
    'line_items[0][price_data][unit_amount]' => (int)round($totalAmount * 100),
    'line_items[0][quantity]' => 1,
    'metadata[user_id]' => $userId,
    'metadata[booking_ids]' => implode(',', $bookingIds),
    'metadata[type]' => 'car'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/checkout/sessions');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . STRIPE_SECRET_KEY
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));

$response = curl_exec($ch);
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);

if ($httpStatus >= 200 && $httpStatus < 300 && isset($data['url'])) {
    header('Location: ' . $data['url']);
    exit;
}

echo "Error creating Stripe session.";
