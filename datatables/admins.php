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
    1 => 'username',
    2 => 'sname',
    3 => 'fname',
    4 => 'mname',
    5 => 'email',
    6 => 'acct_activation',
    7 => 'del_status'
);

// getting total number records without any search
$sql = "SELECT COUNT(id) FROM admins";
$query = $dbh->prepare($sql);
$query->execute();
$totalData = $query->fetchColumn();
$totalFiltered = $totalData;

$sql = "SELECT id, username, sname, fname, mname, email, acct_activation, del_status FROM admins";

if(!empty($requestData['search']['value'])) {
    // when there is a search parameter then we have to modify total number filtered rows as per search result.
    $sql.= " AND (username LIKE '%".$requestData['search']['value']."%' ";
    $sql.= " OR sname LIKE '%".$requestData['search']['value']."%'";
    $sql.= " OR fname LIKE '%".$requestData['search']['value']."%'";
    $sql.= " OR mname LIKE '%".$requestData['search']['value']."%'";
    $sql.= " OR email LIKE '%".$requestData['search']['value']."%')";
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
    $db_acct_status = ($result['acct_activation'] == 0) ? "<span class='badge badge-danger'>Not Activated</span>" : "<span class='badge badge-success'>Activated</span>";
    $db_status = ($result['del_status'] == 0) ? "<span class='badge badge-success'>Active</span>" : "<span class='badge badge-danger'>Deactivated</span>";
    $del_status = ($result['del_status'] == 0) ? "Deactivate" : "Activate";

    $nestedData = array();
	$nestedData[] = $i;
    $nestedData[] = $result['username'];
    $nestedData[] = format_name($result['fname'], $result['mname'], $result['sname'], $name_format);
    $nestedData[] = $result['email'];
    $nestedData[] = $db_acct_status;
    $nestedData[] = $db_status;
    $nestedData[] = "<div class=\"dropdown d-inline-block\">
                        <a class=\"dropdown-toggle arrow-none\" title=\"Options\" data-toggle=\"dropdown\" href=\"javascript: void(0);\" role=\"button\" aria-haspopup=\"false\" aria-expanded=\"false\"><i class=\"las la-ellipsis-v font-20\"></i></a>
                        <div class=\"dropdown-menu dropdown-menu-right\">
                            <a class=\"dropdown-item\" href=\"edit_admin.php?" . http_build_query(array("token"=>base64_encode($result['id']))) . "\">Edit</a>
                            <a class=\"dropdown-item\" href=\"javascript: void(0);\" onClick=\"toggle_admin_status(".$result['id'].")\">" . $del_status . "</a>
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