<?php
error_reporting(0);
error_reporting(E_ALL & ~E_NOTICE);
//ini_set('display_errors', 1);

$con_hostname = "localhost";
$con_database = "onlinepos";
$con_username = "root";
$con_password = "";

// "L" for live connection and "M" for maintenance mode
$con_mode = strtoupper('l');

if($con_mode == strtoupper('L')) {
	// MySQL Database
	try {
		// Connecting to database
		$dbh = new PDO("mysql:host=$con_hostname; dbname=$con_database", $con_username, $con_password);
		// set the PDO error mode to exception
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(PDOException $e) {
		$location = "offline/index.php";
		header("Location: {$location}");
		exit();
	}
} else if($con_mode == strtoupper('M')) {
	$location = "offline/index.php";
	header("Location: {$location}");
	exit();
}
?>