<?php
// Current date & time
$now = date('Y-m-d h:i:s');

// Current date
$cdate = date('Y-m-d');

// Get Currency
function get_currency($dbh)
{
	try {
		$sql = "SELECT setting_value FROM system_settings WHERE setting_key = 'currency_code' LIMIT 1";
		$query = $dbh->prepare($sql);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_ASSOC);
		return $result ? $result['setting_value'] : 'NGN';
	} catch (PDOException $e) {
		return 'NGN'; // Fallback to NGN if query fails
	}
}

// Validation Patterns
$username_pattern = "/^[a-z]{1}[_0-9a-z]{5,}$/i";
$password_pattern = "/^[a-zA-Z0-9]{5,}/"; // Min 5 xters and a-z A-Z and 0-9 only
$varchar_pattern = "/^[-\.\,\(\)\/\'\w\s\\\\]*$/i";
$number_pattern = "/^[0-9\.]*$/i";
$mobile_pattern = "/^((234)[1-9]{1}[0-9]{9}|(0){1}[1-9]{1}[0-9]{9})$/";

// College Name
function profile_name($dbh)
{
	return $data = "POS System";
}

//Validation of Existing Names in DB
// Product Name Validation
function val_product_name($dbh, $product_name)
{
	$sql = "select count(id) from products where name= :product_name and is_active=1";
	$query = $dbh->prepare($sql);
	$query->bindParam(':product_name', $product_name, PDO::PARAM_STR);
	$query->execute();
	$data = $query->fetchColumn();
	return $data;
}


// Calculate Age
function calculate_age($dob)
{
	$dob = strtotime($dob);
	$y = date('Y', $dob);
	$m = date('m') - date('m', $dob);

	if ($m < 0) {
		$y++;
	} elseif ($m == 0 && date('d') - date('d', $dob) < 0) {
		$y++;
	}
	return date('Y') - $y;
}

function format_name($fname, $mname, $sname, $format)
{
	switch ($format) {
		case "sfm": {
				$fullname = !empty($mname) ? ($sname . ', ' . $fname . ' ' . $mname) : ($sname . ', ' . $fname);
				break;
			}
		case "fms": {
				$fullname = !empty($mname) ? ($fname . ' ' . $mname . ' ' . $sname) : ($fname . ' ' . $sname);
				break;
			}
		default:
			$fullname = !empty($mname) ? ($fname . ' ' . $mname . ' ' . $sname) : ($fname . ' ' . $sname);
	}
	return $fullname;
}

// Bcrypt Hashing
function generateHash($password)
{
	if (defined("CRYPT_BLOWFISH") && CRYPT_BLOWFISH) {
		$salt = "$2y$11$" . substr(md5(uniqid(rand(), true)), 0, 22);
		return crypt($password, $salt);
	}
}

// Bcrypt Hashing verification
function verifyHash($password, $hashedPassword)
{
	// Use password_verify for bcrypt hashes (PHP 5.5+)
	return password_verify($password, $hashedPassword);
}

// Six (6) character random password generator
function randomPassword()
{
	$alphabet = "AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789";
	$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	for ($i = 0; $i < 6; $i++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}
	return implode($pass); //turn the array into a string
}

// Redirect function
function go2($location = NULL)
{
	if ($location != NULL) {
		header("Location: {$location}");
		exit;
	}
}

function get_admin_name($dbh, $username)
{
	$sql = "SELECT fname, mname, sname FROM admins WHERE username= :username";
	$query = $dbh->prepare($sql);
	$query->bindParam(':username', $username, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_ASSOC);
	return $result['fname'] . " " . $result['mname'] . " " . $result['sname'];
}

// Get Product Name
function convert_product_id($dbh, $product_id)
{
	$sql = "select name from products where id= :product_id";
	$query = $dbh->prepare($sql);
	$query->bindParam(':product_id', $product_id, PDO::PARAM_INT);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_ASSOC);
	$data = $result['name'];
	return $data;
}

// Get Product Price
function get_product_price($dbh, $product_id)
{
	$sql = "select selling_price from products where id= :product_id";
	$query = $dbh->prepare($sql);
	$query->bindParam(':product_id', $product_id, PDO::PARAM_INT);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_ASSOC);
	$data = $result['selling_price'];
	return $data;
}

// Get Product Quantity
function get_product_quantity($dbh, $product_id)
{
	$sql = "select qty_in_stock from products where id= :product_id";
	$query = $dbh->prepare($sql);
	$query->bindParam(':product_id', $product_id, PDO::PARAM_INT);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_ASSOC);
	$data = $result['qty_in_stock'];
	return $data;
}
?>
