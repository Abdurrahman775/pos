<?php
require("config.php");
require("include/functions.php");
require("include/admin_authentication.php");
require("include/admin_constants.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Point of Sale System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Staff Management System" name="description" />
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
    <script src="template/assets/js/app.js" defer></script>
    <script src="template/plugins/bootbox/bootbox.min.js"></script>
    <script src="template/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="template/plugins/datatables/dataTables.bootstrap5.min.js"></script>
    <script src="template/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="template/plugins/datatables/responsive.bootstrap4.min.js"></script>

    <script>
        function toggle_product_status(id) {
            $.ajax({
                type: "POST",
                url: "include/sql_set_product_active_status.php",
                data: "token=" + id,
                success: function() {
                    $('#datatable').dataTable()._fnAjaxUpdate();
                }
            });
        }

        $(document).ready(function() {
            $("#datatable").DataTable({
                "responsive": true,
                "ordering": false,
                "pageLength": 25,
                //"lengthChange": false,
                "processing": true,
                "aoColumnDefs": [{
                    "sClass": "text-center",
                    "aTargets": [0, 3, 4, 5, 6]
                }],
                "serverSide": true,
                "ajax": {
                    url: "datatables/all_products.php",
                    type: "POST",
                    error: function() {
                        $(".data-grid-error").html("");
                        $("#data-grid").append('<tbody class="data-grid-error text-center"><tr><th colspan="7">Could not fetch records</th></tr></tbody>');
                        $("#data-grid_processing").css("display", "none");
                    }
                }
            });

            //remove the default 'Search' text for all DataTable search boxes
            $.extend(true, $.fn.dataTable.defaults, {
                language: {
                    search: ""
                }
            });
            //custom format of Search boxes
            $('[type=search]').each(function() {
                $(this).attr("placeholder", "Search...");
                $(this).before('<span class="fa fa-search"></span>');
            });

        });
    </script>

    <style>
        /* dataTables Search input box */

        .dataTables_filter {
            position: relative;
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

        .dataTables_filter .fa-search {
            position: absolute;
            top: 10px;
            left: auto;
            right: 10px;
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
                            <div class="row">
                                <div class="col">
                                    <h4 class="page-title">All Products</h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                                        <li class="breadcrumb-item">Products</li>
                                        <li class="breadcrumb-item active">All Products</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <table id="datatable" class="table table-sm table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th><strong>#</strong></th>
                                            <th><strong>Product Name</strong></th>
                                            <th><strong>Description</strong></th>
                                            <th><strong>Price</strong></th>
                                            <th><strong>Qty in Stock</strong></th>
                                            <th><strong>Low Stock Alert</strong></th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                </table>
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