<?php
require("../config.php");
require("../include/functions.php");
require("../include/admin_authentication.php");
require_once("../include/pos_functions.php");

header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$period = isset($_GET['period']) ? $_GET['period'] : 'month';

$response = ['status' => 'error', 'message' => 'Invalid action'];

if ($action == 'financials') {
    // Get aggregated financials
    $sql = "SELECT 
                SUM(ti.line_total) as revenue,
                SUM(ti.quantity * p.cost_price) as cogs
            FROM transactions t 
            JOIN transaction_items ti ON t.transaction_id = ti.transaction_id 
            JOIN products p ON ti.product_id = p.id 
            WHERE DATE(t.transaction_date) BETWEEN :start_date AND :end_date 
            AND t.status = 'completed'";
    
    $query = $dbh->prepare($sql);
    $query->bindParam(':start_date', $start_date);
    $query->bindParam(':end_date', $end_date);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);

    $revenue = $result['revenue'] ? $result['revenue'] : 0;
    $cogs = $result['cogs'] ? $result['cogs'] : 0;
    $profit = $revenue - $cogs;

    $response = [
        'status' => 'success',
        'data' => [
            'revenue' => $revenue,
            'cogs' => $cogs,
            'profit' => $profit
        ]
    ];

} elseif ($action == 'sales_trend') {
    // Get sales trend (daily)
    $sql = "SELECT 
                DATE(transaction_date) as date, 
                SUM(total_amount) as total 
            FROM transactions 
            WHERE DATE(transaction_date) BETWEEN :start_date AND :end_date 
            AND status = 'completed'
            GROUP BY DATE(transaction_date) 
            ORDER BY DATE(transaction_date)";
    
    $query = $dbh->prepare($sql);
    $query->bindParam(':start_date', $start_date);
    $query->bindParam(':end_date', $end_date);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $data = [];

    foreach ($results as $row) {
        $labels[] = date('d M', strtotime($row['date']));
        $data[] = $row['total'];
    }

    $response = [
        'status' => 'success',
        'data' => [
            'labels' => $labels,
            'values' => $data
        ]
    ];

} elseif ($action == 'top_products') {
    // Get top 5 products
    $top_products = get_top_selling_products($dbh, 5, $period);
    
    $labels = [];
    $data = [];

    foreach ($top_products as $p) {
        $labels[] = $p['name'];
        $data[] = $p['total_sold'];
    }

    $response = [
        'status' => 'success',
        'data' => [
            'labels' => $labels,
            'values' => $data
        ]
    ];
} elseif ($action == 'inventory_summary') {
    // Get stock value by category
    $sql = "SELECT c.name as category, SUM(p.qty_in_stock * p.selling_price) as value 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.is_active = 1 
            GROUP BY c.name";
    $query = $dbh->prepare($sql);
    $query->execute();
    $cat_results = $query->fetchAll(PDO::FETCH_ASSOC);

    $cat_labels = [];
    $cat_values = [];
    foreach ($cat_results as $row) {
        $cat_labels[] = $row['category'] ? $row['category'] : 'Uncategorized';
        $cat_values[] = $row['value'];
    }

    // Get stock status
    $low_stock = get_low_stock_count($dbh);
    $total_products = $dbh->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
    $in_stock = $total_products - $low_stock;

    $response = [
        'status' => 'success',
        'data' => [
            'category_labels' => $cat_labels,
            'category_values' => $cat_values,
            'status_labels' => ['In Stock', 'Low Stock'],
            'status_values' => [$in_stock, $low_stock]
        ]
    ];

} elseif ($action == 'sales_breakdown') {
    // Get sales by payment method
    $sql = "SELECT payment_type, SUM(total) as total 
            FROM sales_summary 
            WHERE DATE(reg_date) BETWEEN :start_date AND :end_date 
            GROUP BY payment_type";
    $query = $dbh->prepare($sql);
    $query->bindParam(':start_date', $start_date);
    $query->bindParam(':end_date', $end_date);
    $query->execute();
    $payment_results = $query->fetchAll(PDO::FETCH_ASSOC);

    $payment_labels = [];
    $payment_values = [];
    foreach ($payment_results as $row) {
        $payment_labels[] = $row['payment_type'];
        $payment_values[] = $row['total'];
    }

    // Get daily sales trend (reusing logic but specific for this report if needed)
    // We can reuse the 'sales_trend' action logic or include it here. 
    // Let's include it here for a single call if possible, or just let the frontend call sales_trend separately.
    // For simplicity, let's just return payment breakdown here.

    $response = [
        'status' => 'success',
        'data' => [
            'payment_labels' => $payment_labels,
            'payment_values' => $payment_values
        ]
    ];
}

echo json_encode($response);
?>
