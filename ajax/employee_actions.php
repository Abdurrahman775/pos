<?php
require("../config.php");
require("../include/functions.php");
require("../include/authentication.php");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'get_employee':
            $id = intval($_POST['id']);
            
            $sql = "SELECT * FROM admins WHERE id = :id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $employee = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($employee) {
                // Format full name
                $employee['full_name'] = trim($employee['fname'] . ' ' . ($employee['mname'] ?? '') . ' ' . $employee['sname']);
                
                // Role name
                $roles = [
                    1 => 'Administrator',
                    2 => 'Manager',
                    3 => 'Cashier'
                ];
                $employee['role_name'] = $roles[$employee['role_id']] ?? 'Unknown';
                
                echo json_encode([
                    'success' => true,
                    'data' => $employee
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Employee not found'
                ]);
            }
            break;
            
        case 'update_employee':
            // Require employees permission
            require_permission('employees');
            
            $id = intval($_POST['id']);
            $fname = trim($_POST['fname']);
            $mname = trim($_POST['mname'] ?? '');
            $sname = trim($_POST['sname']);
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $role_id = intval($_POST['role_id']);
            $is_active = intval($_POST['is_active']);
            
            // Validation
            if (empty($fname) || empty($sname) || empty($username) || empty($email)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'First name, surname, username, and email are required'
                ]);
                exit;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid email format'
                ]);
                exit;
            }
            
            // Check if username already exists (excluding current user)
            $check_sql = "SELECT id FROM admins WHERE username = :username AND id != :id";
            $check_query = $dbh->prepare($check_sql);
            $check_query->bindParam(':username', $username);
            $check_query->bindParam(':id', $id, PDO::PARAM_INT);
            $check_query->execute();
            
            if ($check_query->rowCount() > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Username already exists'
                ]);
                exit;
            }
            
            // Check if email already exists (excluding current user)
            $check_sql = "SELECT id FROM admins WHERE email = :email AND id != :id";
            $check_query = $dbh->prepare($check_sql);
            $check_query->bindParam(':email', $email);
            $check_query->bindParam(':id', $id, PDO::PARAM_INT);
            $check_query->execute();
            
            if ($check_query->rowCount() > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Email already exists'
                ]);
                exit;
            }
            
            // Update employee
            $updated_by = $_SESSION['username'] ?? $_SESSION['user_id'] ?? 'system';
            
            $update_sql = "UPDATE admins SET 
                          fname = :fname,
                          mname = :mname,
                          sname = :sname,
                          username = :username,
                          email = :email,
                          role_id = :role_id,
                          is_active = :is_active,
                          updated_by = :updated_by
                          WHERE id = :id";
            
            $update_query = $dbh->prepare($update_sql);
            $update_query->bindParam(':fname', $fname);
            $update_query->bindParam(':mname', $mname);
            $update_query->bindParam(':sname', $sname);
            $update_query->bindParam(':username', $username);
            $update_query->bindParam(':email', $email);
            $update_query->bindParam(':role_id', $role_id, PDO::PARAM_INT);
            $update_query->bindParam(':is_active', $is_active, PDO::PARAM_INT);
            $update_query->bindParam(':updated_by', $updated_by);
            $update_query->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($update_query->execute()) {
                // Log the action
                $description = "Updated employee: $username (ID: $id)";
                log_audit_trail($dbh, $_SESSION['user_id'], 'employee_update', 'admins', $id, $description);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Employee updated successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update employee'
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
            break;
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
}
