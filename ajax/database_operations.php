<?php
/**
 * Database Operations Handler
 * Handles backup, restore, and reset operations for the database
 * Administrator only
 */

session_start();
require("../config.php");
require("../include/functions.php");

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['pos_admin'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Check if user has administrator permissions
try {
    $sql = "SELECT role_id FROM admins WHERE username = :username";
    $query = $dbh->prepare($sql);
    $query->bindParam(':username', $_SESSION['pos_admin'], PDO::PARAM_STR);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || $user['role_id'] != 1) {
        echo json_encode(['status' => 'error', 'message' => 'Administrator access required']);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Authentication error']);
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

switch ($action) {
    case 'backup':
        backupDatabase($dbh);
        break;
    
    case 'restore':
        restoreDatabase($dbh);
        break;
    
    case 'reset':
        resetData($dbh);
        break;
    
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

/**
 * Backup Database
 */
function backupDatabase($dbh) {
    try {
        // Get database connection details from config
        global $con_database;
        
        // Create backup filename with timestamp
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "pos_backup_{$timestamp}.sql";
        $filepath = sys_get_temp_dir() . '/' . $filename;
        
        // Get all tables
        $tables = array();
        $result = $dbh->query("SHOW TABLES");
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }
        
        // Start building SQL dump
        $sql_dump = "-- Database Backup\n";
        $sql_dump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql_dump .= "-- Database: " . $con_database . "\n\n";
        $sql_dump .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $sql_dump .= "SET time_zone = \"+00:00\";\n\n";
        
        // Loop through each table
        foreach ($tables as $table) {
            // Get table structure
            $sql_dump .= "-- --------------------------------------------------------\n";
            $sql_dump .= "-- Table structure for `{$table}`\n";
            $sql_dump .= "-- --------------------------------------------------------\n\n";
            $sql_dump .= "DROP TABLE IF EXISTS `{$table}`;\n";
            
            $create_table = $dbh->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_ASSOC);
            $sql_dump .= $create_table['Create Table'] . ";\n\n";
            
            // Get table data
            $rows = $dbh->query("SELECT * FROM `{$table}`");
            $num_rows = $rows->rowCount();
            
            if ($num_rows > 0) {
                $sql_dump .= "-- Dumping data for table `{$table}`\n\n";
                
                while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
                    $sql_dump .= "INSERT INTO `{$table}` VALUES (";
                    $values = array();
                    foreach ($row as $value) {
                        if ($value === null) {
                            $values[] = 'NULL';
                        } else {
                            $values[] = "'" . addslashes($value) . "'";
                        }
                    }
                    $sql_dump .= implode(', ', $values);
                    $sql_dump .= ");\n";
                }
                $sql_dump .= "\n";
            }
        }
        
        // Write to file
        file_put_contents($filepath, $sql_dump);
        
        // Log activity (optional)
        if (function_exists('log_activity')) {
            log_activity($dbh, 'BACKUP', 'Created database backup: ' . $filename);
        }
        
        // Return file path for download
        echo json_encode([
            'status' => 'success',
            'message' => 'Backup created successfully',
            'filename' => $filename,
            'filepath' => $filepath,
            'size' => filesize($filepath)
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Backup failed: ' . $e->getMessage()
        ]);
    }
}

/**
 * Restore Database from Backup
 */
