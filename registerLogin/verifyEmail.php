<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . "/../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['verificationCode'])) {
    echo json_encode(["success" => false, "message" => "Verification code is required."]);
    exit();
}

$enteredCode = $data['verificationCode'];
$verifyInDatabase = $data['verify'];

$codeRegex = "/^\d{6}$/";
if(!preg_match($codeRegex, $enteredCode)){
    echo json_encode(["success" => false, "message" => "The code must be 6 digits."]);
    exit();
}

if (isset($_SESSION['verification_code']) && $_SESSION['verification_code'] == $enteredCode) {
    if($verifyInDatabase){
        $email = $_SESSION['email'];

        $conn = db_connect();
        
        if ($conn->connect_error) {
            die(json_encode(["success" => false, "message" => "Database connection error: " . $conn->connect_error]));

        }
        $stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $stmt->close();
        $conn->close();
    }

    echo json_encode(["success" => true, "message" => "Success!"]);

    
} else {
    echo json_encode(["success" => false, "message" => "Invalid verification code."]);
}

?>
