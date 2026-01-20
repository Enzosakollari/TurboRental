<?php
// Endpoint: checks if an email already exists in the users table.
require_once __DIR__ . '/../config/db.php';

// Open database connection.
$conn = db_connect();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Only respond to POST requests.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    // Count how many users already have this email.
    $sql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Return a simple JSON flag.
    if ($row['count'] > 0) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }

    // Clean up database resources.
    $stmt->close();
    $conn->close();
}
?>
