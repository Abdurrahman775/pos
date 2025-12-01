<?php
require('../config.php');

$username = $_POST['username'];

$sql = "select count(id) from admins where username= :username";
$query = $dbh->prepare($sql);
$query->bindParam(':username', $username, PDO::PARAM_STR);
$query->execute();
$data = $query->fetchColumn();

if(($query == TRUE) && ($data != 0)) {
	$output = 'false';
} else {
	$output = 'true';
}
echo $output;
?>