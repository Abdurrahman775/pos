<?php
require("config.php");
require("include/functions.php");
require_once("include/pos_functions.php");
require("include/authentication.php");
require("include/admin_constants.php");
require_permission('reports');

// Initialize filters
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$stock_status = isset($_GET['stock_status']) ? $_GET['stock_status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build dynamic SQL query
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.is_active = 1";

$params = array();

// Filter by category
if ($category_id > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
}

// Filter by stock status
if ($stock_status === 'low') {
    $sql .= " AND p.qty_in_stock <= p.low_stock_alert";
} elseif ($stock_status === 'out') {
    $sql .= " AND p.qty_in_stock = 0";
} elseif ($stock_status === 'ok') {
    $sql .= " AND p.qty_in_stock > p.low_stock_alert";
}

// Search filter
if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$sql .= " ORDER BY p.name ASC";

$query = $dbh->prepare($sql);
$query->execute($params);
$products = $query->fetchAll(PDO::FETCH_ASSOC);

// Get all categories for filter dropdown
$cat_sql = "SELECT id, name FROM categories ORDER BY name ASC";
$cat_query = $dbh->prepare($cat_sql);
$cat_query->execute();
$categories = $cat_query->fetchAll(PDO::FETCH_ASSOC);

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

                <!-- Filters Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="header-title mb-3">Filter Report</h5>
                                <form method="GET" action="" id="filterForm">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Search Product</label>
                                                <input type="text" class="form-control form-control-sm" name="search"
                                                    placeholder="Product name or description" value="<?php echo htmlspecialchars($search); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Category</label>
                                                <select class="form-control form-control-sm" name="category_id">
                                                    <option value="">-- All Categories --</option>
                                                    <?php foreach ($categories as $cat): ?>
                                                        <option value="<?php echo $cat['id']; ?>"
                                                            <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($cat['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Stock Status</label>
                                                <select class="form-control form-control-sm" name="stock_status">
                                                    <option value="">-- All Status --</option>
                                                    <option value="ok" <?php echo ($stock_status === 'ok') ? 'selected' : ''; ?>>In Stock</option>
                                                    <option value="low" <?php echo ($stock_status === 'low') ? 'selected' : ''; ?>>Low Stock</option>
                                                    <option value="out" <?php echo ($stock_status === 'out') ? 'selected' : ''; ?>>Out of Stock</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div>
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fa fa-filter"></i> Apply Filter
                                                    </button>
                                                    <a href="report_inventory.php" class="btn btn-secondary btn-sm">
                                                        <i class="fa fa-refresh"></i> Clear
                                                    </a>
                                                    <button type="button" class="btn btn-success btn-sm" id="exportBtn">
                                                        <i class="fa fa-download"></i> Export
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.min.js"></script>
    <script src="template/assets/js/app.js"></script>
    <script>
        $(document).ready(function() {
            $('#inventoryTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            // Export to Excel/CSV
            $('#exportBtn').click(function() {
                var table = $('#inventoryTable').DataTable();
                var data = table.rows({
                    search: 'applied'
                }).data();

                var ws_data = [
                    ['Product Name', 'Category', 'Qty', 'Cost Price', 'Selling Price', 'Total Cost', 'Total Sales', 'Status']
                ];

                data.each(function(row) {
                    ws_data.push([
                        $(row[0]).text(),
                        $(row[1]).text(),
                        $(row[2]).text(),
                        $(row[3]).text(),
                        $(row[4]).text(),
                        $(row[5]).text(),
                        $(row[6]).text(),
                        $(row[7]).text()
                    ]);
                });

                var ws = XLSX.utils.aoa_to_sheet(ws_data);
                var wb = XLSX.utils.book_new();
                XLSX.utils.book_append_sheet(wb, ws, "Inventory");
                XLSX.writeFile(wb, "inventory_report_<?php echo date('Y-m-d'); ?>.xlsx");
            });

            // Load Charts
            $.ajax({
                url: 'ajax/get_report_data.php',
                data: {
                    action: 'inventory_summary',
                    category_id: <?php echo $category_id; ?>,
                    stock_status: '<?php echo $stock_status; ?>'
                },
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
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });

                        // Status Chart
                        new Chart(document.getElementById('statusChart'), {
                            type: 'pie',
                            data: {
                                labels: data.status_labels,
                                datasets: [{
                                    data: data.status_values,
                                    backgroundColor: ['#03d87f', '#f5325c', '#ffc107']
                                }]
                            },
                            options: {
                                responsive: true
                            }
                        });
                    }
                }
            });

            // Real-time search and filter
            $('#filterForm').on('change', 'select', function() {
                $('#filterForm').submit();
            });
        });
    </script>
</body>

</html>