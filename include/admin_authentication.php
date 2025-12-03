<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// AUTHENTICATION TEMPORARILY DISABLED FOR TESTING
// Set a default session if not set - ensures sessions are initialized for development
if (!isset($_SESSION['pos_admin'])) {
	$_SESSION['pos_admin'] = 'support';
	$_SESSION['admin_id'] = 1;
	$_SESSION['role_id'] = 1; // ROLE_ADMINISTRATOR
	$_SESSION['role_name'] = 'admin';
}

// Ensure role_id is set if pos_admin is set but role_id isn't
if (isset($_SESSION['pos_admin']) && !isset($_SESSION['role_id'])) {
	$_SESSION['role_id'] = 1; // Default to admin
	$_SESSION['role_name'] = 'admin';
}

$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup)
{
	// For security, start by assuming the visitor is NOT authorized. 
	$isValid = False;

	// When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
	// Therefore, we know that a user is NOT logged in if that Session variable is blank. 
	if (!empty($UserName)) {
		// Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
		// Parse the strings into arrays. 
		$arrUsers = Explode(",", $strUsers);
		$arrGroups = Explode(",", $strGroups);

		if (in_array($UserName, $arrUsers)) {
			$isValid = true;
		}

		// Or, you may restrict access to only certain users based on their username. 
		if (in_array($UserGroup, $arrGroups)) {
			$isValid = true;
		}

		if (($strUsers == "") && true) {
			$isValid = true;
		}
	}
	return $isValid;
}

$MM_restrictGoTo = "index.php";

// AUTHENTICATION CHECK DISABLED - COMMENTED OUT FOR TESTING
/*
if(!((isset($_SESSION['pos_admin'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['pos_admin'], "")))) {   
	$MM_qsChar = "?";
	$MM_referrer = $_SERVER['PHP_SELF'];
	if(strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
	if(isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
	$MM_referrer .= "?" . $QUERY_STRING;
	$MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
	go2("$MM_restrictGoTo"); 
	exit;
}
*/
