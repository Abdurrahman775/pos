<?php

/**
 * Top Products Report Page - Dynamic
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
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'units'; // units, revenue, profit
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$cashier_id = isset($_GET['cashier_id']) ? intval($_GET['cashier_id']) : 0;

// Build dynamic SQL query
$sql = "SELECT 
            p.id,
            p.name,
            p.selling_price,
            p.cost_price,
            c.name as category_name,
            SUM(ti.quantity) as total_sold,
            SUM(ti.line_total) as revenue,
            SUM(ti.quantity * p.cost_price) as total_cost,
            (SUM(ti.line_total) - SUM(ti.quantity * p.cost_price)) as total_profit
        FROM transaction_items ti
        JOIN transactions t ON ti.transaction_id = t.transaction_id
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

$sql .= " GROUP BY p.id, p.name, p.selling_price, p.cost_price, c.name";

// Add sort order
if ($sort_by === 'revenue') {
    $sql .= " ORDER BY revenue DESC";
} elseif ($sort_by === 'profit') {
    $sql .= " ORDER BY total_profit DESC";
} else {
    $sql .= " ORDER BY total_sold DESC";
}

$sql .= " LIMIT :limit";

$query = $dbh->prepare($sql);
$query->bindParam(':limit', $limit, PDO::PARAM_INT);
foreach ($params as $key => $value) {
    $query->bindParam($key, $params[$key]);
}
$query->execute();
$top_products = $query->fetchAll(PDO::FETCH_ASSOC);

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

// Calculate summary statistics
$total_units_sold = 0;
$total_revenue = 0;
$total_cost = 0;
$total_profit = 0;
$avg_profit_margin = 0;

foreach ($top_products as $product) {
    $total_units_sold += $product['total_sold'];
    $total_revenue += $product['revenue'];
    $total_cost += $product['total_cost'];
    $total_profit += $product['total_profit'];
}

$avg_profit_margin = $total_revenue > 0 ? ($total_profit / $total_revenue) * 100 : 0;
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
                            <h4 class="page-title">Top Products Report</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="reports_dashboard.php">Reports</a></li>
                                <li class="breadcrumb-item active">Top Products</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="header-title mb-3">Filter Report</h5>
                                <form method="GET" action="" id="filterForm">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Start Date</label>
                                                <input type="date" class="form-control form-control-sm" name="start_date"
                                                    value="<?php echo htmlspecialchars($start_date); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>End Date</label>
                                                <input type="date" class="form-control form-control-sm" name="end_date"
                                                    value="<?php echo htmlspecialchars($end_date); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Category</label>
                                                <select class="form-control form-control-sm" name="category_id">
                                                    <option value="">All Categories</option>
                                                    <?php foreach ($categories as $cat): ?>
                                                        <option value="<?php echo $cat['id']; ?>"
                                                            <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($cat['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Cashier</label>
                                                <select class="form-control form-control-sm" name="cashier_id">
                                                    <option value="">All Cashiers</option>
                                                    <?php foreach ($cashiers as $cashier): ?>
                                                        <option value="<?php echo $cashier['id']; ?>"
                                                            <?php echo ($cashier_id == $cashier['id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($cashier['username']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Sort By</label>
                                                <select class="form-control form-control-sm" name="sort_by">
                                                    <option value="units" <?php echo ($sort_by === 'units') ? 'selected' : ''; ?>>Units Sold</option>
                                                    <option value="revenue" <?php echo ($sort_by === 'revenue') ? 'selected' : ''; ?>>Revenue</option>
                                                    <option value="profit" <?php echo ($sort_by === 'profit') ? 'selected' : ''; ?>>Profit</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Limit</label>
                                                <select class="form-control form-control-sm" name="limit">
                                                    <option value="5" <?php echo ($limit == 5) ? 'selected' : ''; ?>>Top 5</option>
                                                    <option value="10" <?php echo ($limit == 10) ? 'selected' : ''; ?>>Top 10</option>
                                                    <option value="20" <?php echo ($limit == 20) ? 'selected' : ''; ?>>Top 20</option>
                                                    <option value="50" <?php echo ($limit == 50) ? 'selected' : ''; ?>>Top 50</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary btn-sm">
                                                <i class="fa fa-filter"></i> Apply Filter
                                            </button>
                                            <a href="report_top_products.php" class="btn btn-secondary btn-sm">
                                                <i class="fa fa-refresh"></i> Clear
                                            </a>
                                            <button type="button" class="btn btn-success btn-sm" id="exportBtn">
                                                <i class="fa fa-download"></i> Export
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white-50 font-weight-bold mb-2">Total Units Sold</h6>
                                <h3 class="text-white"><?php echo number_format($total_units_sold); ?></h3>
                                <p class="mb-0 text-white-50 font-size-12">All Top Products</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white-50 font-weight-bold mb-2">Total Revenue</h6>
                                <h3 class="text-white"><?php echo format_currency($dbh, $total_revenue); ?></h3>
                                <p class="mb-0 text-white-50 font-size-12">From Top Products</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white-50 font-weight-bold mb-2">Total Profit</h6>
                                <h3 class="text-white"><?php echo format_currency($dbh, $total_profit); ?></h3>
                                <p class="mb-0 text-white-50 font-size-12">Margin: <?php echo number_format($avg_profit_margin, 2); ?>%</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body">
                                <h6 class="card-title text-dark-50 font-weight-bold mb-2">Avg Price</h6>
                                <h3 class="text-dark"><?php echo format_currency($dbh, ($total_units_sold > 0 ? $total_revenue / $total_units_sold : 0)); ?></h3>
                                <p class="mb-0 text-dark-50 font-size-12">Per Unit</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Products Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="header-title mt-0 mb-3">Detailed Product Analysis</h5>
                                <div class="table-responsive">
                                    <table id="topProductsTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Product Name</th>
                                                <th>Category</th>
                                                <th>Units Sold</th>
                                                <th>Selling Price</th>
                                                <th>Cost Price</th>
                                                <th>Revenue</th>
                                                <th>Cost</th>
                                                <th>Profit</th>
                                                <th>Margin %</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $rank = 1;
                                            foreach ($top_products as $p):
                                                $margin = $p['revenue'] > 0 ? ($p['total_profit'] / $p['revenue']) * 100 : 0;
                                            ?>
                                                <tr>
                                                    <td><strong><?php echo $rank++; ?></strong></td>
                                                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($p['category_name'] ?? '-'); ?></td>
                                                    <td class="text-right"><?php echo number_format($p['total_sold']); ?></td>
                                                    <td class="text-right"><?php echo format_currency($dbh, $p['selling_price']); ?></td>
                                                    <td class="text-right"><?php echo format_currency($dbh, $p['cost_price']); ?></td>
                                                    <td class="text-right"><strong><?php echo format_currency($dbh, $p['revenue']); ?></strong></td>
                                                    <td class="text-right"><?php echo format_currency($dbh, $p['total_cost']); ?></td>
                                                    <td class="text-right"><strong><?php echo format_currency($dbh, $p['total_profit']); ?></strong></td>
                                                    <td class="text-right">
                                                        <span class="badge <?php echo ($margin >= 20) ? 'badge-success' : (($margin >= 10) ? 'badge-warning' : 'badge-danger'); ?>">
                                                            <?php echo number_format($margin, 2); ?>%
                                                        </span>
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
            $('#topProductsTable').DataTable({
                "order": [
                    [0, "asc"]
                ],
                "pageLength": 10
            });

            // Prepare chart data
            var productNames = [];
            var unitsData = [];
            var revenueData = [];
            var profitData = [];

            <?php foreach ($top_products as $p): ?>
                productNames.push('<?php echo addslashes($p['name']); ?>');
                unitsData.push(<?php echo $p['total_sold']; ?>);
                revenueData.push(<?php echo $p['revenue']; ?>);
                profitData.push(<?php echo $p['total_profit']; ?>);
            <?php endforeach; ?>

            // Units Chart
            new Chart(document.getElementById('unitsChart'), {
                type: 'bar',
                data: {
                    labels: productNames,
                    datasets: [{
                        label: 'Units Sold',
                        data: unitsData,
                        backgroundColor: '#44a2d2'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });

            // Revenue vs Profit Chart
            new Chart(document.getElementById('revenueChart'), {
                type: 'bar',
                data: {
                    labels: productNames,
                    datasets: [{
                            label: 'Revenue',
                            data: revenueData,
                            backgroundColor: '#28a745'
                        },
                        {
                            label: 'Profit',
                            data: profitData,
                            backgroundColor: '#007bff'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        }
                    }
                }
            });

            // Export functionality
            $('#exportBtn').click(function() {
                var ws_data = [
                    ['TOP PRODUCTS REPORT', '', '', '', '', '', '', '', '', ''],
                    ['Period', '<?php echo $start_date; ?> to <?php echo $end_date; ?>', '', '', '', '', '', '', '', ''],
                    ['', '', '', '', '', '', '', '', '', ''],
                    ['SUMMARY', '', '', '', '', '', '', '', '', ''],
                    ['Total Units Sold', <?php echo $total_units_sold; ?>, '', '', '', '', '', '', '', ''],
                    ['Total Revenue', <?php echo $total_revenue; ?>, '', '', '', '', '', '', '', ''],
                    ['Total Profit', <?php echo $total_profit; ?>, '', '', '', '', '', '', '', ''],
                    ['Avg Profit Margin %', '<?php echo number_format($avg_profit_margin, 2); ?>', '', '', '', '', '', '', '', ''],
                    ['', '', '', '', '', '', '', '', '', ''],
                    ['DETAILED BREAKDOWN', '', '', '', '', '', '', '', '', ''],
                    ['Rank', 'Product Name', 'Category', 'Units Sold', 'Selling Price', 'Cost Price', 'Revenue', 'Cost', 'Profit', 'Margin %']
                ];

                <?php $rank = 1;
                foreach ($top_products as $p):
                    $margin = $p['revenue'] > 0 ? ($p['total_profit'] / $p['revenue']) * 100 : 0;
                ?>
                    ws_data.push([
                        <?php echo $rank++; ?>,
                        '<?php echo addslashes($p['name']); ?>',
                        '<?php echo addslashes($p['category_name'] ?? '-'); ?>',
                        <?php echo $p['total_sold']; ?>,
                        <?php echo $p['selling_price']; ?>,
                        <?php echo $p['cost_price']; ?>,
                        <?php echo $p['revenue']; ?>,
                        <?php echo $p['total_cost']; ?>,
                        <?php echo $p['total_profit']; ?>,
                        '<?php echo number_format($margin, 2); ?>'
                    ]);
                <?php endforeach; ?>

                var ws = XLSX.utils.aoa_to_sheet(ws_data);
                var wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Top Products");
                XLSX.writeFile(wb, "top_products_report_<?php echo date('Y-m-d'); ?>.xlsx");
            });

            // Auto-submit form on filter change
            $('select').change(function() {
                $('#filterForm').submit();
            });
        });
    </script>
</body>

</html>