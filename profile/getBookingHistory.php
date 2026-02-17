<?php
require_once __DIR__ . '/../config/db.php';
session_start();
header('Content-Type: application/json');

$conn = db_connect();

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (!isset($data['userId'])) {
    echo json_encode(['success' => false, 'message' => 'User ID not provided']);
    exit();
}

$userId = $data['userId'];

$query = "
    SELECT b.start_date, b.end_date, b.booking_date, b.total_price, c.name AS car_name
    FROM bookings b
    INNER JOIN cars c ON b.car_id = c.id
    WHERE b.user_id = ? AND b.status = 'confirmed'
    ORDER BY b.booking_date DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
    echo json_encode(['success' => true, 'bookings' => $bookings]);
} else {
    echo json_encode(['success' => false, 'message' => 'No confirmed bookings found for this user']);
}

$stmt->close();
$conn->close();
?>
