<?php
require("config.php");
require("include/functions.php");
require_once("include/session_manager.php");

initialize_session();

// Log logout activity if user is logged in
if (isset($_SESSION['pos_admin']) && isset($dbh)) {
    try {
        require_once('include/authentication.php');
        log_activity($dbh, 'LOGOUT', 'User logged out');
    } catch (Exception $e) {
        // Continue with logout even if logging fails
    }
}

// Completely destroy session using our session manager
destroy_session();

// Redirect to login page with cache busting to prevent browser caching issues
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
header("Location: index.php?logout=1&t=" . time());
exit();
