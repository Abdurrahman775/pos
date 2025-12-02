<?php
/**
 * Save Customer API
 * Quick customer creation from sales window
 */
require("config.php");

header('Content-Type: application/json');

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if(empty($name) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'Name and phone are required']);
    exit;
}

try {
    // Check if phone already exists
    $sql = "SELECT id FROM customers WHERE phone = :phone";
    $query = $dbh->prepare($sql);
    $query->bindParam(':phone', $phone);
    $query->execute();
    
    if($query->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Phone number already exists']);
        exit;
    }
    
    // Insert new customer
    $sql = "INSERT INTO customers (name, phone, email, created_at) VALUES (:name, :phone, :email, NOW())";
    $query = $dbh->prepare($sql);
    $query->bindParam(':name', $name);
    $query->bindParam(':phone', $phone);
    $query->bindParam(':email', $email);
    $query->execute();
    
    echo json_encode(['success' => true, 'customer_id' => $dbh->lastInsertId()]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
