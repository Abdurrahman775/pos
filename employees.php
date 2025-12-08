<?php

/**
 * Employees Management Page
 * List all employees/users in the system
 */
require("config.php");
require("include/functions.php");
require_once("include/pos_functions.php");
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
    <link href="template/plugins/datatables/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
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
                                <!-- Filters -->
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label>Role:</label>
                                        <select id="roleFilter" class="form-control">
                                            <option value="">All Roles</option>
                                            <option value="1">Administrator</option>
                                            <option value="2">Manager</option>
                                            <option value="3">Cashier</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Status:</label>
                                        <select id="statusFilter" class="form-control">
                                            <option value="all">All Status</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>

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
                                            <!-- Data loaded via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Employee Modal -->
                <div class="modal fade" id="viewEmployeeModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Employee Details</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>ID:</strong> <span id="view_id"></span></p>
                                        <p><strong>Username:</strong> <span id="view_username"></span></p>
                                        <p><strong>Full Name:</strong> <span id="view_full_name"></span></p>
                                        <p><strong>Email:</strong> <span id="view_email"></span></p>
                                        <p><strong>Mobile:</strong> <span id="view_mobile"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Role:</strong> <span id="view_role"></span></p>
                                        <p><strong>Status:</strong> <span id="view_status"></span></p>
                                        <p><strong>Last Login:</strong> <span id="view_last_login"></span></p>
                                    </div>
                                </div>
                                <hr>
                                <h6 class="mt-3 mb-3">Audit Trail</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Created At:</strong> <span id="view_created_at"></span></p>
                                        <p><strong>Created By:</strong> <span id="view_created_by"></span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Updated At:</strong> <span id="view_updated_at"></span></p>
                                        <p><strong>Updated By:</strong> <span id="view_updated_by"></span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Employee Modal -->
                <div class="modal fade" id="editEmployeeModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Employee</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>
                            <form id="editEmployeeForm">
                                <div class="modal-body">
                                    <input type="hidden" id="edit_id" name="id">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>First Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="edit_fname" name="fname" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Middle Name</label>
                                                <input type="text" class="form-control" id="edit_mname" name="mname">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Surname <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="edit_sname" name="sname" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Username <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="edit_username" name="username" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="edit_email" name="email" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Role <span class="text-danger">*</span></label>
                                                <select class="form-control" id="edit_role_id" name="role_id" required>
                                                    <option value="1">Administrator</option>
                                                    <option value="2">Manager</option>
                                                    <option value="3">Cashier</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Status <span class="text-danger">*</span></label>
                                                <select class="form-control" id="edit_is_active" name="is_active" required>
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
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
    <script src="template/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="template/plugins/datatables/dataTables.bootstrap5.min.js"></script>
    <script src="template/assets/js/app.js"></script>

    <script>
        let employeesTable;

        $(document).ready(function() {
            // Initialize DataTable with server-side processing
            employeesTable = $('#employeesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: 'datatables/employees.php',
                    type: 'POST',
                    data: function(d) {
                        d.role_filter = $('#roleFilter').val();
                        d.status_filter = $('#statusFilter').val();
                    }
                },
                pageLength: 25,
                order: [[0, 'desc']],
                columns: [{
                        data: 0
                    }, // ID
                    {
                        data: 1
                    }, // Username
                    {
                        data: 2
                    }, // Full Name
                    {
                        data: 3
                    }, // Email
                    {
                        data: 4,
                        orderable: false
                    }, // Role
                    {
                        data: 5,
                        orderable: false
                    }, // Status
                    {
                        data: 6
                    }, // Last Login
                    {
                        data: 7,
                        orderable: false
                    } // Actions
                ]
            });

            // Filter event handlers
            $('#roleFilter, #statusFilter').on('change', function() {
                employeesTable.ajax.reload();
            });
        });

        // View employee details
        function viewEmployee(id) {
            $.ajax({
                url: 'ajax/employee_actions.php',
                type: 'POST',
                data: {
                    action: 'get_employee',
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const emp = response.data;

                        // Populate view modal
                        $('#view_id').text(emp.id);
                        $('#view_username').text(emp.username);
                        $('#view_full_name').text(emp.full_name);
                        $('#view_email').text(emp.email);
                        $('#view_mobile').text(emp.mobile || 'N/A');
                        $('#view_role').html(getRoleBadge(emp.role_id, emp.role_name));
                        $('#view_status').html(emp.is_active == 1 ?
                            '<span class="badge badge-success">Active</span>' :
                            '<span class="badge badge-secondary">Inactive</span>');
                        $('#view_last_login').text(emp.last_update ? formatDate(emp.last_update) : 'Never');
                        
                        // Audit trail
                        $('#view_created_at').text(emp.reg_date ? formatDate(emp.reg_date) : 'N/A');
                        $('#view_created_by').text(emp.reg_by || 'N/A');
                        $('#view_updated_at').text(emp.last_update ? formatDate(emp.last_update) : 'Never');
                        $('#view_updated_by').text(emp.updated_by || 'N/A');

                        // Show modal
                        $('#viewEmployeeModal').modal('show');
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Failed to load employee details');
                }
            });
        }

        // Edit employee
        function editEmployee(id) {
            $.ajax({
                url: 'ajax/employee_actions.php',
                type: 'POST',
                data: {
                    action: 'get_employee',
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const emp = response.data;

                        // Populate edit form
                        $('#edit_id').val(emp.id);
                        $('#edit_fname').val(emp.fname);
                        $('#edit_mname').val(emp.mname || '');
                        $('#edit_sname').val(emp.sname);
                        $('#edit_username').val(emp.username);
                        $('#edit_email').val(emp.email);
                        $('#edit_role_id').val(emp.role_id);
                        $('#edit_is_active').val(emp.is_active);

                        // Show modal
                        $('#editEmployeeModal').modal('show');
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('Failed to load employee details');
                }
            });
        }

        // Handle edit form submission
        $('#editEmployeeForm').submit(function(e) {
            e.preventDefault();

            const formData = $(this).serialize() + '&action=update_employee';

            $.ajax({
                url: 'ajax/employee_actions.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#editEmployeeModal').modal('hide');
                        showNotification('Success!', response.message, 'success');
                        employeesTable.ajax.reload(null, false); // Reload table without page reset
                    } else {
                        showNotification('Error!', response.message, 'error');
                    }
                },
                error: function() {
                    showNotification('Error!', 'Failed to update employee', 'error');
                }
            });
        });

        // Helper function to get role badge HTML
        function getRoleBadge(roleId, roleName) {
            const badges = {
                1: 'badge-danger',
                2: 'badge-warning',
                3: 'badge-info'
            };
            const badgeClass = badges[roleId] || 'badge-secondary';
            return '<span class="badge ' + badgeClass + '">' + roleName + '</span>';
        }

        // Helper function to format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            return day + '/' + month + '/' + year + ' ' + hours + ':' + minutes;
        }

        // Simple notification function
        function showNotification(title, message, type) {
            const bgColor = type === 'success' ? '#28a745' : '#dc3545';
            const notification = $('<div>')
                .css({
                    position: 'fixed',
                    top: '20px',
                    right: '20px',
                    backgroundColor: bgColor,
                    color: 'white',
                    padding: '15px 20px',
                    borderRadius: '4px',
                    zIndex: 9999,
                    minWidth: '250px',
                    boxShadow: '0 4px 8px rgba(0,0,0,0.2)'
                })
                .html('<strong>' + title + '</strong><br>' + message);

            $('body').append(notification);

            setTimeout(function() {
                notification.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }
    </script>
</body>

</html>