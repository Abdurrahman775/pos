<?php
/**
 * Reports Dashboard
 * Central hub for all reporting and analytics
 */
require("config.php");
require("include/functions.php");
require("include/pos_functions.php");
require("include/authentication.php");
require("include/admin_constants.php");

// Require reports permission
require_permission('reports');

// Get key metrics
$today_sales = get_today_sales($dbh);
$week_sales = 0;
$month_sales = 0;

try {
    $sql = "SELECT COALESCE(SUM(total_amount), 0) as total 
            FROM transactions 
            WHERE YEARWEEK(transaction_date) = YEARWEEK(NOW()) AND status = 'completed'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $week_sales = $result['total'];
    
    $sql = "SELECT COALESCE(SUM(total_amount), 0) as total 
            FROM transactions 
            WHERE YEAR(transaction_date) = YEAR(NOW()) 
            AND MONTH(transaction_date) = MONTH(NOW()) AND status = 'completed'";
    $query = $dbh->prepare($sql);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $month_sales = $result['total'];
} catch(PDOException $e) {
    // Handle error
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Reports Dashboard | POS System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="template/assets/images/favicon.ico">
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="dark-sidenav">
    <div class="left-sidenav">
        <div class="brand"><?php require('template/brand_admin.php'); ?></div>
        <div class="menu-content h-100" data-simplebar><?php require('include/menus.php'); ?></div>
    </div>
    <div class="page-wrapper">
        <div class="topbar"><?php require('template/top_nav_admin.php'); ?></div>
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Reports Dashboard</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Reports</li>
                            </ol>
                        </div>
                    </div>
                </div>
                
                <!-- Sales Overview -->
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Today's Sales</h5>
                                <h2 class="mb-0"><?php echo format_currency($dbh, $today_sales); ?></h2>
                                <small class="text-muted">Current day total</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">This Week</h5>
                                <h2 class="mb-0"><?php echo format_currency($dbh, $week_sales); ?></h2>
                                <small class="text-muted">Week to date</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">This Month</h5>
                                <h2 class="mb-0"><?php echo format_currency($dbh, $month_sales); ?></h2>
                                <small class="text-muted">Month to date</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Report Categories -->
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="media">
                                    <div class="avatar-sm rounded-circle bg-soft-primary align-self-center mr-3">
                                        <i class="fas fa-chart-line avatar-title font-24 text-primary"></i>
                                    </div>
                                    <div class="media-body">
                                        <h5 class="mt-0 mb-2">Sales Reports</h5>
                                        <p class="text-muted mb-3">Daily, weekly, monthly sales analysis and trends</p>
                                        <a href="report_sales.php" class="btn btn-primary btn-sm">
                                            <i class="fas fa-arrow-right mr-1"></i> View Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="media">
                                    <div class="avatar-sm rounded-circle bg-soft-success align-self-center mr-3">
                                        <i class="fas fa-boxes avatar-title font-24 text-success"></i>
                                    </div>
                                    <div class="media-body">
                                        <h5 class="mt-0 mb-2">Inventory Reports</h5>
                                        <p class="text-muted mb-3">Stock levels, movement history, and analysis</p>
                                        <a href="report_inventory.php" class="btn btn-success btn-sm">
                                            <i class="fas fa-arrow-right mr-1"></i> View Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="media">
                                    <div class="avatar-sm rounded-circle bg-soft-warning align-self-center mr-3">
                                        <i class="fas fa-coins avatar-title font-24 text-warning"></i>
                                    </div>
                                    <div class="media-body">
                                        <h5 class="mt-0 mb-2">Financial Reports</h5>
                                        <p class="text-muted mb-3">Revenue, profit margins, and payment methods</p>
                                        <a href="report_financial.php" class="btn btn-warning btn-sm">
                                            <i class="fas fa-arrow-right mr-1"></i> View Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="media">
                                    <div class="avatar-sm rounded-circle bg-soft-info align-self-center mr-3">
                                        <i class="fas fa-users avatar-title font-24 text-info"></i>
                                    </div>
                                    <div class="media-body">
                                        <h5 class="mt-0 mb-2">Customer Reports</h5>
                                        <p class="text-muted mb-3">Customer purchases and spending patterns</p>
                                        <a href="report_customers.php" class="btn btn-info btn-sm">
                                            <i class="fas fa-arrow-right mr-1"></i> View Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="media">
                                    <div class="avatar-sm rounded-circle bg-soft-secondary align-self-center mr-3">
                                        <i class="fas fa-user-tie avatar-title font-24 text-secondary"></i>
                                    </div>
                                    <div class="media-body">
                                        <h5 class="mt-0 mb-2">Employee Reports</h5>
                                        <p class="text-muted mb-3">Employee sales performance and productivity</p>
                                        <a href="employee_performance.php" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-arrow-right mr-1"></i> View Reports
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="media">
                                    <div class="avatar-sm rounded-circle bg-soft-danger align-self-center mr-3">
                                        <i class="fas fa-fire avatar-title font-24 text-danger"></i>
                                    </div>
                                    <div class="media-body">
                                        <h5 class="mt-0 mb-2">Top Products</h5>
                                        <p class="text-muted mb-3">Best-selling products and categories</p>
                                        <a href="report_top_products.php" class="btn btn-danger btn-sm">
                                            <i class="fas fa-arrow-right mr-1"></i> View Reports
                                        </a>
                                    </div>
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
    <script src="template/assets/js/app.js"></script>
</body>
</html>
