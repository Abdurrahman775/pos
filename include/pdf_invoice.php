<?php
require_once('../config.php');
require_once('functions.php');
require_once('../template/plugins/fpdf/fpdf.php');

// Define custom class to add helper methods
class PDF_Receipt extends FPDF
{
    function NbLines($w, $txt)
    {
        // Computes the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

// Get order ID from token
$order_id = isset($_GET['token']) ? base64_decode($_GET['token']) : '';

if (empty($order_id)) {
    die('Invalid order ID');
}

try {
    // Try to fetch from transactions table first (new flow), then fallback to sales_summary (old flow)
    $transactionSQL = "SELECT * FROM transactions WHERE transaction_id = :order_id LIMIT 1";
    $transactionQuery = $dbh->prepare($transactionSQL);
    $transactionQuery->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $transactionQuery->execute();
    $transaction = $transactionQuery->fetch(PDO::FETCH_ASSOC);

    // Fallback to sales_summary for backward compatibility
    if (!$transaction) {
        $summarySQL = "SELECT * FROM sales_summary WHERE order_id = :order_id LIMIT 1";
        $summaryQuery = $dbh->prepare($summarySQL);
        $summaryQuery->bindParam(':order_id', $order_id, PDO::PARAM_STR);
        $summaryQuery->execute();
        $summary = $summaryQuery->fetch(PDO::FETCH_ASSOC);
        if (!$summary) {
            die('Order not found');
        }
    } else {
        // Map transaction fields to summary format for template compatibility
        $summary = [
            'order_id' => $transaction['transaction_id'],
            'customer' => 'Customer ID: ' . ($transaction['customer_id'] ?? 'Walk-in'),
            'total' => $transaction['total_amount'],
            'actual_total' => $transaction['subtotal'],
            'tax_amount' => $transaction['tax_amount'],
            'discount' => $transaction['discount_amount'],
            'payment_type' => $transaction['payment_method'],
            'reg_date' => $transaction['created_at'],
            'reg_by' => $transaction['created_by']
        ];
    }

    // Get transaction items
    if ($transaction) {
        $itemsSQL = "SELECT ti.*, p.name as product_name 
                     FROM transaction_items ti 
                     LEFT JOIN products p ON ti.product_id = p.id 
                     WHERE ti.transaction_id = :order_id 
                     ORDER BY ti.item_id ASC";
        $itemsQuery = $dbh->prepare($itemsSQL);
        $itemsQuery->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $itemsQuery->execute();
        $items = $itemsQuery->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Get sales items (old flow)
        $salesSQL = "SELECT s.*, p.name as product_name 
                     FROM sales s 
                     LEFT JOIN products p ON s.product_id = p.id 
                     WHERE s.order_id = :order_id 
                     ORDER BY s.id ASC";
        $salesQuery = $dbh->prepare($salesSQL);
        $salesQuery->bindParam(':order_id', $order_id, PDO::PARAM_STR);
        $salesQuery->execute();
        $items = $salesQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    // Calculate dynamic height
    // Base height (Header + Info + Table Header + Totals + Footer) approx 120mm
    // + Item height (approx 10mm per item to be safe for wrapping)
    $page_width = 80;
    $margin = 3;
    $content_width = $page_width - ($margin * 2);
    $item_count = count($items);
    $estimated_height = 140 + ($item_count * 10); // Slightly increased base height

    // Create PDF with custom size for thermal printer (80mm receipt width)
    $pdf = new PDF_Receipt('P', 'mm', array($page_width, $estimated_height));
    $pdf->AddPage();
    $pdf->SetMargins($margin, $margin, $margin);
    $pdf->SetAutoPageBreak(false);

    // Set colors: white background, black text (no background)
    $pdf->SetFillColor(255, 255, 255); // White background
    $pdf->SetTextColor(0, 0, 0);       // Black text
    $pdf->SetDrawColor(0, 0, 0);       // Black lines

    // Company/Store Header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($content_width, 5, 'S & I IT PARTNERS LTD', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell($content_width, 4, 'Point of Sale System', 0, 1, 'C');
    $pdf->Ln(2);

    // Invoice Title
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($content_width, 6, 'SALES RECEIPT', 0, 1, 'C');
    $pdf->Ln(2);

    // Order Information
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(20, 4, 'Order ID:', 0, 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 4, $order_id, 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(20, 4, 'Date:', 0, 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 4, date('d/m/Y H:i', strtotime($summary['reg_date'])), 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(20, 4, 'Customer:', 0, 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 4, $summary['customer'], 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(20, 4, 'Cashier:', 0, 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 4, $summary['reg_by'], 0, 1);

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(20, 4, 'Payment:', 0, 0);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(0, 4, $summary['payment_type'], 0, 1);

    if (!empty($summary['payment_ref'])) {
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(20, 4, 'Ref. No:', 0, 0);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, 4, $summary['payment_ref'], 0, 1);
    }
    $pdf->Ln(3);

    // Items Table Header
    // Widths: # (5), Name (35), Qty (8), Price (13), Total (13) = 74mm
    $w_no = 5;
    $w_name = 35;
    $w_qty = 8;
    $w_price = 13;
    $w_total = 13;

    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell($w_no, 5, '#', 1, 0, 'C');
    $pdf->Cell($w_name, 5, 'Item', 1, 0, 'L');
    $pdf->Cell($w_qty, 5, 'Qty', 1, 0, 'C');
    $pdf->Cell($w_price, 5, 'Price', 1, 0, 'R');
    $pdf->Cell($w_total, 5, 'Total', 1, 1, 'R');

    // Items Table Body
    $pdf->SetFont('Arial', '', 7);
    $counter = 1;
    foreach ($items as $item) {
        // Calculate height for this row based on name length
        $nb = $pdf->NbLines($w_name, $item['product_name']);
        $h = 5 * $nb;

        // Check if we need a page break (unlikely with custom height but good practice)
        if ($pdf->GetY() + $h > $pdf->GetPageHeight() - 20) {
            $pdf->AddPage($pdf->CurOrientation, $pdf->CurPageSize);
        }

        // Save current position
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        // Print MultiCell for Name
        $pdf->SetXY($x + $w_no, $y);
        $pdf->MultiCell($w_name, 5, $item['product_name'], 1, 'L');

        // Print other cells
        $pdf->SetXY($x, $y);
        $pdf->Cell($w_no, $h, $counter++, 1, 0, 'C');

        $pdf->SetXY($x + $w_no + $w_name, $y);
        $pdf->Cell($w_qty, $h, $item['quantity'], 1, 0, 'C');

        $pdf->SetXY($x + $w_no + $w_name + $w_qty, $y);
        $pdf->Cell($w_price, $h, number_format($item['unit_price'], 0), 1, 0, 'R');

        $pdf->SetXY($x + $w_no + $w_name + $w_qty + $w_price, $y);
        $pdf->Cell($w_total, $h, number_format($item['total'], 2), 1, 1, 'R');

        // Move to next line
        $pdf->SetXY($x, $y + $h);
    }

    // Totals Section
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 8);

    // Right align totals
    $pdf->Cell($content_width - 25, 5, 'Actual Total:', 0, 0, 'R');
    $pdf->Cell(25, 5, get_currency($dbh) . number_format($summary['actual_total'], 2), 0, 1, 'R');

    if ($summary['discount'] > 0) {
        $pdf->Cell($content_width - 25, 5, 'Discount:', 0, 0, 'R');
        $pdf->Cell(25, 5, '-' . get_currency($dbh) . number_format($summary['discount'], 2), 0, 1, 'R');
    }

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell($content_width - 25, 6, 'TOTAL:', 0, 0, 'R');
    $pdf->Cell(25, 6, get_currency($dbh) . number_format($summary['total'], 2), 0, 1, 'R');

    // Cash Details
    if ($summary['payment_type'] == 'CASH' && !empty($summary['cash_received'])) {
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($content_width - 25, 5, 'Cash Received:', 0, 0, 'R');
        $pdf->Cell(25, 5, get_currency($dbh) . number_format($summary['cash_received'], 2), 0, 1, 'R');

        $pdf->Cell($content_width - 25, 5, 'Change:', 0, 0, 'R');
        $pdf->Cell(25, 5, get_currency($dbh) . number_format($summary['cash_change'], 2), 0, 1, 'R');
    }
    // POS Details
    if ($summary['payment_type'] == 'POS' && !empty($summary['cash_received'])) {
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($content_width - 25, 5, 'Amount Paid:', 0, 0, 'R');
        $pdf->Cell(25, 5, get_currency($dbh) . number_format($summary['cash_received'], 2), 0, 1, 'R');

        if ($summary['cash_change'] > 0) {
            $pdf->Cell($content_width - 25, 5, 'Change:', 0, 0, 'R');
            $pdf->Cell(25, 5, get_currency($dbh) . number_format($summary['cash_change'], 2), 0, 1, 'R');
        }
    }

    // Footer
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'I', 7);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell($content_width, 4, 'Thank you for your business!', 0, 1, 'C');
    $pdf->Cell($content_width, 4, 'Powered by POS System', 0, 1, 'C');

    // Add extra space for thermal printer auto-cut
    $pdf->Ln(3);

    // Output PDF to browser for printing
    // The printer will auto-cut when using Xprinter thermal printer
    $pdf->Output('I', 'Receipt_' . $order_id . '.pdf');
} catch (PDOException $e) {
    die('Error: ' . $e->getMessage());
}
