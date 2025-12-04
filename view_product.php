<?php
require("config.php");
require("include/functions.php");
require("include/admin_authentication.php");
require("include/admin_constants.php");

$token = base64_decode($_GET['token']);
$product_id = urldecode($token);

$sql = "SELECT * FROM products WHERE id= :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $product_id, PDO::PARAM_INT);
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);

$db_reg_by = get_admin_name($dbh, $result['reg_by']) . ' (' . $result['reg_by'] . ')';
$db_reg_date = date('l, j F Y H:i a', strtotime($result['reg_date']));
$db_updated_by = !empty($result['updated_by']) ? get_admin_name($dbh, $result['updated_by']) . ' (' . $result['updated_by'] . ')' : "N/A";
$db_last_update = !empty($result['last_update']) ? date('l, j F Y H:i a', strtotime($result['last_update'])) : "N/A";

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
</head>

<body class="dark-sidenav">
    <?php include('include/sidebar.php'); ?>
    <div class="page-wrapper">
        <div class="topbar">
            <?php require('template/top_nav_admin.php'); ?>
        </div>

        <!-- Page Content-->
        <div class="page-content">
            <div class="container-fluid">
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="page-title-box">
                            <div class="row">
                                <div class="col">
                                    <h4 class="page-title">View Product</h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="javascript:void(0);"><i class="fa fa-home"></i></a></li>
                                        <li class="breadcrumb-item">products</li>
                                        <li class="breadcrumb-item active">View Product                                                                                                                                                                                                                                                     </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 order-lg-1 order-2">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="table-responsive">                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                           
                                        <table class="table table-sm mb-0 table-centered">
                                            <tbody>
                                                <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                    <td class="fw-semibold col-3">Name:</td>
                                                                                                                                                                                                                                                                                                                                                                                                                                    <td class="col-9"><?php echo $result['name']; ?></td>
                                                                                                                                                                                                                                                                                                                                                                                                                                </tr>
                                                                                                                                                                                                                                                                                                                                                                                                                                <tr>
                                                    <td class="fw-semibold col-3">Description:</td>
                                                    <td class="col-9"><?php echo $result['description']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold col-3">Barcode:</td>
                                                    <td class="col-9"><?php echo $result['barcode']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold col-3">Category:</td>
                                                    <td class="col-9"><?php echo $result['category_id']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold col-3">Supplier:</td>
                                                    <td class="col-9"><?php echo $result['supplier_id']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold col-3">Selling Price:</td>
                                                    <td class="col-9"><?php echo $result['selling_price']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold col-3">Low Stock Alert:</td>
                                                    <td class="col-9"><?php echo $result['low_stock_alert']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold col-3">Status:</td>
                                                    <td class="col-9"><?php echo $db_status = ($result['is_active'] == 1) ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 order-lg-2 order-1">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title"><i class="fa fa-user-tag me-1"></i>Audit Trail</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0 table-centered">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-semibold col-3">Registered By:</td>
                                                    <td class="col-9"><?php echo $db_reg_by; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold col-3">Date Registered:</td>
                                                    <td class="col-9"><?php echo $db_reg_date; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold col-3">Updated By:</td>
                                                    <td class="col-9"><?php echo $db_updated_by; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold col-3">Date Updated:</td>
                                                    <td class="col-9"><?php echo $db_last_update; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
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