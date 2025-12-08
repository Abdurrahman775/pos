<?php
session_start();
require("../config.php");
require("../include/functions.php");

// DataTables server-side processing
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$length = isset($_POST['length']) ? intval($_POST['length']) : 10;
$search_value = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';

// Build search condition
$search_condition = "";
$search_params = [];

if (!empty($search_value)) {
    $search_condition = " AND (name LIKE :search OR barcode LIKE :search)";
    $search_params[':search'] = "%{$search_value}%";
}

// Get total records
$sql = "SELECT COUNT(*) as total FROM products WHERE is_active = 1 AND qty_in_stock > 0" . $search_condition;
$query = $dbh->prepare($sql);
foreach ($search_params as $key => $value) {
    $query->bindValue($key, $value);
}
$query->execute();
$total_records = $query->fetch(PDO::FETCH_ASSOC)['total'];

// Get filtered records
$sql = "SELECT id, name, selling_price, qty_in_stock 
        FROM products 
        WHERE is_active = 1 AND qty_in_stock > 0" . $search_condition . "
        ORDER BY name ASC
        LIMIT :start, :length";

$query = $dbh->prepare($sql);
foreach ($search_params as $key => $value) {
    $query->bindValue($key, $value);
}
$query->bindValue(':start', $start, PDO::PARAM_INT);
$query->bindValue(':length', $length, PDO::PARAM_INT);
$query->execute();
$products = $query->fetchAll(PDO::FETCH_ASSOC);

// Format data for DataTables
$data = [];
$counter = $start + 1;

foreach ($products as $product) {
    $data[] = [
        $counter++,
        htmlspecialchars($product['name']),
        get_currency($dbh) . number_format($product['selling_price'], 2),
        $product['qty_in_stock'],
        '<button class="btn btn-sm btn-primary" onclick="addtocart(' . $product['id'] . ')"><i class="fa fa-plus"></i></button>'
    ];
}

// Return JSON response
$response = [
    "draw" => $draw,
    "recordsTotal" => $total_records,
    "recordsFiltered" => $total_records,
    "data" => $data
];

header('Content-Type: application/json');
echo json_encode($response);
?>
