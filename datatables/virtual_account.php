<?php
require("../config.php");
require("../include/functions.php");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Columns to be displayed
$columns = [
    0 => 'row_num',
    1 => 'transaction_id',
    2 => 'created_at',
    3 => 'customer_name',
    4 => 'payment_method',
    5 => 'total_amount',
    6 => 'cashier_name'
];

// Base Query
$sql = "SELECT t.transaction_id, t.created_at, t.customer_id, t.user_id, t.total_amount, t.payment_method, t.notes,
        CASE 
            WHEN c.name IS NOT NULL THEN c.name
            WHEN t.notes LIKE '%Customer:%' THEN TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(t.notes, 'Customer:', -1), '|', 1))
            ELSE 'Walk-in'
        END as customer_name,
        COALESCE(CONCAT(a.fname, ' ', a.sname), a.username) as cashier_name
        FROM transactions t 
        LEFT JOIN customers c ON t.customer_id = c.customer_id 
        LEFT JOIN admins a ON t.user_id = a.id
        WHERE 1=1";

$params = [];

// Date Range Filter
if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
    $sql .= " AND DATE(t.created_at) BETWEEN :start_date AND :end_date";
    $params[':start_date'] = $_POST['start_date'];
    $params[':end_date'] = $_POST['end_date'];
}

// Cashier Filter
if (!empty($_POST['cashier_id'])) {
    $sql .= " AND t.user_id = :user_id";
    $params[':user_id'] = intval($_POST['cashier_id']);
}

// Search Filter
if (!empty($_POST['search']['value'])) {
    $search_value = $_POST['search']['value'];
    $sql .= " AND (
        t.transaction_id LIKE :search 
        OR t.notes LIKE :search 
        OR c.name LIKE :search 
        OR t.payment_method LIKE :search
    )";
    $params[':search'] = "%$search_value%";
}

// Get Total Records Count
$count_sql = "SELECT COUNT(*) FROM transactions";
$count_query = $dbh->prepare($count_sql);
$count_query->execute();
$totalData = $count_query->fetchColumn();

// Get Total Filtered Records Count
$count_filtered_sql = "SELECT COUNT(*) FROM transactions t 
                        LEFT JOIN customers c ON t.customer_id = c.customer_id 
                        LEFT JOIN admins a ON t.user_id = a.id
                        WHERE 1=1";

// Re-apply filters for count
if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
    $count_filtered_sql .= " AND DATE(t.created_at) BETWEEN :start_date AND :end_date";
}
if (!empty($_POST['cashier_id'])) {
    $count_filtered_sql .= " AND t.user_id = :user_id";
}
if (!empty($_POST['search']['value'])) {
    $count_filtered_sql .= " AND (
        t.transaction_id LIKE :search 
        OR t.notes LIKE :search 
        OR c.name LIKE :search 
        OR t.payment_method LIKE :search
    )";
}

$count_filtered_query = $dbh->prepare($count_filtered_sql);
foreach ($params as $key => $value) {
    $count_filtered_query->bindValue($key, $value);
}
$count_filtered_query->execute();
$totalFiltered = $count_filtered_query->fetchColumn();

// Ordering
if (isset($_POST['order'])) {
    $column_index = $_POST['order'][0]['column'];
    $column_name = isset($columns[$column_index]) ? $columns[$column_index] : 'created_at';
    
    // Handle row_num column (not sortable)
    if ($column_name == 'row_num') {
        $column_name = 'created_at';
    }
    
    $order = (strtoupper($_POST['order'][0]['dir']) === 'ASC') ? 'ASC' : 'DESC';
    
    // Handle aliased columns
    if ($column_name == 'customer_name' || $column_name == 'cashier_name') {
        $sql .= " ORDER BY " . $column_name . " " . $order;
    } else {
        $sql .= " ORDER BY t." . $column_name . " " . $order;
    }
} else {
    $sql .= " ORDER BY t.created_at DESC";
}

// Pagination
if (isset($_POST['length']) && $_POST['length'] != -1) {
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 25;
    $sql .= " LIMIT :start, :length";
}

// Execute Main Query
$query = $dbh->prepare($sql);
foreach ($params as $key => $value) {
    $query->bindValue($key, $value);
}

if (isset($_POST['length']) && $_POST['length'] != -1) {
    $query->bindValue(':start', $start, PDO::PARAM_INT);
    $query->bindValue(':length', $length, PDO::PARAM_INT);
}

$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);

// Cache currency symbol once for performance
$currency = get_currency($dbh);

// Build data array
$data = [];
$counter = (isset($_POST['start']) ? intval($_POST['start']) : 0) + 1;

foreach ($results as $row) {
    $nestedData = [];
    
    // Row number
    $nestedData[] = $counter++;
    
    // Transaction ID
    $nestedData[] = '#' . $row['transaction_id'];
    
    // Date
    $nestedData[] = date('d/m/Y H:i', strtotime($row['created_at']));
    
    // Customer
    $nestedData[] = htmlspecialchars($row['customer_name']);
    
    // Payment Method
    $payment_badge = '';
    if ($row['payment_method'] == 'CASH') {
        $payment_badge = '<span class="badge badge-success">CASH</span>';
    } elseif ($row['payment_method'] == 'POS') {
        $payment_badge = '<span class="badge badge-primary">POS</span>';
    } elseif ($row['payment_method'] == 'MIXED') {
        $payment_badge = '<span class="badge badge-warning">MIXED</span>';
    } else {
        $payment_badge = '<span class="badge badge-secondary">' . ucfirst($row['payment_method']) . '</span>';
    }
    $nestedData[] = $payment_badge;
    
    // Total Amount
    $nestedData[] = '<strong>' . $currency . number_format($row['total_amount'], 2) . '</strong>';
    
    // Cashier
    $nestedData[] = htmlspecialchars($row['cashier_name']);
    
    $data[] = $nestedData;
}

// Return JSON response
$json_data = [
    "draw" => intval($_POST['draw'] ?? 1),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
];

header('Content-Type: application/json');
echo json_encode($json_data);
?>
