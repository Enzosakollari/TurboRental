<?php
require_once __DIR__ . '/../config/db.php';
session_start();

if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
    

    $conn = db_connect();

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
}
?>

