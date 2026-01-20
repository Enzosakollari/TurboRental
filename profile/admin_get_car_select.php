<?php
// Admin endpoint: returns full details for a specific car.
require_once __DIR__ . '/../config/db.php';
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Open database connection.
$conn = db_connect();

if ($conn->connect_error) {
    http_response_code(500); 
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Validate the ID parameter.
$idx = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($idx === null) {
    http_response_code(400); 
    echo json_encode(["error" => "Missing or invalid 'id' parameter"]);
    $conn->close();
    exit;
}

// Fetch the full car record.
$sql = "SELECT id, 
               name, 
               price_per_day, 
               fuel, 
               seating_capacity, 
               engine, 
               transmission, 
               year,
               bluetooth,
               gps,
               color,
               type
        FROM cars 
        WHERE id = ?";
$stmt = $conn->prepare($sql);

// Handle SQL preparation errors.
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to prepare SQL statement"]);
    $conn->close();
    exit;
}
$stmt->bind_param("i", $idx);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();

// Return a 404 if the car does not exist.
if (!$car) {
    http_response_code(404);
    echo json_encode(["error" => "No car found with the given ID"]);
} else {
    http_response_code(200); 
    echo json_encode($car);
}

// Clean up database resources.
$stmt->close();
$conn->close();
?>
