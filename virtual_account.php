<?php
require("config.php");
require("include/functions.php");
require("include/admin_authentication.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Virtual Account | POS System</title>
    <meta name="viewport" content="width=device-width, shrink-to-fit=no">
    <meta content="Virtual Account - POS System" name="description" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="template/assets/images/favicon.ico">
    <!-- App css -->
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <link href="template/plugins/datatables/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="template/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="template/plugins/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />
</head>

<body class="dark-sidenav">
    <?php include('include/sidebar.php'); ?>
    <div class="page-wrapper">
        <div class="topbar">
            <?php require('template/top_nav_admin.php'); ?>
        </div>
        <div class="page-content">
            <div class="container-fluid">
                <!-- Page Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">Virtual Account</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Virtual Account</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Balance Summary Cards -->
                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="avatar-md rounded-circle bg-soft-success">
                                            <i data-feather="dollar-sign" class="align-self-center icon-dual-success icon-lg"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 text-right">
                                        <p class="text-muted mb-2 font-13 font-weight-semibold">CASH Balance</p>
                                        <h3 class="mt-0 mb-1" id="cash_balance">
                                            <span class="spinner-border spinner-border-sm" role="status"></span>
                                        </h3>
                                        <p class="text-muted mb-0" id="cash_count">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="avatar-md rounded-circle bg-soft-primary">
                                            <i data-feather="credit-card" class="align-self-center icon-dual-primary icon-lg"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 text-right">
                                        <p class="text-muted mb-2 font-13 font-weight-semibold">POS Balance</p>
                                        <h3 class="mt-0 mb-1" id="pos_balance">
                                            <span class="spinner-border spinner-border-sm" role="status"></span>
                                        </h3>
                                        <p class="text-muted mb-0" id="pos_count">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="avatar-md rounded-circle bg-soft-info">
                                            <i data-feather="bar-chart-2" class="align-self-center icon-dual-info icon-lg"></i>
                                        </div>
                                    </div>
                                    <div class="col-8 text-right">
                                        <p class="text-muted mb-2 font-13 font-weight-semibold">Total Balance</p>
                                        <h3 class="mt-0 mb-1" id="total_balance">
                                            <span class="spinner-border spinner-border-sm" role="status"></span>
                                        </h3>
                                        <p class="text-muted mb-0" id="total_count">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mt-0">Filters</h5>
                                <form id="filter_form">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Start Date</label>
                                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo date('Y-m-01'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>End Date</label>
                                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Cashier</label>
                                                <select class="form-control" id="cashier_id" name="cashier_id">
                                                    <option value="">All Cashiers</option>
                                                    <?php
                                                    $sql = "SELECT id, fname, sname, username FROM admins WHERE is_active = 1 ORDER BY fname ASC";
                                                    $query = $dbh->prepare($sql);
                                                    $query->execute();
                                                    $cashiers = $query->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach ($cashiers as $cashier) {
                                                        $name = !empty($cashier['fname']) ? $cashier['fname'] . ' ' . $cashier['sname'] : $cashier['username'];
                                                        echo '<option value="' . $cashier['id'] . '">' . htmlspecialchars($name) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <button type="button" id="filter_btn" class="btn btn-primary btn-block">
                                                    <i class="fas fa-filter"></i> Apply Filter
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Transaction History</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-striped table-bordered nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Transaction ID</th>
                                                <th>Date</th>
                                                <th>Customer</th>
                                                <th>Payment Method</th>
                                                <th>Amount</th>
                                                <th>Cashier</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <footer class="footer text-center text-sm-left">
                <?php include('template/copyright.php'); ?>
                <span class="d-none d-sm-inline-block float-right"><?php include('template/developed_by.php'); ?></span>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="template/assets/js/jquery.min.js"></script>
    <script src="template/assets/js/bootstrap.bundle.min.js"></script>
    <script src="template/assets/js/metismenu.min.js"></script>
    <script src="template/assets/js/waves.js"></script>
    <script src="template/assets/js/feather.min.js"></script>
    <script src="template/plugins/moment/moment.js"></script>
    <script src="template/plugins/daterangepicker/daterangepicker.js"></script>
    <script src="template/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="template/plugins/datatables/dataTables.bootstrap5.min.js"></script>
    <script src="template/plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="template/plugins/datatables/responsive.bootstrap4.min.js"></script>
    <script src="template/assets/js/app.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var dataTable = $("#datatable").DataTable({
                "responsive": true,
                "ordering": true,
                "order": [[1, "desc"]], // Sort by transaction ID descending
                "pageLength": 25,
                "processing": true,
                "serverSide": true,
                "bLengthChange": true,
                "ajax": {
                    url: "datatables/virtual_account.php",
                    type: "POST",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.cashier_id = $('#cashier_id').val();
                    }
                }
            });

            // Load account balances
            function loadBalances() {
                $.ajax({
                    url: 'ajax/get_account_balance.php',
                    method: 'GET',
                    data: {
                        start_date: $('#start_date').val(),
                        end_date: $('#end_date').val(),
                        cashier_id: $('#cashier_id').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#cash_balance').html('<?php echo get_currency($dbh); ?>' + parseFloat(response.data.cash_balance).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#cash_count').text(response.data.cash_count + ' transactions');
                            
                            $('#pos_balance').html('<?php echo get_currency($dbh); ?>' + parseFloat(response.data.pos_balance).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#pos_count').text(response.data.pos_count + ' transactions');
                            
                            $('#total_balance').html('<?php echo get_currency($dbh); ?>' + parseFloat(response.data.total_balance).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                            $('#total_count').text(response.data.total_count + ' transactions');
                        }
                    },
                    error: function() {
                        $('#cash_balance').html('<span class="text-danger">Error</span>');
                        $('#pos_balance').html('<span class="text-danger">Error</span>');
                        $('#total_balance').html('<span class="text-danger">Error</span>');
                    }
                });
            }

            // Filter button click
            $('#filter_btn').on('click', function() {
                dataTable.ajax.reload();
                loadBalances();
            });

            // Load balances on page load
            loadBalances();
        });
    </script>
</body>

</html>
