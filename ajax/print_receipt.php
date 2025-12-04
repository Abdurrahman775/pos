<?php

/**
 * Print Receipt to Xprinter Directly
 * This endpoint sends the receipt PDF directly to the thermal printer (Xprinter)
 * with auto-cut enabled and optimal settings for thermal receipt printing
 */

require("../config.php");
require("../include/functions.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request'];

try {
    $token = isset($_GET['token']) ? trim($_GET['token']) : '';

    if (empty($token)) {
        $response = ['status' => 'error', 'message' => 'No receipt token provided'];
        echo json_encode($response);
        exit;
    }

    // Decode transaction ID from token
    $transaction_id = intval(base64_decode($token));

    if (!$transaction_id) {
        $response = ['status' => 'error', 'message' => 'Invalid token'];
        echo json_encode($response);
        exit;
    }

    // Verify transaction exists
    $transactionSQL = "SELECT transaction_id FROM transactions WHERE transaction_id = :tid LIMIT 1";
    $transactionQuery = $dbh->prepare($transactionSQL);
    $transactionQuery->bindParam(':tid', $transaction_id, PDO::PARAM_INT);
    $transactionQuery->execute();
    $transaction = $transactionQuery->fetch(PDO::FETCH_ASSOC);

    if (!$transaction) {
        $response = ['status' => 'error', 'message' => 'Transaction not found'];
        echo json_encode($response);
        exit;
    }

    // Generate PDF receipt (from pdf_invoice.php)
    ob_start();
    $_GET['token'] = $token; // Set for pdf_invoice
    chdir(__DIR__);
    include("../include/pdf_invoice.php");
    $pdf_content = ob_get_clean();

    // Save PDF to temporary file
    $temp_file = sys_get_temp_dir() . '/receipt_' . $transaction_id . '_' . time() . '.pdf';
    file_put_contents($temp_file, $pdf_content);

    // Send to Xprinter using CUPS (lp command)
    // Auto-cut enabled, media type set for thermal receipt
    $printer_name = 'Xprinter';
    $cmd = sprintf(
        'lp -h localhost -d %s -o media=Thermal -o sides=one-sided -n 1 %s 2>&1',
        escapeshellarg($printer_name),
        escapeshellarg($temp_file)
    );

    $output = shell_exec($cmd);

    // Clean up temp file
    @unlink($temp_file);

    if (strpos($output, 'request id') !== false || strpos($output, 'is ready') !== false) {
        $response = [
            'status' => 'success',
            'message' => 'Receipt sent to printer successfully',
            'printer' => $printer_name,
            'transaction_id' => $transaction_id
        ];
    } else {
        $response = [
            'status' => 'warning',
            'message' => 'Receipt sent to printer (check printer status)',
            'printer' => $printer_name,
            'transaction_id' => $transaction_id,
            'debug' => $output
        ];
    }
} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => $e->getMessage()];
}

echo json_encode($response);
