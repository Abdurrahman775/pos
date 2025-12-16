<?php

/**
 * Transactions List Page
 * View all transactions with filters and search
 */
require("config.php");
require("include/functions.php");
require_once("include/pos_functions.php");
require("include/authentication.php");
require("include/admin_constants.php");

// Require transactions permission
require_permission('transactions');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Transactions | POS System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="template/assets/images/favicon.ico">
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="template/plugins/datatables/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="template/plugins/datatables/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="template/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <style>
        /* DataTables styling */
        .dataTables_wrapper {
            padding: 10px 0;
        }
        
        .dataTables_length select {
            margin: 0 5px;
        }
        
        .dataTables_filter input {
            margin-left: 10px;
        }
        
        /* Column alignment */
        .text-right {
            text-align: right !important;
        }
        
        .text-center {
            text-align: center !important;
        }
        
        /* Status badges */
        .badge-success {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .badge-secondary {
            background-color: #6c757d;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
        }
        
        /* Action buttons */
        .btn-group .btn-sm {
            padding: 3px 8px;
            font-size: 12px;
            margin-right: 3px;
        }
        
        /* Date range inputs */
        #date-range {
            display: flex;
            gap: 10px;
        }
        
        #date-range input {
            flex: 1;
        }
        
        /* Table row hover */
        #transactionsTable tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }
        
        /* Responsive table */
        @media (max-width: 768px) {
            .dataTables_wrapper .dataTables_filter {
                float: none !important;
                text-align: left !important;
                margin-top: 10px;
            }
            
            .dataTables_wrapper .dataTables_length {
                float: none !important;
                text-align: left !important;
            }
        }
        
        /* Filter section */
        .filter-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }
        
        .filter-section label {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        /* Card styling */
        .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }
        
        .card-title {
            margin-bottom: 0;
            color: #495057;
        }
    </style>
</head>

