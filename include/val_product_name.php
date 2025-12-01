<?php
require('../config.php');

$product_name = $_POST['product_name'];

$sql = "select count(id) from products where name= :product_name";
$query = $dbh->prepare($sql);
$query->bindParam(':product_name', $product_name, PDO::PARAM_STR);
$query->execute();
$data = $query->fetchColumn();

if(($query == TRUE) && ($data != 0)) {
	$output = 'false';
} else {
	$output = 'true';
}
echo $output;
?>