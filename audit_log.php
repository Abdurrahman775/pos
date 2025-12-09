<?php

/**
 * Audit Log Page
 * View system activity logs using centralized logger
 */
require("config.php");
require("logger.php");
require("include/functions.php");
require("include/admin_authentication.php");
require("include/admin_constants.php");

// Get filters from GET parameters
$filters = [
    'start_date' => $_GET['start_date'] ?? '',
    'end_date' => $_GET['end_date'] ?? '',
    'action_type' => $_GET['action_type'] ?? '',
    'search' => $_GET['search'] ?? '',
    'page' => isset($_GET['page']) ? intval($_GET['page']) : 1
];

$per_page = 50;
$filters['limit'] = $per_page;
$filters['offset'] = ($filters['page'] - 1) * $per_page;

// Get logs and total count
$logs = get_logs($dbh, $filters);
$total_logs = get_logs_count($dbh, $filters);
$total_pages = ceil($total_logs / $per_page);
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
                                <h5 class="card-title mb-0"><i class="fas fa-history mr-2"></i>System Activity Log</h5>
                            </div>
                            <div class="card-body">
                                <!-- Filter Form -->
                                <form method="GET" action="audit_log.php" class="mb-3">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Start Date</label>
                                            <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($filters['start_date']); ?>" />
                                        </div>
                                        <div class="col-md-3">
                                            <label>End Date</label>
                                            <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($filters['end_date']); ?>" />
                                        </div>
                                        <div class="col-md-2">
                                            <label>Action Type</label>
                                            <select name="action_type" class="form-control">
                                                <option value="">All Actions</option>
                                                <option value="LOGIN" <?php echo ($filters['action_type'] == 'LOGIN') ? 'selected' : ''; ?>>Login</option>
                                                <option value="LOGOUT" <?php echo ($filters['action_type'] == 'LOGOUT') ? 'selected' : ''; ?>>Logout</option>
                                                <option value="ADD_CUSTOMER" <?php echo ($filters['action_type'] == 'ADD_CUSTOMER') ? 'selected' : ''; ?>>Add Customer</option>
                                                <option value="ADD_PRODUCT" <?php echo ($filters['action_type'] == 'ADD_PRODUCT') ? 'selected' : ''; ?>>Add Product</option>
                                                <option value="UPDATE" <?php echo ($filters['action_type'] == 'UPDATE') ? 'selected' : ''; ?>>Update</option>
                                                <option value="DELETE" <?php echo ($filters['action_type'] == 'DELETE') ? 'selected' : ''; ?>>Delete</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Search</label>
                                            <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($filters['search']); ?>" />
                                        </div>
                                        <div class="col-md-2">
                                            <label>&nbsp;</label>
                                            <div>
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
                                                <a href="audit_log.php" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <!-- Results Count -->
                                <div class="mb-2">
                                    <small class="text-muted">Showing <?php echo count($logs); ?> of <?php echo $total_logs; ?> records</small>
                                </div>

                                <!-- Table -->
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th width="5%">ID</th>
                                                <th width="15%">Date/Time</th>
                                                <th width="15%">User</th>
                                                <th width="12%">Action</th>
                                                <th width="40%">Description</th>
                                                <th width="13%">IP Address</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($logs)): ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">No audit logs found</td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($logs as $log): ?>
                                                    <tr>
                                                        <td><?php echo $log['id']; ?></td>
                                                        <td><?php echo date('d/m/Y H:i:s', strtotime($log['Timestamp'])); ?></td>
                                                        <td><?php echo htmlspecialchars($log['user_name']); ?></td>
                                                        <td>
                                                            <?php
                                                            $badge_colors = [
                                                                'LOGIN' => 'success',
                                                                'LOGOUT' => 'secondary',
                                                                'ADD_CUSTOMER' => 'info',
                                                                'ADD_PRODUCT' => 'info',
                                                                'UPDATE' => 'warning',
                                                                'DELETE' => 'danger',
                                                                'DELETE_CUSTOMER' => 'danger'
                                                            ];
                                                            $badge_color = $badge_colors[$log['ActionType']] ?? 'secondary';
                                                            ?>
                                                            <span class="badge badge-<?php echo $badge_color; ?>"><?php echo htmlspecialchars($log['ActionType']); ?></span>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($log['Description']); ?></td>
                                                        <td><small><?php echo htmlspecialchars($log['ip_address']); ?></small></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <?php if ($total_pages > 1): ?>
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-center">
                                            <?php if ($filters['page'] > 1): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?php echo ($filters['page'] - 1); ?><?php echo !empty($filters['start_date']) ? '&start_date=' . $filters['start_date'] : ''; ?><?php echo !empty($filters['end_date']) ? '&end_date=' . $filters['end_date'] : ''; ?><?php echo !empty($filters['action_type']) ? '&action_type=' . $filters['action_type'] : ''; ?><?php echo !empty($filters['search']) ? '&search=' . $filters['search'] : ''; ?>">Previous</a>
                                                </li>
                                            <?php endif; ?>

                                            <?php for ($i = max(1, $filters['page'] - 2); $i <= min($total_pages, $filters['page'] + 2); $i++): ?>
                                                <li class="page-item <?php echo ($i == $filters['page']) ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($filters['start_date']) ? '&start_date=' . $filters['start_date'] : ''; ?><?php echo !empty($filters['end_date']) ? '&end_date=' . $filters['end_date'] : ''; ?><?php echo !empty($filters['action_type']) ? '&action_type=' . $filters['action_type'] : ''; ?><?php echo !empty($filters['search']) ? '&search=' . $filters['search'] : ''; ?>"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <?php if ($filters['page'] < $total_pages): ?>
                                                <li class="page-item">
                                                    <a class="page-link" href="?page=<?php echo ($filters['page'] + 1); ?><?php echo !empty($filters['start_date']) ? '&start_date=' . $filters['start_date'] : ''; ?><?php echo !empty($filters['end_date']) ? '&end_date=' . $filters['end_date'] : ''; ?><?php echo !empty($filters['action_type']) ? '&action_type=' . $filters['action_type'] : ''; ?><?php echo !empty($filters['search']) ? '&search=' . $filters['search'] : ''; ?>">Next</a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
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

    <!-- Scripts -->
    <script src="template/assets/js/jquery.min.js"></script>
    <script src="template/assets/js/bootstrap.bundle.min.js"></script>
    <script src="template/assets/js/metismenu.min.js"></script>
    <script src="template/assets/js/waves.js"></script>
    <script src="template/assets/js/feather.min.js"></script>
    <script src="template/assets/js/app.js"></script>

</body>

</html>
