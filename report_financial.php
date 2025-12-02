<?php
/**
 * Financial Report Page
 */
require("config.php");
require("include/functions.php");
require("include/pos_functions.php");
require("include/authentication.php");
require("include/admin_constants.php");

// Require reports permission
require_permission('reports');

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get completed transactions
$sql = "SELECT t.*, ti.product_id, ti.quantity, ti.line_total, p.cost_price 
        FROM transactions t 
        JOIN transaction_items ti ON t.transaction_id = ti.transaction_id 
        JOIN products p ON ti.product_id = p.id 
        WHERE DATE(t.transaction_date) BETWEEN :start_date AND :end_date 
        AND t.status = 'completed'";
$query = $dbh->prepare($sql);
$query->bindParam(':start_date', $start_date, PDO::PARAM_STR);
$query->bindParam(':end_date', $end_date, PDO::PARAM_STR);
$query->execute();
$items = $query->fetchAll(PDO::FETCH_ASSOC);

// Calculate financials
$total_revenue = 0;
$total_cogs = 0; // Cost of Goods Sold
$total_profit = 0;

foreach($items as $item) {
    $revenue = $item['line_total'];
    $cost = $item['quantity'] * $item['cost_price'];
    $profit = $revenue - $cost;
    
    $total_revenue += $revenue;
    $total_cogs += $cost;
    $total_profit += $profit;
}

$margin = $total_revenue > 0 ? ($total_profit / $total_revenue) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Financial Report | POS System</title>
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
                            <h4 class="page-title">Financial Report</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="reports_dashboard.php">Reports</a></li>
                                <li class="breadcrumb-item active">Financial Report</li>
                            </ol>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="GET" action="" class="form-inline mb-4">
                                    <div class="form-group mr-2">
                                        <label for="start_date" class="mr-2">Start Date:</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                                    </div>
                                    <div class="form-group mr-2">
                                        <label for="end_date" class="mr-2">End Date:</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </form>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body">
                                                <h5 class="card-title text-white">Total Revenue</h5>
                                                <h3 class="text-white"><?php echo format_currency($dbh, $total_revenue); ?></h3>
                                                <p class="mb-0">Gross Sales</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-danger text-white">
                                            <div class="card-body">
                                                <h5 class="card-title text-white">Cost of Goods Sold</h5>
                                                <h3 class="text-white"><?php echo format_currency($dbh, $total_cogs); ?></h3>
                                                <p class="mb-0">Total Cost</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-success text-white">
                                            <div class="card-body">
                                                <h5 class="card-title text-white">Gross Profit</h5>
                                                <h3 class="text-white"><?php echo format_currency($dbh, $total_profit); ?></h3>
                                                <p class="mb-0">Margin: <?php echo number_format($margin, 2); ?>%</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-info mt-4">
                                    <i class="fas fa-info-circle mr-1"></i> Note: This report calculates profit based on the difference between Selling Price and Cost Price for sold items.
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
