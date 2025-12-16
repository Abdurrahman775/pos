<?php

/**
 * Financial Report Page - Dynamic
 */
require("config.php");
require("include/functions.php");
require_once("include/pos_functions.php");
require("include/authentication.php");
require("include/admin_constants.php");

// Require reports permission
require_permission('reports');

// Get filter parameters
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$cashier_id = isset($_GET['cashier_id']) ? intval($_GET['cashier_id']) : 0;
$payment_method = isset($_GET['payment_method']) ? $_GET['payment_method'] : '';
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

// Build dynamic SQL query
$sql = "SELECT t.transaction_id, t.transaction_date, t.subtotal, t.tax_amount, t.discount_amount, 
               t.total_amount, t.payment_method, t.created_by, ti.product_id, ti.quantity, 
               ti.line_total, p.cost_price, p.name as product_name, c.name as category_name
        FROM transactions t 
        JOIN transaction_items ti ON t.transaction_id = ti.transaction_id 
        JOIN products p ON ti.product_id = p.id 
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE DATE(t.transaction_date) BETWEEN :start_date AND :end_date 
        AND t.status = 'completed'";

$params = array(':start_date' => $start_date, ':end_date' => $end_date);

// Add category filter
if ($category_id > 0) {
    $sql .= " AND p.category_id = :category_id";
    $params[':category_id'] = $category_id;
}

// Add cashier filter
if ($cashier_id > 0) {
    $sql .= " AND t.created_by = :cashier_id";
    $params[':cashier_id'] = $cashier_id;
}

// Add payment method filter
if (!empty($payment_method)) {
    $sql .= " AND t.payment_method = :payment_method";
    $params[':payment_method'] = $payment_method;
}

// Add product filter
if ($product_id > 0) {
    $sql .= " AND ti.product_id = :product_id";
    $params[':product_id'] = $product_id;
}

$sql .= " ORDER BY t.transaction_date DESC";

$query = $dbh->prepare($sql);
$query->execute($params);
$items = $query->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter
$cat_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$cat_query = $dbh->prepare($cat_sql);
$cat_query->execute();
$categories = $cat_query->fetchAll(PDO::FETCH_ASSOC);

// Get cashiers for filter
$staff_sql = "SELECT id, username FROM admins WHERE is_active = 1 ORDER BY username";
$staff_query = $dbh->prepare($staff_sql);
$staff_query->execute();
$cashiers = $staff_query->fetchAll(PDO::FETCH_ASSOC);

// Get products for filter
$prod_sql = "SELECT id, name FROM products WHERE is_active = 1 ORDER BY name ASC";
$prod_query = $dbh->prepare($prod_sql);
$prod_query->execute();
$products = $prod_query->fetchAll(PDO::FETCH_ASSOC);

// Calculate financials
$total_revenue = 0;
$total_cogs = 0; // Cost of Goods Sold
$total_profit = 0;
$total_tax = 0;
$total_discount = 0;
$transaction_count = 0;
$payment_summary = array('cash' => 0, 'pos' => 0, 'mixed' => 0, 'other' => 0);
$category_summary = array();
$daily_summary = array();

foreach ($items as $item) {
    $revenue = $item['line_total'];
    $cost = $item['quantity'] * $item['cost_price'];
    $profit = $revenue - $cost;
    $date_key = date('Y-m-d', strtotime($item['transaction_date']));

    $total_revenue += $revenue;
    $total_cogs += $cost;
    $total_profit += $profit;

    // Payment summary
    $payment_key = isset($item['payment_method']) ? strtolower($item['payment_method']) : 'other';
    if (isset($payment_summary[$payment_key])) {
        $payment_summary[$payment_key] += $revenue;
    } else {
        $payment_summary['other'] += $revenue;
    }

    // Category summary
    $cat_name = $item['category_name'] ?? 'Uncategorized';
    if (!isset($category_summary[$cat_name])) {
        $category_summary[$cat_name] = array('revenue' => 0, 'cost' => 0, 'profit' => 0, 'items' => 0);
    }
    $category_summary[$cat_name]['revenue'] += $revenue;
    $category_summary[$cat_name]['cost'] += $cost;
    $category_summary[$cat_name]['profit'] += $profit;
    $category_summary[$cat_name]['items'] += $item['quantity'];

    // Daily summary
    if (!isset($daily_summary[$date_key])) {
        $daily_summary[$date_key] = array('revenue' => 0, 'cost' => 0, 'profit' => 0);
    }
    $daily_summary[$date_key]['revenue'] += $revenue;
    $daily_summary[$date_key]['cost'] += $cost;
    $daily_summary[$date_key]['profit'] += $profit;

    // Count unique transactions
    $transaction_count = count(array_unique(array_column($items, 'transaction_id')));
}

