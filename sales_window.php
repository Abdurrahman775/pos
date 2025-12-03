<?php
require("config.php");
require("include/functions.php");
require("include/admin_authentication.php");
require("include/admin_constants.php");
require("include/customer_cart.php");

$productSQL = "select * from products where qty_in_stock != 0 and is_active= 1 order by name asc";
$productQuery = $dbh->prepare($productSQL);
$productQuery->execute();
$productResult = $productQuery->fetch(PDO::FETCH_ASSOC);

if ($_REQUEST['command'] == 'add') {
    $product_id = $_REQUEST['product_id'];
    addtocart($product_id);
}

if ($_REQUEST['command'] == 'delete' && $_REQUEST['product_id'] > 0) {
    remove_product($_REQUEST['product_id']);
}

if ($_REQUEST['command'] == 'clear') {
    unset($_SESSION['cart']);
}

if (isset($_REQUEST['command']) && $_REQUEST['command'] == 'update') {
    // Initialize cart if not set
    $_SESSION['cart'] = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $msg = ''; // Initialize message variable

    $max = count($_SESSION['cart']);
    for ($i = 0; $i < $max; $i++) {
        // Check if cart item exists at current index
        if (!isset($_SESSION['cart'][$i]['product_id']))
            continue;

        $product_id = $_SESSION['cart'][$i]['product_id'];

        // Validate request parameter exists
        if (!isset($_REQUEST['product' . $product_id]))
            continue;

        $quantity = intval($_REQUEST['product' . $product_id]);
        $db_quantity = get_product_quantity($dbh, $product_id);

        if ($quantity > $db_quantity) {
            $msg .= "<div align=\"center\" style=\"margin-bottom:10px;\">" . convert_product_id($dbh, $product_id) . " quantity exceeded! Available is " . $db_quantity . "</div>";
        } elseif ($quantity > 0 && $quantity <= 999) {
            $_SESSION['cart'][$i]['quantity'] = $quantity;
        } else {
            $msg .= "<div align=\"center\" style=\"margin-bottom:10px;\">Product quantity can not be zero (0)</div>";
        }
    }
}

