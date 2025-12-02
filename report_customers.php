<?php
/**
 * Customer Report Page
 */
require("config.php");
require("include/functions.php");
require("include/pos_functions.php");
require("include/authentication.php");
require("include/admin_constants.php");

// Require reports permission
require_permission('reports');

// Get customer data with purchase history
$sql = "SELECT c.*, 
        COUNT(t.transaction_id) as total_transactions, 
        COALESCE(SUM(t.total_amount), 0) as total_spent,
        MAX(t.transaction_date) as last_purchase
        FROM customers c 
        LEFT JOIN transactions t ON c.phone = t.customer_phone AND t.status = 'completed'
        WHERE c.is_active = 1
        GROUP BY c.id
        ORDER BY total_spent DESC";
$query = $dbh->prepare($sql);
$query->execute();
$customers = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Customer Report | POS System</title>
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
                            <h4 class="page-title">Customer Report</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="reports_dashboard.php">Reports</a></li>
                                <li class="breadcrumb-item active">Customer Report</li>
                            </ol>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="customerTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Customer Name</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Total Transactions</th>
                                                <th>Total Spent</th>
                                                <th>Last Purchase</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($customers as $c): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($c['name']); ?></td>
                                                <td><?php echo htmlspecialchars($c['phone']); ?></td>
                                                <td><?php echo htmlspecialchars($c['email'] ?? '-'); ?></td>
                                                <td><?php echo $c['total_transactions']; ?></td>
                                                <td><?php echo format_currency($dbh, $c['total_spent']); ?></td>
                                                <td><?php echo $c['last_purchase'] ? date('d M Y', strtotime($c['last_purchase'])) : 'Never'; ?></td>
                                                <td>
                                                    <a href="customer_details.php?id=<?php echo $c['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i> View
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
        $('#customerTable').DataTable({
            order: [[4, 'desc']] // Sort by Total Spent by default
        });
    });
    </script>
</body>
</html>
