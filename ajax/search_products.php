<?php
require("../config.php");
require("../include/functions.php");
require("../include/admin_authentication.php");

header('Content-Type: application/json');

$term = isset($_GET['term']) ? trim($_GET['term']) : '';

if (empty($term)) {
    echo json_encode([]);
    exit;
}

try {
    $sql = "SELECT p.id, p.name, p.barcode, p.selling_price, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE (p.name LIKE :term OR p.barcode LIKE :term OR c.name LIKE :term) 
            AND p.qty_in_stock > 0 AND p.is_active = 1 
            ORDER BY p.name ASC 
            LIMIT 20";
            
    $query = $dbh->prepare($sql);
    $searchTerm = "%" . $term . "%";
    $query->bindParam(':term', $searchTerm, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    $json = [];
    foreach ($results as $row) {
        $label = $row['name'] . " (" . $row['barcode'] . ") - " . get_currency($dbh) . number_format($row['selling_price'], 2);
        if (!empty($row['category_name'])) {
            $label .= " [" . $row['category_name'] . "]";
        }
        
        $json[] = [
            'id' => $row['id'],
            'value' => $row['name'], // Value to put in the input
            'label' => $label, // Label to show in the list
            'barcode' => $row['barcode'],
            'price' => $row['selling_price']
        ];
    }

    echo json_encode($json);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
