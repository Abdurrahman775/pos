<?php
/**
 * Transactions List Page
 * View all transactions with filters and search
 */
require("config.php");
require("include/functions.php");
require_once("include/pos_functions.php");
require("include/authentication.php");
require("include/admin_constants.php");

// Require transactions permission
require_permission('transactions');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Transactions | POS System</title>
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
    <div class="left-sidenav">
        <div class="brand"><?php require('template/brand_admin.php'); ?></div>
        <div class="menu-content h-100" data-simplebar><?php require('include/menus.php'); ?></div>
    </div>
    <div class="page-wrapper">
        <div class="topbar"><?php require('template/top_nav_admin.php'); ?></div>
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Transactions</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Transactions</li>
                            </ol>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">All Transactions</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="transactionsTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Date</th>
                                                <th>Customer</th>
                                                <th>Subtotal</th>
                                                <th>Tax</th>
                                                <th>Discount</th>
                                                <th>Total</th>
                                                <th>Payment</th>
                                                <th>Status</th>
                                                <th>Cashier</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT t.*, a.username, c.name as customer_name
                                                    FROM transactions t
                                                    LEFT JOIN admins a ON t.user_id = a.id
                                                    LEFT JOIN customers c ON t.customer_id = c.customer_id
                                                    ORDER BY t.transaction_date DESC";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $transactions = $query->fetchAll(PDO::FETCH_ASSOC);
                                            
                                            foreach($transactions as $trans):
                                            ?>
                                            <tr>
                                                <td>#<?php echo $trans['transaction_id']; ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($trans['transaction_date'])); ?></td>
                                                <td><?php echo $trans['customer_name'] ?? 'Walk-in'; ?></td>
                                                <td><?php echo format_currency($dbh, $trans['subtotal']); ?></td>
                                                <td><?php echo format_currency($dbh, $trans['tax_amount']); ?></td>
                                                <td><?php echo format_currency($dbh, $trans['discount_amount']); ?></td>
                                                <td><strong><?php echo format_currency($dbh, $trans['total_amount']); ?></strong></td>
                                                <td><?php echo ucfirst($trans['payment_method']); ?></td>
                                                <td>
                                                    <?php
                                                    $badge = [
                                                        'completed' => 'success',
                                                        'void' => 'danger',
                                                        'refunded' => 'warning',
                                                        'held' => 'info'
                                                    ];
                                                    $status_badge = $badge[$trans['status']] ?? 'secondary';
                                                    ?>
                                                    <span class="badge badge-<?php echo $status_badge; ?>"><?php echo ucfirst($trans['status']); ?></span>
                                                </td>
                                                <td><?php echo $trans['username']; ?></td>
                                                <td>
                                                    <a href="view_transaction.php?id=<?php echo $trans['transaction_id']; ?>" 
                                                       class="btn btn-sm btn-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="#" onclick="printReceipt(<?php echo $trans['transaction_id']; ?>)" 
                                                       class="btn btn-sm btn-info" title="Print Receipt">
                                                        <i class="fas fa-print"></i>
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
    <script src="template/plugins/bootbox/bootbox.min.js"></script>
    <script src="datatables/datatables.min.js"></script>
    <script src="template/assets/js/app.js"></script>
    
    <script>
    $(document).ready(function() {
        $('#transactionsTable').DataTable({
            order: [[1, 'desc']],
            pageLength: 25
        });
    });
    
    function printReceipt(transactionId) {
        window.open('receipt.php?id=' + transactionId, '_blank', 'width=800,height=600');
    }
    </script>
</body>
</html>
