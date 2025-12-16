<?php
require("../config.php");
require("../include/functions.php");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Columns to be displayed (maps to data array indices)
$columns = [
    0 => 'transaction_id',
    1 => 'created_at',
    2 => 'customer_id',
    3 => 'subtotal',
    4 => 'tax_amount',
    5 => 'discount_amount',
    6 => 'total_amount',
    7 => 'payment_method',
    8 => 'status',
    9 => 'created_by',
    10 => 'actions'
];

// Base Query - fetch from transactions table
$sql = "SELECT t.transaction_id, t.created_at, t.customer_id, t.user_id, t.subtotal, t.tax_amount, t.discount_amount, t.total_amount, t.payment_method, t.status, t.notes, t.created_by,
        CASE 
            WHEN c.name IS NOT NULL THEN c.name
            WHEN t.notes LIKE '%Customer:%' THEN TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(t.notes, 'Customer:', -1), '|', 1))
            ELSE 'Walk-in'
        END as customer_name,
        COALESCE(CONCAT(a.fname, ' ', a.sname), a.username, t.created_by) as cashier_name
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

// Cashier Filter (user_id in transactions table)
if (!empty($_POST['cashier_id'])) {
    $sql .= " AND t.user_id = :user_id";
    $params[':user_id'] = intval($_POST['cashier_id']);
}

// Payment Method Filter
if (!empty($_POST['payment_method'])) {
    $sql .= " AND t.payment_method = :payment_method";
    $params[':payment_method'] = $_POST['payment_method'];
}

// Search Filter
if (!empty($_POST['search']['value'])) {
    $search_value = $_POST['search']['value'];
    $sql .= " AND (
        t.transaction_id LIKE :search 
        OR t.notes LIKE :search 
        OR c.name LIKE :search 
        OR c.phone LIKE :search
    )";
    $params[':search'] = "%$search_value%";
}

// Get Total Records Count (before filtering)
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
if (!empty($_POST['payment_method'])) {
    $count_filtered_sql .= " AND t.payment_method = :payment_method";
}
if (!empty($_POST['search']['value'])) {
    $count_filtered_sql .= " AND (
        t.transaction_id LIKE :search 
        OR t.notes LIKE :search 
        OR c.name LIKE :search 
        OR c.phone LIKE :search
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

    // Handle actions column (not sortable)
    if ($column_name == 'actions') {
        $column_name = 'created_at';
    }

    $order = (strtoupper($_POST['order'][0]['dir']) === 'ASC') ? 'ASC' : 'DESC';
    $sql .= " ORDER BY t." . $column_name . " " . $order;
} else {
    $sql .= " ORDER BY t.created_at DESC";
}

// Pagination: only apply when not requesting full dataset
$fetch_all = false;
if (isset($_REQUEST['fetch_all']) && $_REQUEST['fetch_all']) {
    $fetch_all = true;
}

if (!$fetch_all) {
    if (isset($_POST['length']) && $_POST['length'] != -1) {
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $sql .= " LIMIT :start, :length";
    }
}

// Execute Main Query
$query = $dbh->prepare($sql);
foreach ($params as $key => $value) {
    $query->bindValue($key, $value);
}

if (!$fetch_all) {
    if (isset($_POST['length']) && $_POST['length'] != -1) {
        $query->bindValue(':start', $start, PDO::PARAM_INT);
        $query->bindValue(':length', $length, PDO::PARAM_INT);
    }
}

$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);

// Cache currency symbol once for performance
$currency = get_currency($dbh);

// Build data array
$data = [];
foreach ($results as $row) {
    $nestedData = [];

    // Transaction ID
    $nestedData[] = '#' . $row['transaction_id'];

    // Date
    $nestedData[] = date('d/m/Y H:i', strtotime($row['created_at']));

    // Customer
    $nestedData[] = $row['customer_name'];

    // Subtotal
    $nestedData[] = $currency . number_format($row['subtotal'], 2);

    // Tax
    $nestedData[] = $currency . number_format($row['tax_amount'], 2);

    // Discount
    $nestedData[] = $currency . number_format($row['discount_amount'], 2);

    // Total (bold)
    $nestedData[] = '<strong>' . $currency . number_format($row['total_amount'], 2) . '</strong>';

    // Payment Method with badge
    $payment_method = $row['payment_method'];
    $payment_badge = '';
    if ($payment_method == 'CASH') {
        $payment_badge = '<span class="badge badge-success">CASH</span>';
    } elseif ($payment_method == 'POS') {
        $payment_badge = '<span class="badge badge-primary">POS</span>';
    } elseif ($payment_method == 'MIXED') {
        $payment_badge = '<span class="badge badge-warning">MIXED</span>';
    } else {
        $payment_badge = '<span class="badge badge-secondary">' . ucfirst($payment_method) . '</span>';
    }
    $nestedData[] = $payment_badge;

    // Status Badge
    $status = $row['status'] ?? 'completed';
    $badge_colors = [
        'completed' => 'success',
        'void' => 'danger',
        'refunded' => 'warning',
        'held' => 'info'
    ];
    $badge_color = $badge_colors[$status] ?? 'secondary';
    $nestedData[] = '<span class="badge badge-' . $badge_color . '">' . ucfirst($status) . '</span>';

    // Cashier (full name from admin lookup or fallback to created_by)
    $nestedData[] = $row['cashier_name'];

    // Actions
    $actions = '<a href="receipt.php?id=' . $row['transaction_id'] . '" class="btn btn-sm btn-primary" title="View Receipt"><i class="fas fa-receipt"></i></a> ';
    $actions .= '<a href="javascript:printReceipt(' . $row['transaction_id'] . ')" class="btn btn-sm btn-info" title="Print Receipt"><i class="fas fa-print"></i></a>';

    $nestedData[] = $actions;
    $data[] = $nestedData;
}

// Return JSON response
if ($fetch_all) {
    $json_data = [
        "data" => $data
    ];
} else {
    $json_data = [
        "draw" => intval($_POST['draw'] ?? 1),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
    ];
}

header('Content-Type: application/json');
echo json_encode($json_data);
