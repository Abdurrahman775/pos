<?php
/**
 * Edit Supplier Page
 * Update existing supplier details
 */
require("config.php");
require("include/functions.php");
require("include/authentication.php");
require("include/admin_constants.php");

// Require suppliers permission
require_permission('suppliers');

$id = $_GET['id'] ?? 0;
$error = '';
$success = '';

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

// Handle update supplier
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_supplier'])) {
    $supplier_name = trim($_POST['supplier_name']);
    $contact_name = trim($_POST['contact_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    if (empty($supplier_name)) {
        $error = 'Supplier name is required';
    } else {
        try {
            $sql = "UPDATE suppliers SET 
                    supplier_name = :supplier_name, 
                    contact_name = :contact_name, 
                    phone = :phone, 
                    email = :email, 
                    address = :address, 
                    updated_by = :updated_by 
                    WHERE id = :id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':supplier_name', $supplier_name, PDO::PARAM_STR);
            $query->bindParam(':contact_name', $contact_name, PDO::PARAM_STR);
            $query->bindParam(':phone', $phone, PDO::PARAM_STR);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->bindParam(':address', $address, PDO::PARAM_STR);
            $query->bindParam(':updated_by', $_SESSION['pos_admin'], PDO::PARAM_STR);
            $query->bindParam(':id', $id, PDO::PARAM_INT);

            if ($query->execute()) {
                log_activity($dbh, 'UPDATE_SUPPLIER', "Updated supplier: $supplier_name (ID: $id)");
                $success = 'Supplier updated successfully!';
                
                // Refresh supplier data
                $sql = "SELECT * FROM suppliers WHERE id = :id";
                $query = $dbh->prepare($sql);
                $query->bindParam(':id', $id, PDO::PARAM_INT);
                $query->execute();
                $supplier = $query->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            $error = 'Error updating supplier: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Edit Supplier | POS System</title>
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
                            <h4 class="page-title">Edit Supplier</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="all_products.php">Inventory</a></li>
                                <li class="breadcrumb-item"><a href="manage_suppliers.php">Suppliers</a></li>
                                <li class="breadcrumb-item active">Edit Supplier</li>
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
                    <div class="col-lg-8 mx-auto">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Update Supplier Details</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="supplier_name">Supplier Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="<?php echo htmlspecialchars($supplier['supplier_name']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="contact_name">Contact Person</label>
                                        <input type="text" class="form-control" id="contact_name" name="contact_name" value="<?php echo htmlspecialchars($supplier['contact_name'] ?? ''); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($supplier['phone'] ?? ''); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($supplier['email'] ?? ''); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($supplier['address'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <a href="manage_suppliers.php" class="btn btn-secondary btn-block">
                                                <i class="fas fa-arrow-left mr-1"></i> Cancel
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="submit" name="update_supplier" class="btn btn-primary btn-block">
                                                <i class="fas fa-save mr-1"></i> Update Supplier
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
