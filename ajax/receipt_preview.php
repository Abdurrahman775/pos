<?php

/**
 * Receipt Preview - Returns HTML preview of receipt
 * Used for displaying receipt before printing
 */
require_once(dirname(dirname(__FILE__)) . "/config.php");
require_once(dirname(dirname(__FILE__)) . "/include/functions.php");

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get token from request (base64 encoded transaction_id)
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (empty($token)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'No token provided']);
    exit;
}

// Decode token to get transaction_id
$transaction_id = intval(base64_decode($token));

if ($transaction_id <= 0) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid token']);
    exit;
}

// Get transaction details
$sql = "SELECT t.*, c.name as customer_name, c.phone as customer_phone,
        COALESCE(a.username, t.created_by) as cashier_name
        FROM transactions t
        LEFT JOIN customers c ON t.customer_id = c.customer_id
        LEFT JOIN admins a ON (CAST(t.created_by AS UNSIGNED) = a.id OR t.created_by = a.username)
        WHERE t.transaction_id = :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $transaction_id, PDO::PARAM_INT);
$query->execute();
$transaction = $query->fetch(PDO::FETCH_ASSOC);

if (!$transaction) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Transaction not found']);
    exit;
}

// Get transaction items
$sql = "SELECT ti.*
        FROM transaction_items ti
        WHERE ti.transaction_id = :transaction_id";
$query = $dbh->prepare($sql);
$query->bindParam(':transaction_id', $transaction_id, PDO::PARAM_INT);
$query->execute();
$items = $query->fetchAll(PDO::FETCH_ASSOC);

// Get store settings (with defaults)
$store_name = 'S & I IT PARTNERS LTD';
$store_address = '';
$store_phone = '';
$store_email = '';
$receipt_footer = 'Thank you for your business!';
$currency_symbol = get_currency($dbh);

// Build receipt HTML
$html = '<div style="font-family: monospace; font-size: 12px; line-height: 1.4; width: 80mm; margin: 0 auto;">';

// Header
$html .= '<div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 10px;">';
$html .= '<h3 style="margin: 0; font-size: 14px; font-weight: bold;">' . htmlspecialchars($store_name) . '</h3>';
if ($store_address) {
    $html .= '<p style="margin: 0; font-size: 10px;">' . htmlspecialchars($store_address) . '</p>';
}
if ($store_phone) {
    $html .= '<p style="margin: 0; font-size: 10px;">Tel: ' . htmlspecialchars($store_phone) . '</p>';
}
if ($store_email) {
    $html .= '<p style="margin: 0; font-size: 10px;">Email: ' . htmlspecialchars($store_email) . '</p>';
}
$html .= '</div>';

// Transaction Info
$html .= '<div style="margin-bottom: 10px;">';
$html .= '<div style="display: flex; justify-content: space-between;"><span>Receipt #:</span><span><strong>' . str_pad($transaction_id, 6, '0', STR_PAD_LEFT) . '</strong></span></div>';
$html .= '<div style="display: flex; justify-content: space-between;"><span>Date:</span><span>' . date('d/m/Y H:i', strtotime($transaction['created_at'])) . '</span></div>';
if ($transaction['customer_name']) {
    $html .= '<div style="display: flex; justify-content: space-between;"><span>Customer:</span><span>' . htmlspecialchars($transaction['customer_name']) . '</span></div>';
}
$html .= '<div style="display: flex; justify-content: space-between;"><span>Cashier:</span><span>' . htmlspecialchars($transaction['cashier_name'] ?? 'System') . '</span></div>';
$html .= '<div style="display: flex; justify-content: space-between;"><span>Payment:</span><span>' . htmlspecialchars($transaction['payment_method']) . '</span></div>';
$html .= '</div>';

// Items Table
$html .= '<table style="width: 100%; border-collapse: collapse; border-bottom: 2px solid #000; margin-bottom: 10px;">';
$html .= '<thead>';
$html .= '<tr style="border-bottom: 1px solid #000;">';
$html .= '<th style="text-align: left; padding: 2px;">Item</th>';
$html .= '<th style="text-align: center; padding: 2px;">Qty</th>';
$html .= '<th style="text-align: right; padding: 2px;">Price</th>';
$html .= '<th style="text-align: right; padding: 2px;">Total</th>';
$html .= '</tr>';
$html .= '</thead>';
$html .= '<tbody>';

foreach ($items as $item) {
    $html .= '<tr>';
    $html .= '<td style="text-align: left; padding: 2px;">' . htmlspecialchars($item['product_name']) . '</td>';
    $html .= '<td style="text-align: center; padding: 2px;">' . $item['quantity'] . '</td>';
    $html .= '<td style="text-align: right; padding: 2px;">' . $currency_symbol . number_format($item['unit_price'], 2) . '</td>';
    $html .= '<td style="text-align: right; padding: 2px;">' . $currency_symbol . number_format($item['line_total'], 2) . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody>';
$html .= '</table>';

// Totals
$html .= '<div style="margin-bottom: 10px;">';
$html .= '<div style="display: flex; justify-content: space-between;"><span>Subtotal:</span><span>' . $currency_symbol . number_format($transaction['subtotal'], 2) . '</span></div>';

if ($transaction['discount_amount'] > 0) {
    $html .= '<div style="display: flex; justify-content: space-between;"><span>Discount:</span><span>-' . $currency_symbol . number_format($transaction['discount_amount'], 2) . '</span></div>';
}

if ($transaction['tax_amount'] > 0) {
    $html .= '<div style="display: flex; justify-content: space-between;"><span>Tax:</span><span>' . $currency_symbol . number_format($transaction['tax_amount'], 2) . '</span></div>';
}

$html .= '<div style="display: flex; justify-content: space-between; font-weight: bold; border-top: 1px solid #000; padding-top: 5px;"><span>TOTAL:</span><span>' . $currency_symbol . number_format($transaction['total_amount'], 2) . '</span></div>';

if ($transaction['amount_paid'] > 0) {
    $html .= '<div style="display: flex; justify-content: space-between; margin-top: 5px;"><span>Paid:</span><span>' . $currency_symbol . number_format($transaction['amount_paid'], 2) . '</span></div>';
    $html .= '<div style="display: flex; justify-content: space-between;"><span>Change:</span><span>' . $currency_symbol . number_format($transaction['change_amount'] ?? 0, 2) . '</span></div>';
}

$html .= '</div>';

// Footer
$html .= '<div style="text-align: center; margin-top: 10px; border-top: 2px solid #000; padding-top: 8px;">';
$html .= '<p style="margin: 0; font-size: 10px;">' . nl2br(htmlspecialchars($receipt_footer)) . '</p>';
$html .= '<p style="margin: 0; font-size: 9px;">Powered by POS System</p>';
$html .= '</div>';

$html .= '</div>';

// Return as JSON
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'html' => $html,
    'transaction_id' => $transaction_id
]);
