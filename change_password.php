<?php
require('./config.php');
require('include/functions.php');
require("include/authentication.php");
require("include/admin_constants.php");

// Initialize variables
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $csrf_token = $_POST['csrf_token'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    $new_password1 = $_POST['new_password1'] ?? '';
    $new_password2 = $_POST['new_password2'] ?? '';
    
    // Validate CSRF token
    if (!validate_csrf_token($csrf_token)) {
        $error = "Security token validation failed. Please refresh and try again.";
    } else {
        try {
            // Validate inputs
            if (empty($current_password) || empty($new_password1) || empty($new_password2)) {
                $error = "All fields are required";
            } elseif ($new_password1 !== $new_password2) {
                $error = "New passwords do not match";
            } elseif (strlen($new_password1) < 6) {
                $error = "New password must be at least 6 characters long";
            } else {
                // Get current password hash from database
                $sql = "SELECT password FROM admins WHERE username = :username";
                $query = $dbh->prepare($sql);
                $query->bindParam(':username', $_SESSION['pos_admin'], PDO::PARAM_STR);
                $query->execute();
                $result = $query->fetch(PDO::FETCH_ASSOC);

                if (!$result) {
                    $error = "User not found";
                } elseif (!password_verify($current_password, $result['password'])) {
                    $error = "Current password is incorrect";
                } else {
                    // Hash new password
                    $hashed_password = password_hash($new_password1, PASSWORD_BCRYPT);

                    // Update password
                    $sql = "UPDATE admins SET password = :password WHERE username = :username";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                    $query->bindParam(':username', $_SESSION['pos_admin'], PDO::PARAM_STR);
                    $query->execute();

                    if ($query->rowCount() > 0) {
                        // Log the activity
                        log_activity($dbh, 'UPDATE', 'Changed password');
                        
                        $success = "<script>$(function() {
                            bootbox.alert({
                                centerVertical: true,
                                size: 'small',
                                title: 'Success',
                                message: 'Password changed successfully. Please login again.',
                                buttons: {
                                    ok: {
                                        label: \"<i class='fa fa-check'></i> OK\",
                                        className: 'btn-success btn-sm'
                                    }
                                },
                                callback: function () {
                                    window.location.replace('logout.php');
                                }
                            });
                        });
                        </script>";
                    } else {
                        $error = "Failed to update password. Please try again.";
                    }
                }
            }
        } catch (PDOException $e) {
            error_log("Password change error: " . $e->getMessage());
            $error = "System error occurred. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Change Password | POS System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Point of Sale System" name="description" />
    <meta content="S & I IT Partners Ltd" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="template/assets/images/favicon.ico">
    <!-- App css -->
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/jquery-ui.min.css" rel="stylesheet" type="text/css">
    <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- jQuery  -->
    <script src="template/assets/js/jquery.min.js"></script>
    <script src="template/assets/js/bootstrap.bundle.min.js"></script>
    <script src="template/assets/js/metismenu.min.js"></script>
    <script src="template/assets/js/waves.js"></script>
    <script src="template/assets/js/feather.min.js"></script>
    <script src="template/assets/js/jquery-ui.min.js"></script>
    <script src="template/plugins/bootbox/bootbox.min.js"></script>
    <script src="template/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="template/plugins/jquery-validation/additional-methods.min.js"></script>
    <script src="template/assets/js/app.js" defer></script>
</head>

<body class="dark-sidenav">
    <?php
    // Displaying success or error messages
    if ($success) {
        echo $success;
    } elseif ($error) {
        echo "<script>$(function() {
            bootbox.alert({
                centerVertical: true,
                size: 'small',
                title: 'Error',
                message: ' . escape_js($error) . ',
                buttons: {
                    ok: {
                        label: \"<i class='fa fa-times'></i> OK\",
                        className: 'btn-danger btn-sm'
                    }
                }
            });
        });
        </script>";
    }
    ?>
    <?php include('include/sidebar.php'); ?>
    <div class="page-wrapper">
        <div class="topbar">
            <?php require('template/top_nav_admin.php'); ?>
        </div>

        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="row">
                                <div class="col">
                                    <h4 class="page-title">Change Password</h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                        <li class="breadcrumb-item active">Change Password</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- body here -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <p class="text-muted mb-0">All fields marked with asterisk(<span class="text-danger">*</span>) are mandatory.</p>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <form name="form1" id="form1" method="post" action="" autocomplete="off">
                                            <?php echo csrf_token_field(); ?>
                                            <div class="form-group">
                                                <label>Current Password <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input type="password" class="form-control" name="current_password" id="current_password" placeholder="Enter Current Password" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>New Password <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input type="password" class="form-control" name="new_password1" id="new_password1" placeholder="Enter New Password" required minlength="6">
                                                </div>
                                                <small class="form-text text-muted">Password must be at least 6 characters long</small>
                                            </div>
                                            <div class="form-group">
                                                <label>Confirm New Password <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input type="password" class="form-control" name="new_password2" id="new_password2" placeholder="Confirm New Password" required>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-12 mt-2 p-0">
                                                    <button type="submit" name="save" id="save" class="btn btn-primary waves-effect waves-light"><i class="mdi mdi-content-save-outline mr-2"></i>Change Password</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <p class="text-muted mb-0"><i class="fa fa-lightbulb"></i> Password Requirements</p>
                            </div>
                            <div class="card-body">
                                <ul class="mb-0">
                                    <li>Password must be at least 6 characters long</li>
                                    <li>Use a combination of letters, numbers, and special characters for better security</li>
                                    <li>Avoid using common words or personal information</li>
                                    <li>You will be logged out after changing your password</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <footer class="footer text-center text-sm-left">
                <?php include('template/copyright.php'); ?> <span class="d-none d-sm-inline-block float-right"><?php include('template/developed_by.php'); ?></span>
            </footer>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#form1').validate({
                rules: {
                    current_password: {
                        required: true
                    },
                    new_password1: {
                        required: true,
                        minlength: 6
                    },
                    new_password2: {
                        required: true,
                        equalTo: "#new_password1"
                    }
                },
                messages: {
                    current_password: {
                        required: "Enter current password"
                    },
                    new_password1: {
                        required: "Enter new password",
                        minlength: "Password must be at least 6 characters"
                    },
                    new_password2: {
                        required: "Confirm new password",
                        equalTo: "Passwords do not match"
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
</body>

</html>