<?php
// Admin endpoint: creates a new user and optionally uploads a profile image.
require_once __DIR__ . '/../config/db.php';

// Open database connection.
$conn = db_connect();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only handle POST requests from the admin form.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read and sanitize form input.
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $role_id = htmlspecialchars($_POST['role_id']);
    $is_verified = htmlspecialchars($_POST['is_verified']);
    $fullname = htmlspecialchars($_POST['fullname']);
    $address = htmlspecialchars($_POST['address']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $profile_image = '';

    // Create the user record first.
    $sql = "INSERT INTO users (username, full_name, address, phone_number, email, password, role_id, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssii", $username, $fullname, $address, $telephone, $email, $password, $role_id, $is_verified);

    if ($stmt->execute()) {
        $userId = $stmt->insert_id; 

        // If a profile image was uploaded, validate and save it.
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
            $fileName = $_FILES['profile_picture']['name'];
            $fileSize = $_FILES['profile_picture']['size'];
            $fileType = $_FILES['profile_picture']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $newFileName = $userId . '.' . $fileExtension; 

            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $uploadFileDir = __DIR__ . '/../images/';
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Store the image file name on the user record.
                    $profile_image = $newFileName; 
                    $sql = "UPDATE users SET profile_image = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("si", $profile_image, $userId);

                    if ($stmt->execute()) {
                        echo 'User added successfully with profile image!';
                    } else {
                        echo 'Error updating profile image: ' . $stmt->error;
                    }
                } else {
                    echo 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
                }
            } else {
                echo 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
            }
        } else {
            echo 'User added successfully!';
        }
    } else {
        echo 'Error: ' . $stmt->error;
    }

    // Clean up database resources.
    $stmt->close();
    $conn->close();
}
?>

