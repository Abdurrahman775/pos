<?php
require("config.php");
require("include/functions.php");
require("include/admin_authentication.php");

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$msg = "";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Sales Window | POS System</title>
    <meta name="viewport" content="width=device-width, shrink-to-fit=no">
    <meta content="Point of Sale System" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="template/assets/images/favicon.ico">
    <!-- App css -->
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/jquery-ui.min.css" rel="stylesheet" type="text/css">
    <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="template/plugins/datatables/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="template/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    
    <style>
        .dataTables_filter input {
            width: 250px;
            height: 32px;
            background: #fcfcfc;
            border: 1px solid #aaa;
            border-radius: 5px;
            padding: 5px 10px;
        }
        #product_input:focus {
            border-color: #007bff;
            outline: none;
        }
    </style>
</head>

<body class="dark-sidenav">
    <?php include('include/sidebar.php'); ?>
    <div class="page-wrapper">
        <div class="topbar">
            <?php require('template/top_nav_admin.php'); ?>
        </div>
        <div class="page-content">
            <div class="container-fluid">
                <!-- Page Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Sales Window</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Sales Window</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="row">
                    <!-- Left Panel: Product List -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Products</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-sm table-striped table-bordered nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Price</th>
                                                <th>Stock</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Panel: Cart -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Customer Cart</h4>
                            </div>
                            <div class="card-body">
                                <!-- Barcode/Search Input -->
                                <div class="mb-3">
                                    <label for="product_input"><strong>Scan Barcode or Search Product:</strong></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                        </div>
                                        <input type="text" class="form-control" id="product_input" placeholder="Barcode or Product Name" autocomplete="off">
                                    </div>
                                </div>

                                <!-- Cart Table -->
                                <div id="cart_container">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr class="table-active">
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Price</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                                <th><a href="javascript:clearCart()" class="text-danger" title="Clear Cart">Del</a></th>
                                            </tr>
                                        </thead>
                                        <tbody id="cart_body">
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">Customer cart is empty!</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <footer class="footer text-center text-sm-left">
                <?php include('template/copyright.php'); ?>
                <span class="d-none d-sm-inline-block float-right"><?php include('template/developed_by.php'); ?></span>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="template/assets/js/jquery.min.js"></script>
    <script src="template/assets/js/bootstrap.bundle.min.js"></script>
    <script src="template/assets/js/metismenu.min.js"></script>
    <script src="template/assets/js/waves.js"></script>
    <script src="template/assets/js/feather.min.js"></script>
    <script src="template/assets/js/jquery-ui.min.js"></script>
    <script src="template/plugins/bootbox/bootbox.min.js"></script>
    <script src="template/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="template/plugins/datatables/dataTables.bootstrap5.min.js"></script>
    <script src="template/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="template/plugins/datatables/responsive.bootstrap4.min.js"></script>
    <script src="template/assets/js/app.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var dataTable = $("#datatable").DataTable({
                "responsive": true,
                "ordering": false,
                "pageLength": 10,
                "language": {
                    search: "_INPUT_",
                    searchPlaceholder: "Search Product"
                },
                "processing": true,
                "serverSide": true,
                "bLengthChange": false,
                "ajax": {
                    url: "datatables/sales_window.php",
                    type: "POST"
                }
            });

            // Auto-focus on product input
            $('#product_input').focus();
            
            // Refocus on click anywhere
            $(document).on('click', function(e) {
                if (!$(e.target).closest('input, textarea, select, button, a, .ui-menu-item-wrapper').length) {
                    $('#product_input').focus();
                }
            });

            // Product autocomplete
            $("#product_input").autocomplete({
                source: "ajax/search_products.php",
                minLength: 2,
                autoFocus: false,
                select: function(event, ui) {
                    addtocart(ui.item.id);
                    $(this).val('');
                    return false;
                }
            });

            // Barcode scanner (Enter key)
            $('#product_input').keypress(function(e) {
                if (e.which == 13) {
                    var menu = $(this).autocomplete("widget");
                    if (menu.is(":visible") && menu.find(".ui-state-active").length > 0) {
                        return;
                    }
                    
                    e.preventDefault();
                    var barcode = $(this).val().trim();
                    
                    if (barcode !== "") {
                        $(this).autocomplete("close");
                        $.ajax({
                            url: "ajax/handle_cart.php",
                            method: "POST",
                            data: {
                                action: "add_by_barcode",
                                barcode: barcode
                            },
                            dataType: "json",
                            success: function(response) {
                                if (response.status == "success") {
                                    $('#product_input').val('');
                                    refreshCart();
                                } else {
                                    bootbox.alert({
                                        size: "small",
                                        message: "<i class='fa fa-times-circle text-danger'></i> " + response.message,
                                        callback: function() {
                                            $('#product_input').val('').focus();
                                        }
                                    });
                                }
                            }
                        });
                    }
                }
            });

            // Initial cart load
            refreshCart();
        });

        // Add to cart
        function addtocart(product_id) {
            $.ajax({
                url: "ajax/handle_cart.php",
                method: "POST",
                data: {
                    action: "add",
                    product_id: product_id
                },
                dataType: "json",
                success: function(response) {
                    if (response.status == "success") {
                        refreshCart();
                    } else {
                        bootbox.alert(response.message);
                    }
                }
            });
        }

        // Update cart quantity
        function update_cart(product_id, qty) {
            qty = parseInt(qty);
            if (qty < 0) {
                bootbox.alert({
                    size: "small",
                    message: "<i class='fa fa-times-circle text-danger'></i> Negative quantities are not accepted",
                    callback: function() {
                        refreshCart();
                    }
                });
                return;
            }
            
            $.ajax({
                url: "ajax/handle_cart.php",
                method: "POST",
                data: {
                    action: "update",
                    product_id: product_id,
                    quantity: qty
                },
                dataType: "json",
                success: function(response) {
                    if (response.status == 'success') {
                        refreshCart();
                    } else {
                        bootbox.alert(response.message);
                        refreshCart();
                    }
                }
            });
        }

        // Delete item
        function del(product_id) {
            $.ajax({
                url: "ajax/handle_cart.php",
                method: "POST",
                data: {
                    action: "delete",
                    product_id: product_id
                },
                dataType: "json",
                success: function(response) {
                    if (response.status == 'success') {
                        refreshCart();
                    }
                }
            });
        }

        // Clear cart
        function clearCart() {
            bootbox.confirm("Are you sure you want to clear the cart?", function(result) {
                if (result) {
                    $.ajax({
                        url: "ajax/handle_cart.php",
                        method: "POST",
                        data: {
                            action: "clear"
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.status == 'success') {
                                refreshCart();
                            }
                        }
                    });
                }
            });
        }

        // Refresh cart
        function refreshCart() {
            $.ajax({
                url: "ajax/handle_cart.php",
                method: "POST",
                data: {
                    action: "get_cart_html"
                },
                dataType: "json",
                success: function(response) {
                    if (response.status == 'success') {
                        $('#cart_body').html(response.html);
                        setupCartHandlers();
                    }
                }
            });
        }

        // Setup cart event handlers
        function setupCartHandlers() {
            // Payment type toggle
            $('input[name="payment_type"]').off('change').on('change', function() {
                var paymentType = $(this).val();
                
                if (paymentType === 'CASH') {
                    $('#cash_section').show();
                    $('#pos_section').hide();
                    $('#mixed_section').hide();
                } else if (paymentType === 'POS') {
                    $('#cash_section').hide();
                    $('#pos_section').show();
                    $('#mixed_section').hide();
                } else if (paymentType === 'MIXED') {
                    $('#cash_section').hide();
                    $('#pos_section').hide();
                    $('#mixed_section').show();
                }
            });

            // Customer type toggle
            $('input[name="customer_type"]').off('change').on('change', function() {
                if ($(this).val() === 'existing') {
                    $('#existing_customer').show();
                    $('#new_customer').hide();
                    $('#customer_search').focus();
                } else {
                    $('#existing_customer').hide();
                    $('#new_customer').show();
                    $('#customer_name_new').focus();
                    $('#customer_id').val('');
                    // Remove readonly when switching to new customer
                    $('#customer_search').val('').prop('readonly', false).css('background-color', '');
                }
            });

            // Customer autocomplete
            $("#customer_search").autocomplete({
                source: 'ajax/search_customers.php',
                minLength: 1,
                select: function(event, ui) {
                    $("#customer_search").val(ui.item.label);
                    $("#customer_id").val(ui.item.id);
                    // Make field readonly after selection
                    $("#customer_search").prop('readonly', true).css('background-color', '#e9ecef');
                    return false;
                }
            });

            // Cash change calculation
            $('#cash_received, #discount, #mixed_pos_amount, #mixed_cash_amount').off('keyup').on('keyup', function() {
                var cash_received = parseFloat($('#cash_received').val()) || 0;
                var discount = parseFloat($('#discount').val()) || 0;
                var mixed_pos_amount = parseFloat($('#mixed_pos_amount').val()) || 0;
                var mixed_cash_amount = parseFloat($('#mixed_cash_amount').val()) || 0;
                var order_total = parseFloat($('#hidden_order_total').val()) || 0;
                
                // Validate for negative values
                if (cash_received < 0) {
                    bootbox.alert({
                        size: "small",
                        message: "<i class='fa fa-times-circle text-danger'></i> Negative amounts are not accepted"
                    });
                    $('#cash_received').val('');
                    return;
                }
                
                if (discount < 0) {
                    bootbox.alert({
                        size: "small",
                        message: "<i class='fa fa-times-circle text-danger'></i> Negative discount is not accepted"
                    });
                    $('#discount').val('0');
                    return;
                }
                
                if (mixed_pos_amount < 0) {
                    bootbox.alert({
                        size: "small",
                        message: "<i class='fa fa-times-circle text-danger'></i> Negative POS amount is not accepted"
                    });
                    $('#mixed_pos_amount').val('');
                    return;
                }
                
                if (mixed_cash_amount < 0) {
                    bootbox.alert({
                        size: "small",
                        message: "<i class='fa fa-times-circle text-danger'></i> Negative cash amount is not accepted"
                    });
                    $('#mixed_cash_amount').val('');
                    return;
                }
                
                var final_total = order_total - discount;
                
                // Calculate change based on payment type
                var payment_type = $('input[name="payment_type"]:checked').val();
                var cash_change = 0;
                
                if (payment_type === 'CASH') {
                    cash_change = cash_received - final_total;
                } else if (payment_type === 'MIXED') {
                    var total_received = mixed_pos_amount + mixed_cash_amount;
                    cash_change = total_received - final_total;
                }
                
                $('#display_cash_change').text('<?php echo get_currency($dbh); ?>' + cash_change.toFixed(2));
                $('#hidden_cash_change').val(cash_change);
                $('#display_order_total').text('<?php echo get_currency($dbh); ?>' + final_total.toFixed(2));
            });

            // Place order
            $('#finaliseTrigger').off('click').on('click', function(e) {
                e.preventDefault();
                
                var payment_type = $('input[name="payment_type"]:checked').val();
                var cash_received = $('#cash_received').val();
                var payment_ref = $('#payment_ref').val();
                var mixed_pos_ref = $('#mixed_pos_ref').val();
                var mixed_pos_amount = $('#mixed_pos_amount').val();
                var mixed_cash_amount = $('#mixed_cash_amount').val();
                
                if (payment_type == "CASH" && !cash_received) {
                    bootbox.alert("Please enter amount received");
                    return;
                }
                
                if (payment_type == "POS" && !payment_ref) {
                    bootbox.alert("Please enter payment reference");
                    return;
                }
                
                if (payment_type == "MIXED") {
                    if (!mixed_pos_ref) {
                        bootbox.alert("Please enter POS reference number");
                        return;
                    }
                    if (!mixed_pos_amount && !mixed_cash_amount) {
                        bootbox.alert("Please enter at least one payment amount");
                        return;
                    }
                }
                
                bootbox.confirm({
                    size: "small",
                    message: "Are you sure you want to place this order?",
                    buttons: {
                        cancel: {
                            label: 'No',
                            className: 'btn-danger btn-sm'
                        },
                        confirm: {
                            label: 'Yes',
                            className: 'btn-success btn-sm'
                        }
                    },
                    callback: function(result) {
                        if (result) {
                            processOrder();
                        }
                    }
                });
            });
        }

        // Process order
        function processOrder() {
            var formData = {
                action: 'finalise',
                payment_type: $('input[name="payment_type"]:checked').val(),
                cash_received: $('#cash_received').val(),
                payment_ref: $('#payment_ref').val(),
                mixed_pos_ref: $('#mixed_pos_ref').val(),
                mixed_pos_amount: $('#mixed_pos_amount').val(),
                mixed_cash_amount: $('#mixed_cash_amount').val(),
                customer_type: $('input[name="customer_type"]:checked').val(),
                customer_id: $('#customer_id').val(),
                customer_name_new: $('#customer_name_new').val(),
                discount: $('#discount').val(),
                hidden_order_total: $('#hidden_order_total').val(),
                hidden_cash_change: $('#hidden_cash_change').val()
            };
            
            $.ajax({
                url: "ajax/process_sale.php",
                method: "POST",
                data: formData,
                dataType: "json",
                success: function(response) {
                    if (response.status == "success") {
                        // Show receipt preview
                        $.ajax({
                            url: "ajax/receipt_preview.php?token=" + response.token,
                            method: "GET",
                            dataType: "json",
                            success: function(previewResp) {
                                if (previewResp.status == "success") {
                                    bootbox.dialog({
                                        title: "Receipt Preview #" + String(previewResp.transaction_id).padStart(6, '0'),
                                        size: "large",
                                        message: '<div style="max-height: 500px; overflow-y: auto;">' + previewResp.html + '</div>',
                                        buttons: {
                                            cancel: {
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
                                                    printDoc.write(previewResp.html);
                                                    printDoc.write('</body></html>');
                                                    printDoc.close();
                                                    
                                                    // Wait for content to load, then print
                                                    printFrame.contentWindow.focus();
                                                    setTimeout(function() {
                                                        printFrame.contentWindow.print();
                                                        
                                                        // Remove iframe after printing
                                                        setTimeout(function() {
                                                            document.body.removeChild(printFrame);
                                                            resetSalesWindow();
                                                        }, 1000);
                                                    }, 500);
                                                    
                                                    return false; // Keep dialog open during print
                                                }
                                            }
                                        }
                                    });
                                } else {
                                    bootbox.alert("Error: " + previewResp.message);
                                    resetSalesWindow();
                                }
                            }
                        });
                    } else {
                        bootbox.alert("Error: " + response.message);
                    }
                }
            });
        }

        // Reset sales window
        function resetSalesWindow() {
            refreshCart();
            $('#product_input').val('').focus();
        }
    </script>
</body>

</html>
