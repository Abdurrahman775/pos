<?php

/**
 * Process Transaction API
 * Complete sale and save to database
 */
require("config.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

try {
    $dbh->beginTransaction();

    // Insert transaction
    $sql = "INSERT INTO transactions (
        customer_id, cashier_id, transaction_date, subtotal, tax_amount, 
        discount_amount, total_amount, payment_method, amount_paid, 
        change_given, status, created_at
    ) VALUES (
        :customer_id, :cashier_id, NOW(), :subtotal, :tax_amount, 
        :discount_amount, :total_amount, :payment_method, :amount_paid, 
        :change_given, 'completed', NOW()
    )";

    $query = $dbh->prepare($sql);
    $query->bindParam(':customer_id', $data['customer_id']);
    $query->bindParam(':cashier_id', $data['cashier_id'], PDO::PARAM_INT);
    $query->bindParam(':subtotal', $data['subtotal']);
    $query->bindParam(':tax_amount', $data['tax_amount']);
    $query->bindParam(':discount_amount', $data['discount_amount']);
    $query->bindParam(':total_amount', $data['total_amount']);
    $query->bindParam(':payment_method', $data['payment_method']);
    $query->bindParam(':amount_paid', $data['amount_paid']);
    $query->bindParam(':change_given', $data['change_given']);
    $query->execute();

    $transaction_id = $dbh->lastInsertId();

    // Insert transaction items and update stock
    foreach ($data['items'] as $item) {
        // Insert transaction item
        $sql = "INSERT INTO transaction_items (
            transaction_id, product_id, quantity, unit_price, subtotal
        ) VALUES (
            :transaction_id, :product_id, :quantity, :unit_price, :subtotal
        )";

        $query = $dbh->prepare($sql);
        $query->bindParam(':transaction_id', $transaction_id, PDO::PARAM_INT);
        $query->bindParam(':product_id', $item['id'], PDO::PARAM_INT);
        $query->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
        $query->bindParam(':unit_price', $item['price']);
        $subtotal = $item['price'] * $item['quantity'];
        $query->bindParam(':subtotal', $subtotal);
        $query->execute();

        // Update product stock
        $sql = "UPDATE products SET qty_in_stock = qty_in_stock - :quantity WHERE id = :product_id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
        $query->bindParam(':product_id', $item['id'], PDO::PARAM_INT);
        $query->execute();

        // Log stock movement
        $sql = "INSERT INTO stock_movements (
            product_id, movement_type, quantity, reference_id, reference_type, 
            notes, created_by, created_at
        ) VALUES (
            :product_id, 'sale', :quantity, :transaction_id, 'transaction', 
            'Sale transaction', :cashier_id, NOW()
        )";

        $query = $dbh->prepare($sql);
        $query->bindParam(':product_id', $item['id'], PDO::PARAM_INT);
        $quantity_negative = -$item['quantity'];
        $query->bindParam(':quantity', $quantity_negative, PDO::PARAM_INT);
        $query->bindParam(':transaction_id', $transaction_id, PDO::PARAM_INT);
        $query->bindParam(':cashier_id', $data['cashier_id'], PDO::PARAM_INT);
        $query->execute();
    }

    // Update customer total purchases if customer selected
    if ($data['customer_id']) {
        $sql = "UPDATE customers SET total_purchases = total_purchases + :amount WHERE id = :customer_id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':amount', $data['total_amount']);
        $query->bindParam(':customer_id', $data['customer_id'], PDO::PARAM_INT);
        $query->execute();
    }

    // Log activity
    if (isset($_SESSION['pos_admin'])) {
        log_activity($dbh, $_SESSION['pos_admin'], 'CREATE', 'Completed sale transaction #' . $transaction_id);
    }

    $dbh->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Transaction completed successfully',
        'transaction_id' => $transaction_id
    ]);
} catch (Exception $e) {
    $dbh->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