<body class="dark-sidenav">
    <?php include('include/sidebar.php'); ?>
    <div class="page-wrapper">
        <div class="topbar"><?php require('template/top_nav_admin.php'); ?></div>
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Transactions</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Transactions</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">All Transactions</h5>
                            </div>
                            <div class="card-body">
                                <!-- Filter Section -->
                                <div class="filter-section mb-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Start Date</label>
                                            <input type="date" class="form-control" name="start_date" id="start_date" />
                                        </div>
                                        <div class="col-md-3">
                                            <label>End Date</label>
                                            <input type="date" class="form-control" name="end_date" id="end_date" />
                                        </div>
                                        <div class="col-md-3">
                                            <label>Cashier</label>
                                            <select class="form-control" id="cashier_id">
                                                <option value="">All Cashiers</option>
                                                <?php
                                                $cashiers_sql = "SELECT id, username FROM admins WHERE is_active = 1 ORDER BY username";
                                                $cashiers_query = $dbh->prepare($cashiers_sql);
                                                $cashiers_query->execute();
                                                while ($cashier = $cashiers_query->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<option value='" . $cashier['id'] . "'>" . $cashier['username'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label>Payment Method</label>
                                            <select class="form-control" id="payment_method">
                                                <option value="">All Methods</option>
                                                <option value="CASH">Cash</option>
                                                <option value="POS">POS</option>
                                                <option value="MIXED">Mixed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12 text-right">
                                            <button class="btn btn-primary" id="filterBtn">
                                                <i class="fas fa-filter"></i> Apply Filters
                                            </button>
                                            <button class="btn btn-secondary" id="resetBtn">
                                                <i class="fas fa-redo"></i> Reset
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Transactions Table -->
                                <div class="table-responsive">
                                    <table id="transactionsTable" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Date</th>
                                                <th>Customer</th>
                                                <th>Subtotal</th>
                                                <th>Tax</th>
                                                <th>Discount</th>
                                                <th>Total</th>
                                                <th>Payment</th>
                                                <th>Status</th>
                                                <th>Cashier</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data will be loaded via DataTables -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="10" style="text-align:right">Total:</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="footer text-center text-sm-left">
                <?php include('template/copyright.php'); ?>
            </footer>
        </div>
    </div>

    <script src="template/assets/js/jquery.min.js"></script>
    <script src="template/assets/js/bootstrap.bundle.min.js"></script>
    <script src="template/assets/js/metismenu.min.js"></script>
    <script src="template/assets/js/waves.js"></script>
    <script src="template/assets/js/feather.min.js"></script>
    <script src="template/plugins/bootbox/bootbox.min.js"></script>
    <!-- <script src="datatables/datatables.min.js"></script> -->
    <script src="template/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="template/plugins/datatables/dataTables.bootstrap5.min.js"></script>
    <script src="template/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="template/plugins/datatables/responsive.bootstrap4.min.js"></script>
    <script src="template/assets/js/app.js"></script>


    <script>
        $(document).ready(function() {
            // Initialize DataTable with server-side processing
            var table = $('#transactionsTable').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [[1, "desc"]], // Sort by date column (index 1)
                "ajax": {
                    "url": "datatables/transactions.php",
                    "type": "POST",
                    "data": function(d) {
                        // Add custom filter parameters for server-side processing
                        return {
                            start_date: $('#start_date').val(),
                            end_date: $('#end_date').val(),
                            cashier_id: $('#cashier_id').val(),
                            payment_method: $('#payment_method').val(),
                            search: {
                                value: d.search.value
                            },
                            order: d.order,
                            start: d.start,
                            length: d.length,
                            draw: d.draw,
                            columns: d.columns
                        };
                    },
                    "error": function(xhr, error, thrown) {
                        console.error('DataTables error:', error, thrown);
                        // Show user-friendly error
                        if (xhr.responseText) {
                            try {
                                var response = JSON.parse(xhr.responseText);
                                bootbox.alert('Error: ' + (response.error || 'Failed to load transactions'));
                            } catch (e) {
                                bootbox.alert('Server error occurred. Please try again.');
                            }
                        }
                    }
                },
                "columns": [
                    { "data": 0, "orderable": true },
                    { "data": 1, "orderable": true },
                    { "data": 2, "orderable": true },
                    { "data": 3, "orderable": false, "className": "text-right" },
                    { "data": 4, "orderable": false, "className": "text-right" },
                    { "data": 5, "orderable": false, "className": "text-right" },
                    { "data": 6, "orderable": false, "className": "text-right" },
                    { "data": 7, "orderable": true },
                    { "data": 8, "orderable": true },
                    { "data": 9, "orderable": true },
                    { 
                        "data": 10, 
                        "orderable": false, 
                        "className": "text-center",
                        "render": function(data, type, row) {
                            // Extract transaction ID from the first column data
                            var transactionId = row[0] ? row[0].replace('#', '') : '';
                            if (!transactionId) return '';
                            
                            return '<div class="btn-group" role="group">' +
                                   '<button type="button" class="btn btn-sm btn-primary" onclick="printReceipt(\'' + transactionId + '\')" title="Print Receipt">' +
                                   '<i class="fas fa-print"></i>' +
                                   '</button>' +
                                   '<button type="button" class="btn btn-sm btn-info" onclick="viewReceipt(\'' + transactionId + '\')" title="View Receipt">' +
                                   '<i class="fas fa-eye"></i>' +
                                   '</button>' +
                                   '</div>';
                        }
                    }
                ],
                "language": {
                    "emptyTable": "No transactions found",
                    "zeroRecords": "No matching transactions found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ transactions",
                    "infoEmpty": "Showing 0 to 0 of 0 transactions",
                    "infoFiltered": "(filtered from _MAX_ total transactions)",
                    "loadingRecords": "Loading transactions...",
                    "processing": '<div class="spinner-border spinner-border-sm" role="status"><span class="sr-only">Loading...</span></div> Processing...',
                    "search": "Search:",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    },
                    "lengthMenu": "Show _MENU_ entries"
                },
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                "pageLength": 10,
                "drawCallback": function(settings) {
                    // Update total footer if needed
                    var api = this.api();
                    var total = api.column(6, {page: 'current'}).data().sum();
                    $(api.column(6).footer()).html(
                        'NGN ' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')
                    );
                }
            });

            // Calculate sum for footer
            $.fn.dataTable.Api.register('sum()', function() {
                return this.flatten().reduce(function(a, b) {
                    // Remove currency symbol and commas, then convert to number
                    var val = typeof b === 'string' ? 
                        parseFloat(b.replace(/[^0-9.-]+/g, '')) : 
                        parseFloat(b);
                    return isNaN(val) ? a : a + val;
                }, 0);
            });

            // Filter button click handler
            $('#filterBtn').click(function() {
                table.draw();
            });

            // Reset button click handler
            $('#resetBtn').click(function() {
                $('#start_date').val('');
                $('#end_date').val('');
                $('#cashier_id').val('');
                $('#payment_method').val('');
                table.search('').draw();
            });

            // Enter key in date fields triggers filter
            $('#start_date, #end_date').keypress(function(e) {
                if (e.which === 13) {
                    table.draw();
                }
            });

            // Change events on filter inputs
            $('#cashier_id, #payment_method').change(function() {
                table.draw();
            });

            // Auto-refresh every 60 seconds
            var refreshInterval = setInterval(function() {
                table.ajax.reload(null, false); // false means don't reset paging
            }, 60000);

            // Clean up interval on page unload
            $(window).on('beforeunload', function() {
                clearInterval(refreshInterval);
            });
        });

        function printReceipt(transactionId) {
            // Get the transaction token from the database
            $.ajax({
                url: "ajax/get_transaction_token.php",
                method: "POST",
                data: { transaction_id: transactionId },
                dataType: "json",
                success: function(tokenResponse) {
                    if (tokenResponse.status == "success" && tokenResponse.token) {
                        // Get receipt HTML
                        $.ajax({
                            url: "ajax/receipt_preview.php?token=" + tokenResponse.token,
                            method: "GET",
                            dataType: "json",
                            success: function(response) {
                                if (response.status == "success") {
                                    // Create hidden iframe for printing
                                    var printFrame = document.createElement('iframe');
                                    printFrame.style.position = 'absolute';
                                    printFrame.style.width = '0px';
                                    printFrame.style.height = '0px';
                                    printFrame.style.border = 'none';
                                    document.body.appendChild(printFrame);
                                    
                                    // Write receipt HTML to iframe
                                    var printDoc = printFrame.contentWindow.document;
                                    printDoc.open();
                                    printDoc.write('<html><head><title>Receipt</title>');
                                    printDoc.write('<style>');
                                    printDoc.write('@media print { @page { size: 80mm auto; margin: 0; } body { margin: 0; padding: 10px; } }');
                                    printDoc.write('</style>');
                                    printDoc.write('</head><body>');
                                    printDoc.write(response.html);
                                    printDoc.write('</body></html>');
                                    printDoc.close();
                                    
                                    // Trigger print
                                    printFrame.contentWindow.focus();
                                    setTimeout(function() {
                                        printFrame.contentWindow.print();
                                        // Remove iframe after printing
                                        setTimeout(function() {
                                            document.body.removeChild(printFrame);
                                        }, 1000);
                                    }, 500);
                                } else {
                                    bootbox.alert("Error loading receipt: " + response.message);
                                }
                            },
                            error: function() {
                                bootbox.alert("Failed to load receipt for printing");
                            }
                        });
                    } else {
                        bootbox.alert("Error: " + (tokenResponse.message || 'Transaction not found'));
                    }
                },
                error: function() {
                    bootbox.alert("Failed to retrieve transaction");
                }
            });
        }

        function viewReceipt(transactionId) {
            // First, get the transaction token from the database
            $.ajax({
                url: "ajax/get_transaction_token.php",
                method: "POST",
                data: { transaction_id: transactionId },
                dataType: "json",
                success: function(tokenResponse) {
                    if (tokenResponse.status == "success" && tokenResponse.token) {
                        // Now fetch receipt preview with the token
                        $.ajax({
                            url: "ajax/receipt_preview.php?token=" + tokenResponse.token,
                            method: "GET",
                            dataType: "json",
                            success: function(response) {
                                if (response.status == "success") {
                                    // Show receipt in modal popup
                                    bootbox.dialog({
                                        title: "Receipt #" + String(response.transaction_id).padStart(6, '0'),
                                        centerVertical: true,
                                        size: "large",
                                        message: '<div style="max-height: 500px; overflow-y: auto; background: white; padding: 20px;">' + response.html + '</div>',
                                        buttons: {
                                            close: {
                                                label: 'Close',
                                                className: 'btn-secondary btn-sm'
                                            },
                                            print: {
                                                label: '<i class="fa fa-print"></i> Print',
                                                className: 'btn-primary btn-sm',
                                                callback: function() {
                                                    // Create hidden iframe for printing
                                                    var printFrame = document.createElement('iframe');
                                                    printFrame.style.position = 'absolute';
                                                    printFrame.style.width = '0px';
                                                    printFrame.style.height = '0px';
                                                    printFrame.style.border = 'none';
                                                    document.body.appendChild(printFrame);
                                                    
                                                    // Write receipt HTML to iframe
                                                    var printDoc = printFrame.contentWindow.document;
                                                    printDoc.open();
                                                    printDoc.write('<html><head><title>Receipt</title>');
                                                    printDoc.write('<style>');
                                                    printDoc.write('@media print { @page { size: 80mm auto; margin: 0; } body { margin: 0; padding: 10px; } }');
                                                    printDoc.write('</style>');
                                                    printDoc.write('</head><body>');
                                                    printDoc.write(response.html);
                                                    printDoc.write('</body></html>');
                                                    printDoc.close();
                                                    
                                                    // Trigger print
                                                    printFrame.contentWindow.focus();
                                                    setTimeout(function() {
                                                        printFrame.contentWindow.print();
                                                        // Remove iframe after printing
                                                        setTimeout(function() {
                                                            document.body.removeChild(printFrame);
                                                        }, 1000);
                                                    }, 500);
                                                    
                                                    return false; // Keep modal open
                                                }
                                            }
                                        }
                                    });
                                } else {
                                    bootbox.alert("Error: " + (response.message || 'Failed to load receipt'));
                                }
                            },
                            error: function() {
                                bootbox.alert("Failed to load receipt preview");
                            }
                        });
                    } else {
                        bootbox.alert("Error: " + (tokenResponse.message || 'Transaction not found'));
                    }
                },
                error: function() {
                    bootbox.alert("Failed to retrieve transaction");
                }
            });
        }

        // Optional: Export function
        function exportTransactions(format) {
            var url = 'export_transactions.php?format=' + format;
            
            // Add current filters to export
            var start_date = $('#start_date').val();
            var end_date = $('#end_date').val();
            var cashier_id = $('#cashier_id').val();
            var payment_method = $('#payment_method').val();
            
            if (start_date) url += '&start_date=' + start_date;
            if (end_date) url += '&end_date=' + end_date;
            if (cashier_id) url += '&cashier_id=' + cashier_id;
            if (payment_method) url += '&payment_method=' + payment_method;
            
            window.open(url, '_blank');
        }
    </script>
</body>

</html>