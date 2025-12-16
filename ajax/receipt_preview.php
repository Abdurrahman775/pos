<?php
session_start();
require("../config.php");
require("../include/functions.php");

header('Content-Type: application/json');

$token = isset($_GET['token']) ? trim($_GET['token']) : '';

if (empty($token)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid token']);
    exit;
}

try {
    // Get transaction details - token is stored in notes field
    $sql = "SELECT t.*, c.name as customer_name, c.phone, a.fname, a.sname 
            FROM transactions t
            LEFT JOIN customers c ON t.customer_id = c.customer_id
            LEFT JOIN admins a ON t.user_id = a.id
            WHERE t.notes LIKE :token";
    
    $query = $dbh->prepare($sql);
    $token_search = "%Token: " . $token . "%";
    $query->bindParam(':token', $token_search, PDO::PARAM_STR);
    $query->execute();
    $transaction = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!$transaction) {
        echo json_encode(['status' => 'error', 'message' => 'Transaction not found']);
        exit;
    }
    
    // Extract walk-in customer name from notes if customer_id is NULL
    $customer_name = $transaction['customer_name'];
    if (empty($customer_name) && !empty($transaction['notes'])) {
        // Extract customer name from notes (format: "Token: xxx | Customer: Name")
        if (preg_match('/Customer:\s*([^|]+)/', $transaction['notes'], $matches)) {
            $customer_name = trim($matches[1]);
        } else {
            $customer_name = 'Walk-in';
        }
    }
    
    // Get transaction items
    $sql = "SELECT ti.*, p.name as product_name 
            FROM transaction_items ti
            JOIN products p ON ti.product_id = p.id
            WHERE ti.transaction_id = :transaction_id";
    
    $query = $dbh->prepare($sql);
    $query->bindParam(':transaction_id', $transaction['transaction_id'], PDO::PARAM_INT);
    $query->execute();
    $items = $query->fetchAll(PDO::FETCH_ASSOC);
    
    
    // Get store info from system settings
    $sql = "SELECT setting_key, setting_value FROM system_settings 
            WHERE setting_key IN ('store_name', 'store_address', 'store_phone', 'receipt_footer')";
    $query = $dbh->query($sql);
    $settings = $query->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $company_name = $settings['store_name'] ?? 'POS System';
    $company_address = $settings['store_address'] ?? '';
    $company_phone = $settings['store_phone'] ?? '';
    $receipt_footer = $settings['receipt_footer'] ?? 'Thank you for your purchase!';
    
    // Generate receipt HTML
    $html = '<div style="font-family: monospace; max-width: 300px; margin: 0 auto;">';
    $html .= '<div style="text-align: center; border-bottom: 2px dashed #000; padding-bottom: 10px; margin-bottom: 10px;">';
    $html .= '<h3 style="margin: 5px 0;">' . htmlspecialchars($company_name) . '</h3>';
    if ($company_address) $html .= '<div style="font-size: 12px;">' . htmlspecialchars($company_address) . '</div>';
    if ($company_phone) $html .= '<div style="font-size: 12px;">Tel: ' . htmlspecialchars($company_phone) . '</div>';
    $html .= '</div>';
    
    $html .= '<div style="margin-bottom: 10px;">';
    $html .= '<div><strong>Receipt #:</strong> ' . str_pad($transaction['transaction_id'], 6, '0', STR_PAD_LEFT) . '</div>';
    $html .= '<div><strong>Date:</strong> ' . date('d/m/Y H:i', strtotime($transaction['created_at'])) . '</div>';
    $html .= '<div><strong>Cashier:</strong> ' . htmlspecialchars($transaction['fname'] . ' ' . $transaction['sname']) . '</div>';
    if ($customer_name) {
        $html .= '<div><strong>Customer:</strong> ' . htmlspecialchars($customer_name) . '</div>';
    }
    $html .= '</div>';
    
    $html .= '<div style="border-top: 2px dashed #000; border-bottom: 2px dashed #000; padding: 10px 0; margin-bottom: 10px;">';
    $html .= '<table style="width: 100%; font-size: 12px;">';
    $html .= '<thead><tr><th align="left">Item</th><th align="center">Qty</th><th align="right">Price</th><th align="right">Total</th></tr></thead>';
    $html .= '<tbody>';
    
    foreach ($items as $item) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($item['product_name']) . '</td>';
        $html .= '<td align="center">' . $item['quantity'] . '</td>';
        $html .= '<td align="right">' . number_format($item['unit_price'], 2) . '</td>';
        $html .= '<td align="right">' . number_format($item['line_total'], 2) . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table>';
    $html .= '</div>';
    
    
    $html .= '<div style="text-align: right; margin-bottom: 10px;">';
    $html .= '<div><strong>Subtotal:</strong> ' . get_currency($dbh) . ' ' . number_format($transaction['subtotal'], 2) . '</div>';
    if ($transaction['discount_amount'] > 0) {
        $html .= '<div><strong>Discount:</strong> ' . get_currency($dbh) . ' ' . number_format($transaction['discount_amount'], 2) . '</div>';
    }
    $html .= '<div style="font-size: 18px; margin-top: 5px;"><strong>TOTAL:</strong> ' . get_currency($dbh) . ' ' . number_format($transaction['total_amount'], 2) . '</div>';
    $html .= '</div>';
    
    $html .= '<div style="border-top: 2px dashed #000; padding-top: 10px; font-size: 12px;">';
    $html .= '<div><strong>Payment:</strong> ' . $transaction['payment_method'] . '</div>';
    
    if ($transaction['payment_method'] === 'CASH') {
        $html .= '<div><strong>Cash Received:</strong> ' . get_currency($dbh) . ' ' . number_format($transaction['amount_paid'], 2) . '</div>';
        $html .= '<div><strong>Change:</strong> ' . get_currency($dbh) . ' ' . number_format($transaction['change_amount'], 2) . '</div>';
    } elseif ($transaction['payment_method'] === 'MIXED') {
        // Extract mixed payment details from notes
        $notes = $transaction['notes'];
        $pos_ref = '';
        $pos_amount = 0;
        $cash_amount = 0;
        
        if (preg_match('/POS Ref:\s*([^|]+)/', $notes, $matches)) {
            $pos_ref = trim($matches[1]);
        }
        if (preg_match('/POS Amount:\s*([\d,]+\.?\d*)/', $notes, $matches)) {
            $pos_amount = floatval(str_replace(',', '', $matches[1]));
        }
        if (preg_match('/Cash Amount:\s*([\d,]+\.?\d*)/', $notes, $matches)) {
            $cash_amount = floatval(str_replace(',', '', $matches[1]));
        }
        
        $html .= '<div style="margin-top: 5px;"><strong>Payment Breakdown:</strong></div>';
        if ($pos_ref) {
            $html .= '<div>POS Ref: ' . htmlspecialchars($pos_ref) . '</div>';
        }
        if ($pos_amount > 0) {
            $html .= '<div>POS: ' . get_currency($dbh) . ' ' . number_format($pos_amount, 2) . '</div>';
        }
        if ($cash_amount > 0) {
            $html .= '<div>Cash: ' . get_currency($dbh) . ' ' . number_format($cash_amount, 2) . '</div>';
        }
        $total_paid = $pos_amount + $cash_amount;
        if ($total_paid > $transaction['total_amount']) {
            $change = $total_paid - $transaction['total_amount'];
            $html .= '<div><strong>Change:</strong> ' . get_currency($dbh) . ' ' . number_format($change, 2) . '</div>';
        }
    } elseif ($transaction['payment_method'] === 'POS') {
        // Extract POS reference from notes if available
        if (!empty($transaction['notes']) && preg_match('/POS Ref:\s*([^|]+)/', $transaction['notes'], $matches)) {
            $pos_ref = trim($matches[1]);
            $html .= '<div><strong>Reference:</strong> ' . htmlspecialchars($pos_ref) . '</div>';
        } else {
            $html .= '<div><strong>Reference:</strong> Payment processed</div>';
        }
    }
    $html .= '</div>';
    
    $html .= '<div style="text-align: center; margin-top: 20px; font-size: 11px;">';
    $html .= '<p>' . nl2br(htmlspecialchars($receipt_footer)) . '</p>';
    $html .= '<p>** Keep this receipt for your record **</p>';
    $html .= '</div>';
    
    $html .= '</div>';
    
    echo json_encode([
        'status' => 'success',
        'html' => $html,
        'transaction_id' => $transaction['transaction_id']
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
