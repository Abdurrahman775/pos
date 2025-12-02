<?php
require("config.php");
require("include/functions.php");

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Log logout activity if user is logged in
if(isset($_SESSION['pos_admin']) && isset($dbh)) {
    try {
        require_once('include/authentication.php');
        log_activity($dbh, 'LOGOUT', 'User logged out');
    } catch (Exception $e) {
        // Continue with logout even if logging fails
    }
}

// Destroy session
session_unset();
session_destroy();

// Redirect to login page
header("Location: index.php");
exit();
?>
