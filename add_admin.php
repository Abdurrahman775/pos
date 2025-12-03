<?php
require("config.php");
require("include/functions.php");
require("include/admin_authentication.php");
require("include/admin_constants.php");
require('include/functions_messaging.php');

// Initialize variables
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = strtolower(trim($_POST['username']));
    $sname = strtoupper(trim($_POST['sname']));
    $fname = strtoupper(trim($_POST['fname']));
    $mname = !empty(trim($_POST['mname'])) ? strtoupper(trim($_POST['mname'])) : NULL;
    $email = strtolower(trim($_POST['email']));

    try {
        $auto_password = randomPassword();
        $hashedPassword = generateHash($auto_password);

        $sql = "INSERT INTO admins (username, password, sname, fname, mname, email, reg_by, reg_date) VALUES (:username, :password, :sname, :fname, :mname, :email, :reg_by, :reg_date)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $query->bindParam(':sname', $sname, PDO::PARAM_STR);
        $query->bindParam(':fname', $fname, PDO::PARAM_STR);
        $query->bindParam(':mname', $mname, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':reg_by', $admin, PDO::PARAM_STR);
        $query->bindParam(':reg_date', $now, PDO::PARAM_STR);
        $query->execute();

        if ($query == TRUE) {
            $fullname = !empty($mname) ? ($fname . ' ' . $mname . ' ' . $sname) : ($fname . ' ' . $sname);
            $html_file = newUserEmail($dbh, $fullname, $username, $auto_password);
            //sendMail($dbh, $email, $subject= "New User Notification", $html_file, $linkAttachments= [], $stringAttachments= []);

            $success = "<script>$(function(){ bootbox.alert({ centerVertical: true, size: 'small', message: 'Record saved', buttons: { ok: { label: \"<i class='fa fa-check'></i> OK\", className: 'btn-success btn-sm' } } }); });</script>";
        } else {
            $error = "<script>$(function(){ bootbox.alert({ centerVertical: true, size: 'small', message: 'ERROR! Try again', buttons: { ok: { label: \"<i class='fa fa-check'></i> OK\", className: 'btn-success btn-sm' } } }); });</script>";
        }
    } catch (PDOException $e) {
        $error = "<script>$(function(){ bootbox.alert({ centerVertical: true, size: 'small', message: 'System error!', buttons: { ok: { label: \"<i class='fa fa-check'></i> OK\", className: 'btn-success btn-sm' } } }); });</script>";
        echo $e->getMessage();
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
                    username: {
                        required: true,
                        remote: {
                            url: "include/val_username.php",
                            type: "post",
                            async: false,
                            cache: false
                        }
                    },
                    sname: {
                        required: true
                    },
                    fname: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    }
                },
                messages: {
                    username: {
                        required: "Enter Username",
                        remote: "Username Exists"
                    },
                    sname: {
                        required: "Enter Surname"
                    },
                    fname: {
                        required: "Enter First Name"
                    },
                    email: {
                        required: "Enter Email"
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
                                    <h4 class="page-title">Add User</h4>
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
                                        <li class="breadcrumb-item">User Management</li>
                                        <li class="breadcrumb-item active">Add User</li>
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
                                                <label>Username <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control form-control-sm" name="username" id="username" placeholder="Enter Username">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Surname <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control form-control-sm" name="sname" id="sname" placeholder="Enter Surname">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>First Name <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control form-control-sm" name="fname" id="fname" placeholder="Enter First Name">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Middle Name</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control form-control-sm" name="mname" id="mname" placeholder="Enter Middle Name">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Email</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control form-control-sm" name="email" id="email" placeholder="Enter Email">
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