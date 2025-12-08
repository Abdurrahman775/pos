<?php
session_start();
require("../config.php");
require("../include/functions.php");

$term = isset($_GET['term']) ? trim($_GET['term']) : '';

if (strlen($term) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $sql = "SELECT id, name, selling_price, qty_in_stock 
            FROM products 
            WHERE name LIKE :term 
            AND is_active = 1 
            AND qty_in_stock > 0
            LIMIT 10";
    
    $query = $dbh->prepare($sql);
    $search_term = "%{$term}%";
    $query->bindParam(':term', $search_term, PDO::PARAM_STR);
    $query->execute();
    $products = $query->fetchAll(PDO::FETCH_ASSOC);
    
    $result = [];
    foreach ($products as $product) {
        $result[] = [
            'id' => $product['id'],
            'label' => $product['name'] . " (" . get_currency($dbh) . number_format($product['selling_price'], 2) . ") - Stock: " . $product['qty_in_stock'],
            'value' => $product['name']
        ];
    }
    
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>
