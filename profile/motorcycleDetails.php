<?php
require_once __DIR__ . '/../config/db.php';
$data = json_decode(file_get_contents("php://input"), true);


$actionCalled = $data['action'];

$conn = db_connect();


if($actionCalled == "getMotorcycleDetails"){
    

    
    if (empty($data['motorcycleId'])) {
        echo json_encode(["success" => false, "message" => "motorcycleId was not provided."]);
        exit();
        
    }
    $motorcycleId = $data['motorcycleId'];

    $stmt = $conn->prepare("SELECT * FROM motorcycles WHERE id = ?");
    $stmt->bind_param("i", $motorcycleId); 
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $motorcycle = $result->fetch_assoc();

        
    }
    else{
        echo json_encode(['success' => false, 'message' => 'No motorcycle found with this ID.']);
        exit;
    }
    
    $stmt = $conn->prepare("SELECT image_path FROM motorcycle_images WHERE motorcycle_id = ? ORDER BY image_order");
    $stmt->bind_param("i", $motorcycleId);  
    $stmt->execute();
    
    $stmt->bind_result($image_path);
    $images = [];
    while ($stmt->fetch()) {
        $images[] = $image_path;  
    }

    
    echo json_encode(["success" => true, 
                            "name" => $motorcycle['name'], 
                            "pricePerDay" => $motorcycle['price_per_day'], 
                            "fuel" => $motorcycle['fuel'], 
                            "engineCc" => $motorcycle['engine_cc'],
                            "transmission" => $motorcycle['transmission'], 
                            "year" => $motorcycle['year'], 
                            "abs" => $motorcycle['abs'], 
                            "color" => $motorcycle['color'], 
                            "type" => $motorcycle['type'],
                            "weightKg" => $motorcycle['weight_kg'],
                            "seatHeightMm" => $motorcycle['seat_height_mm'],
                            'images' => $images]);
    exit;
    
}
else if($actionCalled == "addToCart"){
    
    session_start();

    if (!isset($_SESSION['id'])) {
        echo json_encode(["success" => false, "message" => "User is not logged in."]);
        exit();
    }

    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['motorcycleId']) || empty($data['startDate']) || empty($data['endDate']) || empty($data['totalDays']) || empty($data['pricePerDay'])) {
        echo json_encode(["success" => false, "message" => "Missing required information."]);
        exit();
    }

    
    $totalPrice = $data['totalDays'] * $data['pricePerDay'];

    
    $checkStmt = $conn->prepare(
        "SELECT * FROM motorcycle_bookings 
         WHERE motorcycle_id = ? 
         AND (
             (status = 'confirmed' AND end_date >= ? AND start_date <= ?) 
             OR 
             (status = 'pending' AND booking_date >= NOW() - INTERVAL 5 MINUTE AND end_date >= ? AND start_date <= ?)
         )"
    );
    $checkStmt->bind_param(
        "issss", 
        $data['motorcycleId'], 
        $data['startDate'], 
        $data['endDate'], 
        $data['startDate'], 
        $data['endDate']
    );
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "This vehicle is already reserved for at least one of the selected dates."]);
        $checkStmt->close();
        $conn->close();
        exit();
    }

    
    $stmt = $conn->prepare("INSERT INTO motorcycle_cart (user_id, motorcycle_id, start_date, end_date, total_price) VALUES (?, ?, ?, ?, ?)");

    
    $stmt->bind_param("iissi", $_SESSION['id'],  $data['motorcycleId'], $data['startDate'], $data['endDate'], $totalPrice);
    
    if ($stmt->execute()){
        echo json_encode(["success" => true, "message" => "Added to cart."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    $checkStmt->close();
    
}

$stmt->close();
$conn->close();

?>

