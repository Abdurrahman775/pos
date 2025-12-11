<?php
/**
 * Download Backup File
 * Simple helper to download generated backup files
 */

session_start();
require("../config.php");

// Check if user is logged in and is administrator
if (!isset($_SESSION['pos_admin'])) {
    die('Unauthorized access');
}

try {
    $sql = "SELECT role_id FROM admins WHERE username = :username";
    $query = $dbh->prepare($sql);
    $query->bindParam(':username', $_SESSION['pos_admin'], PDO::PARAM_STR);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || $user['role_id'] != 1) {
        die('Administrator access required');
    }
} catch (PDOException $e) {
    die('Authentication error');
}

// Get file path
$filepath = isset($_GET['file']) ? $_GET['file'] : '';

if (empty($filepath) || !file_exists($filepath)) {
    die('File not found');
}

// Security check - ensure file is in temp directory
if (strpos(realpath($filepath), sys_get_temp_dir()) !== 0) {
    die('Invalid file path');
}

// Get file info
$filename = basename($filepath);
$filesize = filesize($filepath);

// Set headers for download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . $filesize);
header('Cache-Control: must-revalidate');
header('Pragma: public');

// Output file
readfile($filepath);

// Clean up - delete temp file after download
unlink($filepath);

exit;
?>
