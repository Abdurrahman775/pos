<?php
require("../config.php");
require("../include/functions.php");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Columns to be displayed (maps to data array indices)
$columns = [
    0 => 'id',
    1 => 'username',
    2 => 'full_name',
    3 => 'email',
    4 => 'role_id',
    5 => 'is_active',
    6 => 'last_update',
    7 => 'actions'
];

// Base Query - fetch from admins table
$sql = "SELECT id, username, fname, mname, sname, email, role_id, is_active, last_update
        FROM admins 
        WHERE 1=1";

$params = [];

// Role Filter
if (!empty($_POST['role_filter'])) {
    $sql .= " AND role_id = :role_id";
    $params[':role_id'] = intval($_POST['role_filter']);
}

// Status Filter
if (!empty($_POST['status_filter']) && $_POST['status_filter'] !== 'all') {
    $sql .= " AND is_active = :is_active";
    $params[':is_active'] = ($_POST['status_filter'] === 'active') ? 1 : 0;
}

// Search Filter
if (!empty($_POST['search']['value'])) {
    $search_value = $_POST['search']['value'];
    $sql .= " AND (
        username LIKE :search 
        OR email LIKE :search 
        OR CONCAT(fname, ' ', IFNULL(mname, ''), ' ', sname) LIKE :search
    )";
    $params[':search'] = "%$search_value%";
}

// Get Total Records Count (before filtering)
$count_sql = "SELECT COUNT(*) FROM admins";
$count_query = $dbh->prepare($count_sql);
$count_query->execute();
$totalData = $count_query->fetchColumn();

// Get Total Filtered Records Count
$count_filtered_sql = "SELECT COUNT(*) FROM admins WHERE 1=1";

// Re-apply filters for count
if (!empty($_POST['role_filter'])) {
    $count_filtered_sql .= " AND role_id = :role_id";
}
if (!empty($_POST['status_filter']) && $_POST['status_filter'] !== 'all') {
    $count_filtered_sql .= " AND is_active = :is_active";
}
if (!empty($_POST['search']['value'])) {
    $count_filtered_sql .= " AND (
        username LIKE :search 
        OR email LIKE :search 
        OR CONCAT(fname, ' ', IFNULL(mname, ''), ' ', sname) LIKE :search
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
    $column_name = isset($columns[$column_index]) ? $columns[$column_index] : 'id';

    // Handle actions column (not sortable)
    if ($column_name == 'actions') {
        $column_name = 'id';
    }
    
    // Handle full_name sorting
    if ($column_name == 'full_name') {
        $column_name = "CONCAT(fname, ' ', IFNULL(mname, ''), ' ', sname)";
    }

    $order = (strtoupper($_POST['order'][0]['dir']) === 'ASC') ? 'ASC' : 'DESC';
    $sql .= " ORDER BY " . $column_name . " " . $order;
} else {
    $sql .= " ORDER BY id DESC";
}

// Pagination
if (isset($_POST['length']) && $_POST['length'] != -1) {
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
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

// Build data array
$data = [];
foreach ($results as $row) {
    $nestedData = [];

    // ID
    $nestedData[] = $row['id'];

    // Username
    $nestedData[] = htmlspecialchars($row['username']);

    // Full Name
    $full_name = trim($row['fname'] . ' ' . ($row['mname'] ?? '') . ' ' . $row['sname']);
    $nestedData[] = htmlspecialchars($full_name);

    // Email
    $nestedData[] = htmlspecialchars($row['email']);

    // Role Badge
    $role_badges = [
        1 => '<span class="badge badge-danger">Administrator</span>',
        2 => '<span class="badge badge-warning">Manager</span>',
        3 => '<span class="badge badge-info">Cashier</span>'
    ];
    $nestedData[] = $role_badges[$row['role_id']] ?? '<span class="badge badge-secondary">Unknown</span>';

    // Status Badge
    if ($row['is_active']) {
        $nestedData[] = '<span class="badge badge-success">Active</span>';
    } else {
        $nestedData[] = '<span class="badge badge-secondary">Inactive</span>';
    }

    // Last Login
    $nestedData[] = $row['last_update'] ? date('d/m/Y H:i', strtotime($row['last_update'])) : 'Never';

    // Actions
    $actions = '<a href="javascript:void(0)" onclick="viewEmployee(' . $row['id'] . ')" class="btn btn-sm btn-info" title="View Details"><i class="fas fa-eye"></i></a> ';
    $actions .= '<a href="javascript:void(0)" onclick="editEmployee(' . $row['id'] . ')" class="btn btn-sm btn-primary" title="Edit"><i class="fas fa-edit"></i></a>';

    $nestedData[] = $actions;
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
