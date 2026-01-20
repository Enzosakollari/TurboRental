<?php
// Admin endpoint: creates a new car and uploads its images.
require_once __DIR__ . '/../config/db.php';

// Open database connection.
$conn = db_connect();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// first we check if the request method we are sending is post 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // for xss we also use the htmlspecialchars to not allow scripts .
    $name = htmlspecialchars($_POST['name']);
    $price = htmlspecialchars($_POST['price_per_day']);
    $fuel = htmlspecialchars($_POST['fuel']);
    $seats = htmlspecialchars($_POST['seating_capacity']);
    $engine = htmlspecialchars($_POST['engine']);
    $transmission = htmlspecialchars($_POST['transmission']);
    $year = htmlspecialchars($_POST['year']);
    $bluetooth = htmlspecialchars($_POST['bluetooth']);
    $gps = htmlspecialchars($_POST['gps']);
    $color = htmlspecialchars($_POST['color']);
    $type = htmlspecialchars($_POST['type']);

    // first we will insert all the neccessary data except the photos  we store the query 

    $sql = "INSERT INTO cars (name, price_per_day, fuel, seating_capacity, engine, transmission, year, bluetooth, gps, color, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    //prepare the sql statment for execution 
    $stmt = $conn->prepare($sql);
    //bind the input parameters
    $stmt->bind_param("sisissiiiss", $name, $price, $fuel, $seats, $engine, $transmission, $year, $bluetooth, $gps, $color, $type);

    //execute the query 
    if ($stmt->execute()) {
        $carId = $stmt->insert_id; 

        // If images were uploaded, validate and save them.
        if (isset($_FILES['profile_pictures']) && count($_FILES['profile_pictures']['name']) > 0) {
            $uploadFileDir = __DIR__ . '/../images/cars/';
            
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

            for ($i = 0; $i < count($_FILES['profile_pictures']['name']); $i++) {
                $fileTmpPath = $_FILES['profile_pictures']['tmp_name'][$i];
                $fileName = $_FILES['profile_pictures']['name'][$i];
                $fileSize = $_FILES['profile_pictures']['size'][$i];
                $fileType = $_FILES['profile_pictures']['type'][$i];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $newFileName = $carId . '_' . $i . '.' . $fileExtension; 

                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $dest_path = $uploadFileDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        // Store each image path with an explicit order.
                        $sql = "INSERT INTO car_images (car_id, image_path, image_order) VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $image_order = $i + 1;
                        $stmt->bind_param("isi", $carId, $newFileName, $image_order);

                        if (!$stmt->execute()) {
                            echo 'Error inserting car image: ' . $stmt->error;
                        }
                    } else {
                        echo 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
                    }
                } else {
                    echo 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
                }
            }
            echo 'Car added successfully with profile images!';
        } else {
            echo 'Car added successfully!';
        }
    } else {
        echo 'Error: ' . $stmt->error;
    }

    // Clean up database resources.
    $stmt->close();
    $conn->close();
}
?>

