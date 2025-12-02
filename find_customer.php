<?php
/**
 * Find Customer API
 * Lookup customer by phone number
 */
require("config.php");

header('Content-Type: application/json');

$phone = isset($_GET['phone']) ? trim($_GET['phone']) : '';

if(empty($phone)) {
    echo json_encode(null);
    exit;
}

try {
    $sql = "SELECT id, name, phone, email, total_purchases FROM customers WHERE phone = :phone AND is_active = 1";
    $query = $dbh->prepare($sql);
    $query->bindParam(':phone', $phone);
    $query->execute();
    
    $customer = $query->fetch(PDO::FETCH_ASSOC);
    echo json_encode($customer ?: null);
    
} catch(Exception $e) {
    echo json_encode(null);
}
?>
