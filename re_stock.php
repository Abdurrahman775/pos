<?php
/**
 * Re-Stock Product Page
 * Update product stock quantity
 */
require("config.php");
require("include/functions.php");
require("include/authentication.php");
require("include/admin_constants.php");

// Require products permission
require_permission('products');

$token = $_GET['token'] ?? '';
$id = 0;
if ($token) {
    $id = base64_decode($token);
}

$error = '';
$success = '';

// Fetch product details
$sql = "SELECT * FROM products WHERE id = :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->execute();
$product = $query->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: all_products.php");
    exit;
}

// Handle stock update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_stock'])) {
    $new_stock = intval($_POST['new_stock']);
    
    if ($new_stock <= 0) {
        $error = 'Please enter a valid quantity greater than 0';
    } else {
        try {
            // Update stock
            $sql = "UPDATE products SET qty_in_stock = qty_in_stock + :new_stock, updated_by = :updated_by WHERE id = :id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':new_stock', $new_stock, PDO::PARAM_INT);
            $query->bindParam(':updated_by', $_SESSION['pos_admin'], PDO::PARAM_STR);
            $query->bindParam(':id', $id, PDO::PARAM_INT);

            if ($query->execute()) {
                log_activity($dbh, 'RESTOCK_PRODUCT', "Added $new_stock items to: {$product['name']} (ID: $id)");
                $success = 'Stock updated successfully!';
                
                // Refresh product data
                $sql = "SELECT * FROM products WHERE id = :id";
                $query = $dbh->prepare($sql);
                $query->bindParam(':id', $id, PDO::PARAM_INT);
                $query->execute();
                $product = $query->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            $error = 'Error updating stock: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Re-Stock Product | POS System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="template/assets/images/favicon.ico">
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
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
                            <h4 class="page-title">Re-Stock Product</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="all_products.php">Inventory</a></li>
                                <li class="breadcrumb-item active">Re-Stock</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-6 mx-auto">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Update Stock Level</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                                    <p class="text-muted">Current Stock: <strong><?php echo $product['qty_in_stock']; ?></strong></p>
                                </div>
                                
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="new_stock">Quantity to Add <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="new_stock" name="new_stock" min="1" required>
                                        <small class="form-text text-muted">Enter the number of items you are adding to the inventory.</small>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="all_products.php" class="btn btn-secondary btn-block">
                                                <i class="fas fa-arrow-left mr-1"></i> Cancel
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="submit" name="update_stock" class="btn btn-primary btn-block">
                                                <i class="fas fa-plus-circle mr-1"></i> Add Stock
                                            </button>
                                        </div>
                                    </div>
                                </form>
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
</body>

</html>
