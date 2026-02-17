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
$motorcyclesPerPage = isset($data['motorcyclesPerPage']) ? $data['motorcyclesPerPage'] : 3;
$offset = ($page - 1) * $motorcyclesPerPage;

$sql .= " LIMIT $offset, $motorcyclesPerPage";

$result = $conn->query($sql);

$motorcyclesData = [];
if ($result->num_rows > 0) {
    while ($motorcycle = $result->fetch_assoc()) {
        $motorcycleId = $motorcycle['id'];
        $image_sql = "SELECT image_path FROM motorcycle_images WHERE motorcycle_id = ? AND image_order = 1";
        $image_stmt = $conn->prepare($image_sql);
        $image_stmt->bind_param("i", $motorcycleId);
        $image_stmt->execute();
        $image_stmt->bind_result($image_path);
        
        $image = null;
        if ($image_stmt->fetch()) {
            $image = $image_path;
        }
        $image_stmt->close();
        $motorcycle['image_path'] = $image;
        $motorcyclesData[] = $motorcycle;
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

$totalMotorcyclesResult = $conn->query($totalSql);
$totalMotorcycles = $totalMotorcyclesResult->fetch_assoc()['total'];

$totalPages = ceil($totalMotorcycles / $motorcyclesPerPage);

echo json_encode([
    "success" => true,
    "motorcycles" => $motorcyclesData,
    "totalPages" => $totalPages
]);

$conn->close();

?>

