<?php
require(""../config.php");
require("../include/functions.php");
require("../include/admin_authentication.php");
require("../include/admin_constants.php");

// storing  request (ie, get/post) global array to a variable
$requestData = $_REQUEST;
$columns = array( 
// datatable column index  => database column name
    0 => 'id',
    1 => 'rank',
    2 => 'del_status'
);

// getting total number records without any search
$sql = "SELECT COUNT(id) FROM ranks";
$query = $dbh->prepare($sql);
$query->execute();
$totalData = $query->fetchColumn();
$totalFiltered = $totalData;

$sql = "SELECT id, rank, del_status FROM ranks";

if(!empty($requestData['search']['value'])) {
    // when there is a search parameter then we have to modify total number filtered rows as per search result.
    $sql.= " WHERE (rank LIKE '%".$requestData['search']['value']."%' )";
    $sql.= " GROUP BY id ORDER BY del_status";
    $sql.= " LIMIT ".$requestData['start']." ,".$requestData['length'];
    $query = $dbh->prepare($sql);
    $query->execute();
    $totalFiltered = $query->rowCount();
} else {
    $sql.= " GROUP BY id ORDER BY del_status";
    $sql.= " LIMIT ".$requestData['start']." ,".$requestData['length'];
    $query = $dbh->prepare($sql);
    $query->execute();
}

$data = array();
$i = 1 + $requestData['start'];

// preparing an array
while($result = $query->fetch(PDO::FETCH_ASSOC)) {
    $db_status = ($result['del_status'] == 0) ? "<span class='badge badge-success'>Active</span>" : "<span class='badge badge-danger'>Deactivated</span>";
    $del_status = ($result['del_status'] == 0) ? "Deactivate" : "Activate";

    $nestedData = array();
	$nestedData[] = $i;
    $nestedData[] = $result['rank'];
    $nestedData[] = $db_status;
    $nestedData[] = "<div class=\"dropdown d-inline-block\">
                        <a class=\"dropdown-toggle arrow-none\" title=\"Options\" data-toggle=\"dropdown\" href=\"javascript: void(0);\" role=\"button\" aria-haspopup=\"false\" aria-expanded=\"false\"><i class=\"las la-ellipsis-v font-20\"></i></a>
                        <div class=\"dropdown-menu dropdown-menu-right\">
                            <a class=\"dropdown-item\" href=\"edit_rank.php?" . http_build_query(array("token"=>base64_encode($result['id']))) . "\">Edit</a>
                            <a class=\"dropdown-item\" href=\"javascript: void(0);\" onClick=\"toggle_rank_status(".$result['id'].")\">" . $del_status . "</a>
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