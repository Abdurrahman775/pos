<?php
/**
 * Employees Management Page
 * List all employees/users in the system
 */
require("config.php");
require("include/functions.php");
require("include/pos_functions.php");
require("include/authentication.php");
require("include/admin_constants.php");

// Require employees permission (Administrator only)
require_permission('employees');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Employees | POS System</title>
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
                            <div class="row">
                                <div class="col">
                                    <h4 class="page-title">Employees</h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                        <li class="breadcrumb-item active">Employees</li>
                                    </ol>
                                </div>
                                <div class="col-auto">
                                    <a href="add_admin.php" class="btn btn-primary">
                                        <i class="fas fa-plus mr-1"></i> Add New Employee
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">All Employees</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="employeesTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Username</th>
                                                <th>Full Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Last Login</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM admins ORDER BY id DESC";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $employees = $query->fetchAll(PDO::FETCH_ASSOC);
                                            
                                            foreach($employees as $emp):
                                                $full_name = trim($emp['fname'] . ' ' . ($emp['mname'] ?? '') . ' ' . $emp['sname']);
                                            ?>
                                            <tr>
                                                <td><?php echo $emp['id']; ?></td>
                                                <td><?php echo htmlspecialchars($emp['username']); ?></td>
                                                <td><?php echo htmlspecialchars($full_name); ?></td>
                                                <td><?php echo htmlspecialchars($emp['email']); ?></td>
                                                <td>
                                                    <?php
                                                    $role_badges = [
                                                        1 => '<span class="badge badge-danger">Administrator</span>',
                                                        2 => '<span class="badge badge-warning">Manager</span>',
                                                        3 => '<span class="badge badge-info">Cashier</span>'
                                                    ];
                                                    echo $role_badges[$emp['role_id']] ?? '<span class="badge badge-secondary">Unknown</span>';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php if($emp['is_active']): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $emp['last_update'] ? date('d/m/Y H:i', strtotime($emp['last_update'])) : 'Never'; ?></td>
                                                <td>
                                                    <a href="edit_employee.php?id=<?php echo $emp['id']; ?>" 
                                                       class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="employee_details.php?id=<?php echo $emp['id']; ?>" 
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
        $('#employeesTable').DataTable({
            pageLength: 25,
            order: [[0, 'desc']]
        });
    });
    </script>
</body>
</html>
