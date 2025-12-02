<?php
/**
 * Authentication and Session Management
 * Handles user authentication, session timeout, and activity tracking
 */

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include RBAC functions
require_once('rbac.php');

// Session timeout in minutes (default: 30 minutes as per requirements)
define('SESSION_TIMEOUT', 30);

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['pos_admin']);
}

/**
 * Check session timeout
 * Logout user if session has been inactive for more than SESSION_TIMEOUT minutes
 */
function check_session_timeout() {
    if (isset($_SESSION['last_activity'])) {
        $inactive_time = time() - $_SESSION['last_activity'];
        $timeout_seconds = SESSION_TIMEOUT * 60;
        
        if ($inactive_time > $timeout_seconds) {
            // Session has timed out
            session_unset();
            session_destroy();
            header("Location: index.php?timeout=1");
            exit();
        }
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
}

/**
 * Log user activity to audit log
 * 
 * @param PDO $dbh Database connection
 * @param string $action_type Type of action performed
 * @param string $description Description of the action
 */
function log_activity($dbh, $action_type, $description = '') {
    try {
        $username = isset($_SESSION['pos_admin']) ? $_SESSION['pos_admin'] : 'guest';
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $sql = "INSERT INTO auditlog (username, ActionType, Description, ip_address, user_agent, Timestamp) 
                VALUES (:username, :action_type, :description, :ip_address, :user_agent, NOW())";
        
        $query = $dbh->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':action_type', $action_type, PDO::PARAM_STR);
        $query->bindParam(':description', $description, PDO::PARAM_STR);
        $query->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
        $query->bindParam(':user_agent', $user_agent, PDO::PARAM_STR);
        $query->execute();
    } catch (PDOException $e) {
        // Silently fail - don't stop execution for logging errors
        error_log("Audit log error: " . $e->getMessage());
    }
}

// Check if user is logged in
if (!is_logged_in()) {
    $referrer = $_SERVER['PHP_SELF'] ?? '';
    if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) {
        $referrer .= "?" . $_SERVER['QUERY_STRING'];
    }
    
    $redirect_url = "index.php?accesscheck=" . urlencode($referrer);
    header("Location: $redirect_url");
    exit();
}

// Check session timeout
check_session_timeout();
?>
