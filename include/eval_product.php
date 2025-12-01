<?php
require('../config.php');

$product_name = $_POST['product_name'];
$product_id = $_POST['product_id'];

$sql = "SELECT COUNT(id) FROM products WHERE id != :id AND name= :product_name";
$query = $dbh->prepare($sql);
$query->bindParam(':product_name', $product_name, PDO::PARAM_STR);
$query->bindParam(':id', $product_id, PDO::PARAM_INT);
$query->execute();
$data = $query->fetchColumn();

if(($query == TRUE) && ($data != 0)) {
	$output = 'false';
} else {
	$output = 'true';
}
echo $output;
?>