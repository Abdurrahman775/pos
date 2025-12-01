<?php
require('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartData = json_decode(file_get_contents('php://input'), true);

    $cart = $cartData['cart'];
    $totalAmount = $cartData['totalAmount'];

    // Insert into orders table
    $stmt = $dbh->prepare("INSERT INTO orders (total_amount) VALUES (:total_amount)");
    $stmt->bindParam(':total_amount', $totalAmount);
    $stmt->execute();
    $orderId = $dbh->lastInsertId();

    // Insert each product into order_items table
    foreach ($cart as $item) {
        $stmt = $dbh->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)");
        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':product_id', $item['id']);
        $stmt->bindParam(':quantity', $item['quantity']);
        $stmt->bindParam(':price', $item['price']);
        $stmt->execute();
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
