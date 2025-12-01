<?php
function login_admin($dbh, $username, $password) {
	$msg = NULL;
	$username = strtolower($username);

    $sql = "select username, password, acct_activation from admins where username= :username and is_active= 1";
	$query = $dbh->prepare($sql);
	$query->bindParam(':username', $username, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_ASSOC);
	
	if($result) {
		$hashedPassword = $result['password'];
		$activation_status = $result['acct_activation'];
		
		if(verifyHash($password, $hashedPassword)) {
            if(session_status() === PHP_SESSION_NONE) {
                session_start();
            }

			if($activation_status == 0) {
				if(!isset($_SESSION['pos_admin_temp'])) {
					// Redirect to form
					$_SESSION['pos_admin_temp'] = $username;
					go2("activation.php");
				} else {
					$msg = "Another session is active. End it and try again";
				}
			} else {
				if(!isset($_SESSION['pos_admin'])) {
					// Redirect to form
					$_SESSION['pos_admin'] = $username;
					go2("dashboard.php");
				} else {
					$msg = "Another session is active. End it and try again";
				}
			}
		} else {
			$msg = "Invalid Username or Password";
		}
	} else {
		$msg = "Invalid Username or Password";
	}

	return $msg;
}
?>