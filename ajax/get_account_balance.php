<?php
require("../config.php");
require("../include/functions.php");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

try {
    // Get filter parameters
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
    $cashier_id = isset($_GET['cashier_id']) ? intval($_GET['cashier_id']) : 0;
    
    // Build base query
    $where_clause = "WHERE DATE(created_at) BETWEEN :start_date AND :end_date";
    $params = [
        ':start_date' => $start_date,
        ':end_date' => $end_date
    ];
    
    // Add cashier filter if specified
    if ($cashier_id > 0) {
        $where_clause .= " AND user_id = :user_id";
        $params[':user_id'] = $cashier_id;
    }
    
    // Get CASH balance and count
    $cash_sql = "SELECT 
                    COALESCE(SUM(total_amount), 0) as total,
                    COUNT(*) as count
                FROM transactions 
                $where_clause AND payment_method = 'CASH'";
    $cash_query = $dbh->prepare($cash_sql);
    foreach ($params as $key => $value) {
        $cash_query->bindValue($key, $value);
    }
    $cash_query->execute();
    $cash_result = $cash_query->fetch(PDO::FETCH_ASSOC);
    
    // Get POS balance and count
    $pos_sql = "SELECT 
                    COALESCE(SUM(total_amount), 0) as total,
                    COUNT(*) as count
                FROM transactions 
                $where_clause AND payment_method = 'POS'";
    $pos_query = $dbh->prepare($pos_sql);
    foreach ($params as $key => $value) {
        $pos_query->bindValue($key, $value);
    }
    $pos_query->execute();
    $pos_result = $pos_query->fetch(PDO::FETCH_ASSOC);
    
    // Get MIXED payments and extract individual amounts
    $mixed_sql = "SELECT notes FROM transactions $where_clause AND payment_method = 'MIXED'";
    $mixed_query = $dbh->prepare($mixed_sql);
    foreach ($params as $key => $value) {
        $mixed_query->bindValue($key, $value);
    }
    $mixed_query->execute();
    $mixed_results = $mixed_query->fetchAll(PDO::FETCH_ASSOC);
    
    $mixed_cash_total = 0;
    $mixed_pos_total = 0;
    $mixed_count = count($mixed_results);
    
    foreach ($mixed_results as $row) {
        $notes = $row['notes'];
        
        // Extract POS Amount from notes
        if (preg_match('/POS Amount:\s*([\d,]+\.?\d*)/', $notes, $matches)) {
            $mixed_pos_total += floatval(str_replace(',', '', $matches[1]));
        }
        
        // Extract Cash Amount from notes
        if (preg_match('/Cash Amount:\s*([\d,]+\.?\d*)/', $notes, $matches)) {
            $mixed_cash_total += floatval(str_replace(',', '', $matches[1]));
        }
    }
    
    // Get total count
    $total_sql = "SELECT COUNT(*) as count FROM transactions $where_clause";
    $total_query = $dbh->prepare($total_sql);
    foreach ($params as $key => $value) {
        $total_query->bindValue($key, $value);
    }
    $total_query->execute();
    $total_result = $total_query->fetch(PDO::FETCH_ASSOC);
    
    // Calculate totals including mixed payments
    $cash_balance = floatval($cash_result['total']) + $mixed_cash_total;
    $cash_count = intval($cash_result['count']) + $mixed_count;
    
    $pos_balance = floatval($pos_result['total']) + $mixed_pos_total;
    $pos_count = intval($pos_result['count']) + $mixed_count;
    
    $total_balance = $cash_balance + $pos_balance;
    $total_count = intval($total_result['count']);
    
    // Return response
    echo json_encode([
        'status' => 'success',
        'data' => [
            'cash_balance' => number_format($cash_balance, 2, '.', ''),
            'cash_count' => $cash_count,
            'pos_balance' => number_format($pos_balance, 2, '.', ''),
            'pos_count' => $pos_count,
            'total_balance' => number_format($total_balance, 2, '.', ''),
            'total_count' => $total_count
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
