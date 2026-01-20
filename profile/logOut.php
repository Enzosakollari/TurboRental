<?php
// Endpoint: clears the current session and returns JSON.

session_start();
session_unset();    // Unset session variables
session_destroy();  // Destroy the session

echo json_encode([
    'success' => true,
    'message' => 'Session destroyed.'
]);
exit;
?>
