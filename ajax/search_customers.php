<?php
session_start();
require("../config.php");

$term = isset($_GET['term']) ? trim($_GET['term']) : '';

if (strlen($term) < 1) {
    echo json_encode([]);
    exit;
}

try {
    $sql = "SELECT customer_id, name, phone 
            FROM customers 
            WHERE name LIKE :term 
            AND is_active = 1
            AND del_status = 0
            LIMIT 10";
    
    $query = $dbh->prepare($sql);
    $search_term = "%{$term}%";
    $query->bindParam(':term', $search_term, PDO::PARAM_STR);
    $query->execute();
    $customers = $query->fetchAll(PDO::FETCH_ASSOC);
    
    $result = [];
    foreach ($customers as $customer) {
        $phone_text = $customer['phone'] ? " - " . $customer['phone'] : "";
        $result[] = [
            'id' => $customer['customer_id'],
            'label' => $customer['name'] . $phone_text,
            'value' => $customer['name']
        ];
    }
    
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>
