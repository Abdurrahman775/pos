<?php
require("../config.php");
require("../include/functions.php");

// Columns to be displayed
$columns = [
    0 => 'order_id',
    1 => 'reg_date',
    2 => 'customer',
    3 => 'actual_total',
    4 => 'tax_amount', // Placeholder
    5 => 'discount',
    6 => 'total',
    7 => 'payment_type',
    8 => 'status', // Placeholder
    9 => 'reg_by',
    10 => 'actions'
];

// Base Query
$sql = "SELECT ss.* FROM sales_summary ss WHERE 1=1";

$params = [];

// Filters
if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
    $sql .= " AND DATE(ss.reg_date) BETWEEN :start_date AND :end_date";
    $params[':start_date'] = $_POST['start_date'];
    $params[':end_date'] = $_POST['end_date'];
}

if (!empty($_POST['cashier_id'])) {
    // sales_summary stores username in reg_by, not ID. Fetch username or use subquery.
    $sql .= " AND ss.reg_by = (SELECT username FROM admins WHERE id = :cashier_id)";
    $params[':cashier_id'] = $_POST['cashier_id'];
}

if (!empty($_POST['payment_method'])) {
    $sql .= " AND ss.payment_type = :payment_method";
    $params[':payment_method'] = $_POST['payment_method'];
}

// Search
if (!empty($_POST['search']['value'])) {
    $search_value = $_POST['search']['value'];
    $sql .= " AND (
        ss.order_id LIKE :search 
        OR ss.payment_ref LIKE :search 
        OR ss.customer LIKE :search 
        OR EXISTS (
            SELECT 1 FROM sales s 
            JOIN products p ON s.product_id = p.id 
            WHERE s.order_id = ss.order_id 
            AND p.name LIKE :search
        )
    )";
    $params[':search'] = "%$search_value%";
}

// Total Records (before filtering)
$count_sql = "SELECT COUNT(*) FROM sales_summary";
$query = $dbh->prepare($count_sql);
$query->execute();
$totalData = $query->fetchColumn();

// Total Filtered Records
$count_filtered_sql = "SELECT COUNT(*) FROM sales_summary ss WHERE 1=1";
// Re-apply filters for count
if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
    $count_filtered_sql .= " AND DATE(ss.reg_date) BETWEEN :start_date AND :end_date";
}
if (!empty($_POST['cashier_id'])) {
    $count_filtered_sql .= " AND ss.reg_by = (SELECT username FROM admins WHERE id = :cashier_id)";
}
if (!empty($_POST['payment_method'])) {
    $count_filtered_sql .= " AND ss.payment_type = :payment_method";
}
if (!empty($_POST['search']['value'])) {
    $count_filtered_sql .= " AND (
        ss.order_id LIKE :search 
        OR ss.payment_ref LIKE :search 
        OR ss.customer LIKE :search 
        OR EXISTS (
            SELECT 1 FROM sales s 
            JOIN products p ON s.product_id = p.id 
            WHERE s.order_id = ss.order_id 
            AND p.name LIKE :search
        )
    )";
}

$query = $dbh->prepare($count_filtered_sql);
$query->execute($params);
$totalFiltered = $query->fetchColumn();

// Ordering
if (isset($_POST['order'])) {
    $column_name = $columns[$_POST['order'][0]['column']];
    // Handle placeholders or mapped columns
    if ($column_name == 'tax_amount' || $column_name == 'status' || $column_name == 'actions') {
        $column_name = 'reg_date'; // Default sort
    }
    $order = $_POST['order'][0]['dir'];
    $sql .= " ORDER BY " . $column_name . " " . $order;
} else {
    $sql .= " ORDER BY ss.reg_date DESC";
}

// Pagination: only apply when not requesting full dataset
$fetch_all = false;
if (isset($_REQUEST['fetch_all']) && $_REQUEST['fetch_all']) {
    $fetch_all = true;
}
if (!$fetch_all) {
    if (isset($_POST['length']) && $_POST['length'] != -1) {
        $start = $_POST['start'];
        $length = $_POST['length'];
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
        $query->bindValue(':start', (int)$start, PDO::PARAM_INT);
        $query->bindValue(':length', (int)$length, PDO::PARAM_INT);
    }
}
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);

$data = [];
foreach ($results as $row) {
    $nestedData = [];
    $nestedData[] = '#' . $row['order_id'];
    $nestedData[] = date('d/m/Y H:i', strtotime($row['reg_date']));
    $nestedData[] = $row['customer'];
    $nestedData[] = get_currency($dbh) . number_format($row['actual_total'], 2);
    $nestedData[] = get_currency($dbh) . number_format(0, 2); // Tax not tracked
    $nestedData[] = get_currency($dbh) . number_format($row['discount'], 2);
    $nestedData[] = '<strong>' . get_currency($dbh) . number_format($row['total'], 2) . '</strong>';
    $nestedData[] = ucfirst($row['payment_type']);

    // Status is not in DB, assume Completed
    $status = 'completed';
    $badge = [
        'completed' => 'success',
        'void' => 'danger',
        'refunded' => 'warning',
        'held' => 'info'
    ];
    $status_badge = $badge[$status] ?? 'secondary';
    $nestedData[] = '<span class="badge badge-' . $status_badge . '">' . ucfirst($status) . '</span>';

    $nestedData[] = $row['reg_by'];

    // Use order_id for view/print
    $actions = '<a href="view_transaction.php?id=' . $row['order_id'] . '" class="btn btn-sm btn-primary" title="View Details"><i class="fas fa-eye"></i></a> ';
    $actions .= '<a href="#" onclick="printReceipt(\'' . $row['order_id'] . '\')" class="btn btn-sm btn-info" title="Print Receipt"><i class="fas fa-print"></i></a>';

    $nestedData[] = $actions;

    $data[] = $nestedData;
}

// If fetching all, return data property (DataTables accepts {data: [...]})
if ($fetch_all) {
    $json_data = [
        "data" => $data
    ];
} else {
    $json_data = [
        "draw" => intval($_POST['draw']),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
    ];
}

echo json_encode($json_data);
