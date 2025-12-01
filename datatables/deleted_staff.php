<?php
require(""../config.php");
require("../include/functions.php");
require("../include/admin_authentication.php");
require("../include/admin_constants.php");

// storing  request (ie, get/post) global array to a variable
$requestData = $_REQUEST;
$columns = array( 
// datatable column index  => database column name
    0 => 's.id',
    1 => 's.sname',
    2 => 's.fname',
    3 => 's.mname',
    4 => 's.dob',
    5 => 's.mobile1',
    6 => 's.staff_id',
    7 => 's.del_status',
    8 => 'sx.sex',
    9 => 'r.rank'
);

// getting total number records without any search
$sql = "SELECT COUNT(id) FROM staff WHERE del_status= 1";
$query = $dbh->prepare($sql);
$query->execute();
$totalData = $query->fetchColumn();
$totalFiltered = $totalData;

$sql = "SELECT s.id, s.sname, s.fname, s.mname, s.dob, s.mobile1, s.staff_id, s.del_status, sx.sex, r.rank FROM staff s JOIN sexes sx ON s.sex_id = sx.id JOIN ranks r ON s.rank_id = r.id WHERE s.del_status= 1";

if(!empty($requestData['search']['value'])) {
    // when there is a search parameter then we have to modify total number filtered rows as per search result.
    $sql.= " AND (s.sname LIKE '%".$requestData['search']['value']."%' ";
    $sql.= " OR s.fname LIKE '%".$requestData['search']['value']."%'";
    $sql.= " OR s.mname LIKE '%".$requestData['search']['value']."%'";
    $sql.= " OR s.mobile1 LIKE '%".$requestData['search']['value']."%'";
    $sql.= " OR s.staff_id LIKE '%".$requestData['search']['value']."%'";
    $sql.= " OR r.rank LIKE '%".$requestData['search']['value']."%')";
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
    $db_status = ($result['del_status'] == 0) ? "Deactivate" : "Activate";
    $nestedData = array();
	$nestedData[] = $i;
    $nestedData[] = $result['staff_id'];
    $nestedData[] = format_name($result['fname'], $result['mname'], $result['sname'], $name_format);
    $nestedData[] = substr($result['sex'], 0, 1);
    $nestedData[] = calculate_age($result['dob']);
    $nestedData[] = $result['mobile1'];
    $nestedData[] = $result['rank'];
    $nestedData[] = "<div class=\"dropdown d-inline-block\">
                        <a class=\"dropdown-toggle arrow-none\" title=\"Options\" data-toggle=\"dropdown\" href=\"javascript: void(0);\" role=\"button\" aria-haspopup=\"false\" aria-expanded=\"false\"><i class=\"las la-ellipsis-v font-20\"></i></a>
                        <div class=\"dropdown-menu dropdown-menu-right\">
                            <a class=\"dropdown-item\" href=\"view_staff.php?" . http_build_query(array("token"=>base64_encode($result['id']))) . "\">View</a>
                            <a class=\"dropdown-item\" href=\"edit_staff.php?" . http_build_query(array("token"=>base64_encode($result['id']))) . "\">Edit</a>
                            <a class=\"dropdown-item\" href=\"include/pdf_staff_data.php?" . http_build_query(array("token"=>base64_encode($result['id']))) . "\">Print</a>
                            <a class=\"dropdown-item\" href=\"javascript: void(0);\" onClick=\"toggle_staff_status(".$result['id'].")\">" . $db_status . "</a>
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