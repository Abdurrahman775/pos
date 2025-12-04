<?php
require("config.php");
require("include/functions.php");
require("include/admin_authentication.php");
require("include/customer_cart.php");

$msg = "";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Point of Sale System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Point of Sales System" name="description" />
    <meta content="S & I IT Partners Ltd" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="template/assets/images/favicon.ico">
    <!-- App css -->
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/jquery-ui.min.css" rel="stylesheet" type="text/css">
    <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="template/plugins/datatables/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="template/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <!-- jQuery  -->
    <script src="template/assets/js/jquery.min.js"></script>
    <script src="template/assets/js/bootstrap.bundle.min.js"></script>
    <script src="template/assets/js/metismenu.min.js"></script>
    <script src="template/assets/js/waves.js"></script>
    <script src="template/assets/js/feather.min.js"></script>
    <script src="template/assets/js/jquery-ui.min.js"></script>
    <script src="template/plugins/bootbox/bootbox.min.js"></script>
    <script src="template/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="template/plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="template/assets/js/app.js" defer></script>
    <script src="template/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="template/plugins/datatables/dataTables.bootstrap5.min.js"></script>
    <script src="template/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="template/plugins/datatables/responsive.bootstrap4.min.js"></script>
    <script>
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

        function update_cart(product_id, qty) {
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
                    }
                }
            });
        }

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
                    } else {
                        bootbox.alert(response.message);
                    }
                }
            });
        }

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

        function numberOnly(e) {
            var charCode = (e.which) ? e.which : e.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) return false;
            return true;
        }

        $(document).ready(function() {
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

            // Unified Product Search & Barcode Logic
            $("#product_input").autocomplete({
                source: "ajax/search_products.php",
                minLength: 2,
                autoFocus: false,
                select: function(event, ui) {
                    $.ajax({
                        url: "ajax/handle_cart.php",
                        method: "POST",
                        data: {
                            action: "add",
                            product_id: ui.item.id
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.status == "success") {
                                refreshCart();
                                $("#product_input").val('');
                            } else {
                                bootbox.alert(response.message);
                            }
                        }
                    });
                    return false;
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li>").append("<div>" + item.label + "</div>").appendTo(ul);
            };

            $('#product_input').keypress(function(e) {
                if (e.which == 13) {
                    var menu = $("#product_input").autocomplete("widget");
                    if (menu.is(":visible") && menu.find(".ui-state-active").length > 0) {
                        return;
                    }
                    e.preventDefault();
                    var barcode = $(this).val().trim();
                    if (barcode != "") {
                        $("#product_input").autocomplete("close");
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
                                        centerVertical: true,
                                        size: "small",
                                        message: "<i class='fa fa-times-circle text-danger'></i> " + response.message,
                                        callback: function() {
                                            $('#product_input').focus();
                                        }
                                    });
                                    $('#product_input').val('');
                                }
                            }
                        });
                    }
                }
            });

            $('#product_input').focus();
            $(document).on('click', function(e) {
                if (!$(e.target).closest('input, textarea, select, button, a, .ui-menu-item-wrapper').length) {
                    $('#product_input').focus();
                }
            });

            // Customer autocomplete search
            $("#customer").autocomplete({
                source: 'ajax/search_customers.php',
                minLength: 2,
                autoFocus: false,
                select: function(event, ui) {
                    $("#customer").val(ui.item.value);
                    $("#customer_id").val(ui.item.id);
                    return false;
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li>").append("<div><strong>" + item.label + "</strong></div>").appendTo(ul);
            };

            $('#finaliseTrigger').on('click', function(e) {
                e.preventDefault();
                var payment_type = $('input[name="payment_type"]:checked').val();
                var cash_received = $('#cash_received').val();
                var payment_ref = $('#payment_ref').val();
                if (payment_type == "CASH" && !cash_received) {
                    bootbox.alert("Cash received is required");
                    return;
                }
                if (payment_type == "POS" && !payment_ref) {
                    bootbox.alert("Payment reference is required");
                    return;
                }

                bootbox.confirm({
                    centerVertical: true,
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
                            var formData = $('#cart_form').serialize();
                            formData += '&action=finalise';
                            $.ajax({
                                url: "ajax/process_sale.php",
                                method: "POST",
                                data: formData,
                                dataType: "json",
                                success: function(response) {
                                    if (response.status == "success") {
                                        bootbox.alert({
                                            centerVertical: true,
                                            size: "small",
                                            message: "<i class='fa fa-check'></i> " + response.message,
                                            callback: function() {
                                                bootbox.confirm("Print Receipt?", function(printConfirm) {
                                                    if (printConfirm) {
                                                        // Send receipt to Xprinter via auto-print endpoint
                                                        $.ajax({
                                                            url: "ajax/print_receipt.php?token=" + response.token,
                                                            method: "GET",
                                                            dataType: "json",
                                                            success: function(printResp) {
                                                                bootbox.alert({
                                                                    centerVertical: true,
                                                                    size: "small",
                                                                    message: "<i class='fa fa-print'></i> " + (printResp.message || "Receipt printed to Xprinter"),
                                                                    callback: function() {
                                                                        resetSalesWindow();
                                                                    }
                                                                });
                                                            },
                                                            error: function() {
                                                                // Fallback: open PDF in browser if server printing fails
                                                                window.open("include/pdf_invoice.php?token=" + response.token, "_blank");
                                                                resetSalesWindow();
                                                            }
                                                        });
                                                    } else {
                                                        resetSalesWindow();
                                                    }
                                                });
                                            }
                                        });
                                    } else {
                                        bootbox.alert(response.message);
                                    }
                                }
                            });
                        }
                    }
                });
            });

            // Event delegation for dynamic elements
            $('#cart_form').on('change', 'input[name="payment_type"]', function() {
                var payment_type = $(this).val();
                if (payment_type == 'CASH') {
                    $('#ref_no').hide();
                } else {
                    $('#ref_no').show();
                }
            });

            $('#cart_form').on('keyup', '#cash_received, #discount', function() {
                var cash_received = parseFloat($('#cash_received').val()) || 0;
                var discount = parseFloat($('#discount').val()) || 0;
                var order_total = parseFloat($('#hidden_order_total').val()) || 0;

                var final_total = order_total - discount;
                var cash_change = cash_received - final_total;

                $('#display_cash_change').text('N' + cash_change.toFixed(2));
                $('#hidden_cash_change').val(cash_change);
            });
        });

        function resetSalesWindow() {
            refreshCart();
            $('#cash_received, #payment_ref, #discount').val('');
            $('#display_cash_change').text('N0.00');
            $('#customer').val('Customer').attr('data-customer-id', '');
            $('#customer_id').val('');
            $('#product_input').focus();
        }

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
                        $('#cart_form').html(response.html);
                        // Re-apply logic after refresh
                        var payment_type = $('input[name="payment_type"]:checked').val();
                        if (payment_type == 'CASH') {
                            $('#ref_no').hide();
                        } else {
                            $('#ref_no').show();
                        }
                    }
                }
            });
        }
    </script>
    <style>
        .dataTables_filter label {
            text-align: left;
            float: left;
            display: none;
        }

        .dataTables_filter input {
            width: 250px;
            height: 32px;
            background: #fcfcfc;
            border: 1px solid #aaa;
            border-radius: 5px;
            box-shadow: 0 0 3px #ccc, 0 10px 15px #ebebeb inset;
            text-indent: 10px;
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
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Sales Window</h4>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h2 align="center" style="margin-bottom:0;">Products</h2>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-sm table-striped table-bordered dt-responsive nowrap" style="width: 100%;">
                                        <thead>
                                            <tr bgcolor="#FFFFFF" style="font-weight:bold">
                                                <th class="text-head-center">#</th>
                                                <th class="text-head-center">Name</th>
                                                <th class="text-head-center">Unit Price</th>
                                                <th class="text-head-center">Qty</th>
                                                <th style="width:70px;"><i class="fa fa-arrows-h fa-lg"></i></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h2 align="center" style="margin-bottom:0;">Customer Cart</h2>
                            </div>
                            <div class="card-body">
                                <div id="customer_tab">
                                    <!-- Unified Product Search & Barcode Input -->
                                    <div class="mt-2 mb-2">
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-barcode"></i></span></div>
                                            <input type="text" class="form-control" id="product_input" placeholder="Scan Barcode or Search Product" autocomplete="off">
                                        </div>
                                    </div>
                                    <form name="cart_form" id="cart_form" method="post" autocomplete="off">
                                        <div style="color:#F00"><?php echo $msg; ?></div>
                                        <table class="table table-sm table-striped table-bordered dt-responsive nowrap" style="width: 100%;">
                                            <?php if (is_array($_SESSION['cart']) && !empty($_SESSION['cart'])) { ?>
                                                <thead>
                                                    <tr bgcolor="#FFFFFF" style="font-weight:bold">
                                                        <th class="text-head-center">#</th>
                                                        <th class="text-head-left">Name</th>
                                                        <th class="text-head-left">Unit Price</th>
                                                        <th class="text-head-left">Qty</th>
                                                        <th class="text-head-left">Amount</th>
                                                        <th class="text-head-center"><a href="javascript:clearCart()" title="Clear Customer Cart">Del</a></th>
                                                    </tr>
                                                </thead>
                                                <?php
                                                $max = count($_SESSION['cart']);
                                                for ($i = 0; $i < $max; $i++) {
                                                    $product_id = $_SESSION['cart'][$i]['product_id'];
                                                    $quantity = $_SESSION['cart'][$i]['quantity'];
                                                    $product_name = convert_product_id($dbh, $product_id);
                                                    if ($quantity == 0) continue;
                                                ?>
                                                    <tr bgcolor="#FFFFFF">
                                                        <td width="6%" align="center"><?php echo $i + 1; ?></td>
                                                        <td width="37%"><?php echo $product_name; ?></td>
                                                        <td width="21%" align="right"><?php echo get_currency($dbh) . number_format(get_product_price($dbh, $product_id), 2); ?></td>
                                                        <td width="10%"><input type="number" name="product<?php echo $product_id; ?>" value="<?php echo $quantity; ?>" onChange="update_cart(<?php echo $product_id; ?>, this.value)" style="width:40px !important; height:25px" /></td>
                                                        <td width="17%" align="right"><?php echo get_currency($dbh) . number_format(get_product_price($dbh, $product_id) * $quantity, 2); ?></td>
                                                        <td width="9%" align="center"><a href="javascript:del(<?php echo $product_id; ?>)" title="Delete Item"><i class="fa fa-trash fa-lg"></i></a></td>
                                                    </tr>
                                                <?php } ?>
                                                <tr bgcolor="#FFFFFF">
                                                    <td colspan="3" width="64%" style="font-weight:bold;">TOTAL</td>
                                                    <td width="10%" align="center" style="font-weight:bold;"><?php echo get_quantity_total($dbh); ?></td>
                                                    <td width="17%" align="right" style="font-weight:bold;"><?php echo get_currency($dbh) . number_format(get_order_total($dbh), 2); ?></td>
                                                    <td width="9%" align="center">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">
                                                        <div style="font-weight:bold;"><span style=" display:inline-table; width:120px;">Payment</span> : <input type="radio" name="payment_type" value="CASH" id="payment_type_0" class="cash" checked>Cash <input type="radio" name="payment_type" value="POS" id="payment_type_1" class="pos">POS</div>
                                                        <div id="amount_collected" style="font-weight:bold; margin-top:10px;"><span style="display:inline-table; width:120px;">Amount Received</span> : <input type="text" name="cash_received" id="cash_received" style="height:20px !important; width:150px !important;" onkeypress="return numberOnly(event)"></div>
                                                        <div id="ref_no" style="font-weight:bold; margin-top:10px; display:none;"><span style="display:inline-table; width:120px;">Ref. No.</span> : <input type="text" name="payment_ref" id="payment_ref" style="height:20px !important; width:150px !important;"></div>
                                                        <div style="font-weight:bold; margin-top:10px;"><span style=" display:inline-table; width:120px;">Customer</span> : <input type="text" name="customer" id="customer" value="Customer" placeholder="Search or enter customer name" style="height:20px !important; width:150px !important;" autocomplete="off"><input type="hidden" id="customer_id" name="customer_id" value=""></div>
                                                        <div style="font-weight:bold; margin-top:10px;"><span style=" display:inline-table; width:120px;">Discount</span> : <input type="text" id="discount" name="discount" value="0" style="height:20px !important; width:150px !important;" onkeypress="return numberOnly(event)" /></div>
                                                        <div style="font-weight:bold; margin-top:10px;"><span style=" display:inline-table; width:120px;">Cash Change</span> : <span id="display_cash_change">N0.00</span></div>
                                                        <div style="font-size:larger; font-weight:bold; margin-top:10px;"><span style="display:inline-table; width:120px; color:#F00;">Order Total</span> : <span id="display_order_total"><?php echo get_currency($dbh) . number_format(get_order_total($dbh), 2); ?></span></div>
                                                        <div style="text-align: center;"><button id="finaliseTrigger" style="width: 150px; height: 40px;">Place Order</button></div>
                                                        <input type="hidden" id="hidden_order_total" name="hidden_order_total" value="<?php echo get_order_total($dbh); ?>" />
                                                        <input type="hidden" id="hidden_cash_change" name="hidden_cash_change" />
                                                    </td>
                                                </tr>
                                            <?php } else {
                                                echo "<tr bgColor='#FFFFFF'><td align=\"center\" style=\"color:#F00;\">Customer cart is empty!</td></tr>";
                                            } ?>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="footer text-center text-sm-left">
                <?php include('template/copyright.php'); ?> <span class="d-none d-sm-inline-block float-right"><?php include('template/developed_by.php'); ?></span>
            </footer>
        </div>
    </div>
</body>

</html>