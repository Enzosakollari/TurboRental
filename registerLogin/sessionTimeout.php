<?php
session_start();

if (!isset($_SESSION['lastActivity'])) {
    $_SESSION['lastActivity'] = time();
}

$data = json_decode(file_get_contents('php://input'), true);
$updateActivity = $data['updateActivity'];


if($updateActivity){
    $_SESSION['lastActivity'] = time();
}

if ((time() - $_SESSION['lastActivity']) > 900) {
    session_unset();
    session_destroy();

    echo json_encode(["redirect" => true]);
    exit();
}

echo json_encode(["active" => true]);

?>