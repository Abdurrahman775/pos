<?php
/**
 * Receipt Page
 * Display and print transaction receipt
 */
require("config.php");
require("include/functions.php");

$transaction_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get transaction details
$sql = "SELECT t.*, c.name as customer_name, c.phone as customer_phone,
        a.fname, a.sname
        FROM transactions t
        LEFT JOIN customers c ON t.customer_id = c.id
        LEFT JOIN admins a ON t.cashier_id = a.id
        WHERE t.id = :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $transaction_id, PDO::PARAM_INT);
$query->execute();
$transaction = $query->fetch(PDO::FETCH_ASSOC);

if(!$transaction) {
    die('Transaction not found');
}

// Get transaction items
$sql = "SELECT ti.*, p.name as product_name
        FROM transaction_items ti
        LEFT JOIN products p ON ti.product_id = p.id
        WHERE ti.transaction_id = :transaction_id";
$query = $dbh->prepare($sql);
$query->bindParam(':transaction_id', $transaction_id, PDO::PARAM_INT);
$query->execute();
$items = $query->fetchAll(PDO::FETCH_ASSOC);

// Get store settings
$store_name = get_setting($dbh, 'store_name', 'S & I IT PARTNERS LTD');
$store_address = get_setting($dbh, 'store_address', '');
$store_phone = get_setting($dbh, 'store_phone', '');
$store_email = get_setting($dbh, 'store_email', '');
$receipt_footer = get_setting($dbh, 'receipt_footer', 'Thank you for your business!');
$currency_symbol = get_setting($dbh, 'currency_symbol', '₦');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt #<?php echo $transaction_id; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            @page {
                margin: 0;
                size: 80mm 100mm; /* Fixed size: 80mm width, 100mm height */
            }
            html, body {
                height: 100mm; /* Force height */
                margin: 0;
                padding: 0;
                background-color: #ffffff !important;
                color: #000000 !important;
                overflow: hidden; /* Ensure it doesn't spill over */
            }
            .no-print {
                display: none;
            }
            .receipt {
                width: 100%;
                border: none;
                padding: 0;
                margin: 0;
                background-color: #ffffff !important; /* Force White */
            }
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 13px;
            background: #f5f5f5;
            color: #000;
        }
        .receipt {
            width: 72mm; /* Fits within 80mm paper */
            margin: 0 auto; /* Center on screen */
            padding: 2mm;
            background: #fff;
            box-sizing: border-box;
        }
        /* ... rest of styles ... */
        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .receipt-header h3 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .receipt-header p {
            margin: 2px 0;
            font-size: 12px;
        }
        .receipt-info {
            margin-bottom: 10px;
            font-size: 12px;
        }
        .receipt-info div {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 12px;
        }
        .items-table th {
            border-bottom: 1px solid #000;
            padding: 3px 0;
            text-align: left;
            font-weight: bold;
        }
        .items-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .items-table .text-right {
            text-align: right;
        }
        .totals {
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        .totals div {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-size: 13px;
        }
        .totals .grand-total {
            font-size: 15px;
            font-weight: bold;
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        .receipt-footer {
            text-align: center;
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 15px;
            font-size: 11px;
        }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="receipt-header">
            <h3><?php echo htmlspecialchars($store_name); ?></h3>
            <?php if($store_address): ?>
            <p><?php echo htmlspecialchars($store_address); ?></p>
            <?php endif; ?>
            <?php if($store_phone): ?>
            <p>Tel: <?php echo htmlspecialchars($store_phone); ?></p>
            <?php endif; ?>
            <?php if($store_email): ?>
            <p>Email: <?php echo htmlspecialchars($store_email); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="receipt-info">
            <div>
                <span>Receipt #:</span>
                <span><strong><?php echo str_pad($transaction_id, 6, '0', STR_PAD_LEFT); ?></strong></span>
            </div>
            <div>
                <span>Date:</span>
                <span><?php echo date('d/m/Y H:i', strtotime($transaction['transaction_date'])); ?></span>
            </div>
            <?php if($transaction['customer_name']): ?>
            <div>
                <span>Customer:</span>
                <span><?php echo htmlspecialchars($transaction['customer_name']); ?></span>
            </div>
            <?php endif; ?>
            <div>
                <span>Cashier:</span>
                <span><?php echo htmlspecialchars($transaction['fname'] . ' ' . $transaction['sname']); ?></span>
            </div>
            <div>
                <span>Payment:</span>
                <span><?php echo htmlspecialchars($transaction['payment_method']); ?></span>
            </div>
        </div>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td class="text-right"><?php echo $item['quantity']; ?></td>
                    <td class="text-right"><?php echo $currency_symbol . number_format($item['unit_price'], 2); ?></td>
                    <td class="text-right"><?php echo $currency_symbol . number_format($item['subtotal'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="totals">
            <div>
                <span>Subtotal:</span>
                <span><?php echo $currency_symbol . number_format($transaction['subtotal'], 2); ?></span>
            </div>
            <?php if($transaction['discount_amount'] > 0): ?>
            <div>
                <span>Discount:</span>
                <span>-<?php echo $currency_symbol . number_format($transaction['discount_amount'], 2); ?></span>
            </div>
            <?php endif; ?>
            <?php if($transaction['tax_amount'] > 0): ?>
            <div>
                <span>Tax:</span>
                <span><?php echo $currency_symbol . number_format($transaction['tax_amount'], 2); ?></span>
            </div>
            <?php endif; ?>
            <div class="grand-total">
                <span>TOTAL:</span>
                <span><?php echo $currency_symbol . number_format($transaction['total_amount'], 2); ?></span>
            </div>
            <?php if($transaction['amount_paid'] > 0): ?>
            <div>
                <span>Paid:</span>
                <span><?php echo $currency_symbol . number_format($transaction['amount_paid'], 2); ?></span>
            </div>
            <div>
                <span>Change:</span>
                <span><?php echo $currency_symbol . number_format($transaction['change_given'], 2); ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="receipt-footer">
            <p><?php echo nl2br(htmlspecialchars($receipt_footer)); ?></p>
            <p><small>Powered by POS System</small></p>
        </div>
    </div>
    
    <div class="text-center no-print" style="margin: 20px;">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Print Receipt
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Close
        </button>
    </div>
    
    <script>
    // Auto print on load (optional)
    // window.onload = function() { window.print(); }
    </script>
</body>
</html>
