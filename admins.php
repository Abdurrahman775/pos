<?php
/**
 * All Users Page
 * View and manage system users (admins, managers, cashiers)
 */
require("config.php");
require("include/functions.php");
require("include/admin_authentication.php");
require("include/admin_constants.php");

// Get all users
$sql = "SELECT * FROM admins ORDER BY fname ASC";
$query = $dbh->prepare($sql);
$query->execute();
$users = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>All Users | POS System</title>
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
    <div class="brand">
        <?php require('template/brand_admin.php'); ?>
    </div>
    <div class="menu-content h-100" data-simplebar>
        <?php require('include/menus.php'); ?>
    </div>
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
                                    <h4 class="page-title">All Users</h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                        <li class="breadcrumb-item active">Users</li>
                                    </ol>
                                </div>
                                <div class="col-auto">
                                    <a href="add_admin.php" class="btn btn-primary">
                                        <i class="fas fa-plus mr-1"></i> Add New User
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
                                <h5 class="card-title">System Users</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="usersTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($users as $user): ?>
                                            <tr>
                                                <td><?php echo $user['id']; ?></td>
                                                <td><?php echo htmlspecialchars($user['fname'] . ' ' . $user['sname']); ?></td>
                                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                                <td><?php echo htmlspecialchars($user['email'] ?? '-'); ?></td>
                                                <td>
                                                    <?php
                                                    $role_badges = [
                                                        'admin' => 'danger',
                                                        'manager' => 'warning',
                                                        'cashier' => 'info'
                                                    ];
                                                    $badge_color = $role_badges[$user['user_role']] ?? 'secondary';
                                                    ?>
                                                    <span class="badge badge-<?php echo $badge_color; ?>">
                                                        <?php echo ucfirst($user['user_role']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if($user['is_active']): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('d/m/Y', strtotime($user['reg_date'])); ?></td>
                                                <td>
                                                    <a href="edit_admin.php?id=<?php echo $user['id']; ?>" 
                                                       class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if($user['username'] != 'admin'): // Prevent deleting super admin ?>
                                                    <button class="btn btn-sm btn-danger" 
                                                            onclick="confirmDelete(<?php echo $user['id']; ?>)" 
                                                            title="Deactivate">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <?php endif; ?>
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
        $('#usersTable').DataTable({
            order: [[1, 'asc']],
            pageLength: 25
        });
    });
    
    function confirmDelete(userId) {
        bootbox.confirm({
            message: "Are you sure you want to deactivate this user?",
            buttons: {
                confirm: {
                    label: 'Yes, Deactivate',
                    className: 'btn-danger'
                },
                cancel: {
                    label: 'Cancel',
                    className: 'btn-secondary'
                }
            },
            callback: function (result) {
                if (result) {
                    window.location.href = 'delete_admin.php?id=' + userId;
                }
            }
        });
    }
    </script>
</body>
</html>
