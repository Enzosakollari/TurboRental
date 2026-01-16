<?php
require_once __DIR__ . '/../config/db.php';

$conn = db_connect();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
