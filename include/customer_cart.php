<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

function product_exists($product_id)
{
    $product_id = intval($product_id);
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
    $max = count($cart);
    $flag = 0;
    for ($i = 0; $i < $max; $i++) {
        if (isset($cart[$i]['product_id']) && $product_id == $cart[$i]['product_id']) {
            $flag = 1;
            break;
        }
    }
    return $flag;
}

function addtocart($product_id)
{
    $quantity = 1;
    if (product_exists($product_id)) {
        return;
    }
    $_SESSION['cart'][] = array(
        'product_id' => $product_id,
        'quantity' => $quantity
    );
}

function remove_product($product_id)
{
    $product_id = intval($product_id);
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
    foreach ($cart as $key => $product) {
        if ($product['product_id'] == $product_id) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
}

function get_order_total($dbh)
{
    $sum = 0;
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
    foreach ($cart as $item) {
        if (isset($item['product_id']) && isset($item['quantity'])) {
            $price = get_product_price($dbh, $item['product_id']);
            $sum += $price * $item['quantity'];
        }
    }
    return $sum;
}

function get_quantity_total($dbh)
{
    $sum = 0;
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
    foreach ($cart as $item) {
        if (isset($item['quantity'])) {
            $sum += $item['quantity'];
        }
    }
    return $sum;
}

function count_items()
{
    return count($_SESSION['cart']);
}
