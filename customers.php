<?php

/**
 * Customers Management Page
 * List and manage customer database
 */
require("config.php");
require("include/functions.php");
require("include/admin_authentication.php");
require("include/admin_constants.php");

// Require customers permission
require_permission('customers');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Customers | POS System</title>
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
                                    <h4 class="page-title">Customers</h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                        <li class="breadcrumb-item active">Customers</li>
                                    </ol>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCustomerModal">
                                        <i class="fas fa-plus mr-1"></i> Add New Customer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle mr-2"></i> Customer added successfully!
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle mr-2"></i> Customer deleted successfully!
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Customer Database</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="customersTable" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Total Purchases</th>
                                                <th>Created</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT * FROM customers WHERE del_status = 0 ORDER BY created_at DESC";
                                            $query = $dbh->prepare($sql);
                                            $query->execute();
                                            $customers = $query->fetchAll(PDO::FETCH_ASSOC);

                                            foreach ($customers as $customer):
                                            ?>
                                                <tr>
                                                    <td><?php echo $customer['customer_id']; ?></td>
                                                    <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($customer['phone'] ?? '-'); ?></td>
                                                    <td><?php echo htmlspecialchars($customer['email'] ?? '-'); ?></td>
                                                    <td><?php echo format_currency($dbh, $customer['total_purchases']); ?></td>
                                                    <td><?php echo date('d/m/Y', strtotime($customer['created_at'])); ?></td>
                                                    <td>
                                                        <?php if ($customer['is_active']): ?>
                                                            <span class="badge badge-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="customer_details.php?id=<?php echo $customer['customer_id']; ?>"
                                                            class="btn btn-sm btn-info" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="edit_customer.php?id=<?php echo $customer['customer_id']; ?>"
                                                            class="btn btn-sm btn-primary" title="Edit">
                                                            <i class="fas fa-edit"></i>
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

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addCustomerModalLabel">
                        <i class="fas fa-user-plus mr-2"></i>Add New Customer
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">All fields marked with asterisk(<span class="text-danger">*</span>) are mandatory.</p>
                    <form id="addCustomerForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer Name <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control form-control-sm" id="customer_name" name="name" placeholder="Enter Customer Name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone Number <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control form-control-sm" id="customer_phone" name="phone" placeholder="Enter Phone Number" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <div class="input-group mb-3">
                                        <input type="email" class="form-control form-control-sm" id="customer_email" name="email" placeholder="Enter Email">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Address</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control form-control-sm" id="customer_address" name="address" placeholder="Enter Address">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">
                        <i class="mdi mdi-close mr-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" id="saveCustomerBtn">
                        <i class="mdi mdi-content-save-outline mr-1"></i>Save Customer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Alert Container -->
    <div id="customAlert" style="display: none; position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;"></div>

    <script src="template/assets/js/jquery.min.js"></script>
    <script src="template/assets/js/bootstrap.bundle.min.js"></script>
    <script src="template/assets/js/metismenu.min.js"></script>
    <script src="template/assets/js/waves.js"></script>
    <script src="template/assets/js/feather.min.js"></script>
    <script src="template/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="template/plugins/datatables/dataTables.bootstrap5.min.js"></script>
    <script src="template/plugins/bootbox/bootbox.min.js"></script>
    <script src="template/assets/js/app.js"></script>

    <script>
        $(document).ready(function() {
            console.log('Document ready - initializing...');
            
            $('#customersTable').DataTable({
                order: [[5, 'desc']],
                pageLength: 25
            });
            
            console.log('Save button exists on page load:', $('#saveCustomerBtn').length);
            
            // Use event delegation for modal button since it may not exist at page load
            $(document).on('click', '#saveCustomerBtn', function() {
                console.log('Save button clicked!');
                var name = $('#customer_name').val().trim();
                var phone = $('#customer_phone').val().trim();
                
                // Validate required fields
                if (!name || !phone) {
                    showCustomAlert('error', 'Name and phone are required fields!');
                    return;
                }
                
                // Show confirmation dialog using bootbox
                bootbox.confirm({
                    centerVertical: true,
                    size: "small",
                    message: "Are you sure you want to add this customer: <strong>" + name + "</strong>?",
                    buttons: {
                        cancel: {
                            label: '<i class="fa fa-times"></i> No',
                            className: 'btn btn-danger btn-sm'
                        },
                        confirm: {
                            label: '<i class="fa fa-check"></i> Yes',
                            className: 'btn btn-success btn-sm'
                        }
                    },
                    callback: function(result) {
                        if (result) {
                            // Get form data
                            var formData = $('#addCustomerForm').serialize();
                            
                            console.log('Form data:', formData);
                            console.log('About to call:', 'ajax/add_customer.php');
                            
                            // Show loading state
                            $('#saveCustomerBtn').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin mr-1"></i>Saving...');
                            
                            // Send AJAX request
                            $.ajax({
                                url: 'ajax/add_customer.php',
                                type: 'POST',
                                data: formData,
                                dataType: 'json',
                                success: function(response) {
                                    console.log('Success response:', response);
                                    if (response.status === 'success') {
                                        // Show success alert
                                        showCustomAlert('success', response.message);
                                        
                                        // Close modal
                                        $('#addCustomerModal').modal('hide');
                                        
                                        // Reset form
                                        $('#addCustomerForm')[0].reset();
                                        
                                        // Reload page after 1.5 seconds
                                        setTimeout(function() {
                                            location.reload();
                                        }, 1500);
                                    } else {
                                        showCustomAlert('error', response.message);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.log('AJAX Error - Status Code:', xhr.status);
                                    console.log('AJAX Error - Full xhr:', xhr);
                                    console.log('AJAX Error - Status:', status);
                                    console.log('AJAX Error - Error:', error);
                                    console.log('Response Text:', xhr.responseText);
                                    showCustomAlert('error', 'An error occurred: ' + (xhr.responseJSON?.message || xhr.statusText || error || 'Please try again.'));
                                },
                                complete: function() {
                                    $('#saveCustomerBtn').prop('disabled', false).html('<i class="mdi mdi-content-save-outline mr-1"></i>Save Customer');
                                }
                            });
                        }
                    }
                });
            });
            
            // Reset form when modal is closed
            $('#addCustomerModal').on('hidden.bs.modal', function() {
                $('#addCustomerForm')[0].reset();
            });
        });
        
        // Custom alert function
        function showCustomAlert(type, message) {
            var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            var alertHtml = '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert" style="box-shadow: 0 4px 6px rgba(0,0,0,0.1);">' +
                '<i class="fas ' + icon + ' mr-2"></i>' + message +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>';
            
            $('#customAlert').html(alertHtml).fadeIn();
            
            // Auto hide after 5 seconds
            setTimeout(function() {
                $('#customAlert').fadeOut();
            }, 5000);
        }
    </script>
</body>

</html>