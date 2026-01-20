<?php
// Endpoint: returns the logged-in user's ID and role.
require_once __DIR__ . '/../config/db.php';
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Open a database connection.
$conn = db_connect();

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Pull the current user ID from the session.
$idx = isset($_SESSION['id']) ? intval($_SESSION['id']) : null;

if ($idx === null) {
    echo json_encode(["error" => "User not logged in or session expired"]);
    $conn->close();
    exit;
}

// Fetch the user's minimal identity info.
$sql = "SELECT id, username, email, role_id FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["error" => "Failed to prepare the SQL statement"]);
    $conn->close();
    exit;
}

$stmt->bind_param("i", $idx);
$stmt->execute();
$result = $stmt->get_result();


// Return a compact JSON response.
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode(["success" => true, "id" => $user['id'], "role_id" => $user['role_id']]);
} else {
    echo json_encode(["success" => false, "message" => "No user found with the given ID"]);
}

// Clean up database resources.
$stmt->close();
$conn->close();
?>


