<?php
require_once __DIR__ . '/../config/db.php';
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$conn = db_connect();

if ($conn->connect_error) {
    http_response_code(500); 
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

$idx = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($idx === null) {
    http_response_code(400); 
    echo json_encode(["error" => "Missing or invalid 'id' parameter"]);
    $conn->close();
    exit;
}

$sql = "SELECT id, 
               username, 
               email, 
               profile_image, 
               role_id, 
               is_verified,
               created_at, 
               updated_at,
               full_name,
               address,
               phone_number 
        FROM users 
        WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode(["error" => "Failed to prepare SQL statement"]);
    $conn->close();
    exit;
}
$stmt->bind_param("i", $idx);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
if (!$user) {
    http_response_code(404); 
    echo json_encode(["error" => "No user found with the given ID"]);
} else {
    http_response_code(200); 
    echo json_encode($user);
}

$stmt->close();
$conn->close();
?>
