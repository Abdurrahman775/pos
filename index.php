<?php
require('config.php');
require('include/functions.php');
require('include/login.php');

// Initialize variables
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    if (isset($_POST['login_admin'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        try {
            $login = login_admin($dbh, $username, $password);
            $error = '<script>$(function() {
                bootbox.alert({
                    centerVertical: true,
                    size: "small",
                    message: "<span class=\'text-danger\'>' . $login . '</span>"
                });
            });
            </script>';
        } catch (PDOException $e) {
            $err = $e->getMessage();
            $error = '<script>$(function() {
                bootbox.alert({
                    centerVertical: true,
                    size: "small",
                    message: "<span class=\'text-danger\'>System Error!</span>"
                });
            });
            </script>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Point of Sale | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Staff Management System" name="description" />
    <meta content="S & I IT Partners Ltd" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="template/assets/images/favicon.ico">
    <!-- App css -->
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- jQuery  -->
    <script src="template/assets/js/jquery.min.js"></script>
    <script src="template/assets/js/bootstrap.bundle.min.js"></script>
    <script src="template/assets/js/waves.js"></script>
    <script src="template/assets/js/feather.min.js"></script>
    <script src="template/assets/js/simplebar.min.js"></script>
    <script src="template/plugins/bootbox/bootbox.min.js"></script>
    <script src="template/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="template/plugins/jquery-validation/additional-methods.min.js"></script>
    <script>
        window.onload = function() {
            document.getElementById('staff_id').focus();
        }

        $(document).ready(function() {
            $('#resetAdmin').click(function(e) {
                e.preventDefault();
                bootbox.prompt({
                    centerVertical: true,
                    size: "small",
                    title: "Reset Password",
                    placeholder: "Enter Username",
                    required: true,
                    callback: function(result) {
                        if (result) {
                            var username = result.replace(/(<([^>]+)>)/ig, "");
                            var dialog = bootbox.dialog({
                                centerVertical: true,
                                size: "small",
                                title: "Resetting password",
                                message: '<div class="text-center"><i class="fa fa-spin fa-spinner"></i> Loading...</div>',
                                closeButton: false
                            });

                            if (username) {
                                dialog.modal('show');
                                $.ajax({
                                    type: "POST",
                                    url: "include/reset_password.php",
                                    data: "token=" + username,
                                    success: function(output) {
                                        dialog.modal('hide');
                                        if (output == 0) {
                                            bootbox.alert({
                                                centerVertical: true,
                                                size: "small",
                                                message: "<span class='text-danger'>Error! Try again</span>"
                                            });
                                        } else if (output == 1) {
                                            bootbox.alert({
                                                centerVertical: true,
                                                size: "small",
                                                message: "<span class='text-danger'>No email configured for resetting password</span>"
                                            });
                                        } else {
                                            bootbox.alert({
                                                centerVertical: true,
                                                size: "small",
                                                message: "Reset instructions sent to: <span class='font-weight-bold'>" + output + "</span>"
                                            });
                                        }
                                    }
                                });
                            }
                        }
                    }
                });
            });

            $('#form1').validate({
                rules: {
                    username: {
                        required: true
                    },
                    password: {
                        required: true
                    }
                },
                messages: {
                    username: {
                        required: "Enter Username"
                    },
                    password: {
                        required: "Enter Password"
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
</head>

<body class="account-body accountbg">
    <?php
    // Displaying Notifications
    echo "{$error}";
    ?>
    <div class="container">
        <div class="row vh-100 d-flex justify-content-center">
            <div class="col-12 align-self-center">
                <div class="row">
                    <div class="col-lg-5 mx-auto">
                        <div class="card">
                            <div class="card-body p-0 auth-header-box">
                                <div class="text-center p-3">
                                    <?php
                                    // Get company logo and name from settings
                                    $logo_sql = "SELECT setting_value FROM system_settings WHERE setting_key = 'company_logo'";
                                    $logo_query = $dbh->prepare($logo_sql);
                                    $logo_query->execute();
                                    $company_logo = $logo_query->fetchColumn();
                                    
                                    $name_sql = "SELECT setting_value FROM system_settings WHERE setting_key = 'store_name'";
                                    $name_query = $dbh->prepare($name_sql);
                                    $name_query->execute();
                                    $company_name = $name_query->fetchColumn() ?: 'POS System';
                                    ?>
                                    <a href="javascript: void(0);" class="logo logo-admin">
                                        <?php if ($company_logo && file_exists($company_logo)): ?>
                                            <img src="<?php echo htmlspecialchars($company_logo); ?>" height="50" alt="Company Logo" class="auth-logo">
                                        <?php else: ?>
                                            <img src="template/assets/images/logo-sm.png" height="50" alt="Logo" class="auth-logo">
                                        <?php endif; ?>
                                    </a>
                                    <h4 class="mt-3 mb-1 font-weight-semibold text-white font-14"><?php echo htmlspecialchars($company_name); ?></h4>
                                    <p class="text-white mb-0">Point of Sale Management System</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="text-center font-weight-bold mb-0 mt-0 font-15">Admin Login</div>
                                <!--                                     <ul class="nav-border nav nav-pills" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active font-weight-semibold" data-toggle="tab" href="#AdminLogIn_Tab" role="tab">Admin Login</a>
                                        </li>
                                    </ul> -->
                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div class="tab-pane active p-3 pt-3" id="AdminLogIn_Tab" role="tabpanel">
                                        <form name="form1" id="form1" class="form-horizontal auth-form my-4" method="post" action="" autocomplete="off">
                                            <div class="form-group">
                                                <label for="username">Username</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" name="username" id="username" placeholder="Enter Username">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="userpassword">Password</label>
                                                <div class="input-group mb-3">
                                                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password">
                                                </div>
                                            </div>
                                            <div class="form-group row mt-4">
                                                <div class="col-sm-12 text-right">
                                                    <a href="javascript: void();" id="resetAdmin" class="text-muted font-13"><i class="dripicons-lock"></i> Forgot password?</a>
                                                </div>
                                            </div>
                                            <div class="form-group mb-0 row">
                                                <div class="col-12 mt-2">
                                                    <button class="btn btn-primary btn-block waves-effect waves-light" type="submit" name="login_admin" id="login_admin">Log In <i class="fas fa-sign-in-alt ml-1"></i></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body bg-light-alt text-center">
                                <span class="text-muted d-none d-sm-inline-block"><?php include('template/copyright.php'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>