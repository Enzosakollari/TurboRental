<?php

require_once __DIR__ . '/../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);





$actionCalled = $data['action'];



$conn = db_connect();





if($actionCalled == "getCarDetails"){


    if (empty($data['carId'])) {

        echo json_encode(["success" => false, "message" => "carId was not provided."]);

        exit();

        

    }

    $carId = $data['carId'];

    $stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");

    $stmt->bind_param("i", $carId); 

    $stmt->execute();

    $result = $stmt->get_result();



    if($result->num_rows > 0) {

        $car = $result->fetch_assoc();
    }

    else{

        echo json_encode(['success' => false, 'message' => 'No vehicle found with this ID.']);

        exit;

    }

    

    $stmt = $conn->prepare("SELECT image_path FROM car_images WHERE car_id = ? ORDER BY image_order");

    $stmt->bind_param("i", $carId);  

    $stmt->execute();

    

    $stmt->bind_result($image_path);

    $images = [];

    while ($stmt->fetch()) {

        $images[] = $image_path;  

    }

    echo json_encode(["success" => true, 

                            "name" => $car['name'], 

                            "pricePerDay" => $car['price_per_day'], 

                            "fuel" => $car['fuel'], 

                            "seatingCapacity" => $car['seating_capacity'], 

                            "engine" => $car['engine'],

                            "transmission" => $car['transmission'], 

                            "year" => $car['year'], 

                            "bluetooth" => $car['bluetooth'], 

                            "gps" => $car['gps'], 

                            "color" => $car['color'], 

                            "type" => $car['type'],

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

    if (empty($data['carId']) || empty($data['startDate']) || empty($data['endDate']) || empty($data['totalDays']) || empty($data['pricePerDay'])) {

        echo json_encode(["success" => false, "message" => "Missing required information."]);

        exit();

    }

    $totalPrice = $data['totalDays'] * $data['pricePerDay'];

    $checkStmt = $conn->prepare(

        "SELECT * FROM bookings 

         WHERE car_id = ? 

         AND (

             (status = 'confirmed' AND end_date >= ? AND start_date <= ?) 

             OR 

             (status = 'pending' AND booking_date >= NOW() - INTERVAL 5 MINUTE AND end_date >= ? AND start_date <= ?)

         )"

    );

    $checkStmt->bind_param(

        "issss", 

        $data['carId'], 

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


    $stmt = $conn->prepare("INSERT INTO cart (user_id, car_id, start_date, end_date, total_price) VALUES (?, ?, ?, ?, ?)");

    $stmt->bind_param("iissi", $_SESSION['id'],  $data['carId'], $data['startDate'], $data['endDate'], $totalPrice);

    

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



