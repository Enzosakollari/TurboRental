<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db_config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized.']);
    exit;
}

$type = $_POST['type'] ?? 'car';
if (!in_array($type, ['car', 'motorcycle'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid checkout type.']);
    exit;
}

$userId = $_SESSION['id'];
$cartTable = $type === 'motorcycle' ? 'motorcycle_cart' : 'cart';
$bookingTable = $type === 'motorcycle' ? 'motorcycle_bookings' : 'bookings';
$itemColumn = $type === 'motorcycle' ? 'motorcycle_id' : 'car_id';

$stmt = $conn->prepare("SELECT * FROM {$cartTable} WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (empty($products)) {
    http_response_code(400);
    echo json_encode(['error' => 'Cart is empty.']);
    exit;
}

$bookingIds = [];
$totalAmount = 0.0;

foreach ($products as $product) {
    $itemId = $product[$itemColumn];
    $startDate = $product['start_date'];
    $endDate = $product['end_date'];
    $totalPrice = $product['total_price'];

    $stmt = $conn->prepare("INSERT INTO {$bookingTable} (user_id, {$itemColumn}, start_date, end_date, total_price, status, booking_date) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("iissd", $userId, $itemId, $startDate, $endDate, $totalPrice);
    $stmt->execute();
    $bookingIds[] = $stmt->insert_id;
    $stmt->close();

    $totalAmount += (float)$totalPrice;
}

$payload = [
    'amount' => (int)round($totalAmount * 100),
    'currency' => STRIPE_CURRENCY,
    'payment_method_types[]' => 'card',
    'metadata[user_id]' => $userId,
    'metadata[type]' => $type,
    'metadata[booking_ids]' => implode(',', $bookingIds)
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.stripe.com/v1/payment_intents');
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

if ($httpStatus >= 200 && $httpStatus < 300 && isset($data['client_secret'])) {
    echo json_encode([
        'client_secret' => $data['client_secret'],
        'payment_intent' => $data['id'] ?? null
    ]);
    exit;
}

http_response_code($httpStatus >= 400 ? $httpStatus : 500);
echo json_encode([
    'error' => $data['error']['message'] ?? 'Unable to create payment intent.'
]);
