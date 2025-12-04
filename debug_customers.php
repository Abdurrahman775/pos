<?php
require("config.php");
echo "<h1>Customer Debug</h1>";
try {
    $sql = "SELECT id, name, is_active FROM customers LIMIT 10";
    $query = $dbh->prepare($sql);
    $query->execute();
    $customers = $query->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Active</th></tr>";
    foreach ($customers as $c) {
        echo "<tr><td>{$c['id']}</td><td>{$c['name']}</td><td>{$c['is_active']}</td></tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