function restoreDatabase($dbh) {
    try {
        // Check if file was uploaded
        if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('No file uploaded or upload error');
        }
        
        $file = $_FILES['backup_file'];
        
        // Validate file type
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($file_ext !== 'sql') {
            throw new Exception('Invalid file type. Only SQL files are allowed');
        }
        
        // Validate file size (max 50MB)
        $max_size = 50 * 1024 * 1024; // 50MB in bytes
        if ($file['size'] > $max_size) {
            throw new Exception('File size exceeds 50MB limit');
        }
        
        // Read SQL file
        $sql_content = file_get_contents($file['tmp_name']);
        
        if (empty($sql_content)) {
            throw new Exception('Backup file is empty');
        }
        
        // Log for debugging
        error_log("RESTORE DEBUG: Starting restore from file: " . $file['name'] . " (" . strlen($sql_content) . " bytes)");
        
        // Begin transaction for safety
        $dbh->beginTransaction();
        
        // Disable foreign key checks
        $dbh->exec("SET FOREIGN_KEY_CHECKS = 0");
        $dbh->exec("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'");
        $dbh->exec("SET AUTOCOMMIT = 0");
        
        // Split SQL into individual statements
        // Remove comments and split by semicolon
        $sql_content = preg_replace('/--[^\n]*\n/', '', $sql_content); // Remove single-line comments
        $sql_content = preg_replace('/\/\*.*?\*\//s', '', $sql_content); // Remove multi-line comments
        
        $queries = explode(';', $sql_content);
        $executed = 0;
        $errors = [];
        
        // Execute each query
        foreach ($queries as $query) {
            $query = trim($query);
            
            // Skip empty queries
            if (empty($query)) {
                continue;
            }
            
            try {
                $dbh->exec($query);
                $executed++;
                
                // Log every 50 queries
                if ($executed % 50 === 0) {
                    error_log("RESTORE DEBUG: Executed $executed queries so far...");
                }
            } catch (PDOException $e) {
                $errors[] = "Query error: " . substr($query, 0, 100) . "... - " . $e->getMessage();
                // Continue on error (some statements might fail, that's ok for DROP/CREATE scenarios)
                error_log("RESTORE WARNING: " . $e->getMessage());
                continue;
            }
        }
        
        // Commit transaction
        $dbh->commit();
        
        // Re-enable settings
        $dbh->exec("SET FOREIGN_KEY_CHECKS = 1");
        $dbh->exec("SET AUTOCOMMIT = 1");
        
        error_log("RESTORE DEBUG: Successfully executed $executed queries");
        
        // Log activity (optional)
        if (function_exists('log_activity')) {
            log_activity($dbh, 'RESTORE', 'Restored database from backup: ' . $file['name'] . " ($executed queries executed)");
        }
        
        $message = "Database restored successfully! Executed $executed SQL statements.";
        if (count($errors) > 0 && count($errors) < 10) {
            $message .= " (" . count($errors) . " non-critical errors)";
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'queries_executed' => $executed,
            'errors' => count($errors)
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction
        try {
            if ($dbh->inTransaction()) {
                $dbh->rollBack();
            }
        } catch (PDOException $ex) {
            // Ignore rollback errors
        }
        
        // Re-enable foreign key checks in case of error
        try {
            $dbh->exec("SET FOREIGN_KEY_CHECKS = 1");
            $dbh->exec("SET AUTOCOMMIT = 1");
        } catch (Exception $ex) {
            // Ignore
        }
        
        error_log("RESTORE ERROR: " . $e->getMessage());
        
        echo json_encode([
            'status' => 'error',
            'message' => 'Restore failed: ' . $e->getMessage()
        ]);
    }
}

/**
 * Reset All Data (except admins, categories, and system_settings)
 */
function resetData($dbh) {
    try {
        error_log("RESET DEBUG: Starting reset operation");
        
        // Begin transaction
        try {
            $dbh->beginTransaction();
            error_log("RESET DEBUG: Transaction started successfully");
        } catch (PDOException $tx_err) {
            error_log("RESET ERROR: Failed to start transaction - " . $tx_err->getMessage());
            throw $tx_err;
        }
        
        // Tables to preserve
        $preserve_tables = ['admins', 'categories', 'system_settings'];
        
        // Get all tables
        $tables = array();
        $result = $dbh->query("SHOW TABLES");
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $table = $row[0];
            // Skip preserved tables
            if (!in_array($table, $preserve_tables)) {
                $tables[] = $table;
            }
        }
        
        error_log("RESET DEBUG: Found " . count($tables) . " tables to clear");
        
        // Disable foreign key checks
        $dbh->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // Delete data from each table
        $cleared_tables = array();
        foreach ($tables as $table) {
            try {
                $dbh->exec("TRUNCATE TABLE `{$table}`");
                $cleared_tables[] = $table;
            } catch (PDOException $e) {
                // If TRUNCATE fails, try DELETE
                try {
                    $dbh->exec("DELETE FROM `{$table}`");
                    $cleared_tables[] = $table;
                } catch (PDOException $ex) {
                    // Continue on error
                    continue;
                }
            }
        }
        
        error_log("RESET DEBUG: Cleared " . count($cleared_tables) . " tables");
        
        // Re-enable foreign key checks
        $dbh->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        // Commit transaction
        try {
            $dbh->commit();
            error_log("RESET DEBUG: Transaction committed successfully");
        } catch (PDOException $commit_err) {
            error_log("RESET ERROR: Failed to commit - " . $commit_err->getMessage());
            throw $commit_err;
        }
        
        // Log activity (optional - don't let logging errors break the success)
        try {
            if (function_exists('log_activity')) {
                log_activity($dbh, 'RESET', 'Reset all data (preserved: admins, categories, system_settings). Cleared ' . count($cleared_tables) . ' tables');
            }
        } catch (Exception $log_ex) {
            // Ignore logging errors - reset was successful
            error_log("Reset successful but logging failed: " . $log_ex->getMessage());
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'All data has been reset successfully',
            'tables_cleared' => count($cleared_tables),
            'tables_preserved' => count($preserve_tables)
        ]);
        
    } catch (Exception $e) {
        // Log detailed error for debugging
        error_log("RESET ERROR: " . $e->getMessage());
        error_log("RESET ERROR TRACE: " . $e->getTraceAsString());
        
        // Try to rollback if transaction is active
        if ($dbh->inTransaction()) {
            try {
                $dbh->rollBack();
            } catch (PDOException $ex) {
                error_log("RESET ROLLBACK ERROR: " . $ex->getMessage());
            }
        }
        
        // Re-enable foreign key checks in case of error
        try {
            $dbh->exec("SET FOREIGN_KEY_CHECKS = 1");
        } catch (Exception $ex) {
            // Ignore
        }
        
        echo json_encode([
            'status' => 'error',
            'message' => 'Reset failed: ' . $e->getMessage()
        ]);
    }
}
?>
