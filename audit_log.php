<?php

/**
 * Audit Log Page
 * View system activity logs
 */
require("config.php");
require("include/functions.php");
require("include/admin_authentication.php");
require("include/admin_constants.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Audit Log | POS System</title>
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
                            <h4 class="page-title">Audit Log</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">Audit Log</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">System Activity Log</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="auditTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Date/Time</th>
                                                <th>User</th>
                                                <th>Action</th>
                                                <th>Description</th>
                                                <th>IP Address</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT al.*, a.username, a.fname, a.sname 
                                                    FROM auditlog al
                                                    LEFT JOIN admins a ON al.username = a.username
                                                    ORDER BY al.log_date DESC
                                                    LIMIT 500";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $logs = $query->fetchAll(PDO::FETCH_ASSOC);

                                            foreach ($logs as $log):
                                                $user_display = $log['fname'] && $log['sname']
                                                    ? $log['fname'] . ' ' . $log['sname']
                                                    : $log['username'];
                                            ?>
                                                <tr>
                                                    <td><?php echo $log['id']; ?></td>
                                                    <td><?php echo date('d/m/Y H:i:s', strtotime($log['log_date'])); ?></td>
                                                    <td><?php echo htmlspecialchars($user_display); ?></td>
                                                    <td>
                                                        <?php
                                                        $action_badges = [
                                                            'LOGIN' => '<span class="badge badge-success">Login</span>',
                                                            'LOGOUT' => '<span class="badge badge-secondary">Logout</span>',
                                                            'CREATE' => '<span class="badge badge-primary">Create</span>',
                                                            'UPDATE' => '<span class="badge badge-info">Update</span>',
                                                            'DELETE' => '<span class="badge badge-danger">Delete</span>',
                                                            'VIEW' => '<span class="badge badge-light">View</span>'
                                                        ];
                                                        echo $action_badges[$log['action']] ?? '<span class="badge badge-dark">' . htmlspecialchars($log['action']) . '</span>';
                                                        ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($log['description']); ?></td>
                                                    <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
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
            $('#auditTable').DataTable({
                pageLength: 25,
                order: [
                    [0, 'desc']
                ]
            });
        });
    </script>
</body>

</html>