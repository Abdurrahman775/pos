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
        <title>POS System</title>
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
        <!-- jQuery  -->
        <script src="template/assets/js/jquery.min.js"></script>
        <script src="template/assets/js/bootstrap.bundle.min.js"></script>
        <script src="template/assets/js/metismenu.min.js"></script>
        <script src="template/assets/js/waves.js"></script>
        <script src="template/assets/js/feather.min.js"></script>
        <script src="template/assets/js/jquery-ui.min.js"></script>
        <script src="template/plugins/bootbox/bootbox.min.js"></script>
        <script src="template/assets/js/app.js" defer></script>
    </head>
    <body class="dark-sidenav">
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
                                        <h4 class="page-title">Dashboard</h4>
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                                            <li class="breadcrumb-item active">Dashboard</li>
                                        </ol>
                                    </div>
                                </div>                                                           
                            </div>
                        </div>
                    </div>
                    
                    <!-- body here -->
                </div>

                <footer class="footer text-center text-sm-left">
                    <?php include('template/copyright.php'); ?> <span class="d-none d-sm-inline-block float-right"><?php include('template/developed_by.php'); ?></span>
                </footer>
            </div>
        </div>
    </body>
</html>