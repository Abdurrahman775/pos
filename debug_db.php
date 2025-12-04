<?php
require("config.php");
try {
    $stmt = $dbh->query("SELECT COUNT(*) FROM transactions");
    echo "Transactions Count: " . $stmt->fetchColumn() . "\n";
    
    $stmt = $dbh->query("SHOW TABLES LIKE 'transactions'");
    echo "Table exists: " . ($stmt->rowCount() > 0 ? "Yes" : "No") . "\n";
    
    $stmt = $dbh->query("DESCRIBE transactions");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
