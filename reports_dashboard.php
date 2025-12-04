<?php
require("config.php");
require("include/functions.php");
require("include/authentication.php");
require("include/admin_constants.php");
require_permission('reports');
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
                            <h4 class="page-title">Reports Dashboard</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Reports</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form id="filterForm" class="form-inline">
                                    <div class="form-group mr-2">
                                        <label for="start_date" class="mr-2">Start Date:</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo date('Y-m-01'); ?>">
                                    </div>
                                    <div class="form-group mr-2">
                                        <label for="end_date" class="mr-2">End Date:</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="header-title mt-0 mb-3">Financial Overview</h5>
                                <div class="text-center">
                                    <h3 id="totalRevenue">N0.00</h3>
                                    <p class="text-muted">Total Revenue</p>
                                </div>
                                <canvas id="financialChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="header-title mt-0 mb-3">Sales Trend</h5>
                                <canvas id="salesTrendChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="header-title mt-0 mb-3">Top 5 Products</h5>
                                <canvas id="topProductsChart" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="header-title mt-0 mb-3">Quick Links</h5>
                                <div class="list-group">
                                    <a href="report_financial.php" class="list-group-item list-group-item-action">Detailed Financial Report</a>
                                    <a href="report_top_products.php" class="list-group-item list-group-item-action">Top Products Report</a>
                                    <a href="report_inventory.php" class="list-group-item list-group-item-action">Inventory Report</a>
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

    <script>
        $(document).ready(function() {
            // Initialize Charts
            var financialCtx = document.getElementById('financialChart').getContext('2d');
            var financialChart = new Chart(financialCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Revenue', 'Cost', 'Profit'],
                    datasets: [{
                        data: [0, 0, 0],
                        backgroundColor: ['#44a2d2', '#f5325c', '#03d87f']
                    }]
                }
            });

            var salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
            var salesTrendChart = new Chart(salesTrendCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Sales',
                        data: [],
                        borderColor: '#44a2d2',
                        fill: false
                    }]
                }
            });

            var topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
            var topProductsChart = new Chart(topProductsCtx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Units Sold',
                        data: [],
                        backgroundColor: '#03d87f'
                    }]
                },
                options: {
                    indexAxis: 'y',
                }
            });

            function loadData() {
                var start_date = $('#start_date').val();
                var end_date = $('#end_date').val();

                // Financials
                $.ajax({
                    url: 'ajax/get_report_data.php',
                    data: { action: 'financials', start_date: start_date, end_date: end_date },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            var data = response.data;
                            $('#totalRevenue').text('N' + parseFloat(data.revenue).toLocaleString(undefined, {minimumFractionDigits: 2}));
                            financialChart.data.datasets[0].data = [data.revenue, data.cogs, data.profit];
                            financialChart.update();
                        }
                    }
                });

                // Sales Trend
                $.ajax({
                    url: 'ajax/get_report_data.php',
                    data: { action: 'sales_trend', start_date: start_date, end_date: end_date },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            salesTrendChart.data.labels = response.data.labels;
                            salesTrendChart.data.datasets[0].data = response.data.values;
                            salesTrendChart.update();
                        }
                    }
                });

                // Top Products
                $.ajax({
                    url: 'ajax/get_report_data.php',
                    data: { action: 'top_products', period: 'month' }, // Can be dynamic
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            topProductsChart.data.labels = response.data.labels;
                            topProductsChart.data.datasets[0].data = response.data.values;
                            topProductsChart.update();
                        }
                    }
                });
            }

            $('#filterBtn').click(function() {
                loadData();
            });

            // Load initial data
            loadData();
        });
    </script>
</body>

</html>