<?php
require("config.php");
require("include/functions.php");
require_once("include/pos_functions.php");
require("include/authentication.php");
require("include/admin_constants.php");
require_permission('reports');

// Get inventory data
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.is_active = 1 
        ORDER BY p.name ASC";
$query = $dbh->prepare($sql);
$query->execute();
$products = $query->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$total_items = 0;
$total_cost_value = 0;
$total_sales_value = 0;
$low_stock_count = 0;

foreach ($products as $p) {
    $qty = $p['qty_in_stock'];
    $total_items += $qty;
    $total_cost_value += ($qty * $p['cost_price']);
    $total_sales_value += ($qty * $p['selling_price']);

    if ($qty <= $p['low_stock_alert']) {
        $low_stock_count++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Inventory Report | POS System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="template/assets/images/favicon.ico">
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="datatables/datatables.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="dark-sidenav">
    <?php include('include/sidebar.php'); ?>
    <div class="page-wrapper">
        <div class="topbar"><?php require('template/top_nav_admin.php'); ?></div>
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Inventory Report</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="reports_dashboard.php">Reports</a></li>
                                <li class="breadcrumb-item active">Inventory Report</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Total Items</h5>
                                <h3 class="text-primary"><?php echo number_format($total_items); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Total Cost Value</h5>
                                <h3 class="text-info"><?php echo format_currency($dbh, $total_cost_value); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Total Sales Value</h5>
                                <h3 class="text-success"><?php echo format_currency($dbh, $total_sales_value); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Low Stock Items</h5>
                                <h3 class="text-danger"><?php echo $low_stock_count; ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="header-title mt-0 mb-3">Stock Value by Category</h5>
                                <canvas id="categoryChart" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="header-title mt-0 mb-3">Stock Status</h5>
                                <canvas id="statusChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="inventoryTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Category</th>
                                                <th>Qty</th>
                                                <th>Cost Price</th>
                                                <th>Selling Price</th>
                                                <th>Total Cost</th>
                                                <th>Total Sales</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $p):
                                                $qty = $p['qty_in_stock'];
                                                $cost_val = $qty * $p['cost_price'];
                                                $sales_val = $qty * $p['selling_price'];
                                            ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($p['category_name'] ?? '-'); ?></td>
                                                    <td><?php echo $qty; ?></td>
                                                    <td><?php echo format_currency($dbh, $p['cost_price']); ?></td>
                                                    <td><?php echo format_currency($dbh, $p['selling_price']); ?></td>
                                                    <td><?php echo format_currency($dbh, $cost_val); ?></td>
                                                    <td><?php echo format_currency($dbh, $sales_val); ?></td>
                                                    <td>
                                                        <?php if ($qty <= $p['low_stock_alert']): ?>
                                                            <span class="badge badge-danger">Low Stock</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-success">In Stock</span>
                                                        <?php endif; ?>
                                                    </td>
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
            $('#inventoryTable').DataTable();

            // Load Charts
            $.ajax({
                url: 'ajax/get_report_data.php',
                data: { action: 'inventory_summary' },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        var data = response.data;

                        // Category Chart
                        new Chart(document.getElementById('categoryChart'), {
                            type: 'bar',
                            data: {
                                labels: data.category_labels,
                                datasets: [{
                                    label: 'Stock Value',
                                    data: data.category_values,
                                    backgroundColor: '#44a2d2'
                                }]
                            }
                        });

                        // Status Chart
                        new Chart(document.getElementById('statusChart'), {
                            type: 'pie',
                            data: {
                                labels: data.status_labels,
                                datasets: [{
                                    data: data.status_values,
                                    backgroundColor: ['#03d87f', '#f5325c']
                                }]
                            }
                        });
                    }
                }
            });
        });
    </script>
</body>

</html>