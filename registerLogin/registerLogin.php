<?php
require_once "functions.php";
require_once __DIR__ . "/../config/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$actionCalled = $data['action'];

$conn = db_connect();

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection error: " . $conn->connect_error]));
}

if($actionCalled == "register"){
    session_start(); 
    
    

    if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
        echo json_encode(["success" => false, "message" => "Required fields are missing."]);
        exit();
        
    }
    $username = $data['username'];
    $email = $data['email'];
    $password = $data['password'];

    $emailRegex = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/";
    $passwordRegex = "/^(?=.*[A-Za-z])(?=.*[-._!#$%&?])[A-Za-z\d!#$%&?]{8,255}$/";
    $usernameRegex = "/^[A-Za-z][A-Za-z0-9._!#$%&?-]{2,20}$/";


    if(!preg_match($emailRegex, $email)){
        echo json_encode(["success" => false, "message" => "Please enter a valid email."]);
        exit();
    }
    if(!preg_match($passwordRegex, $password)){
        echo json_encode(["success" => false, "message" => "Password must be at least 8 characters long and include a letter and a special character."]);
        exit();
    }
    if(!preg_match($usernameRegex, $username)){
        echo json_encode(["success" => false, "message" => "Please enter a valid username."]);
        exit();
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "This email is already registered."]);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);


    try {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role_id, is_verified) VALUES (?, ?, ?, 2, 0)");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);
        $stmt->execute();

        $_SESSION['email'] = $email;

        echo json_encode([
            'success' => true,
            'message' => 'User registered successfully!'
        ]);
    
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
}
else if($actionCalled == "login"){
    header('Content-Type: application/json');
    session_start();

    

    if (empty($data['email']) || empty($data['password'])) {
        echo json_encode(["success" => false, "message" => "Please fill in all fields."]);
        exit();
    }

    $email = $data['email'];
    $password = $data['password'];
    $rememberMe = $data['rememberMe'];

    $emailRegex = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/";
    $passwordRegex = "/^(?=.*[A-Za-z])(?=.*[-._!#$%&?])[A-Za-z\d!#$%&?]{8,255}$/";


    if(!preg_match($emailRegex, $email)){
        echo json_encode(["success" => false, "message" => "Please enter a valid email."]);
        exit();
    }
    if(!preg_match($passwordRegex, $password)){
        echo json_encode(["success" => false, "message" => "Password must be at least 8 characters long and include a letter and a special character."]);
        exit();
    }

    $stmt = $conn->prepare("SELECT id, password, username, is_verified, blocked_until, role_id FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];

        if($user['blocked_until'] && new DateTime() < new DateTime($user['blocked_until'])) {
            echo json_encode(["success" => false, "message" => "Your account is temporarily locked. Please try again later."]);
            exit();
        }

        $recentLoginStmt = $conn->prepare("SELECT attempt_time FROM login_attempts WHERE user_id = ? AND successful = 1 ORDER BY attempt_time DESC LIMIT 1");
        $recentLoginStmt->bind_param("i", $user_id);
        $recentLoginStmt->execute();
        $recentLoginResult = $recentLoginStmt->get_result();

        if($recentLoginResult->num_rows > 0){
            $recentLoginTime = $recentLoginResult->fetch_assoc();
            $recentLoginTime = $recentLoginTime['attempt_time'];
            $recentLoginTime = new DateTime($recentLoginTime);
        }
        else{
            $recentLoginTime = new DateTime('1970-01-01 00:00:00');
        }

        $blockedUntil = $user['blocked_until'] ? new DateTime($user['blocked_until']) : null;


        if($blockedUntil){
            if ($blockedUntil > $recentLoginTime) {
                $startTimeForFaiiledLoginCheck = $blockedUntil;
            } else {
                $startTimeForFaiiledLoginCheck = $recentLoginTime;
            }
        }
        else{
            $startTimeForFaiiledLoginCheck = $recentLoginTime;
        }

        $startTimeForFaiiledLoginCheck = $startTimeForFaiiledLoginCheck->format('Y-m-d H:i:s');

        $failedAttemptsStmt = $conn->prepare("SELECT COUNT(*) AS failed_attempts FROM login_attempts WHERE user_id = ? AND successful = 0 AND attempt_time > ?");
        $failedAttemptsStmt->bind_param("is", $user_id, $startTimeForFaiiledLoginCheck);
        $failedAttemptsStmt->execute();
        $failedAttemptsResult = $failedAttemptsStmt->get_result();
        $failedAttemptsData = $failedAttemptsResult->fetch_assoc();
        $failedAttempts = $failedAttemptsData['failed_attempts'];

                    
        if ($failedAttempts >= 7) {
            $blockTime = new DateTime();
            $blockTime->add(new DateInterval('PT30M'));
            $blockedUntil = $blockTime->format('Y-m-d H:i:s');

            $updateBlockStmt = $conn->prepare("UPDATE users SET blocked_until = ? WHERE id = ?");
            $updateBlockStmt->bind_param("si", $blockedUntil, $user_id);
            if (!$updateBlockStmt->execute()) {
                echo json_encode(["success" => false, "message" => "Unable to lock the user."]);
                exit();
            }

            echo json_encode(["success" => false, "message" => "Too many failed attempts. Your account is locked for 30 minutes."]);
            exit();
        }

        if (password_verify($password, $user['password'])) {
            if ($user['is_verified'] == 1) {
                
                $rememberToken = null;

                if ($rememberMe) {
                    $rememberToken = bin2hex(random_bytes(32));
                    $updateStmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE email = ?");
                    $updateStmt->bind_param("ss", $rememberToken, $email);
                    $updateStmt->execute();
                    setcookie('remember_token', $rememberToken, time() + 3600 * 24 * 30, '/', false, true);
                } else {
                    setcookie('remember_token', '', time() - 3600, '/', false, true);
                }


                $loginAttemptStmt = $conn->prepare("INSERT INTO login_attempts (user_id, successful) VALUES (?, ?)");
                $successful = true; 
                $loginAttemptStmt->bind_param("ii", $user_id, $successful);
                $loginAttemptStmt->execute();

                $_SESSION['id'] = $user['id'];
                $_SESSION['email'] = $email;
                $_SESSION['username'] = $user['username'];
                $_SESSION['role_id'] = $user['role_id'];
                $_SESSION['is_verified'] = $user['is_verified'];

                
                echo json_encode(["success" => true, "verified" => true, "message" => "Login successful."]);

                $recentLoginStmt->close();
                $failedAttemptsStmt->close();
            } else {
                $loginAttemptStmt = $conn->prepare("INSERT INTO login_attempts (user_id, successful) VALUES (?, ?)");
                $successful = false; 
                $loginAttemptStmt->bind_param("ii", $user_id, $successful);
                $loginAttemptStmt->execute();

                echo json_encode(["success" => true, "verified" => false, "message" => "Please verify your email."]);
            }
        } else {
        
            $loginAttemptStmt = $conn->prepare("INSERT INTO login_attempts (user_id, successful) VALUES (?, ?)");
            $successful = false;
            $loginAttemptStmt->bind_param("ii", $user_id, $successful);
            $loginAttemptStmt->execute();

            echo json_encode(["success" => false, "message" => "The password does not match the email."]);
        }
    } else {
        
        
        
        
        
        echo json_encode(["success" => false, "message" => "No user is registered with this email."]);
        exit();
    }

    $loginAttemptStmt->close();
}
else if($actionCalled == "rememberMe"){

    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data['rememberToken'])) {
        echo json_encode(["success" => false, "message" => "Remember token was not provided."]);
        exit();
    }

    $rememberToken = $data['rememberToken'];

    $stmt = $conn->prepare("SELECT email, username FROM users WHERE remember_token = ? LIMIT 1");
    $stmt->bind_param("s", $rememberToken);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(["success" => true, "email" => $user['email'], "username" => $user['username']]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid token."]);
    }
}
else if($actionCalled == "changePassword"){
    session_start();

    if (!isset($_SESSION['email'])) {
        echo json_encode(['success' => false, 'message' => 'Email is not stored in the session.']);
        exit;
    }

    $email = $_SESSION['email'];

    $data = json_decode(file_get_contents('php://input'), true);

    $newPassword = $data['changedPassword'] ?? '';

    $passwordRegex = "/^(?=.*[A-Za-z])(?=.*[-._!#$%&?])[A-Za-z\d!#$%&?]{8,255}$/";

    if(!preg_match($passwordRegex, $newPassword)){
        echo json_encode(["success" => false, "message" => "Password must be at least 8 characters long and include a letter and a special character."]);
        exit();
    }

    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Email is not registered.']);
        exit;
    }

    $user = $result->fetch_assoc();
    $currentPassword = $user['password'];

    if (password_verify($newPassword, $currentPassword)) {
        echo json_encode(['success' => false, 'message' => 'The new password cannot be the same as the current password.']);
        exit;
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param('ss', $hashedPassword, $email);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating the password.']);
    }

}
else if($actionCalled == "saveEmailInSession"){
    session_start();

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['email'])) {
        echo json_encode(["success" => false, "message" => "Email is required."]);
        exit();
    }

    $email = $data['email'];

    $emailRegex = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/";

    if(!preg_match($emailRegex, $email)){
        echo json_encode(["success" => false, "message" => "Please enter a valid email."]);
        exit();
    }
    $stmt = $conn->prepare("SELECT id, username, email, blocked_until FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "No user is registered with this email.."]);
        exit();
    }

    $user = $result->fetch_assoc();

    if ($user['blocked_until'] && new DateTime() < new DateTime($user['blocked_until'])) {
        echo json_encode(["success" => false, "message" => "Your account is temporarily locked. Please try again later."]);
        exit();
    }

    $_SESSION['email'] = $email;

    echo json_encode(["success" => true, "message" => "Email verified and saved in session."]);
}
else if($actionCalled == "sendVerificationCode"){

    session_start();
    header('Content-Type: application/json');

    if (!isset($_SESSION['email'])) {
        echo json_encode(["success" => false, "message" => "Email not found in session."]);
        exit();
    }

    $email = $_SESSION['email'];

    $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ? ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "No user is registered with this email."]);
        exit();
    }

    $newVerificationCode = rand(100000, 999999);

    $user = $result->fetch_assoc();
    $username = $user['username'];

    $_SESSION['verification_code'] = $newVerificationCode;

    $message = "Hello $username, your verification code is: $newVerificationCode. Please enter this code on the verification page to verify your email.";

    $result = sendEmail($message, $email);

    if ($result === true) {
        echo json_encode([
            'success' => true,
            'message' => 'A verification code has been sent to your email.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error sending the code by email.',
            'error' => $result
        ]);
    }
}
else if($actionCalled == "checkLogin"){

    session_start();

if (isset($_SESSION['id'])) {
    echo json_encode(["message" => "User is logged in", "loggedIn" => true]);
} else {
    echo json_encode(["message" => "User is not logged in", "loggedIn" => false]);
}
}


$stmt->close();
$conn->close();


?>
