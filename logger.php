<?php
/**
 * Centralized Logger System
 * Handles all audit logging and retrieval for the POS system
 */

require_once("config.php");

/**
 * Write log entry to audit log
 * 
 * @param PDO $dbh Database connection
 * @param string $action_type Type of action (e.g., LOGIN, CREATE, UPDATE, DELETE)
 * @param string $description Description of the action
 * @return bool Success status
 */
function write_log($dbh, $action_type, $description = '') {
    try {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $username = isset($_SESSION['pos_admin']) ? $_SESSION['pos_admin'] : 'guest';
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $user_agent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);

        $sql = "INSERT INTO auditlog (username, ActionType, Description, ip_address, user_agent, Timestamp) 
                VALUES (:username, :action_type, :description, :ip_address, :user_agent, NOW())";

        $query = $dbh->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':action_type', $action_type, PDO::PARAM_STR);
        $query->bindParam(':description', $description, PDO::PARAM_STR);
        $query->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
        $query->bindParam(':user_agent', $user_agent, PDO::PARAM_STR);
        
        return $query->execute();
    } catch (PDOException $e) {
        // Silently fail - don't stop execution for logging errors
        error_log("Logger error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get audit logs with optional filtering
 * 
 * @param PDO $dbh Database connection
 * @param array $filters Optional filters (start_date, end_date, action_type, search, limit, offset)
 * @return array Array of log entries
 */
function get_logs($dbh, $filters = []) {
    try {
        $sql = "SELECT al.id, al.username, al.ActionType, al.Description, al.Timestamp, al.ip_address,
                COALESCE(CONCAT(a.fname, ' ', a.sname), al.username) as user_name
                FROM auditlog al
                LEFT JOIN admins a ON al.username = a.username
                WHERE 1=1";
        
        $params = [];
        
        // Date range filter
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sql .= " AND DATE(al.Timestamp) BETWEEN :start_date AND :end_date";
            $params[':start_date'] = $filters['start_date'];
            $params[':end_date'] = $filters['end_date'];
        }
        
        // Action type filter
        if (!empty($filters['action_type'])) {
            $sql .= " AND al.ActionType = :action_type";
            $params[':action_type'] = $filters['action_type'];
        }
        
        // Search filter
        if (!empty($filters['search'])) {
            $sql .= " AND (al.username LIKE :search 
                      OR al.ActionType LIKE :search 
                      OR al.Description LIKE :search
                      OR a.fname LIKE :search
                      OR a.sname LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Order by timestamp descending
        $sql .= " ORDER BY al.Timestamp DESC";
        
        // Pagination
        $limit = isset($filters['limit']) ? intval($filters['limit']) : 50;
        $offset = isset($filters['offset']) ? intval($filters['offset']) : 0;
        $sql .= " LIMIT :limit OFFSET :offset";
        
        $query = $dbh->prepare($sql);
        
        // Bind parameters
        foreach ($params as $key => $value) {
            $query->bindValue($key, $value);
        }
        $query->bindValue(':limit', $limit, PDO::PARAM_INT);
        $query->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Logger retrieval error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get total count of logs matching filters
 * 
 * @param PDO $dbh Database connection
 * @param array $filters Optional filters
 * @return int Total count
 */
function get_logs_count($dbh, $filters = []) {
    try {
        $sql = "SELECT COUNT(*) 
                FROM auditlog al
                LEFT JOIN admins a ON al.username = a.username
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $sql .= " AND DATE(al.Timestamp) BETWEEN :start_date AND :end_date";
            $params[':start_date'] = $filters['start_date'];
            $params[':end_date'] = $filters['end_date'];
        }
        
        if (!empty($filters['action_type'])) {
            $sql .= " AND al.ActionType = :action_type";
            $params[':action_type'] = $filters['action_type'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (al.username LIKE :search 
                      OR al.ActionType LIKE :search 
                      OR al.Description LIKE :search
                      OR a.fname LIKE :search
                      OR a.sname LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        $query = $dbh->prepare($sql);
        foreach ($params as $key => $value) {
            $query->bindValue($key, $value);
        }
        $query->execute();
        
        return intval($query->fetchColumn());
        
    } catch (PDOException $e) {
        error_log("Logger count error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Backward compatibility alias for write_log
 */
function log_activity($dbh, $action_type, $description = '') {
    return write_log($dbh, $action_type, $description);
}

?>
