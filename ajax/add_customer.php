<?php
session_start();
require("../config.php");
require("../include/functions.php");
require("../include/authentication.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = !empty(trim($_POST['email'])) ? trim($_POST['email']) : NULL;
    $address = !empty(trim($_POST['address'])) ? trim($_POST['address']) : NULL;

    // Validation
    if (empty($name) || empty($phone)) {
        echo json_encode(['status' => 'error', 'message' => 'Name and phone are required fields.']);
        exit;
    }

    try {
        $sql = "INSERT INTO customers (name, phone, email, address, created_at) 
                VALUES (:name, :phone, :email, :address, NOW())";
        $query = $dbh->prepare($sql);
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->bindParam(':phone', $phone, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':address', $address, PDO::PARAM_STR);

        if ($query->execute()) {
            // log_activity($dbh, 'ADD_CUSTOMER', "Added new customer: $name");
            echo json_encode([
                'status' => 'success',
                'message' => 'Customer added successfully!',
                'customer_name' => $name
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add customer.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
