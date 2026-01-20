<?php
// Admin endpoint: returns a list of motorcycles for the admin table.
require_once __DIR__ . '/../config/db.php';
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Open database connection.
$conn = db_connect();

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "Database connection failed."
    ]);
    exit;
}

// Query the minimal fields for the list view.
$sql = "SELECT id, name, price_per_day, type
        FROM motorcycles
        ORDER BY name";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// Handle query errors explicitly.
if ($result === false) {
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "Query failed."
    ]);
    $conn->close();
    exit;
}
// Build the response list.
$cars = [];
while ($row = $result->fetch_assoc()) {
    $cars[] = $row;
}
// Send JSON response.
http_response_code(200);
echo json_encode([
    "status" => "success", 
    "data" => $cars
]);

// Close the connection.
$conn->close();
?>


