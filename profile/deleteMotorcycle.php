<?php
// Admin endpoint: deletes a motorcycle and its dependent records.
require_once __DIR__ . '/../config/db.php';

// Open database connection.
$conn = db_connect();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read JSON input body.
$data = json_decode(file_get_contents("php://input"));
if (isset($data->motorcycle_id)) {
    $motorcycleId = $data->motorcycle_id;

    // Use a transaction so deletes stay consistent.
    $conn->begin_transaction();

    try {
        // Remove related images and bookings first.
        $deleteImagesQuery = "DELETE FROM motorcycle_images WHERE motorcycle_id = ?";
        $stmt = $conn->prepare($deleteImagesQuery);
        $stmt->bind_param('i', $motorcycleId);
        $stmt->execute();

        $deleteBookingsQuery = "DELETE FROM motorcycle_bookings WHERE motorcycle_id = ?";
        $stmt = $conn->prepare($deleteBookingsQuery);
        $stmt->bind_param('i', $motorcycleId);
        $stmt->execute();

        $deleteMotorcycleQuery = "DELETE FROM motorcycles WHERE id = ?";
        $stmt = $conn->prepare($deleteMotorcycleQuery);
        $stmt->bind_param('i', $motorcycleId);
        $stmt->execute();

        $conn->commit();

       echo json_encode(["message" => "Motorcycle and associated records deleted successfully."]);

    } catch (Exception $e) {
        // Roll back on any error.
        $conn->rollback();
        echo json_encode(["error" => "Error deleting motorcycle: " . $e->getMessage()]);
    }

} else {
    echo json_encode(["error" => "No motorcycle ID provided."]);
}
// Close the connection.
$conn->close();

?>

