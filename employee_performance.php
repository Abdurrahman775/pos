<?php

/**
 * Employee Performance Report Page
 */
require("config.php");
require("include/functions.php");
require_once("include/pos_functions.php");
require("include/authentication.php");
require("include/admin_constants.php");

// Require reports permission
require_permission('reports');

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get employee performance data
$sql = "SELECT u.id, u.fname, u.lname, u.username,
        COUNT(t.transaction_id) as total_transactions, 
        COALESCE(SUM(t.total_amount), 0) as total_sales
        FROM users u 
        LEFT JOIN transactions t ON u.id = t.user_id 
        AND DATE(t.transaction_date) BETWEEN :start_date AND :end_date 
        AND t.status = 'completed'
        WHERE u.is_active = 1
        GROUP BY u.id
        ORDER BY total_sales DESC";
$query = $dbh->prepare($sql);
$query->bindParam(':start_date', $start_date, PDO::PARAM_STR);
$query->bindParam(':end_date', $end_date, PDO::PARAM_STR);
$query->execute();
$employees = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Employee Performance | POS System</title>
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
                            <h4 class="page-title">Employee Performance</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="reports_dashboard.php">Reports</a></li>
                                <li class="breadcrumb-item active">Employee Performance</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="GET" action="" class="form-inline mb-4">
                                    <div class="form-group mr-2">
                                        <label for="start_date" class="mr-2">Start Date:</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                                    </div>
                                    <div class="form-group mr-2">
                                        <label for="end_date" class="mr-2">End Date:</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </form>

                                <div class="table-responsive">
                                    <table id="performanceTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Employee Name</th>
                                                <th>Username</th>
                                                <th>Total Transactions</th>
                                                <th>Total Sales</th>
                                                <th>Average Sale Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($employees as $e):
                                                $avg_sale = $e['total_transactions'] > 0 ? $e['total_sales'] / $e['total_transactions'] : 0;
                                            ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($e['fname'] . ' ' . $e['lname']); ?></td>
                                                    <td><?php echo htmlspecialchars($e['username']); ?></td>
                                                    <td><?php echo $e['total_transactions']; ?></td>
                                                    <td><?php echo format_currency($dbh, $e['total_sales']); ?></td>
                                                    <td><?php echo format_currency($dbh, $avg_sale); ?></td>
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
            $('#performanceTable').DataTable({
                order: [
                    [3, 'desc']
                ] // Sort by Total Sales by default
            });
        });
    </script>
</body>

</html>