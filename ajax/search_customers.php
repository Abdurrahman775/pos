<?php
require("../config.php");

$search = isset($_GET['term']) ? trim($_GET['term']) : '';
$customers = [];

if (!empty($search)) {
    // customers table uses `customer_id` as primary key; alias it to `id` for frontend compatibility
    $sql = "SELECT customer_id AS id, name, phone FROM customers WHERE (name LIKE :search OR phone LIKE :search) AND is_active = 1 ORDER BY name ASC LIMIT 10";
    $query = $dbh->prepare($sql);
    // bindValue accepts expressions; bindParam requires a variable reference
    $query->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $customers[] = [
            'id' => $row['id'],
            'label' => $row['name'] . ' (' . $row['phone'] . ')',
            'value' => $row['name']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($customers);
