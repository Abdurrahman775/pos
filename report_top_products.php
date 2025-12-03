<?php
/**
 * Top Products Report Page
 */
require("config.php");
require("include/functions.php");
require_once("include/pos_functions.php");
require("include/authentication.php");
require("include/admin_constants.php");

// Require reports permission
require_permission('reports');

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$period = isset($_GET['period']) ? $_GET['period'] : 'month';

// Get top products
$top_products = get_top_selling_products($dbh, $limit, $period);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Top Products Report | POS System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="template/assets/images/favicon.ico">
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="datatables/datatables.min.css" rel="stylesheet" type="text/css" />
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
                            <h4 class="page-title">Top Products Report</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="reports_dashboard.php">Reports</a></li>
                                <li class="breadcrumb-item active">Top Products</li>
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
                                        <label for="period" class="mr-2">Period:</label>
                                        <select class="form-control" id="period" name="period">
                                            <option value="today" <?php echo $period == 'today' ? 'selected' : ''; ?>>Today</option>
                                            <option value="week" <?php echo $period == 'week' ? 'selected' : ''; ?>>This Week</option>
                                            <option value="month" <?php echo $period == 'month' ? 'selected' : ''; ?>>This Month</option>
                                        </select>
                                    </div>
                                    <div class="form-group mr-2">
                                        <label for="limit" class="mr-2">Limit:</label>
                                        <select class="form-control" id="limit" name="limit">
                                            <option value="5" <?php echo $limit == 5 ? 'selected' : ''; ?>>Top 5</option>
                                            <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>Top 10</option>
                                            <option value="20" <?php echo $limit == 20 ? 'selected' : ''; ?>>Top 20</option>
                                            <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>Top 50</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </form>
                                
                                <div class="table-responsive">
                                    <table id="topProductsTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Product Name</th>
                                                <th>Selling Price</th>
                                                <th>Units Sold</th>
                                                <th>Total Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $rank = 1;
                                            foreach($top_products as $p): 
                                            ?>
                                            <tr>
                                                <td><?php echo $rank++; ?></td>
                                                <td><?php echo htmlspecialchars($p['name']); ?></td>
                                                <td><?php echo format_currency($dbh, $p['selling_price']); ?></td>
                                                <td><?php echo $p['total_sold']; ?></td>
                                                <td><?php echo format_currency($dbh, $p['revenue']); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
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
            </footer>
        </div>
    </div>
    
    <script src="template/assets/js/jquery.min.js"></script>
    <script src="template/assets/js/bootstrap.bundle.min.js"></script>
    <script src="template/assets/js/metismenu.min.js"></script>
    <script src="template/assets/js/waves.js"></script>
    <script src="template/assets/js/feather.min.js"></script>
    <script src="datatables/datatables.min.js"></script>
    <script src="template/assets/js/app.js"></script>
    <script>
    $(document).ready(function() {
        $('#topProductsTable').DataTable({
            searching: false,
            paging: false,
            info: false
        });
    });
    </script>
</body>
</html>