// Get tax and discount from transactions table
$tax_sql = "SELECT SUM(tax_amount) as total_tax, SUM(discount_amount) as total_discount
           FROM transactions
           WHERE DATE(transaction_date) BETWEEN :start_date AND :end_date
           AND status = 'completed'";
$tax_params = array(':start_date' => $start_date, ':end_date' => $end_date);

if ($cashier_id > 0) {
    $tax_sql .= " AND created_by = :cashier_id";
    $tax_params[':cashier_id'] = $cashier_id;
}
if (!empty($payment_method)) {
    $tax_sql .= " AND payment_method = :payment_method";
    $tax_params[':payment_method'] = $payment_method;
}

$tax_query = $dbh->prepare($tax_sql);
$tax_query->execute($tax_params);
$tax_result = $tax_query->fetch(PDO::FETCH_ASSOC);
$total_tax = $tax_result['total_tax'] ?? 0;
$total_discount = $tax_result['total_discount'] ?? 0;

// Calculate margin
$margin = $total_revenue > 0 ? ($total_profit / $total_revenue) * 100 : 0;
$avg_transaction = $transaction_count > 0 ? $total_revenue / $transaction_count : 0;
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
    <link href="template/plugins/datatables/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="template/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.min.js"></script>
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
                            <h4 class="page-title">Financial Report</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="reports_dashboard.php">Reports</a></li>
                                <li class="breadcrumb-item active">Financial Report</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white-50 font-weight-bold mb-2">Total Revenue</h6>
                                <h3 class="text-white"><?php echo format_currency($dbh, $total_revenue); ?></h3>
                                <p class="mb-0 text-white-50 font-size-12">Gross Sales</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white-50 font-weight-bold mb-2">COGS</h6>
                                <h3 class="text-white"><?php echo format_currency($dbh, $total_cogs); ?></h3>
                                <p class="mb-0 text-white-50 font-size-12">Cost of Goods</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white-50 font-weight-bold mb-2">Gross Profit</h6>
                                <h3 class="text-white"><?php echo format_currency($dbh, $total_profit); ?></h3>
                                <p class="mb-0 text-white-50 font-size-12">Margin: <?php echo number_format($margin, 2); ?>%</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white-50 font-weight-bold mb-2">Other Details</h6>
                                <h6 class="text-white">Transactions: <?php echo $transaction_count; ?></h6>
                                <p class="mb-0 text-white-50 font-size-12">Avg: <?php echo format_currency($dbh, $avg_transaction); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="header-title mt-0 mb-3">Revenue by Payment Method</h5>
                                <canvas id="paymentChart" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="header-title mt-0 mb-3">Profit Margin Trend</h5>
                                <canvas id="profitChart" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Category Breakdown -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="header-title mt-0 mb-3">Financial Summary by Category</h5>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th class="text-right">Revenue</th>
                                                <th class="text-right">COGS</th>
                                                <th class="text-right">Profit</th>
                                                <th class="text-right">Margin %</th>
                                                <th class="text-right">Items Sold</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($category_summary as $category => $summary):
                                                $cat_margin = $summary['revenue'] > 0 ? ($summary['profit'] / $summary['revenue']) * 100 : 0;
                                            ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($category); ?></td>
                                                    <td class="text-right"><?php echo format_currency($dbh, $summary['revenue']); ?></td>
                                                    <td class="text-right"><?php echo format_currency($dbh, $summary['cost']); ?></td>
                                                    <td class="text-right"><?php echo format_currency($dbh, $summary['profit']); ?></td>
                                                    <td class="text-right">
                                                        <span class="badge <?php echo ($cat_margin >= 20) ? 'badge-success' : 'badge-warning'; ?>">
                                                            <?php echo number_format($cat_margin, 2); ?>%
                                                        </span>
                                                    </td>
                                                    <td class="text-right"><?php echo $summary['items']; ?></td>
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
    <script src="template/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="template/plugins/datatables/dataTables.bootstrap5.min.js"></script>
    <script src="template/assets/js/app.js"></script>
    <script>
        $(document).ready(function() {
            //Payment Method Chart
            var paymentData = {
                'cash': <?php echo isset($payment_summary['cash']) ? $payment_summary['cash'] : 0; ?>,
                'pos': <?php echo isset($payment_summary['pos']) ? $payment_summary['pos'] : 0; ?>,
                'mixed': <?php echo isset($payment_summary['mixed']) ? $payment_summary['mixed'] : 0; ?>,
                'other': <?php echo isset($payment_summary['other']) ? $payment_summary['other'] : 0; ?>
            };

            var paymentLabels = Object.keys(paymentData);
            var paymentValues = Object.values(paymentData).map(v => v > 0 ? v : 0);

            new Chart(document.getElementById('paymentChart'), {
                type: 'pie',
                data: {
                    labels: paymentLabels,
                    datasets: [{
                        data: paymentValues,
                        backgroundColor: ['#28a745', '#007bff', '#ffc107', '#6c757d']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Profit Trend Chart
            var dailyData = <?php echo json_encode($daily_summary); ?>;
            var dates = Object.keys(dailyData).sort();
            var profitMargins = dates.map(date => {
                var revenue = dailyData[date].revenue;
                var profit = dailyData[date].profit;
                return revenue > 0 ? ((profit / revenue) * 100) : 0;
            });

            new Chart(document.getElementById('profitChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [{
                        label: 'Profit Margin %',
                        data: profitMargins,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });

            // Export functionality
            $('#exportBtn').click(function() {
                var ws_data = [
                    ['FINANCIAL REPORT', '', '', '', '', ''],
                    ['Period', '<?php echo $start_date; ?> to <?php echo $end_date; ?>', '', '', '', ''],
                    ['', '', '', '', '', ''],
                    ['SUMMARY', '', '', '', '', ''],
                    ['Total Revenue', '<?php echo $total_revenue; ?>', '', '', '', ''],
                    ['Cost of Goods Sold', '<?php echo $total_cogs; ?>', '', '', '', ''],
                    ['Gross Profit', '<?php echo $total_profit; ?>', '', '', '', ''],
                    ['Profit Margin %', '<?php echo $margin; ?>', '', '', '', ''],
                    ['Total Transactions', '<?php echo $transaction_count; ?>', '', '', '', ''],
                    ['', '', '', '', '', ''],
                    ['CATEGORY BREAKDOWN', '', '', '', '', ''],
                    ['Category', 'Revenue', 'COGS', 'Profit', 'Margin %', 'Items Sold']
                ];

                <?php foreach ($category_summary as $category => $summary):
                    $cat_margin = $summary['revenue'] > 0 ? ($summary['profit'] / $summary['revenue']) * 100 : 0;
                ?>
                    ws_data.push([
                        '<?php echo addslashes($category); ?>',
                        <?php echo $summary['revenue']; ?>,
                        <?php echo $summary['cost']; ?>,
                        <?php echo $summary['profit']; ?>,
                        <?php echo $cat_margin; ?>,
                        <?php echo $summary['items']; ?>
                    ]);
                <?php endforeach; ?>

                var ws = XLSX.utils.aoa_to_sheet(ws_data);
                var wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Financial Report");
                XLSX.writeFile(wb, "financial_report_<?php echo date('Y-m-d'); ?>.xlsx");
            });

            // Auto-submit form on filter change
            $('select').change(function() {
                $('#filterForm').submit();
            });
        });
    </script>
</body>

</html>