<?php
session_start();
require("../config.php");
require("../include/functions.php");

header('Content-Type: application/json');

$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if (empty($token)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid token']);
    exit;
}

// For now, just return success
// In production, this would trigger actual printer communication
echo json_encode([
    'status' => 'success',
    'message' => 'Receipt sent to printer'
]);
?>
