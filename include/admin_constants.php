<?php
$admin = $_SESSION['pos_admin'];
$name_format = "fms";

if((!isset($_SESSION['pos_admin_basic'])) || (!is_array($_SESSION['pos_admin_basic']))) {
	$constantSQL = "SELECT id, username, sname, fname, mname FROM admins WHERE username= :username";
	$constantQuery = $dbh->prepare($constantSQL);
	$constantQuery->bindParam(':username', $admin, PDO::PARAM_STR);
	$constantQuery->execute();
	$constantResult = $constantQuery->fetch(PDO::FETCH_ASSOC);
	
	$_SESSION['pos_admin_basic'] = array();
	$fullname = !empty($constantResult['mname']) ? ($constantResult['fname'] . ' ' . $constantResult['mname'] . ' ' . $constantResult['sname']) : ($constantResult['fname'] . ' ' . $constantResult['sname']);
	$admin_fullname = $_SESSION['pos_admin_basic']['fullname'] = strtoupper($fullname);
} else {
	$admin_fullname = $_SESSION['pos_admin_basic']['fullname'];
}
?>