<?php

require_once __DIR__ . '/../config/db.php';
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');


$conn = db_connect();

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "Database connection failed."
    ]);
    exit;
}


$sql = "SELECT id, name, price_per_day,type 
        FROM cars
        ORDER BY name";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();


if ($result === false) {
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "Query failed."
    ]);
    $conn->close();
    exit;
}

$cars = [];
while ($row = $result->fetch_assoc()) {
    $cars[] = $row;
}

http_response_code(200);
echo json_encode([
    "status" => "success", 
    "data" => $cars
]);


$conn->close();
?>


