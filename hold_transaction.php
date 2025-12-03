<?php

/**
 * Hold Transaction API
 * Save incomplete transaction for later retrieval
 */
require("config.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['cart_data'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

try {
    $sql = "INSERT INTO held_transactions (
        cashier_id, customer_id, transaction_name, cart_data, held_at
    ) VALUES (
        :cashier_id, :customer_id, :transaction_name, :cart_data, NOW()
    )";

    $query = $dbh->prepare($sql);
    $query->bindParam(':cashier_id', $data['cashier_id'], PDO::PARAM_INT);
    $query->bindParam(':customer_id', $data['customer_id']);
    $query->bindParam(':transaction_name', $data['transaction_name']);
    $cart_json = json_encode($data['cart_data']);
    $query->bindParam(':cart_data', $cart_json);
    $query->execute();

    // Log activity
    if (isset($_SESSION['pos_admin'])) {
        log_activity($dbh, $_SESSION['pos_admin'], 'CREATE', 'Held transaction: ' . $data['transaction_name']);
    }

    echo json_encode(['success' => true, 'held_id' => $dbh->lastInsertId()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
