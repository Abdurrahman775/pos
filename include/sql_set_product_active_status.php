<?php
require('../config.php');

$product_id = $_REQUEST['token'];

$updateSQL = "UPDATE products SET is_active= NOT is_active WHERE id= :id";
$updateQuery = $dbh->prepare($updateSQL);
$updateQuery->bindParam(':id', $product_id, PDO::PARAM_INT);
$updateQuery->execute();
?>