if ($_REQUEST['command'] == 'finalise') {
    $customer = $_REQUEST['customer'];
    if ($customer == '') {
        $customer = "Customer";
    }
    $payment_type = $_REQUEST['payment_type'];
    $discount = $_REQUEST['discount'];
    $total = $_REQUEST['hidden_order_total'];
    $items_count = $_REQUEST['hidden_items_count'];

    if ($payment_type == strtoupper("CASH")) {
        $cash_received = $_REQUEST['cash_received'];
        $cash_change = $_REQUEST['hidden_cash_change'];
        $payment_ref = NULL;
    } else if ($payment_type == strtoupper("POS")) {
        $payment_ref = $_REQUEST['payment_ref'];
        $cash_received = NULL;
        $cash_change = NULL;
    }

    $order_id = date("dmyHis");
    $actual_total = 0;

    try {
        $dbh->beginTransaction(); // Turn off autocommit mode
        $saleSQL = "insert into sales (order_id, product_id, unit_price, quantity, total, reg_by, reg_date) values (:order_id, :product_id, :unit_price, :quantity, :total, :reg_by, :reg_date)";
        $items = count($_SESSION['cart']);
        for ($i = 0; $i < $items; $i++) {
            $item_product_id = $_SESSION['cart'][$i]['product_id'];
            $item_quantity = $_SESSION['cart'][$i]['quantity'];
            $item_unit_price = get_product_price($dbh, $item_product_id);
            $item_total = $item_unit_price * $item_quantity;
            $actual_total += $item_total;

            $saleQuery = $dbh->prepare($saleSQL);
            $saleQuery->bindParam(':order_id', $order_id, PDO::PARAM_STR);
            $saleQuery->bindParam(':product_id', $item_product_id, PDO::PARAM_INT);
            $saleQuery->bindParam(':unit_price', $item_unit_price, PDO::PARAM_STR);
            $saleQuery->bindParam(':quantity', $item_quantity, PDO::PARAM_INT);
            $saleQuery->bindParam(':total', $item_total, PDO::PARAM_STR);
            $saleQuery->bindParam(':reg_by', $admin, PDO::PARAM_STR);
            $saleQuery->bindParam(':reg_date', $now, PDO::PARAM_STR);
            $saleQuery->execute();
        }

        $updateSQL = "update products set qty_in_stock= (qty_in_stock - :quantity), updated_by= :updated_by where id= :product_id";
        $update_items = count($_SESSION['cart']);
        for ($x = 0; $x < $update_items; $x++) {
            $update_product_id = $_SESSION['cart'][$x]['product_id'];
            $update_quantity = $_SESSION['cart'][$x]['quantity'];

            $updateQuery = $dbh->prepare($updateSQL);
            $updateQuery->bindParam(':product_id', $update_product_id, PDO::PARAM_INT);
            $updateQuery->bindParam(':quantity', $update_quantity, PDO::PARAM_INT);
            $updateQuery->bindParam(':updated_by', $admin, PDO::PARAM_STR);
            $updateQuery->execute();
        }

        $summarySQL = "insert into sales_summary (order_id, customer, payment_type, payment_ref, actual_total, discount, total, cash_received, cash_change, items_count, reg_by, reg_date) values (:order_id, :customer, :payment_type, :payment_ref, :actual_total, :discount, :total, :cash_received, :cash_change, :items_count, :reg_by, :reg_date)";
        $summaryQuery = $dbh->prepare($summarySQL);
        $summaryQuery->bindParam(':order_id', $order_id, PDO::PARAM_STR);
        $summaryQuery->bindParam(':customer', $customer, PDO::PARAM_STR);
        $summaryQuery->bindParam(':payment_type', $payment_type, PDO::PARAM_STR);
        $summaryQuery->bindParam(':payment_ref', $payment_ref, PDO::PARAM_STR);
        $summaryQuery->bindParam(':actual_total', $actual_total, PDO::PARAM_STR);
        $summaryQuery->bindParam(':discount', $discount, PDO::PARAM_STR);
        $summaryQuery->bindParam(':total', $total, PDO::PARAM_STR);
        $summaryQuery->bindParam(':cash_received', $cash_received, PDO::PARAM_STR);
        $summaryQuery->bindParam(':cash_change', $cash_change, PDO::PARAM_STR);
        $summaryQuery->bindParam(':items_count', $items_count, PDO::PARAM_INT);
        $summaryQuery->bindParam(':reg_by', $admin, PDO::PARAM_STR);
        $summaryQuery->bindParam(':reg_date', $now, PDO::PARAM_STR);
        $summaryQuery->execute();

        if ($payment_type == strtoupper("CASH")) {
            $cashSQL = "update accounts set balance= (balance + :total) where account_type= 'CASH'";
            $cashQuery = $dbh->prepare($cashSQL);
            $cashQuery->bindParam(':total', $total, PDO::PARAM_STR);
            $cashQuery->execute();
        } else if ($payment_type == strtoupper("POS")) {
            $posSQL = "update accounts set balance= (balance + :total) where account_type= 'POS'";
            $posQuery = $dbh->prepare($posSQL);
            $posQuery->bindParam(':total', $total, PDO::PARAM_STR);
            $posQuery->execute();
        }

        if ($dbh->commit() == TRUE) {

            //print_invoice($dbh, $order_id);  //Uncomment this line if your printer is attached to your computer
            unset($_SESSION['cart']);
            $success = '<script>$(function(){ bootbox.alert({centerVertical:true,size:"small",message:"Record saved",buttons:{ok:{label:"<i class=\'fa fa-check\'></i> OK",className:"btn-success btn-sm"}},callback:function(){bootbox.confirm({centerVertical:true,size:"small",message:"Print Receipt?",buttons:{cancel:{label:"<i class=\'fa fa-times\'></i> Cancel",className:"btn-danger btn-sm"},confirm:{label:"<i class=\'fa fa-check\'></i> OK",className:"btn-success btn-sm"}},callback:function(printConfirm){if(printConfirm){window.open("include/pdf_invoice.php?' . http_build_query(array('token' => base64_encode($order_id))) . '","_blank")}else{window.location.href="sales_window.php"}}})}})});</script>';
            //$success = '<script>$(function(){ bootbox.alert({centerVertical:true,size:"small",message:"Record saved",buttons:{ok:{label:"<i class=\'fa fa-check\'></i> OK",className:"btn-success btn-sm"}},function(){bootbox.confirm({centerVertical:true,size:"small",message:"Print Receipt?",buttons:{cancel:{label:"<i class=\'fa fa-times\'></i> Cancel",className:"btn-danger btn-sm"},confirm:{label:"<i class=\'fa fa-check\'></i> OK",className:"btn-success btn-sm"}},function(printConfirm){if(printConfirm){window.open("../include/pdf_invoice.php?'.http_build_query(array('token'=>base64_encode($order_id)).'","_blank")}else{window.location.href="sales_window.php"}})});});</script>';
        } else {
            $dbh->rollback();
            $error = "<script>$(function(){ bootbox.alert({ centerVertical: true, size: 'small', message: 'ERROR! Try again', buttons: { ok: { label: \"<i class='fa fa-check'></i> OK\", className: 'btn-danger btn-sm' } } }); });</script>";
        }
    } catch (PDOException $e) {
        $dbh->rollback();
        $error = "<script>$(function(){ bootbox.alert({ centerVertical: true, size: 'small', message: 'System Error!', buttons: { ok: { label: \"<i class='fa fa-check'></i> OK\", className: 'btn-danger btn-sm' } } }); });</script>";
        //echo $e->getMessage();
    }
}
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
    var payment_ref = document.cart_form.payment_ref.value;

    var err = 0;

    if(payment_type == "CASH") {
    if(!cash_received) {
    $('#cash_received').qtip('show'); err += 1;
    } else if(!number_pattern.test(cash_received)) {
    $('#cash_received').qtip({ style: { classes: 'qtip-red' }, content: { text: 'Invalid' }, show: { ready: true } });
    err += 1;
    }
    } else if(payment_type == "POS") {
    if(!payment_ref) {
    $('#payment_ref').qtip('show'); err += 1;
    } else if(!number_pattern.test(payment_ref)) {
    $('#payment_ref').qtip({ style: { classes: 'qtip-red' }, content: { text: 'Invalid' }, show: { ready: true } });
    err += 1;
    }
    }

    if(err != 0) {
    return false;
    }
    */
    }

    function addtocart(product_id) {
    document.product_form.product_id.value = product_id;
    document.product_form.command.value = 'add';
    document.product_form.submit();
    }

    function del(product_id) {
    bootbox.confirm({
    centerVertical: true,
    size: "small",
    title: "",
    message: "Are you sure to delete?",
    buttons: {
    cancel: {
    label: '<i class="fa fa-times"></i> No',
    className: 'btn btn-danger btn-sm'
    },
    confirm: {
    label: '<i class="fa fa-check"></i> Yes',
    className: 'btn btn-success btn-sm'
    }
    },
    callback: function (output) {
    if (output) {
    document.cart_form.product_id.value = product_id;
    document.cart_form.command.value = 'delete';
    document.cart_form.submit();
    }
    }
    });
    }

    function clearCart() {
    bootbox.confirm({
    centerVertical: true,
    size: "small",
    title: "",
    message: "Are you sure to clear cart?",
    buttons: {
    cancel: {
    label: '<i class="fa fa-times"></i> No',
    className: 'btn btn-danger btn-sm'
    },
    confirm: {
    label: '<i class="fa fa-check"></i> Yes',
    className: 'btn btn-success btn-sm'
    }
    },
    callback: function (output) {
    if (output) {
    document.cart_form.command.value = 'clear';
    document.cart_form.submit();
    }
    }
    });
    }

    function update_cart() {
    document.cart_form.command.value = 'update';
    document.cart_form.submit();
    }


    $(document).ready(function () {

    // var dataTable = $('#data-grid').DataTable({ // from all datatable
    var dataTable = $("#datatable").DataTable({
    "responsive": true,
    "ordering": false,
    "pageLength": 10,
    //"lengthChange": false,

    "language": {
    search: "_INPUT_",
    searchPlaceholder: "Search Product"
    },
    "processing": true,
    //"aoColumnDefs": [ {"sClass": "text-center", "aTargets": [0,3,4,5,6]} ],
    "aoColumnDefs": [{ "sClass": "text-center", "aTargets": [2, 3] }, { "sClass": "text-right", "aTargets": [1] }, {
    "bSortable": false, "aTargets": [0, 1, 2, 3] }],
    "serverSide": true,
    "bLengthChange": false,
    "ajax": {
    url: "datatables/sales_window.php",
    type: "POST",
    error: function () {
    $(".data-grid-error").html("");
    $("#data-grid").append('<tbody class="data-grid-error text-center">
        <tr>
            <th colspan="4">Could not fetch records</th>
        </tr>
    </tbody>');
    $("#data-grid_processing").css("display", "none");
    }
    },
    });

    $('.dataTables_filter input').focus();

    // Custom search functionality
    $('#custom-search').on('keyup', function () {
    var value = $(this).val();
    dataTable.search(value).draw();
    });

    //remove the default 'Search' text for all DataTable search boxes
    $.extend(true, $.fn.dataTable.defaults, {
    language: {
    search: ""
    }
    });
    //custom format of Search boxes
    $('[type=search]').each(function () {
    $(this).attr("placeholder", "Search Product...");
    });

    $.validator.addMethod(
    "alphanumeric",
    function (value, element) {
    return /^[A-Za-z0-9]+$/.test(value);
    },
    "Only letters and numbers are allowed"
    );

    // $("#cash_received, #payment_ref").qtip({ content:{ text: "Required" } });

    // $('#amount_collected, #ref_no').hide();

    if ($('.cash:radio[name=payment_type]').is(':checked')) {
    $('#payment_ref').val('');
    $('#ref_no').hide();
    $('#amount_collected').show();
    } else if ($('.pos:radio[name=payment_type]').is(':checked')) {
    $('#cash_received').val('');
    $('#amount_collected').hide();
    $('#ref_no').show();
    }

    $('input[name=payment_type]:radio').change(function () {
    if ($('.cash:radio[name=payment_type]').is(':checked')) {
    $('#payment_ref, #discount').val('');
    $('#discount').val(0);
    $('#ref_no').hide();
    $('#amount_collected').show();
    } else if ($('.pos:radio[name=payment_type]').is(':checked')) {
    var order_total = '<?php echo get_order_total($dbh); ?>';
    var currency = '<?php echo get_currency($dbh); ?>';
    $('#cash_received, #hidden_order_total, #hidden_cash_change, #discount').val('');
    $('#display_order_total').text('');
    $('#discount').val(0);
    $('#display_order_total').text(currency + order_total);
    $('#hidden_order_total').val(order_total);
    $('#display_cash_change').text('N0.00');
    $('#amount_collected').hide();
    $('#ref_no').show();
    }
    });

    $('#cash_received').on('keyup', function () {
    var cash_received = $(this).val();
    var order_total = '<?php echo get_order_total($dbh); ?>';
    var discount = $('#discount').val();
    var cash_change = cash_received - (order_total - discount);
    var final_total = (order_total - discount);
    var currency = '<?php echo get_currency($dbh); ?>';

    if (cash_received == '') {
    $('#display_order_total').text(currency + final_total.toFixed(2));
    $('#display_cash_change').text('N0.00');
    $('#hidden_order_total').val(final_total);
    $('#hidden_cash_change').val(0);
    } else if (cash_received) {
    $('#display_order_total').text(currency + final_total.toFixed(2));
    $('#display_cash_change').text(currency + cash_change.toFixed(2));
    $('#hidden_order_total').val(final_total);
    $('#hidden_cash_change').val(cash_change);
    } else {
    $('#display_order_total').text('<?php echo get_currency($dbh) . number_format(get_order_total($dbh), 2); ?>');
    $('#display_cash_change').text(currency + cash_change.toFixed(2));
    $('#hidden_order_total').val(final_total);
    $('#hidden_cash_change').val(cash_change);
    }
    });

    $('#discount').on('keyup', function () {
    var payment_type = document.cart_form.payment_type.value;
    var discount = $(this).val();
    var cash_received = $('#cash_received').val();
    var order_total = '<?php echo get_order_total($dbh); ?>';
    if (payment_type == "CASH") {
    var cash_change = cash_received - (order_total - discount);
    } else if (payment_type == "POS") {
    var cash_change = 0;
    }
    var final_total = (order_total - discount);
    var currency = '<?php echo get_currency($dbh); ?>';

    if (discount) {
    $('#display_order_total').text(currency + final_total.toFixed(2));
    $('#display_cash_change').text(currency + cash_change.toFixed(2));
    $('#hidden_order_total').val(final_total);
    $('#hidden_cash_change').val(cash_change);
    } else {
    $('#discount').val('');
    $('#display_order_total').text('<?php echo get_currency($dbh) . number_format(get_order_total($dbh), 2); ?>');
    $('#display_cash_change').text(currency + cash_change.toFixed(2));
    $('#hidden_order_total').val(final_total);
    $('#hidden_cash_change').val(cash_change);
    }
    });

    $('#finaliseTrigger').on("click", function (e) {
    e.preventDefault();
    bootbox.confirm({
    centerVertical: true,
    size: "small",
    title: "",
    message: "Finalise Order? This task is irreversible",
    buttons: {
    cancel: {
    label: '<i class="fa fa-times"></i> No',
    className: 'btn btn-danger btn-sm'
    },
    confirm: {
    label: '<i class="fa fa-check"></i> Yes',
    className: 'btn btn-success btn-sm'
    }
    },
    callback: function (output) {
    if (output) {
    if (validateOrder() != false) {
    document.cart_form.payment_type.value;
    document.cart_form.cash_received.value;
    document.cart_form.payment_ref.value;
    document.cart_form.customer.value;
    document.cart_form.discount.value;
    document.cart_form.hidden_order_total.value;
    document.cart_form.hidden_cash_change.value;
    document.cart_form.command.value = 'finalise';
    document.cart_form.submit();
    }
    }
    }
    });
    });

    });
    </script>
    <style>
        .dataTables_filter {
            position: relative;
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
    <?php
    // Displaying Notifications
    echo "{$success} {$error}";
    ?>
    <div class="left-sidenav">
        <div class="brand">
            <?php require('template/brand_admin.php'); ?>
        </div>
        <div class="menu-content h-100" data-simplebar>
            <?php require('include/menus.php'); ?>
        </div>
    </div>
    <div class="page-wrapper">
        <div class="topbar">
            <?php require('template/top_nav_admin.php'); ?>
        </div>

        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="row">
                                <div class="col">
                                    <h4 class="page-title">Sales Window</h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                                        <li class="breadcrumb-item">Sales Management</li>
                                        <li class="breadcrumb-item active">Sales Window</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- body here -->

                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h2 align="center" style="margin-bottom:0;">Our Products</h2>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <input type="search" class="form-control form-control-sm" id="custom-search"
                                            placeholder="Search Product Name">
                                        <form name="product_form" method="post" autocomplete="off">
                                            <input type="hidden" name="product_id" id="product_id">
                                            <input type="hidden" name="command" id="command">
                                        </form>
                                        <div class="container"><!-- Datatable -->
                                            <table id="datatable"
                                                class="table table-sm table-striped table-bordered dt-responsive nowrap"
                                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th class="text-head-left">Product Name</th>
                                                        <th class="text-head-center">Unit Price</th>
                                                        <th class="text-head-center">Qty</th>
                                                        <th style="width:70px;"><i class="fa fa-arrows-h fa-lg"></i>
                                                        </th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div><!-- End of Datetable -->
                                    </div>
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
                                    <form name="cart_form" id="cart_form" method="post" autocomplete="off">
                                        <div style="color:#F00"><?php echo $msg; ?></div>
                                        <table id="datatable"
                                            class="table table-sm table-striped table-bordered dt-responsive nowrap"
                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;"> <?php
                                            if (is_array($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                                                echo "<thead><tr bgcolor=\"#FFFFFF\" style=\"font-weight:bold\">
                                        <th class=\"text-head-center\">#</th>
                                        <th class=\"text-head-left\">Name</th>
                                        <th class=\"text-head-left\">Unit Price</th>
                                        <th class=\"text-head-left\">Qty</th>
                                        <th class=\"text-head-left\">Amount</th>
                                        <th class=\"text-head-center\"><a href=\"javascript:clearCart()\" title=\"Clear Customer Cart\">Del</a></th>
                                        </tr></thead>";
                                                $max = count($_SESSION['cart']);
                                                for ($i = 0; $i < $max; $i++) {
                                                    $product_id = $_SESSION['cart'][$i]['product_id'];
                                                    $quantity = $_SESSION['cart'][$i]['quantity'];
                                                    $product_name = convert_product_id($dbh, $product_id);
                                                    if ($quantity == 0)
                                                        continue;
                                                    ?>
                                                    <tr bgcolor="#FFFFFF">
                                                        <td width="6%" align="center"><?php echo $i + 1; ?></td>
                                                        <td width="37%"><?php echo $product_name; ?></td>
                                                        <td width="21%" align="right">
                                                            <?php echo get_currency($dbh) . number_format(get_product_price($dbh, $product_id), 2); ?>
                                                        </td>
                                                        <td width="10%"><input type="number"
                                                                name="product<?php echo $product_id; ?>"
                                                                value="<?php echo $quantity; ?>" onChange="update_cart()"
                                                                style="width:40px !important; height:25px" /></td>
                                                        <td width="17%" align="right">
                                                            <?php echo get_currency($dbh) . number_format(get_product_price($dbh, $product_id) * $quantity, 2); ?>
                                                        </td>
                                                        <td width="9%" align="center"><a
                                                                href="javascript:del(<?php echo $product_id; ?>)"
                                                                title="Delete Item"><i class="fa fa-trash fa-lg"></i></a></td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                <tr bgcolor="#FFFFFF">
                                                    <td colspan="3" width="64%" style="font-weight:bold;">TOTAL</td>
                                                    <td width="10%" align="center" style="font-weight:bold;">
                                                        <?php echo get_quantity_total($dbh); ?>
                                                    </td>
                                                    <td width="17%" align="right" style="font-weight:bold;">
                                                        <?php echo get_currency($dbh) . number_format(get_order_total($dbh), 2); ?>
                                                    </td>
                                                    <td width="9%" align="center">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">
                                                        <div style="font-weight:bold;"><span
                                                                style=" display:inline-table; width:120px;">Payment</span> :
                                                            <input type="radio" name="payment_type" value="CASH"
                                                                id="payment_type_0" class="cash" checked>Cash
                                                            <input type="radio" name="payment_type" value="POS"
                                                                id="payment_type_1" class="pos">POS
                                                        </div>
                                                        <div id="amount_collected"
                                                            style="font-weight:bold; margin-top:10px;"><span
                                                                style="display:inline-table; width:120px;">Cash
                                                                Received</span> : <input type="text" name="cash_received"
                                                                id="cash_received"
                                                                style="height:20px !important; width:150px !important;"
                                                                onkeypress="return numberOnly(event)"></div>
                                                        <div id="ref_no" style="font-weight:bold; margin-top:10px;"><span
                                                                style="display:inline-table; width:120px;">Ref. No.</span> :
                                                            <input type="text" name="payment_ref" id="payment_ref"
                                                                style="height:20px !important; width:150px !important;">
                                                        </div>
                                                        <div style="font-weight:bold; margin-top:10px;"><span
                                                                style=" display:inline-table; width:120px;">Name</span> :
                                                            <input type="text" name="customer" id="customer"
                                                                value="Customer"
                                                                style="height:20px !important; width:150px !important;">
                                                        </div>
                                                        <div style="font-weight:bold; margin-top:10px;"><span
                                                                style=" display:inline-table; width:120px;">Discount</span>
                                                            : <input type="text" id="discount" name="discount" value="0"
                                                                style="height:20px !important; width:150px !important;"
                                                                onkeypress="return numberOnly(event)" /></div>
                                                        <div style="font-weight:bold; margin-top:10px;"><span
                                                                style=" display:inline-table; width:120px;">Cash
                                                                Change</span> : <span id="display_cash_change">N0.00</span>
                                                        </div>
                                                        <div style="font-size:larger; font-weight:bold; margin-top:10px;">
                                                            <span
                                                                style="display:inline-table; width:120px; color:#F00;">Order
                                                                Total</span> : <span
                                                                id="display_order_total"><?php echo get_currency($dbh) . number_format(get_order_total($dbh), 2); ?></span>
                                                        </div>
                                                        <div style="text-align: center;"><button id="finaliseTrigger"
                                                                style="width: 150px; height: 40px;">Place Order</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            } else {
                                                echo "<tr bgColor='#FFFFFF'><td align=\"center\" style=\"color:#F00;\">Customer cart is empty!</td>";
                                            }
                                            ?>
                                        </table>
                                        <input type="hidden" id="hidden_order_total" name="hidden_order_total" />
                                        <input type="hidden" id="hidden_cash_change" name="hidden_cash_change" />
                                        <input type="hidden" id="hidden_items_count" name="hidden_items_count"
                                            value="<?php echo get_quantity_total($dbh); ?>" />
                                        <input type="hidden" name="product_id" />
                                        <input type="hidden" name="command" />
                                    </form>
                                </div><!-- End of Customer Tab -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="footer text-center text-sm-left">
                <?php include('template/copyright.php'); ?> <span
                    class="d-none d-sm-inline-block float-right"><?php include('template/developed_by.php'); ?></span>
            </footer>
        </div>
    </div>
</body>

</html>