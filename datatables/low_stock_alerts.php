<?php
require("../config.php");
require("../include/functions.php");
require("../include/admin_authentication.php");
require("../include/admin_constants.php");

$requestData = $_REQUEST;

$columns = array(
    0 => 'id',
    1 => 'name',
    2 => 'category_name',
    3 => 'stock_quantity',
    4 => 'min_stock_level',
    5 => 'shortage',
    6 => 'selling_price',
    7 => 'id'
);

// Getting total number of records without any search
$sql = "SELECT count(p.id) FROM products p WHERE p.qty_in_stock <= p.low_stock_alert AND p.is_active = 1";
$query = $dbh->prepare($sql);
$query->execute();
$totalData = $query->fetchColumn();
$totalFiltered = $totalData;

$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.qty_in_stock <= p.low_stock_alert AND p.is_active = 1";

if (!empty($requestData['search']['value'])) {
    $sql .= " AND (p.name LIKE :search ";
    $sql .= " OR p.barcode LIKE :search ";
    $sql .= " OR c.name LIKE :search)";
    
    $query = $dbh->prepare($sql);
    $searchTerm = "%" . $requestData['search']['value'] . "%";
    $query->bindParam(':search', $searchTerm, PDO::PARAM_STR);
    $query->execute();
    $totalFiltered = $query->rowCount();
} else {
    $query = $dbh->prepare($sql);
    $query->execute();
    $totalFiltered = $query->rowCount(); // Should be same as totalData if no search
}

$sql .= " ORDER BY p.qty_in_stock ASC LIMIT " . $requestData['start'] . " ," . $requestData['length'];
$query = $dbh->prepare($sql);
if (!empty($requestData['search']['value'])) {
    $searchTerm = "%" . $requestData['search']['value'] . "%";
    $query->bindParam(':search', $searchTerm, PDO::PARAM_STR);
}
$query->execute();

$data = array();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $nestedData = array();
    $shortage = $row['low_stock_alert'] - $row['qty_in_stock'];
    
    $nestedData[] = $row['id'];
    $nestedData[] = $row['name'];
    $nestedData[] = $row['category_name'] ?? '-';
    $nestedData[] = '<span class="badge badge-danger">' . $row['qty_in_stock'] . '</span>';
    $nestedData[] = $row['low_stock_alert'];
    $nestedData[] = '<span class="text-danger font-weight-bold">' . $shortage . '</span>';
    $nestedData[] = get_currency($dbh) . number_format($row['selling_price'], 2);
    $token = base64_encode($row['id']);
    $actions = '<div class="dropdown d-inline-block">
                    <a class="dropdown-toggle arrow-none" title="Options" data-toggle="dropdown" href="javascript: void(0);"><i class="las la-ellipsis-v font-20"></i></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="re_stock.php?token=' . $token . '">Re-Stock</a>
                        <a class="dropdown-item" href="edit_product.php?token=' . $token . '">Edit</a>
                        <a class="dropdown-item" href="view_product.php?token=' . $token . '">View</a>
                    </div>
                </div>';
    $nestedData[] = $actions;
    
    $data[] = $nestedData;
}

$json_data = array(
    "draw" => intval($requestData['draw']),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
);

echo json_encode($json_data);
?>
