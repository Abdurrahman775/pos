<?php
require("config.php");
require("include/functions.php");
require_once("include/pos_functions.php");
require("include/authentication.php");
require("include/admin_constants.php");
require_permission('reports');

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get sales data
$sql = "SELECT t.*, u.fname, u.lname 
        FROM transactions t 
        LEFT JOIN users u ON t.user_id = u.id 
        WHERE DATE(t.transaction_date) BETWEEN :start_date AND :end_date 
        ORDER BY t.transaction_date DESC";
$query = $dbh->prepare($sql);
$query->bindParam(':start_date', $start_date, PDO::PARAM_STR);
$query->bindParam(':end_date', $end_date, PDO::PARAM_STR);
$query->execute();
$transactions = $query->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$total_sales = 0;
$total_transactions = count($transactions);

foreach ($transactions as $t) {
    if ($t['status'] == 'completed') {
        $total_sales += $t['total_amount'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Sales Report | POS System</title>
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
                            <h4 class="page-title">Sales Report</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="reports_dashboard.php">Reports</a></li>
                                <li class="breadcrumb-item active">Sales Report</li>
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

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h5 class="card-title">Total Sales</h5>
                                                <h3 class="text-primary"><?php echo format_currency($dbh, $total_sales); ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h5 class="card-title">Total Transactions</h5>
                                                <h3 class="text-info"><?php echo $total_transactions; ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-8">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="header-title mt-0 mb-3">Daily Sales Trend</h5>
                                                <canvas id="salesTrendChart" height="150"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="header-title mt-0 mb-3">Payment Methods</h5>
                                                <canvas id="paymentChart" height="200"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table id="salesTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Transaction ID</th>
                                                <th>Cashier</th>
                                                <th>Payment Method</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($transactions as $t): ?>
                                                <tr>
                                                    <td><?php echo date('d M Y h:i A', strtotime($t['transaction_date'])); ?></td>
                                                    <td><?php echo $t['transaction_id']; ?></td>
                                                    <td><?php echo $t['fname'] . ' ' . $t['lname']; ?></td>
                                                    <td><?php echo ucfirst($t['payment_method']); ?></td>
                                                    <td>
                                                        <?php if ($t['status'] == 'completed'): ?>
                                                            <span class="badge badge-success">Completed</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-warning"><?php echo ucfirst($t['status']); ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo format_currency($dbh, $t['total_amount']); ?></td>
                                                    <td>
                                                        <a href="receipt.php?id=<?php echo $t['id']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fas fa-print"></i> Receipt
                                                        </a>
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
            $('#salesTable').DataTable({
                order: [
                    [0, 'desc']
                ]
            });

            var start_date = '<?php echo $start_date; ?>';
            var end_date = '<?php echo $end_date; ?>';

            // Load Charts
            $.ajax({
                url: 'ajax/get_report_data.php',
                data: { action: 'sales_trend', start_date: start_date, end_date: end_date },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        new Chart(document.getElementById('salesTrendChart'), {
                            type: 'line',
                            data: {
                                labels: response.data.labels,
                                datasets: [{
                                    label: 'Sales',
                                    data: response.data.values,
                                    borderColor: '#44a2d2',
                                    fill: false
                                }]
                            }
                        });
                    }
                }
            });

            $.ajax({
                url: 'ajax/get_report_data.php',
                data: { action: 'sales_breakdown', start_date: start_date, end_date: end_date },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        new Chart(document.getElementById('paymentChart'), {
                            type: 'doughnut',
                            data: {
                                labels: response.data.payment_labels,
                                datasets: [{
                                    data: response.data.payment_values,
                                    backgroundColor: ['#44a2d2', '#f5325c', '#03d87f']
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