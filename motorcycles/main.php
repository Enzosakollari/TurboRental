<?php
require_once __DIR__ . '/../config/db.php';
$data = json_decode(file_get_contents("php://input"), true);

$conn = db_connect();

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection error: " . $conn->connect_error]));
}

$sql = "SELECT id, name, price_per_day, engine_cc, fuel, transmission, year, type FROM motorcycles WHERE 1=1";
if (!empty($data['search'])) {
  $searchTerm = $data['search'];
  $sql .= " AND name LIKE '%$searchTerm%'";
} 
if (!empty($data['fuelType'])) {
    $fuelFilter = implode("','", $data['fuelType']);
    $sql .= " AND fuel IN ('$fuelFilter')";
}

if (!empty($data['price'])) {
    $priceFilter = $data['price'];
    $sql .= " AND price_per_day <= $priceFilter";
}

if (!empty($data['transmission'])) {
    $transmissionFilter = $data['transmission'];
    $sql .= " AND transmission = '$transmissionFilter'";
}



$page = isset($data['page']) ? $data['page'] : 1;
$carsPerPage = isset($data['carsPerPage']) ? $data['carsPerPage'] : 3;
$offset = ($page - 1) * $carsPerPage;

$sql .= " LIMIT $offset, $carsPerPage";

$result = $conn->query($sql);

$cars_data = [];
if ($result->num_rows > 0) {
    while ($car = $result->fetch_assoc()) {
        $carId = $car['id'];
        $image_sql = "SELECT image_path FROM motorcycle_images WHERE motorcycle_id = ? AND image_order = 1";
        $image_stmt = $conn->prepare($image_sql);
        $image_stmt->bind_param("i", $carId);
        $image_stmt->execute();
        $image_stmt->bind_result($image_path);
        
        $image = null;
        if ($image_stmt->fetch()) {
            $image = $image_path;
        }
        $image_stmt->close();
        $car['image_path'] = $image;
        $cars_data[] = $car;
    }
}

$totalSql = "SELECT COUNT(*) AS total FROM motorcycles WHERE 1=1";

if (!empty($data['fuelType'])) {
    $fuelFilter = implode("','", $data['fuelType']);
    $totalSql .= " AND fuel IN ('$fuelFilter')";
}

if (!empty($data['price'])) {
    $priceFilter = $data['price'];
    $totalSql .= " AND price_per_day <= $priceFilter";
}

if (!empty($data['transmission'])) {
    $transmissionFilter = $data['transmission'];
    $totalSql .= " AND transmission = '$transmissionFilter'";
}

if (!empty($data['search'])) {
    $searchTerm = $data['search'];
    $totalSql .= " AND name LIKE '%$searchTerm%'";
}

$totalCarsResult = $conn->query($totalSql);
$totalCars = $totalCarsResult->fetch_assoc()['total'];

$totalPages = ceil($totalCars / $carsPerPage);

echo json_encode([
    "success" => true,
    "cars" => $cars_data,
    "totalPages" => $totalPages
]);

$conn->close();

?>

