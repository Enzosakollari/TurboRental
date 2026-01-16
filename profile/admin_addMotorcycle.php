<?php
require_once __DIR__ . '/../config/db.php';

$conn = db_connect();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $price = htmlspecialchars($_POST['price_per_day']);
    $fuel = htmlspecialchars($_POST['fuel']);
    $engineCc = htmlspecialchars($_POST['engine_cc']);
    $transmission = htmlspecialchars($_POST['transmission']);
    $year = htmlspecialchars($_POST['year']);
    $abs = htmlspecialchars($_POST['abs']);
    $color = htmlspecialchars($_POST['color']);
    $type = htmlspecialchars($_POST['type']);
    $weightKg = htmlspecialchars($_POST['weight_kg']);
    $seatHeightMm = htmlspecialchars($_POST['seat_height_mm']);

    $sql = "INSERT INTO motorcycles (name, price_per_day, fuel, engine_cc, transmission, year, abs, color, type, weight_kg, seat_height_mm) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdsisiissii", $name, $price, $fuel, $engineCc, $transmission, $year, $abs, $color, $type, $weightKg, $seatHeightMm);

    if ($stmt->execute()) {
        $motorcycleId = $stmt->insert_id; 

        if (isset($_FILES['moto_pictures']) && count($_FILES['moto_pictures']['name']) > 0) {
            $uploadFileDir = __DIR__ . '/../images/motorcycles/';
            
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

            for ($i = 0; $i < count($_FILES['moto_pictures']['name']); $i++) {
                $fileTmpPath = $_FILES['moto_pictures']['tmp_name'][$i];
                $fileName = $_FILES['moto_pictures']['name'][$i];
                $fileSize = $_FILES['moto_pictures']['size'][$i];
                $fileType = $_FILES['moto_pictures']['type'][$i];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $newFileName = $motorcycleId . '_' . $i . '.' . $fileExtension; 

                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $dest_path = $uploadFileDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $sql = "INSERT INTO motorcycle_images (motorcycle_id, image_path, image_order) VALUES (?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $image_order = $i + 1;
                        $stmt->bind_param("isi", $motorcycleId, $newFileName, $image_order);

                        if (!$stmt->execute()) {
                            echo 'Error inserting motorcycle image: ' . $stmt->error;
                        }
                    } else {
                        echo 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
                    }
                } else {
                    echo 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
                }
            }
            echo 'Motorcycle added successfully with profile images!';
        } else {
            echo 'Motorcycle added successfully!';
        }
    } else {
        echo 'Error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

