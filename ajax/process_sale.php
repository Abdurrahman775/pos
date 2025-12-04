<?php
require("../config.php");
require("../include/functions.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$response = ['status' => 'error', 'message' => 'Invalid request'];

try {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action == 'finalise') {
        $customer_type = isset($_POST['customer_type']) ? $_POST['customer_type'] : 'existing';
        $customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : null;
        $payment_type = isset($_POST['payment_type']) ? $_POST['payment_type'] : 'CASH';
        $discount = isset($_POST['discount']) ? floatval($_POST['discount']) : 0;
        $cash_received = isset($_POST['cash_received']) ? floatval($_POST['cash_received']) : 0;
        $payment_ref = isset($_POST['payment_ref']) ? $_POST['payment_ref'] : '';

        // Determine customer name based on type
        if ($customer_type === 'existing') {
            // Get customer name from database
            if ($customer_id) {
                $sql = "SELECT name FROM customers WHERE customer_id = :customer_id LIMIT 1";
                $query = $dbh->prepare($sql);
                $query->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
                $query->execute();
                $cust = $query->fetch(PDO::FETCH_ASSOC);
                $customer_name = $cust ? $cust['name'] : 'Customer';
            } else {
                $customer_name = 'Customer';
                $customer_id = null;
            }
        } else {
            // New customer - use entered name
            $customer_name = isset($_POST['customer_name_new']) ? trim($_POST['customer_name_new']) : 'Customer';
            $customer_id = null; // No customer ID for new customers
        }

        // Validate cart
        if (empty($_SESSION['cart'])) {
            $response = ['status' => 'error', 'message' => 'Cart is empty'];
            echo json_encode($response);
            exit;
        }

        // Calculate totals
        $subtotal = 0;
        foreach ($_SESSION['cart'] as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = get_product_price($dbh, $product_id);
            $subtotal += ($price * $quantity);
        }

        $tax_amount = ($subtotal * 0.075); // 7.5% tax
        $total_amount = $subtotal + $tax_amount - $discount;

        // Insert transaction using actual transactions table columns
        $cash_change = $cash_received - $total_amount;
        $notes = !empty($payment_ref) ? 'Payment ref: ' . $payment_ref : null;

        $sql = "INSERT INTO transactions (user_id, customer_id, transaction_date, subtotal, tax_amount, discount_amount, total_amount, payment_method, amount_paid, change_amount, status, notes, created_by, created_at)
                VALUES (:user_id, :customer_id, NOW(), :subtotal, :tax_amount, :discount_amount, :total_amount, :payment_method, :amount_paid, :change_amount, 'completed', :notes, :created_by, NOW())";
        $query = $dbh->prepare($sql);
        $query->bindValue(':user_id', isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : 0, PDO::PARAM_INT);
        $query->bindValue(':customer_id', $customer_id, PDO::PARAM_INT);
        $query->bindParam(':subtotal', $subtotal, PDO::PARAM_STR);
        $query->bindParam(':tax_amount', $tax_amount, PDO::PARAM_STR);
        $query->bindParam(':discount_amount', $discount, PDO::PARAM_STR);
        $query->bindParam(':total_amount', $total_amount, PDO::PARAM_STR);
        $query->bindValue(':payment_method', $payment_type, PDO::PARAM_STR);
        $query->bindValue(':amount_paid', $cash_received, PDO::PARAM_STR);
        $query->bindValue(':change_amount', $cash_change, PDO::PARAM_STR);
        $query->bindValue(':notes', $notes, PDO::PARAM_STR);
        $query->bindValue(':created_by', isset($_SESSION['pos_admin']) ? $_SESSION['pos_admin'] : (isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 'system'), PDO::PARAM_STR);

        if (!$query->execute()) {
            throw new Exception("Error inserting transaction");
        }

        $transaction_id = $dbh->lastInsertId();
        // Insert transaction items
        foreach ($_SESSION['cart'] as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $line_price = get_product_price($dbh, $product_id);
            $line_total = $line_price * $quantity;
            $product_name = convert_product_id($dbh, $product_id);
            $line_discount = 0;

            $sql = "INSERT INTO transaction_items (transaction_id, product_id, product_name, quantity, unit_price, discount, line_total) 
                    VALUES (:transaction_id, :product_id, :product_name, :quantity, :unit_price, :discount, :line_total)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':transaction_id', $transaction_id, PDO::PARAM_INT);
            $query->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $query->bindParam(':product_name', $product_name, PDO::PARAM_STR);
            $query->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $query->bindParam(':unit_price', $line_price, PDO::PARAM_STR);
            $query->bindParam(':discount', $line_discount, PDO::PARAM_STR);
            $query->bindParam(':line_total', $line_total, PDO::PARAM_STR);
            $query->execute();
        }

        // Update customer total_purchases if customer exists
        if ($customer_id) {
            // customers table uses `customer_id` as primary key
            $sql = "UPDATE customers SET total_purchases = total_purchases + :amount WHERE customer_id = :customer_id";
            $query = $dbh->prepare($sql);
            $query->bindValue(':amount', $total_amount, PDO::PARAM_STR);
            $query->bindValue(':customer_id', $customer_id, PDO::PARAM_INT);
            $query->execute();
        }

        // Clear cart
        $_SESSION['cart'] = [];

        $response = ['status' => 'success', 'message' => 'Transaction completed successfully!', 'token' => base64_encode($transaction_id)];
    }
} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response);
