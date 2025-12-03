<?php

/**
 * Supplier Management Page
 * Manage supplier database
 */
require("config.php");
require("include/functions.php");
require_once("include/pos_functions.php");
require("include/authentication.php");
require("include/admin_constants.php");

// Require suppliers permission
require_permission('suppliers');

$error = '';
$success = '';

// Handle add supplier
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_supplier'])) {
    $supplier_name = trim($_POST['supplier_name']);
    $contact_name = trim($_POST['contact_name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    if (empty($supplier_name)) {
        $error = 'Supplier name is required';
    } else {
        try {
            $sql = "INSERT INTO suppliers (supplier_name, contact_name, phone, email, address, reg_by, reg_date) 
                    VALUES (:supplier_name, :contact_name, :phone, :email, :address, :reg_by, NOW())";
            $query = $dbh->prepare($sql);
            $query->bindParam(':supplier_name', $supplier_name, PDO::PARAM_STR);
            $query->bindParam(':contact_name', $contact_name, PDO::PARAM_STR);
            $query->bindParam(':phone', $phone, PDO::PARAM_STR);
            $query->bindParam(':email', $email, PDO::PARAM_STR);
            $query->bindParam(':address', $address, PDO::PARAM_STR);
            $query->bindParam(':reg_by', $_SESSION['pos_admin'], PDO::PARAM_STR);

            if ($query->execute()) {
                log_activity($dbh, 'ADD_SUPPLIER', "Added supplier: $supplier_name");
                $success = 'Supplier added successfully!';
            }
        } catch (PDOException $e) {
            $error = 'Error adding supplier: ' . $e->getMessage();
        }
    }
}

// Get all suppliers
$sql = "SELECT * FROM suppliers WHERE is_active = 1 ORDER BY supplier_name ASC";
$query = $dbh->prepare($sql);
$query->execute();
$suppliers = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Manage Suppliers | POS System</title>
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
                            <h4 class="page-title">Manage Suppliers</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="all_products.php">Inventory</a></li>
                                <li class="breadcrumb-item active">Suppliers</li>
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
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Add New Supplier</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="supplier_name">Supplier Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="supplier_name" name="supplier_name" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="contact_name">Contact Person</label>
                                        <input type="text" class="form-control" id="contact_name" name="contact_name">
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="text" class="form-control" id="phone" name="phone">
                                    </div>

                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email">
                                    </div>

                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                                    </div>

                                    <button type="submit" name="add_supplier" class="btn btn-primary btn-block">
                                        <i class="fas fa-plus mr-1"></i> Add Supplier
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">All Suppliers</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="suppliersTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Supplier Name</th>
                                                <th>Contact Person</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Products</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($suppliers as $supplier):
                                                // Count products from this supplier
                                                $sql = "SELECT COUNT(*) FROM products WHERE supplier_id = :sup_id AND is_active = 1";
                                                $query = $dbh->prepare($sql);
                                                $query->bindParam(':sup_id', $supplier['id'], PDO::PARAM_INT);
                                                $query->execute();
                                                $product_count = $query->fetchColumn();
                                            ?>
                                                <tr>
                                                    <td><?php echo $supplier['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($supplier['supplier_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($supplier['contact_name'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($supplier['phone'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($supplier['email'] ?? '-'); ?></td>
                                                    <td><?php echo $product_count; ?></td>
                                                    <td>
                                                        <a href="edit_supplier.php?id=<?php echo $supplier['id']; ?>"
                                                            class="btn btn-sm btn-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="supplier_details.php?id=<?php echo $supplier['id']; ?>"
                                                            class="btn btn-sm btn-info" title="View Details">
                                                            <i class="fas fa-eye"></i>
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
            $('#suppliersTable').DataTable({
                pageLength: 25
            });
        });
    </script>
</body>

</html>