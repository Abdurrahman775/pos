<?php
require("../config.php");
require("../include/functions.php");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Columns to be displayed
$columns = [
    0 => 'id',
    1 => 'Timestamp',
    2 => 'username',
    3 => 'ActionType',
    4 => 'Description'
];

// Base Query
$sql = "SELECT al.id, al.username, al.ActionType, al.Description, al.Timestamp,
        COALESCE(CONCAT(a.fname, ' ', a.sname), al.username) as user_name
        FROM auditlog al
        LEFT JOIN admins a ON al.username = a.username
        WHERE 1=1";

$params = [];

// Date Range Filter
if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
    $sql .= " AND DATE(al.Timestamp) BETWEEN :start_date AND :end_date";
    $params[':start_date'] = $_POST['start_date'];
    $params[':end_date'] = $_POST['end_date'];
}

// Action Type Filter
if (!empty($_POST['action_type'])) {
    $sql .= " AND al.ActionType = :action_type";
    $params[':action_type'] = $_POST['action_type'];
}

// Search Filter
if (!empty($_POST['search']['value'])) {
    $search_value = $_POST['search']['value'];
    $sql .= " AND (
        al.username LIKE :search 
        OR al.ActionType LIKE :search 
        OR al.Description LIKE :search
        OR a.fname LIKE :search
        OR a.sname LIKE :search
    )";
    $params[':search'] = "%$search_value%";
}

// Get Total Records Count
$count_sql = "SELECT COUNT(*) FROM auditlog";
$count_query = $dbh->prepare($count_sql);
$count_query->execute();
$totalData = $count_query->fetchColumn();

// Get Total Filtered Records Count
$count_filtered_sql = "SELECT COUNT(*) FROM auditlog al 
                        LEFT JOIN admins a ON al.username = a.username 
                        WHERE 1=1";

// Re-apply filters for count
if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
    $count_filtered_sql .= " AND DATE(al.Timestamp) BETWEEN :start_date AND :end_date";
}
if (!empty($_POST['action_type'])) {
    $count_filtered_sql .= " AND al.ActionType = :action_type";
}
if (!empty($_POST['search']['value'])) {
    $count_filtered_sql .= " AND (
        al.username LIKE :search 
        OR al.ActionType LIKE :search 
        OR al.Description LIKE :search
        OR a.fname LIKE :search
        OR a.sname LIKE :search
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
    $column_name = isset($columns[$column_index]) ? $columns[$column_index] : 'Timestamp';
    $order = (strtoupper($_POST['order'][0]['dir']) === 'ASC') ? 'ASC' : 'DESC';
    $sql .= " ORDER BY al." . $column_name . " " . $order;
} else {
    $sql .= " ORDER BY al.Timestamp DESC";
}

// Pagination
if (isset($_POST['length']) && $_POST['length'] != -1) {
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 40;
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
    
    // Date/Time
    $nestedData[] = date('d/m/Y H:i:s', strtotime($row['Timestamp']));
    
    // User (full name or username)
    $nestedData[] = htmlspecialchars($row['user_name']);
    
    // Action Type with badge
    $badge_colors = [
        'LOGIN' => 'success',
        'LOGOUT' => 'secondary',
        'CREATE' => 'primary',
        'UPDATE' => 'info',
        'DELETE' => 'danger',
        'VIEW' => 'warning'
    ];
    $badge_color = $badge_colors[$row['ActionType']] ?? 'secondary';
    $nestedData[] = '<span class="badge badge-' . $badge_color . '">' . htmlspecialchars($row['ActionType']) . '</span>';
    
    // Description
    $nestedData[] = htmlspecialchars($row['Description']);
    
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
