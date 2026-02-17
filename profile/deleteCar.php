<?php

require_once __DIR__ . '/../config/db.php';


$conn = db_connect();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$data = json_decode(file_get_contents("php://input"));
if (isset($data->car_id)) {
    $carId = $data->car_id;

    
    $conn->begin_transaction();

    try {
        
        $deleteImagesQuery = "DELETE FROM car_images WHERE car_id = ?";
        $stmt = $conn->prepare($deleteImagesQuery);
        $stmt->bind_param('i', $carId);
        $stmt->execute();

        $deleteBookingsQuery = "DELETE FROM bookings WHERE car_id = ?";
        $stmt = $conn->prepare($deleteBookingsQuery);
        $stmt->bind_param('i', $carId);
        $stmt->execute();

        $deleteCarQuery = "DELETE FROM cars WHERE id = ?";
        $stmt = $conn->prepare($deleteCarQuery);
        $stmt->bind_param('i', $carId);
        $stmt->execute();

        $conn->commit();

       echo json_encode(["message" => "Car and associated records deleted successfully."]);

    } catch (Exception $e) {
        
        $conn->rollback();
        echo json_encode(["error" => "Error deleting car: " . $e->getMessage()]);
    }

} else {
    echo json_encode(["error" => "No car ID provided."]);
}

$conn->close();

?>

