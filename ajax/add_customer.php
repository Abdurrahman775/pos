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
        // Check for duplicate phone
        $check_sql = "SELECT COUNT(*) FROM customers WHERE phone = :phone";
        $check_query = $dbh->prepare($check_sql);
        $check_query->bindParam(':phone', $phone, PDO::PARAM_STR);
        $check_query->execute();
        if ($check_query->fetchColumn() > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Phone number already exists. Please use a different phone number.']);
            exit;
        }
        
        // Check for duplicate email (only if provided)
        if (!empty($email)) {
            $check_sql = "SELECT COUNT(*) FROM customers WHERE email = :email";
            $check_query = $dbh->prepare($check_sql);
            $check_query->bindParam(':email', $email, PDO::PARAM_STR);
            $check_query->execute();
            if ($check_query->fetchColumn() > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Email already exists. Please use a different email.']);
                exit;
            }
        }

        // Insert customer
        $sql = "INSERT INTO customers (name, phone, email, address, created_at) 
                VALUES (:name, :phone, :email, :address, NOW())";
        $query = $dbh->prepare($sql);
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->bindParam(':phone', $phone, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':address', $address, PDO::PARAM_STR);

        if ($query->execute()) {
            log_activity($dbh, 'ADD_CUSTOMER', "Added new customer: $name");
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
