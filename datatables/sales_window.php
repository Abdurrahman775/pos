<?php
require("../config.php");
require("../include/functions.php");
require("../include/admin_authentication.php");
require("../include/admin_constants.php");

// storing  request (ie, get/post) global array to a variable
$requestData = $_REQUEST;
// getting total number records without any search
$sql = "SELECT count(id) FROM products  WHERE is_active= 1";
$query = $dbh->prepare($sql);
$query->execute();
$totalData = $query->fetchColumn();
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

$sql = "SELECT id, name, qty_in_stock, barcode, selling_price FROM products WHERE qty_in_stock != 0 and is_active= 1";

if (!empty($requestData['search']['value'])) {
    // when there is a search parameter then we have to modify total number filtered rows as per search result.
    $sql .= " AND (name LIKE '%" . $requestData['search']['value'] . "%' ";
    $sql .= " OR barcode LIKE '%" . $requestData['search']['value'] . "%')";
    $sql .= " LIMIT " . $requestData['start'] . " ," . $requestData['length'];
    $query = $dbh->prepare($sql);
    $query->execute();
    $totalFiltered = $query->rowCount();
} else {
    $sql .= " LIMIT " . $requestData['start'] . " ," . $requestData['length'];
    $query = $dbh->prepare($sql);
    $query->execute();
}

$data = array();
$i = 1 + $requestData['start'];

// preparing an array
while ($result = $query->fetch(PDO::FETCH_ASSOC)) {
    $nestedData = array();
    $product_id = $result['id'];
    $nestedData[] = $result['name'];
    $nestedData[] = get_currency($dbh) . number_format($result['selling_price'], 2);
    $nestedData[] = $result['qty_in_stock'];
    $nestedData[] = "<button name=\"cartPlus\" class=\"btn btn-outline-primary btn-round waves-effect waves-light btn-sm\" id=\"cartPlus\" title=\"Add {$result['name']} to cart\" onclick=\"addtocart(" . $result['id'] . ")\">+ Cart</button>";
    $data[] = $nestedData;
    $i++;
}

$json_data = array(
    "draw" => intval($requestData['draw']),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
);
echo json_encode($json_data);
