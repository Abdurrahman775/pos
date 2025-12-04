<?php
require("config.php");
echo "<h1>Sales Summary Schema</h1>";
try {
    $sql = "DESCRIBE sales_summary";
    $query = $dbh->prepare($sql);
    $query->execute();
    $columns = $query->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Key</th></tr>";
    foreach ($columns as $c) {
        echo "<tr><td>{$c['Field']}</td><td>{$c['Type']}</td><td>{$c['Key']}</td></tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
