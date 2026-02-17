<?php
require_once __DIR__ . '/../config/db.php';


$conn = db_connect();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId    = intval($_POST['id']);
    $username  = trim($_POST['username']);
    $email     = trim($_POST['email']);
    $fullname  = trim($_POST['emriplote']);
    $address   = trim($_POST['adresa']);
    $telephone = trim($_POST['nrtelefoni']);
    
    $password  = $_POST['password'] ?? ''; 
    $fileInfo  = $_FILES['profile_picture'] ?? null;

    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit;
    }

    if (empty($username)) {
        echo "Username cannot be empty.";
        exit;
    }

    $hashed_password = !empty($password) 
        ? password_hash($password, PASSWORD_BCRYPT) 
        : null;

    
    $target_file = null; 
    if ($fileInfo && $fileInfo['name']) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type     = mime_content_type($fileInfo["tmp_name"]);
        $file_size     = $fileInfo["size"];
        $max_file_size = 2 * 1024 * 1024; 
        $target_dir    = __DIR__ . '/../images/';
    
        if (in_array($file_type, $allowed_types) && $file_size <= $max_file_size) {
            $newFileName = basename($fileInfo['name']); 
            $target_file = $target_dir . $newFileName;
            if (!move_uploaded_file($fileInfo["tmp_name"], $target_file)) {
                echo "Error uploading profile picture.";
                exit;
            }
        } else {
            echo "Invalid file type or size. Only JPG, PNG, and GIF files under 2MB are allowed.";
            exit;
        }
    }

    
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $userId);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "This email is already in use by another user.";
        $stmt->close();
        exit;
    }
    $stmt->close();

    
    $sql    = "UPDATE users SET username = ?, email = ?, full_name = ?, address = ?, phone_number = ?";
    $params = [$username, $email, $fullname, $address, $telephone];
    $types  = "sssss";

    if ($hashed_password) {
        $sql      .= ", password = ?";
        $params[]  = $hashed_password;
        $types    .= "s";
    }

    if ($target_file) {
        $sql      .= ", profile_image = ?";
        $params[]  = $newFileName; 
        $types    .= "s";
    }

    $sql     .= " WHERE id = ?";
    $params[] = $userId;
    $types   .= "i";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    
    if ($stmt->execute()) {
        echo "Profile updated successfully.";
       
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
}


$conn->close();
?>

