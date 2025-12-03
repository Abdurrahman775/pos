<?php
require_once('../config.php');
require_once('functions.php');
require_once('../template/plugins/fpdf/fpdf.php');

// Get order ID from token
$order_id = isset($_GET['token']) ? base64_decode($_GET['token']) : '';

if (empty($order_id)) {
    die('Invalid order ID');
}

try {
    // Get sales summary
    $summarySQL = "SELECT * FROM sales_summary WHERE order_id = :order_id LIMIT 1";
    $summaryQuery = $dbh->prepare($summarySQL);
    $summaryQuery->bindParam(':order_id', $order_id, PDO::PARAM_STR);
    $summaryQuery->execute();
    $summary = $summaryQuery->fetch(PDO::FETCH_ASSOC);

    if (!$summary) {
        die('Order not found');
    }

    // Get sales items
    $salesSQL = "SELECT s.*, p.name as product_name 
                 FROM sales s 
                 LEFT JOIN products p ON s.product_id = p.id 
                 WHERE s.order_id = :order_id 
                 ORDER BY s.id ASC";
    $salesQuery = $dbh->prepare($salesSQL);
    $salesQuery->bindParam(':order_id', $order_id, PDO::PARAM_STR);
    $salesQuery->execute();
    $items = $salesQuery->fetchAll(PDO::FETCH_ASSOC);

    // Create PDF
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(true, 15);

    // Company/Store Header
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->Cell(0, 10, 'S & I IT PARTNERS LTD', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Point of Sale System', 0, 1, 'C');
    $pdf->Ln(3);

    // Invoice Title
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'SALES RECEIPT', 0, 1, 'C');
    $pdf->Ln(5);

    // Order Information
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 6, 'Order ID:', 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(60, 6, $order_id, 0, 0);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 6, 'Date:', 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, date('d/m/Y H:i', strtotime($summary['reg_date'])), 0, 1);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 6, 'Customer:', 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(60, 6, $summary['customer'], 0, 0);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 6, 'Cashier:', 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, $summary['reg_by'], 0, 1);

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(40, 6, 'Payment Type:', 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(60, 6, $summary['payment_type'], 0, 0);

    if (!empty($summary['payment_ref'])) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(40, 6, 'Ref. No:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, $summary['payment_ref'], 0, 1);
    } else {
        $pdf->Ln(6);
    }

    $pdf->Ln(5);

    // Items Table Header
    $pdf->SetFillColor(230, 230, 230);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(10, 8, '#', 1, 0, 'C', true);
    $pdf->Cell(80, 8, 'Product Name', 1, 0, 'L', true);
    $pdf->Cell(30, 8, 'Unit Price', 1, 0, 'R', true);
    $pdf->Cell(20, 8, 'Qty', 1, 0, 'C', true);
    $pdf->Cell(50, 8, 'Amount', 1, 1, 'R', true);

    // Items Table Body
    $pdf->SetFont('Arial', '', 10);
    $counter = 1;
    foreach ($items as $item) {
        $pdf->Cell(10, 7, $counter++, 1, 0, 'C');
        $pdf->Cell(80, 7, $item['product_name'], 1, 0, 'L');
        $pdf->Cell(30, 7, get_currency($dbh) . ' ' . number_format($item['unit_price'], 2), 1, 0, 'R');
        $pdf->Cell(20, 7, $item['quantity'], 1, 0, 'C');
        $pdf->Cell(50, 7, get_currency($dbh) . ' ' . number_format($item['total'], 2), 1, 1, 'R');
    }

    // Totals Section
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 10);

    // Actual Total
    $pdf->Cell(140, 7, 'Actual Total:', 0, 0, 'R');
    $pdf->Cell(50, 7, get_currency($dbh) . ' ' . number_format($summary['actual_total'], 2), 1, 1, 'R');

    // Discount
    if ($summary['discount'] > 0) {
        $pdf->Cell(140, 7, 'Discount:', 0, 0, 'R');
        $pdf->Cell(50, 7, '- ' . get_currency($dbh) . ' ' . number_format($summary['discount'], 2), 1, 1, 'R');
    }

    // Grand Total
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(140, 8, 'TOTAL:', 0, 0, 'R');
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(50, 8, get_currency($dbh) . ' ' . number_format($summary['total'], 2), 1, 1, 'R', true);

    // Cash Details (if cash payment)
    if ($summary['payment_type'] == 'CASH' && !empty($summary['cash_received'])) {
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(140, 7, 'Cash Received:', 0, 0, 'R');
        $pdf->Cell(50, 7, get_currency($dbh) . ' ' . number_format($summary['cash_received'], 2), 1, 1, 'R');

        $pdf->Cell(140, 7, 'Cash Change:', 0, 0, 'R');
        $pdf->Cell(50, 7, get_currency($dbh) . ' ' . number_format($summary['cash_change'], 2), 1, 1, 'R');
    }

    // Footer
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Cell(0, 5, 'Thank you for your business!', 0, 1, 'C');
    $pdf->Cell(0, 5, 'Powered by POS System', 0, 1, 'C');

    // Output PDF
    $pdf->Output('I', 'Receipt_' . $order_id . '.pdf');

} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}
?>