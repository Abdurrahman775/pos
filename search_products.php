<?php
/**
 * Product Search API
 * Search products by name or barcode
 */
require("config.php");

header('Content-Type: application/json');

$search = isset($_GET['q']) ? trim($_GET['q']) : '';

if(strlen($search) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $sql = "SELECT id, name, barcode, selling_price, qty_in_stock 
            FROM products 
            WHERE is_active = 1 
            AND (name LIKE :search OR barcode LIKE :search)
            AND qty_in_stock > 0
            LIMIT 10";
    
    $query = $dbh->prepare($sql);
    $searchTerm = '%' . $search . '%';
    $query->bindParam(':search', $searchTerm);
    $query->execute();
    
    $products = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($products);
    
} catch(Exception $e) {
    echo json_encode([]);
}
?>
