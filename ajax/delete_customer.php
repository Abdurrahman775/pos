<?php
/**
 * AJAX Delete Customer (Soft Delete)
 */
require("../config.php");
require("../include/functions.php");
require("../include/admin_authentication.php");

// Set content type to JSON
header('Content-Type: application/json');

// Check permission
if (!isset($_SESSION['role_id']) || !has_permission($_SESSION['role_id'], 'customers')) {
    echo json_encode(['success' => false, 'message' => 'Permission denied']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id > 0) {
        try {
            // Soft delete: Set is_active to 0
            $sql = "UPDATE customers SET is_active = 0 WHERE customer_id = :id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($query->execute()) {
                log_activity($dbh, 'DELETE_CUSTOMER', "Soft deleted customer ID: $id");
                echo json_encode(['success' => true, 'message' => 'Customer deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete customer']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid customer ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
