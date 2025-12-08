<?php
session_start();
require("../config.php");
require("../include/functions.php");

header('Content-Type: application/json');

if (!isset($_POST['action']) || $_POST['action'] !== 'finalise') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    exit;
}

// Validate cart
if (empty($_SESSION['cart'])) {
    echo json_encode(['status' => 'error', 'message' => 'Cart is empty']);
    exit;
}

// Get form data
$payment_type = isset($_POST['payment_type']) ? $_POST['payment_type'] : 'CASH';
$cash_received = isset($_POST['cash_received']) ? floatval($_POST['cash_received']) : 0;
$payment_ref = isset($_POST['payment_ref']) ? trim($_POST['payment_ref']) : '';
$customer_type = isset($_POST['customer_type']) ? $_POST['customer_type'] : 'existing';
$customer_id = isset($_POST['customer_id']) ? intval($_POST['customer_id']) : null;
$customer_name_new = isset($_POST['customer_name_new']) ? trim($_POST['customer_name_new']) : '';
$discount = isset($_POST['discount']) ? floatval($_POST['discount']) : 0;
$order_total = isset($_POST['hidden_order_total']) ? floatval($_POST['hidden_order_total']) : 0;
$cash_change = isset($_POST['hidden_cash_change']) ? floatval($_POST['hidden_cash_change']) : 0;

// Validation
if ($payment_type === 'CASH' && $cash_received <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter amount received']);
    exit;
}

if ($payment_type === 'POS' && empty($payment_ref)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter payment reference']);
    exit;
}

// Calculate totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $sql = "SELECT selling_price FROM products WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $item['product_id'], PDO::PARAM_INT);
    $query->execute();
    $product = $query->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        $subtotal += $product['selling_price'] * $item['quantity'];
    }
}

$final_total = $subtotal - $discount;

// Handle customer - don't create new customer records for walk-ins
// Only use customer_id if selecting existing customer
$walk_in_customer_name = '';
if ($customer_type === 'new' && !empty($customer_name_new)) {
    $walk_in_customer_name = $customer_name_new;
    $customer_id = null; // Don't link to customer table
} elseif ($customer_type === 'new' && empty($customer_name_new)) {
    $walk_in_customer_name = 'Walk-in';
    $customer_id = null;
}

try {
    // Start transaction
    $dbh->beginTransaction();
    
    // Insert transaction
    $transaction_token = uniqid('txn_', true);
    $user_id = $_SESSION['admin_id'] ?? null;
    $current_user = $_SESSION['pos_admin'] ?? 'system';
    $tax_amount = 0; // Can be calculated if needed
    
    // Build notes with token and customer name if walk-in
    $notes = "Token: " . $transaction_token;
    if (!empty($walk_in_customer_name)) {
        $notes .= " | Customer: " . $walk_in_customer_name;
    }
    
    $sql = "INSERT INTO transactions (
        transaction_date, user_id, customer_id, subtotal, tax_amount,
        discount_amount, total_amount, payment_method, amount_paid, 
        change_amount, status, notes, created_by, created_at
    ) VALUES (
        NOW(), :user_id, :customer_id, :subtotal, :tax_amount,
        :discount_amount, :total_amount, :payment_method, :amount_paid,
        :change_amount, 'completed', :notes, :created_by, NOW()
    )";
    
    $query = $dbh->prepare($sql);
    $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $query->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
    $query->bindParam(':subtotal', $subtotal, PDO::PARAM_STR);
    $query->bindParam(':tax_amount', $tax_amount, PDO::PARAM_STR);
    $query->bindParam(':discount_amount', $discount, PDO::PARAM_STR);
    $query->bindParam(':total_amount', $final_total, PDO::PARAM_STR);
    $query->bindParam(':payment_method', $payment_type, PDO::PARAM_STR);
    $query->bindParam(':amount_paid', $cash_received, PDO::PARAM_STR);
    $query->bindParam(':change_amount', $cash_change, PDO::PARAM_STR);
    $query->bindParam(':notes', $notes, PDO::PARAM_STR);
    $query->bindParam(':created_by', $current_user, PDO::PARAM_STR);
    $query->execute();
    
    $transaction_id = $dbh->lastInsertId();
    
    // Insert transaction items and update stock
    foreach ($_SESSION['cart'] as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        
        
        // Get product details
        $sql = "SELECT name, selling_price, qty_in_stock FROM products WHERE id = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $product_id, PDO::PARAM_INT);
        $query->execute();
        $product = $query->fetch(PDO::FETCH_ASSOC);
        
        if (!$product || $product['qty_in_stock'] < $quantity) {
            throw new Exception('Insufficient stock for product ID: ' . $product_id);
        }
        
        $product_name = $product['name'];
        $unit_price = $product['selling_price'];
        $item_discount = 0; // Per-item discount can be added later
        $line_total = ($unit_price * $quantity) - $item_discount;
        
        // Insert transaction item
        $sql = "INSERT INTO transaction_items (transaction_id, product_id, product_name, quantity, unit_price, discount, line_total) 
                VALUES (:transaction_id, :product_id, :product_name, :quantity, :unit_price, :discount, :line_total)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':transaction_id', $transaction_id, PDO::PARAM_INT);
        $query->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $query->bindParam(':product_name', $product_name, PDO::PARAM_STR);
        $query->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $query->bindParam(':unit_price', $unit_price, PDO::PARAM_STR);
        $query->bindParam(':discount', $item_discount, PDO::PARAM_STR);
        $query->bindParam(':line_total', $line_total, PDO::PARAM_STR);
        $query->execute();
        
        // Update product stock
        $sql = "UPDATE products SET qty_in_stock = qty_in_stock - :quantity WHERE id = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $query->bindParam(':id', $product_id, PDO::PARAM_INT);
        $query->execute();
    }
    
    // Commit transaction
    $dbh->commit();
    
    // Clear cart
    $_SESSION['cart'] = [];
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Transaction completed successfully',
        'token' => $transaction_token,
        'transaction_id' => $transaction_id
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    $dbh->rollBack();
    echo json_encode(['status' => 'error', 'message' => 'Transaction failed: ' . $e->getMessage()]);
}
?>
