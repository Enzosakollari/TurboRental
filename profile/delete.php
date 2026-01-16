<?php
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

$userId = $_POST['id'] ?? null;
if (!$userId) {
    echo json_encode(['status' => 'error', 'message' => 'No user ID provided']);
    exit;
}

try {
    $conn = db_connect();

    if ($conn->connect_error) {
        die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
    }
    $conn->begin_transaction();

    $stmt = $conn->prepare('DELETE FROM bookings WHERE user_id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();

    $stmt = $conn->prepare('DELETE FROM cart WHERE user_id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();

    $stmt = $conn->prepare('DELETE FROM login_attempts WHERE user_id = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();

    $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
    $stmt->bind_param('i', $userId);

    if ($stmt->execute()) {
        $conn->commit();
        echo json_encode(['status' => 'success']);
    } else {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete user']);
    }
} catch (mysqli_sql_exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
} finally {
    $conn->close();
}
?>

