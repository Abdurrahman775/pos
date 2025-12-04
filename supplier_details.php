<?php
/**
 * Supplier Details Page
 * View supplier info and associated products
 */
require("config.php");
require("include/functions.php");
require("include/authentication.php");
require("include/admin_constants.php");

// Require suppliers permission
require_permission('suppliers');

$id = $_GET['id'] ?? 0;

// Fetch supplier details
$sql = "SELECT * FROM suppliers WHERE id = :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->execute();
$supplier = $query->fetch(PDO::FETCH_ASSOC);

if (!$supplier) {
    header("Location: manage_suppliers.php");
    exit;
}

// Fetch products from this supplier
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.supplier_id = :id AND p.is_active = 1 
        ORDER BY p.name ASC";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->execute();
$products = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Supplier Details | POS System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="template/assets/images/favicon.ico">
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="template/plugins/datatables/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="template/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
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
                            <h4 class="page-title">Supplier Details</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="all_products.php">Inventory</a></li>
                                <li class="breadcrumb-item"><a href="manage_suppliers.php">Suppliers</a></li>
                                <li class="breadcrumb-item active">Supplier Details</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-primary">
                                <h5 class="card-title text-white mb-0">Supplier Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <div class="avatar-box thumb-xl align-self-center mr-2">
                                        <span class="avatar-title bg-soft-primary rounded-circle text-primary">
                                            <?php echo strtoupper(substr($supplier['supplier_name'], 0, 1)); ?>
                                        </span>
                                    </div>
                                    <h4 class="mt-3 mb-1"><?php echo htmlspecialchars($supplier['supplier_name']); ?></h4>
                                    <p class="text-muted mb-0">ID: #<?php echo $supplier['id']; ?></p>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <p class="mb-2"><strong>Contact Person:</strong> <span class="float-right text-muted"><?php echo htmlspecialchars($supplier['contact_name'] ?? '-'); ?></span></p>
                                        <p class="mb-2"><strong>Phone:</strong> <span class="float-right text-muted"><?php echo htmlspecialchars($supplier['phone'] ?? '-'); ?></span></p>
                                        <p class="mb-2"><strong>Email:</strong> <span class="float-right text-muted"><?php echo htmlspecialchars($supplier['email'] ?? '-'); ?></span></p>
                                        <p class="mb-2"><strong>Registered Date:</strong> <span class="float-right text-muted"><?php echo date('d M, Y', strtotime($supplier['reg_date'])); ?></span></p>
                                    </div>
                                </div>
                                <hr>
                                <h6 class="mt-3">Address:</h6>
                                <p class="text-muted"><?php echo nl2br(htmlspecialchars($supplier['address'] ?? '-')); ?></p>
                                
                                <div class="mt-4">
                                    <a href="edit_supplier.php?id=<?php echo $supplier['id']; ?>" class="btn btn-primary btn-block"><i class="fas fa-edit mr-1"></i> Edit Supplier</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Supplied Products (<?php echo count($products); ?>)</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="productsTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Product Name</th>
                                                <th>Category</th>
                                                <th>Stock</th>
                                                <th>Price</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $product): ?>
                                                <tr>
                                                    <td><?php echo $product['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['category_name'] ?? '-'); ?></td>
                                                    <td>
                                                        <?php if ($product['qty_in_stock'] <= $product['low_stock_alert']): ?>
                                                            <span class="badge badge-danger"><?php echo $product['qty_in_stock']; ?></span>
                                                        <?php else: ?>
                                                            <span class="badge badge-success"><?php echo $product['qty_in_stock']; ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo get_currency($dbh) . number_format($product['selling_price'], 2); ?></td>
                                                    <td>
                                                        <?php if ($product['is_active']): ?>
                                                            <span class="badge badge-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger">Inactive</span>
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
    <script src="template/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="template/plugins/datatables/dataTables.bootstrap5.min.js"></script>
    <script src="template/assets/js/app.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#productsTable').DataTable({
                pageLength: 10
            });
        });
    </script>
</body>

</html>
