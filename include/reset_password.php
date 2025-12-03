<?php
session_start();
require_once('../config.php');
require_once('functions.php');
require_once('functions_messaging.php');

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = strtolower(trim($_POST['token']));
    
    try {
        // Check if username exists and get email
        $sql = "SELECT email, fname, mname, sname FROM admins WHERE username = :username AND is_active = 1";
        $query = $dbh->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if($result) {
            $email = $result['email'];
            $fullname = trim($result['fname'] . ' ' . $result['mname'] . ' ' . $result['sname']);
            
            if(empty($email)) {
                echo "1"; // No email configured
                exit();
            }
            
            // Generate new random password
            $new_password = randomPassword();
            $hashed_password = generateHash($new_password);
            
            // Update password in database
            $updateSQL = "UPDATE admins SET password = :password WHERE username = :username";
            $updateQuery = $dbh->prepare($updateSQL);
            $updateQuery->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $updateQuery->bindParam(':username', $username, PDO::PARAM_STR);
            $updateQuery->execute();
            
            if($updateQuery) {
                // Send email with new password
                $subject = "Password Reset - POS System";
                $message = "Dear $fullname,\n\n";
                $message .= "Your password has been reset successfully.\n\n";
                $message .= "Your new login credentials are:\n";
                $message .= "Username: $username\n";
                $message .= "Password: $new_password\n\n";
                $message .= "Please login and change your password immediately.\n\n";
                $message .= "Regards,\n";
                $message .= "POS System Administrator";
                
                // Send email
                $mail_result = send_email($email, $subject, $message);
                
                if($mail_result === true) {
                    echo $email; // Return email address
                } else {
                    echo "0"; // Error sending email
                }
            } else {
                echo "0"; // Error updating password
            }
        } else {
            echo "0"; // Username not found
        }
    } catch(PDOException $e) {
        echo "0"; // System error
    }
} else {
    echo "0"; // Invalid request method
}
?>
