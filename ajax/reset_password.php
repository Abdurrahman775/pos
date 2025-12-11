<?php
/**
 * AJAX Endpoint: Reset User Password
 * Resets a user's password to the default "user123"
 */

require("../config.php");
require("../include/functions.php");
require("../include/admin_authentication.php");
require("../logger.php");

header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get user ID from POST
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

if ($user_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

try {
    // Get user information
    $sql = "SELECT username, fname, sname, role_id FROM admins WHERE id = :user_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }
    
    // Get logged-in user's role
    $logged_in_role = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 0;
    
    // Check permissions:
    // Administrator (role_id = 1): can reset Manager (2) and Cashier (3) passwords
    // Manager (role_id = 2): can reset Cashier (3) passwords only
    // Cashier (role_id = 3): cannot reset any passwords
    
    $can_reset = false;
    
    if ($logged_in_role == 1) {
        // Administrator can reset Manager and Cashier
        if ($user['role_id'] == 2 || $user['role_id'] == 3) {
            $can_reset = true;
        }
    } elseif ($logged_in_role == 2) {
        // Manager can reset Cashier only
        if ($user['role_id'] == 3) {
            $can_reset = true;
        }
    }
    
    if (!$can_reset) {
        echo json_encode(['success' => false, 'message' => 'You do not have permission to reset this user\'s password']);
        exit;
    }
    
    // Prevent resetting super admin account (extra security)
    if ($user['username'] === 'admin') {
        echo json_encode(['success' => false, 'message' => 'Cannot reset super admin password']);
        exit;
    }
    
    // Generate hash for default password "user123"
    $default_password = "user123";
    $hashed_password = generateHash($default_password);
    
    // Update password in database
    $sql = "UPDATE admins SET password = :password WHERE id = :user_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $query->execute();
    
    if ($query->rowCount() > 0) {
        // Log the activity
        $fullname = $user['fname'] . ' ' . $user['sname'];
        log_activity($dbh, 'PASSWORD_RESET', "Reset password for user: {$user['username']} ($fullname)");
        
        echo json_encode([
            'success' => true, 
            'message' => "Password reset successfully for {$user['username']}. New password: user123"
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to reset password']);
    }
    
} catch (PDOException $e) {
    error_log("Password reset error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
