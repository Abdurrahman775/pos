<?php

/**
 * Login Function
 * Handles admin/user authentication
 */

function login_admin($dbh, $username, $password, $ip_address = null)
{
	require_once(__DIR__ . '/session_manager.php');

	$msg = NULL;
	$username = strtolower(trim($username));
	
	// Get IP address if not provided
	if ($ip_address === null) {
		$ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
	}
	
	// Input validation - check length limits
	$username_check = validate_input_length($username, 100, 'Username');
	if (!$username_check['valid']) {
		log_login_attempt($dbh, $username, $ip_address, false);
		return $username_check['message'];
	}
	
	$password_check = validate_input_length($password, 255, 'Password');
	if (!$password_check['valid']) {
		log_login_attempt($dbh, $username, $ip_address, false);
		return $password_check['message'];
	}
	
	// Check rate limiting
	$rate_limit = check_login_rate_limit($dbh, $username, $ip_address);
	if (!$rate_limit['allowed']) {
		return $rate_limit['message'];
	}

	$sql = "select username, password, acct_activation from admins where username= :username and is_active= 1";
	$query = $dbh->prepare($sql);
	$query->bindParam(':username', $username, PDO::PARAM_STR);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_ASSOC);

	if ($result) {
		$hashedPassword = $result['password'];
		$activation_status = $result['acct_activation'];

	
			if (verifyHash($password, $hashedPassword)) {
				// Log successful attempt
				log_login_attempt($dbh, $username, $ip_address, true);
				
				initialize_session();

				// If there's an existing login session for a different user, clear it
				if (isset($_SESSION['pos_admin']) && $_SESSION['pos_admin'] !== $username) {
					clear_login_session();
					regenerate_session();
				}

				// Get user details and set session
				$sql_admin = "SELECT id, role_id FROM admins WHERE username = :username";
				$query_admin = $dbh->prepare($sql_admin);
				$query_admin->bindParam(':username', $username, PDO::PARAM_STR);
				$query_admin->execute();
				$admin_data = $query_admin->fetch(PDO::FETCH_ASSOC);

				$_SESSION['pos_admin'] = $username;
				$_SESSION['admin_id'] = $admin_data['id'];
				$_SESSION['role_id'] = $admin_data['role_id'];

				// Map role ID to role name for display
				$role_names = [
					1 => 'admin',
					2 => 'manager',
					3 => 'cashier'
				];
				$_SESSION['role_name'] = $role_names[$admin_data['role_id']] ?? 'cashier';

				// Clear temporary session vars if they exist
				clear_session_var('pos_admin_temp');
				regenerate_session();

				// Redirect to referrer or dashboard
				if (isset($_SESSION['login_referrer'])) {
					$referrer = $_SESSION['login_referrer'];
					unset($_SESSION['login_referrer']);
					go2($referrer);
				} else {
					go2("dashboard.php");
				}
			} else {
			// Log failed attempt
			log_login_attempt($dbh, $username, $ip_address, false);
			$msg = "Invalid Username or Password";
		}
	} else {
		// Log failed attempt
		log_login_attempt($dbh, $username, $ip_address, false);
		$msg = "Invalid Username or Password";
	}

	return $msg;
}
