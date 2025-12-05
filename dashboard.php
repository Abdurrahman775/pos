<?php
require("config.php");
require("include/functions.php");

// Get Dashboard Stats
// 1. Today's Sales
$today_start = date('Y-m-d 00:00:00');
$today_end = date('Y-m-d 23:59:59');
$stmt = $dbh->prepare("SELECT COUNT(*) as count, SUM(total_amount) as amount FROM transactions WHERE created_at BETWEEN ? AND ? AND status = 'completed'");
$stmt->execute([$today_start, $today_end]);
$today_stats = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Month's Sales
$month_start = date('Y-m-01 00:00:00');
$month_end = date('Y-m-t 23:59:59');
$stmt = $dbh->prepare("SELECT COUNT(*) as count, SUM(total_amount) as amount FROM transactions WHERE created_at BETWEEN ? AND ? AND status = 'completed'");
$stmt->execute([$month_start, $month_end]);
$month_stats = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. Total Products
$stmt = $dbh->query("SELECT COUNT(*) FROM products WHERE is_active = 1");
$total_products = $stmt->fetchColumn();

// 4. Low Stock
$stmt = $dbh->query("SELECT COUNT(*) FROM products WHERE qty_in_stock < 10 AND is_active = 1");
$low_stock = $stmt->fetchColumn();

$stats = [
    'today_sales' => $today_stats['count'] ?? 0,
    'today_amount' => $today_stats['amount'] ?? 0,
    'month_sales' => $month_stats['count'] ?? 0,
    'month_amount' => $month_stats['amount'] ?? 0,
    'total_products' => $total_products,
    'low_stock' => $low_stock
];

// 5. Daily Sales Chart (Last 7 days)
$daily_sales = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $start = $date . ' 00:00:00';
    $end = $date . ' 23:59:59';
    $stmt = $dbh->prepare("SELECT COUNT(*) as transactions, SUM(total_amount) as amount FROM transactions WHERE created_at BETWEEN ? AND ? AND status = 'completed'");
    $stmt->execute([$start, $end]);
    $day_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    $daily_sales[] = [
        'date' => $date,
        'transactions' => $day_stats['transactions'] ?? 0,
        'amount' => $day_stats['amount'] ?? 0
    ];
}

// 6. Payment Type Chart
$stmt = $dbh->query("SELECT payment_method as payment_type, SUM(total_amount) as amount FROM transactions WHERE status = 'completed' GROUP BY payment_method");
$payment_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 7. Top 5 Products
$stmt = $dbh->query("
    SELECT p.name, SUM(ti.quantity) as total_qty 
    FROM transaction_items ti 
    JOIN products p ON ti.product_id = p.id 
    JOIN transactions t ON ti.transaction_id = t.transaction_id
    WHERE t.status = 'completed'
    GROUP BY ti.product_id 
    ORDER BY total_qty DESC 
    LIMIT 5
");
$top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Dashboard | POS System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Point of Sale System" name="description" />
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
    <!-- Chart JS -->
    <script src="template/plugins/chartjs/chart.min.js"></script>
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

                <!-- Key Metrics Row -->
                <div class="row">
                    <div class="col-md-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-right">
                                    <i class="fas fa-shopping-cart text-success" style="font-size: 2.5rem; opacity: 0.5;"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0" title="Today's Transactions">Today's Sales</h5>
                                <h3 class="mt-3 mb-3"><?php echo $stats['today_sales']; ?></h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-success mr-2"><i class="fa fa-arrow-up"></i>Amount: <?php echo get_currency($dbh); ?> <?php echo number_format($stats['today_amount'], 2); ?></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-right">
                                    <i class="fas fa-calendar text-info" style="font-size: 2.5rem; opacity: 0.5;"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0" title="This Month's Transactions">Month's Sales</h5>
                                <h3 class="mt-3 mb-3"><?php echo $stats['month_sales']; ?></h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-info mr-2"><i class="fa fa-arrow-up"></i>Amount: <?php echo get_currency($dbh); ?> <?php echo number_format($stats['month_amount'], 2); ?></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-right">
                                    <i class="fas fa-box text-primary" style="font-size: 2.5rem; opacity: 0.5;"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0" title="Active Products">Total Products</h5>
                                <h3 class="mt-3 mb-3"><?php echo $stats['total_products']; ?></h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-primary mr-2"><i class="fa fa-circle"></i>In Stock</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="float-right">
                                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 2.5rem; opacity: 0.5;"></i>
                                </div>
                                <h5 class="text-muted font-weight-normal mt-0" title="Low Stock Items">Low Stock Items</h5>
                                <h3 class="mt-3 mb-3"><?php echo $stats['low_stock']; ?></h3>
                                <p class="mb-0 text-muted">
                                    <span class="text-warning mr-2"><i class="fa fa-circle"></i>
                                        < 10 Units</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="row mt-4">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Daily Sales (Last 7 Days)</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="dailySalesChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Sales by Payment Type</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="paymentTypeChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Products and Sales Table -->
                <div class="row mt-4">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Top 5 Products by Sales</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Quantity Sold</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($top_products)): ?>
                                                <?php foreach ($top_products as $product): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                                        <td><?php echo $product['total_qty']; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="2" class="text-center">No sales data found</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Recent Sales (Last 7 Days)</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Transactions</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (!empty($daily_sales)) {
                                                foreach ($daily_sales as $day) {
                                                    echo "<tr>";
                                                    echo "<td>" . date('M d, Y', strtotime($day['date'])) . "</td>";
                                                    echo "<td><span class='badge badge-info'>" . $day['transactions'] . "</span></td>";
                                                    echo "<td>" . get_currency($dbh) . " " . number_format($day['amount'], 2) . "</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='3' class='text-center text-muted'>No sales data</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
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

    <script>
        $(function() {
            // Daily Sales Chart
            const dailySalesCtx = document.getElementById('dailySalesChart');
            if (dailySalesCtx) {
                const dailySalesData = <?php
                                        $dates = [];
                                        $amounts = [];
                                        foreach (array_reverse($daily_sales) as $day) {
                                            $dates[] = date('M d', strtotime($day['date']));
                                            $amounts[] = $day['amount'] ?? 0;
                                        }
                                        echo json_encode(['dates' => $dates, 'amounts' => $amounts]);
                                        ?>;

                new Chart(dailySalesCtx, {
                    type: 'line',
                    data: {
                        labels: dailySalesData.dates,
                        datasets: [{
                            label: 'Sales Amount',
                            data: dailySalesData.amounts,
                            borderColor: '#4CAF50',
                            backgroundColor: 'rgba(76, 175, 80, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#4CAF50'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '<?php echo get_currency($dbh); ?> ' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Payment Type Chart
            const paymentTypeCtx = document.getElementById('paymentTypeChart');
            if (paymentTypeCtx) {
                const paymentData = <?php
                                    $types = [];
                                    $type_amounts = [];
                                    $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'];
                                    $i = 0;
                                    foreach ($payment_types as $type) {
                                        $types[] = $type['payment_type'];
                                        $type_amounts[] = $type['amount'] ?? 0;
                                        $i++;
                                    }
                                    echo json_encode(['types' => $types, 'amounts' => $type_amounts]);
                                    ?>;

                new Chart(paymentTypeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: paymentData.types,
                        datasets: [{
                            data: paymentData.amounts,
                            backgroundColor: [
                                '#FF6384',
                                '#36A2EB',
                                '#FFCE56',
                                '#4BC0C0',
                                '#9966FF'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>