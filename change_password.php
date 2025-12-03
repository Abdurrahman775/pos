<?php
require('./config.php');
require('include/functions.php');
require("include/admin_authentication.php");
require("include/admin_constants.php");

// Initialize variables
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password1 = $_POST['new_password1'];
    $new_password2 = $_POST['new_password2'];

    try {

        $hashed_password = generateHash($new_password2);

        $sql = "UPDATE admins SET password= :password WHERE username= :username";
        $query = $dbh->prepare($sql);
        $query->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $query->bindParam(':username', $admin, PDO::PARAM_STR);
        $query->execute();

        if ($query == TRUE) {
            $success = "<script>$(function() {
                bootbox.alert({
                    centerVertical: true,
                    size: 'small',
                    title: 'Record saved',
                    message: 'Signin to continue.',
                    buttons: {
                        ok: {
                            label: \"<i class='fa fa-check'></i> OK\",
                            className: 'btn-success btn-sm'
                        }
                    },
                    callback: function () {
                        window.location.replace('../logout.php');
                    }
                });
            });
            </script>";
        } else {
            $error = "<script>$(function() {
                bootbox.alert({
                    centerVertical: true,
                    size: 'small',
                    message: 'ERROR! Try again',
                    buttons: {
                        ok: {
                            label: \"<i class='fa fa-check'></i> OK\",
                            className: 'btn-success btn-sm'
                        }
                    }
                });
            });
            </script>";
        }
    } catch (PDOException $e) {
        $error = "<script>$(function() {
            bootbox.alert({
                centerVertical: true,
                size: 'small',
                message: 'System error!',
                buttons: {
                    ok: {
                        label: \"<i class='fa fa-check'></i> OK\",
                        className: 'btn-success btn-sm'
                    }
                }
            });
        });
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Staff Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Staff Management System" name="description" />
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
    <script>
        $(document).ready(function() {
            $('#form1').validate({
                rules: {
                    current_password: {
                        required: true,
                        remote: {
                            url: "include/val_current_password.php",
                            type: "post",
                            async: false,
                            cache: false
                        }
                    },
                    new_password1: {
                        required: true
                    },
                    new_password2: {
                        required: true,
                        equalTo: "#new_password1"
                    }
                },
                messages: {
                    current_password: {
                        required: "Enter Current Password",
                        remote: "Invalid Password"
                    },
                    new_password1: {
                        required: "Enter New Password"
                    },
                    new_password2: {
                        required: "Confirm New Password"
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

            $('#save').on('click', function(e) {
                e.preventDefault();
                bootbox.confirm({
                    centerVertical: true,
                    size: "small",
                    title: "",
                    message: "Are you sure to save?",
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
                    callback: function(output) {
                        if (output) {
                            $('#form1').submit();
                        }
                    }
                });
            });
        });
    </script>
</head>

<body class="dark-sidenav">
    <?php
    // Displaying Notifications
    echo "{$success} {$error}";
    ?>
    <div class="left-sidenav">
        <div class="brand">
            <?php require('template/brand_admin.php'); ?>
        </div>
        <div class="menu-content h-100" data-simplebar>
            <?php require('include/menus.php'); ?>
        </div>
    </div>
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
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
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
                                <p class="text-muted mb-0">All fields make with asterisk(<span class="text-danger">*</span>) are mandatory.</p>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <form name="form1" id="form1" method="post" action="#" autocomplete="off">
                                            <div class="form-group">
                                                <label>Current Password <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input type="password" class="form-control form-control-sm" name="current_password" id="current_password" placeholder="Enter Current Password">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>New Password <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input type="password" class="form-control form-control-sm" name="new_password1" id="new_password1" placeholder="Enter Password">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Confirm New Password <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input type="password" class="form-control form-control-sm" name="new_password2" id="new_password2" placeholder="Confirm Password">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-12 mt-2 p-0">
                                                    <button type="submit" name="save" id="save" class="btn btn-primary waves-effect waves-light"><i class="mdi mdi-content-save-outline mr-2"></i>Save</button>
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
                                <p class="text-muted mb-0"><i class="fa fa-lightbulb"></i> Hint</p>
                            </div>
                            <div class="card-body">

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
</body>

</html>