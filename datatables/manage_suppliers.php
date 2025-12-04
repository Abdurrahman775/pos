<?php
require("../config.php");
require("../include/functions.php");
require("../include/admin_authentication.php");
require("../include/admin_constants.php");

$requestData = $_REQUEST;

$columns = array(
    0 => 'id',
    1 => 'supplier_name',
    2 => 'contact_name',
    3 => 'phone',
    4 => 'email',
    5 => 'product_count',
    6 => 'id'
);

// Getting total number of records without any search
$sql = "SELECT count(id) FROM suppliers";
$query = $dbh->prepare($sql);
$query->execute();
$totalData = $query->fetchColumn();
$totalFiltered = $totalData;

$sql = "SELECT s.*, (SELECT COUNT(*) FROM products p WHERE p.supplier_id = s.id AND p.is_active = 1) as product_count 
        FROM suppliers s 
        WHERE 1=1";

if (!empty($requestData['search']['value'])) {
    $sql .= " AND (s.supplier_name LIKE :search ";
    $sql .= " OR s.contact_name LIKE :search ";
    $sql .= " OR s.phone LIKE :search ";
    $sql .= " OR s.email LIKE :search)";
    
    $query = $dbh->prepare($sql);
    $searchTerm = "%" . $requestData['search']['value'] . "%";
    $query->bindParam(':search', $searchTerm, PDO::PARAM_STR);
    $query->execute();
    $totalFiltered = $query->rowCount();
} else {
    $query = $dbh->prepare($sql);
    $query->execute();
}

$sql .= " ORDER BY s.supplier_name ASC LIMIT " . $requestData['start'] . " ," . $requestData['length'];
$query = $dbh->prepare($sql);
if (!empty($requestData['search']['value'])) {
    $searchTerm = "%" . $requestData['search']['value'] . "%";
    $query->bindParam(':search', $searchTerm, PDO::PARAM_STR);
}
$query->execute();

$data = array();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $nestedData = array();
    
    $nestedData[] = $row['id'];
    $nestedData[] = htmlspecialchars($row['supplier_name']);
    $nestedData[] = htmlspecialchars($row['contact_name'] ?? '-');
    $nestedData[] = htmlspecialchars($row['phone'] ?? '-');
    $nestedData[] = htmlspecialchars($row['email'] ?? '-');
    $nestedData[] = $row['product_count'];
    
    $actions = '<a href="edit_supplier.php?id=' . $row['id'] . '" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a> ';
    $actions .= '<a href="supplier_details.php?id=' . $row['id'] . '" class="btn btn-sm btn-info" title="View Details"><i class="fas fa-eye"></i></a>';
    
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
