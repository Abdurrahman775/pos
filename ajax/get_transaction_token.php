<?php
session_start();
require("../config.php");

header('Content-Type: application/json');

$transaction_id = isset($_POST['transaction_id']) ? intval($_POST['transaction_id']) : 0;

if ($transaction_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid transaction ID']);
    exit;
}

try {
    // Get transaction notes which contains the token
    $sql = "SELECT notes FROM transactions WHERE transaction_id = :transaction_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':transaction_id', $transaction_id, PDO::PARAM_INT);
    $query->execute();
    $transaction = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!$transaction) {
        echo json_encode(['status' => 'error', 'message' => 'Transaction not found']);
        exit;
    }
    
    // Extract token from notes field (format: "Token: txn_xxxxx")
    $notes = $transaction['notes'];
    $token = null;
    
    if (preg_match('/Token:\s*([^\s]+)/', $notes, $matches)) {
        $token = $matches[1];
    }
    
    if (!$token) {
        echo json_encode(['status' => 'error', 'message' => 'Transaction token not found']);
        exit;
    }
    
    echo json_encode([
        'status' => 'success',
        'token' => $token,
        'transaction_id' => $transaction_id
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
