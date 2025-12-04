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
    <?php include('include/sidebar.php'); ?>
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
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label>Date Range</label>
                                        <div class="input-daterange input-group" id="date-range">
                                            <input type="date" class="form-control" name="start_date" id="start_date" placeholder="Start Date" />
                                            <input type="date" class="form-control" name="end_date" id="end_date" placeholder="End Date" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Cashier</label>
                                        <select class="form-control" id="cashier_id">
                                            <option value="">All Cashiers</option>
                                            <?php
                                            $cashiers_sql = "SELECT id, username FROM admins ORDER BY username";
                                            $cashiers_query = $dbh->prepare($cashiers_sql);
                                            $cashiers_query->execute();
                                            while ($cashier = $cashiers_query->fetch(PDO::FETCH_ASSOC)) {
                                                echo "<option value='" . $cashier['id'] . "'>" . $cashier['username'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Payment Method</label>
                                        <select class="form-control" id="payment_method">
                                            <option value="">All Methods</option>
                                            <option value="CASH">Cash</option>
                                            <option value="POS">POS</option>
                                            <option value="TRANSFER">Transfer</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>&nbsp;</label>
                                        <button class="btn btn-primary btn-block" id="filterBtn">Filter</button>
                                    </div>
                                </div>
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
            // Client-side processing: fetch all transactions in one request
            var table = $('#transactionsTable').DataTable({
                "processing": true,
                "serverSide": false,
                "order": [
                    [1, "desc"]
                ],
                "ajax": {
                    "url": "datatables/transactions.php",
                    "type": "POST",
                    "data": function(d) {
                        // request the full dataset from the server-side script
                        d.fetch_all = 1;
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.cashier_id = $('#cashier_id').val();
                        d.payment_method = $('#payment_method').val();
                    }
                },
                "columns": [{
                        "data": 0
                    },
                    {
                        "data": 1
                    },
                    {
                        "data": 2
                    },
                    {
                        "data": 3
                    },
                    {
                        "data": 4
                    },
                    {
                        "data": 5
                    },
                    {
                        "data": 6
                    },
                    {
                        "data": 7
                    },
                    {
                        "data": 8
                    },
                    {
                        "data": 9
                    },
                    {
                        "data": 10,
                        "orderable": false
                    }
                ]
            });

            $('#filterBtn').click(function() {
                table.draw();
            });
        });

        function printReceipt(transactionId) {
            window.open('receipt.php?id=' + transactionId, '_blank', 'width=400,height=600');
        }
    </script>
</body>

</html>