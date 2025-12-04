<?php
require("../config.php");
require("../include/functions.php");
require("../include/admin_authentication.php");
require("../include/admin_constants.php");

// storing  request (ie, get/post) global array to a variable
$requestData = $_REQUEST;

// getting total number records without any search
$sql = "SELECT COUNT(id) FROM products";
$query = $dbh->prepare($sql);
$query->execute();
$totalData = $query->fetchColumn();
$totalFiltered = $totalData;

$sql = "SELECT id, name, description, selling_price, qty_in_stock, low_stock_alert, is_active from products WHERE 1=1";

if(!empty($requestData['search']['value'])) {
    // when there is a search parameter then we have to modify total number filtered rows as per search result.
    $sql.= " AND (name LIKE '%".$requestData['search']['value']."%' ";
    $sql.= " OR cost_price LIKE '%".$requestData['search']['value']."%')";
    $sql.= " LIMIT ".$requestData['start']." ,".$requestData['length'];
    $query = $dbh->prepare($sql);
    $query->execute();
    $totalFiltered = $query->rowCount();
} else {
    $sql.= " LIMIT ".$requestData['start']." ,".$requestData['length'];
    $query = $dbh->prepare($sql);
    $query->execute();
}

$data = array();
$i = 1 + $requestData['start'];

// preparing an array
while($result = $query->fetch(PDO::FETCH_ASSOC)) {
    $db_status = ($result['is_active'] == 1) ? "Deactivate" : "Activate";
    $nestedData = array();
	$nestedData[] = $i;
    $nestedData[] = $result['name'];
    $nestedData[] = $result['description'];
    $nestedData[] = $result['selling_price'];
    $nestedData[] = $result['qty_in_stock'];
    $nestedData[] = $result['low_stock_alert'];
    $nestedData[] = "<div class=\"dropdown d-inline-block\">
                        <a class=\"dropdown-toggle arrow-none\" title=\"Options\" data-toggle=\"dropdown\" href=\"javascript: void(0);\" role=\"button\" aria-haspopup=\"false\" aria-expanded=\"false\"><i class=\"las la-ellipsis-v font-20\"></i></a>
                        <div class=\"dropdown-menu dropdown-menu-right\">
                            <a class=\"dropdown-item\" href=\"view_product.php?" . http_build_query(array("token"=>base64_encode($result['id']))) . "\">View</a>
                            <a class=\"dropdown-item\" href=\"edit_product.php?" . http_build_query(array("token"=>base64_encode($result['id']))) . "\">Edit</a>
                            <a class=\"dropdown-item\" href=\"re_stock.php?" . http_build_query(array("token"=>base64_encode($result['id']))) . "\">Re-Stock</a>
                            <a class=\"dropdown-item\" href=\"javascript: void(0);\" onClick=\"toggle_product_status(".$result['id'].")\">" . $db_status . "</a>
                        </div>
                    </div>";
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
?>