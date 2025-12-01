<?php
require("config.php");
require("include/functions.php");
require("include/admin_authentication.php");
require("include/admin_constants.php");
require("template/plugins/fpdf/fpdf.php");

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $product_name = ucfirst(trim($_POST['product_name']));
    $description = (trim($_POST['description']));
    $barcode = !empty(trim($_POST['barcode'])) ? (trim($_POST['barcode'])) : NULL;
    $price = strtolower(trim($_POST['price']));
    $qty = strtolower(trim($_POST['qty']));
    $low_stock_alert = (trim($_POST['low_stock_alert']));

    // Validate Product Name
    if (empty($product_name)) {
        echo "<script>$('#product_name').addClass('is-invalid');</script>";
        echo "<script>$('#product_name_error').html('{$errorMessages['product_name']}');</script>";
//        $errorMessages[] = "Product Name is required.";
    } elseif (!preg_match('/^[A-Za-z0-9\s]+$/', $product_name)) {
        $errorMessages[] = "Invalid Product Name. Only letters and numbers are allowed.";
    } elseif(val_product_name($dbh, $product_name) != 0) {
          $errorMessages[] = "Product Exists";
        //  $product_name_err = "<script>$('#product_name').html('{$errorMessages['product_name']}');</script>";   
        $product_name_err = "Product Exists";
        }
    // Validate Description
    if (empty($description)) {
        $errorMessages[] = "Description is required.";
    }

    // Validate Price
    if (empty($price) || !is_numeric($price) || $price <= 0) {
        $errorMessages[] = "Invalid Price. Please enter a numeric value greater than 0.";
    }

    // Validate Quantity
    if (empty($qty) || !is_numeric($qty) || $qty <= 0) {
        $errorMessages[] = "Invalid Quantity. Please enter a numeric value greater than 0.";
    }

    // Validate Low Stock Alert
    if (empty($low_stock_alert) || !is_numeric($low_stock_alert) || $low_stock_alert < 0) {
        $errorMessages[] = "Invalid Low Stock Alert. Please enter a numeric value greater than or equal to 0.";
    }

    if (empty($errorMessages)) {
        
        try {
            
            $sql = "INSERT INTO products (product_name, description, barcode, price, qty_in_stock, low_stock_alert, reg_by, reg_date) VALUES (:product_name, :description, :barcode, :price, :qty_in_stock, :low_stock_alert, :reg_by, :reg_date)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':product_name', $product_name, PDO::PARAM_STR);
            $query->bindParam(':description', $description, PDO::PARAM_STR);
            $query->bindParam(':barcode', $barcode, PDO::PARAM_STR);
            $query->bindParam(':price', $price, PDO::PARAM_STR);
            $query->bindParam(':qty_in_stock', $qty, PDO::PARAM_INT);
            $query->bindParam(':low_stock_alert', $low_stock_alert, PDO::PARAM_INT);
            $query->bindParam(':reg_by', $admin, PDO::PARAM_STR);
            $query->bindParam(':reg_date', $now, PDO::PARAM_STR);
            $query->execute();

            if($query == TRUE) {
                $barcode = !empty($barcode) ? $barcode : null;
                $success = "<script>$(function(){ bootbox.alert({ centerVertical: true, size: 'small', message: 'Record saved', buttons: { ok: { label: \"<i class='fa fa-check'></i> OK\", className: 'btn-success btn-sm' } } }); });</script>";
            } else {
                $error = "<script>$(function(){ bootbox.alert({ centerVertical: true, size: 'small', message: 'ERROR! Try again', buttons: { ok: { label: \"<i class='fa fa-check'></i> OK\", className: 'btn-danger btn-sm' } } }); });</script>";
            }
        } catch (PDOException $e) {
            echo $err = $e->getMessage();
            $error = "<script>$(function(){ bootbox.alert({ centerVertical: true, size: 'small', message: 'System Error!', buttons: { ok: { label: \"<i class='fa fa-check'></i> OK\", className: 'btn-danger btn-sm' } } }); });</script>";
        }
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
        <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <link href="template/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
        <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
        <link href="template/plugins/datatables/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
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
        <script src="template/plugins/bootbox/bootbox.min.js"></script>
        <script src="template/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="template/plugins/datatables/dataTables.bootstrap5.min.js"></script>
        <script src="template/plugins/datatables/dataTables.responsive.min.js"></script>
        <script src="template/plugins/datatables/responsive.bootstrap4.min.js"></script>
        <script>
        $(document).ready(function() {

   // var dataTable = $('#data-grid').DataTable({  // from all datatable
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
                "aoColumnDefs": [ {"sClass": "text-center", "aTargets": [2,3]}, {"sClass": "text-right", "aTargets": [1]}, { "bSortable": false, "aTargets": [0,1,2,3] } ],
                "serverSide": true,
                "bLengthChange": false,
                "ajax":{
                    url :"datatables/sales_window.php",
                    type: "POST",
                    error: function() {
                        $(".data-grid-error").html("");
                        $("#data-grid").append('<tbody class="data-grid-error text-center"><tr><th colspan="4">Could not fetch records</th></tr></tbody>');
                        $("#data-grid_processing").css("display","none");
                    }
                },
            });

            $('.dataTables_filter input').focus();

            $('.dataTables_filter input').on({
                keypress: function() { typed_into = true; },
                change: function() {
                    if (typed_into) {
                    //  alert('Dectect Keyboard typing');
                        typed_into = false; //reset type listener
                    } else {
                    //    alert('Bacode Detected');
                    }
                }
            });

            // Custom search functionality
            $('#custom-search').on('keyup', function() {
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
                function(value, element) {
                return /^[A-Za-z0-9]+$/.test(value);
                },
                "Only letters and numbers are allowed"
            );            
            $('#form1').validate({
                rules: {
                    product_name: {
                        required: true,
                        alphanumeric: true,
                        remote: {
                            url: "include/val_product_name.php",
                            type: "post",
                            async: false,
                            cache: false
                        }
                    },
                    description: {
                        required: true
                    },
                    price: {
                        required: true
                    },
                    qty: {
                        required: true
                    },
                    low_stock_alert: {
                        required: true
                    }
                },
                messages: {
                    product_name: {
                        required: "Enter Product Name",
                        remote: "Product Exists"
                    },
                    description: {
                        required: "Enter Product Description"
                    },
                    price: {
                        required: "Enter Price "
                    },
                    qty: {
                        required: "Enter Quantity "
                    },
                    low_stock_alert: {
                        required: "Enter Min Low Stock "
                    }                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });

            $('#save').on('click',function(e){
                e.preventDefault();
                bootbox.confirm({
                    centerVertical: true,
                    size: "small",
                    title: "",
                    message: "Are you sure to save?",
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
                    callback: function(output) {
                        if(output) {
                            $('#form1').submit();
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
                                            <input type="search" class="form-control form-control-sm" id="custom-search" placeholder="Search Product Name">                                                <form name="product_form" method="post" autocomplete="off">
                                                    <input type="hidden" name="product_id" id="product_id">
                                                    <input type="hidden" name="command" id="command">                                                        
                                                </form>
                                                <div class="container"><!-- Datatable -->
                                                <table id="datatable"  class="table table-sm table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-head-left">Product Name</th>
                                                            <th class="text-head-center">Unit Price</th>
                                                            <th class="text-head-center">Qty. in Stock</th>
                                                            <th style="width:100px;"><i class="fa fa-arrows-h fa-lg"></i></th>
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
                                <form name="cart_form" id="cart_form" method="post" autocomplete="off">
                                
                                </form>
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