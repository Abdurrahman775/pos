<?php

/**
 * Customer Details Page
 * View detailed customer information and purchase history
 */
require("config.php");
require("include/functions.php");
require("include/admin_authentication.php");
require("include/admin_constants.php");

$customer_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get customer details
$sql = "SELECT * FROM customers WHERE id = :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $customer_id, PDO::PARAM_INT);
$query->execute();
$customer = $query->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    header("Location: customers.php");
    exit();
}

// Get customer transactions
$sql = "SELECT * FROM transactions WHERE customer_id = :customer_id ORDER BY transaction_date DESC LIMIT 50";
$query = $dbh->prepare($sql);
$query->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
$query->execute();
$transactions = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Customer Details | POS System</title>
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
                            <div class="row">
                                <div class="col">
                                    <h4 class="page-title">Customer Details</h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                        <li class="breadcrumb-item"><a href="customers.php">Customers</a></li>
                                        <li class="breadcrumb-item active">Customer Details</li>
                                    </ol>
                                </div>
                                <div class="col-auto">
                                    <a href="edit_customer.php?id=<?php echo $customer_id; ?>" class="btn btn-primary">
                                        <i class="fas fa-edit mr-1"></i> Edit Customer
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Customer Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="font-weight-bold">Name:</label>
                                    <p><?php echo htmlspecialchars($customer['name']); ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="font-weight-bold">Phone:</label>
                                    <p><?php echo htmlspecialchars($customer['phone']); ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="font-weight-bold">Email:</label>
                                    <p><?php echo htmlspecialchars($customer['email'] ?: 'N/A'); ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="font-weight-bold">Address:</label>
                                    <p><?php echo htmlspecialchars($customer['address'] ?: 'N/A'); ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="font-weight-bold">Total Purchases:</label>
                                    <p><?php echo format_currency($dbh, $customer['total_purchases']); ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="font-weight-bold">Member Since:</label>
                                    <p><?php echo date('d/m/Y', strtotime($customer['created_at'])); ?></p>
                                </div>
                                <div class="mb-3">
                                    <label class="font-weight-bold">Status:</label>
                                    <p>
                                        <?php if ($customer['is_active']): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Purchase History</h5>
                            </div>
                            <div class="card-body">
                                <?php if (count($transactions) > 0): ?>
                                    <div class="table-responsive">
                                        <table id="transactionsTable" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Transaction ID</th>
                                                    <th>Date</th>
                                                    <th>Total</th>
                                                    <th>Payment Method</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($transactions as $trans): ?>
                                                    <tr>
                                                        <td>#<?php echo $trans['id']; ?></td>
                                                        <td><?php echo date('d/m/Y H:i', strtotime($trans['transaction_date'])); ?></td>
                                                        <td><?php echo format_currency($dbh, $trans['total_amount']); ?></td>
                                                        <td><?php echo htmlspecialchars($trans['payment_method']); ?></td>
                                                        <td>
                                                            <?php
                                                            $status_badges = [
                                                                'completed' => '<span class="badge badge-success">Completed</span>',
                                                                'void' => '<span class="badge badge-danger">Void</span>',
                                                                'refunded' => '<span class="badge badge-warning">Refunded</span>',
                                                                'held' => '<span class="badge badge-info">Held</span>'
                                                            ];
                                                            echo $status_badges[$trans['status']] ?? '<span class="badge badge-secondary">Unknown</span>';
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <a href="receipt.php?id=<?php echo $trans['id']; ?>" class="btn btn-sm btn-info" target="_blank">
                                                                <i class="fas fa-receipt"></i> Receipt
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle mr-2"></i> No purchase history found for this customer.
                                    </div>
                                <?php endif; ?>
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
            $('#transactionsTable').DataTable({
                pageLength: 10,
                order: [
                    [1, 'desc']
                ]
            });
        });
    </script>
</body>

</html>