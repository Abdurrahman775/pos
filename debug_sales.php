<?php
require("config.php");
try {
    $stmt = $dbh->query("SELECT COUNT(*) FROM sales_summary");
    echo "Sales Summary Count: " . $stmt->fetchColumn() . "\n";
    
    $stmt = $dbh->query("DESCRIBE sales_summary");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
