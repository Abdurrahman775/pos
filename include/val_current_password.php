<?php
session_start();
require_once('../config.php');
require_once('functions.php');
require_once('authentication.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];

    try {
        // Get the current user's password from database
        $sql = "SELECT password FROM admins WHERE username = :username AND is_active = 1";
        $query = $dbh->prepare($sql);
        $query->bindParam(':username', $admin, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $hashedPassword = $result['password'];

            // Verify the current password
            if (verifyHash($current_password, $hashedPassword)) {
                echo "true"; // Password is correct
            } else {
                echo "false"; // Password is incorrect
            }
        } else {
            echo "false"; // User not found
        }
    } catch (PDOException $e) {
        echo "false"; // System error
    }
} else {
    echo "false"; // Invalid request method
}
?>