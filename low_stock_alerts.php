<?php

/**
 * Low Stock Alerts Page
 * Display products below minimum stock level
 */
require("config.php");
require("include/functions.php");
require_once("include/pos_functions.php");
require("include/authentication.php");
require("include/admin_constants.php");

// Require low_stock permission
require_permission('low_stock');

$low_stock_products = get_low_stock_products($dbh);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Low Stock Alerts | POS System</title>
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
    <?php include('include/sidebar.php'); ?>
    <div class="page-wrapper">
        <div class="topbar"><?php require('template/top_nav_admin.php'); ?></div>
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Low Stock Alerts</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="all_products.php">Inventory</a></li>
                                <li class="breadcrumb-item active">Low Stock Alerts</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <?php if (empty($low_stock_products)): ?>
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
                                    <h4 class="mt-3">All Stock Levels are Good!</h4>
                                    <p class="text-muted">No products are below their minimum stock level.</p>
                                    <a href="all_products.php" class="btn btn-primary mt-2">
                                        <i class="fas fa-boxes mr-1"></i> View All Products
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="card">
                                <div class="card-header bg-warning">
                                    <h5 class="card-title text-white mb-0">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        <?php echo count($low_stock_products); ?> Product(s) Below Minimum Stock Level
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="lowStockTable" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Product ID</th>
                                                    <th>Product Name</th>
                                                    <th>Category</th>
                                                    <th>Current Stock</th>
                                                    <th>Min. Stock Level</th>
                                                    <th>Shortage</th>
                                                    <th>Price</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($low_stock_products as $product):
                                                    $shortage = $product['min_stock_level'] - $product['stock_quantity'];
                                                ?>
                                                    <tr>
                                                        <td><?php echo $product['id']; ?></td>
                                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                                        <td><?php echo htmlspecialchars($product['category_name'] ?? '-'); ?></td>
                                                        <td>
                                                            <span class="badge badge-danger"><?php echo $product['stock_quantity']; ?></span>
                                                        </td>
                                                        <td><?php echo $product['min_stock_level']; ?></td>
                                                        <td>
                                                            <span class="text-danger font-weight-bold"><?php echo $shortage; ?></span>
                                                        </td>
                                                        <td><?php echo format_currency($dbh, $product['selling_price']); ?></td>
                                                        <td>
                                                            <a href="edit_product.php?id=<?php echo $product['id']; ?>"
                                                                class="btn btn-sm btn-primary" title="Edit Product">
                                                                <i class="fas fa-edit"></i> Update Stock
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
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
            $('#lowStockTable').DataTable({
                order: [
                    [5, 'desc']
                ],
                pageLength: 25
            });
        });
    </script>
</body>

</html>