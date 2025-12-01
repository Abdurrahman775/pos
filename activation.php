<?php
require('../config.php');
require("include/functions.php");
require("include/admin_authentication_temp.php");
require("include/admin_constants_temp.php");

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $password = $_POST['password'];
	$confirm_password = $_POST['confirm_password'];

    try {
        $hashed_password = generateHash($confirm_password);

        $sql = "UPDATE admins SET password= :password, acct_activation= 1 WHERE username= :username";
        $query = $dbh->prepare($sql);
        $query->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $query->bindParam(':username', $admin, PDO::PARAM_STR);
        $query->execute();

        if($query == TRUE) {
            $success = '<script>$(function() {
                bootbox.alert({
                    centerVertical: true,
                    size: "small",
                    message: "Record saved",
                    message: "Login with new password",
                    callback: function () {
                        window.location.replace("../logout.php");
                    }
                });
            });
            </script>';
        } else {
            $error = '<script>$(function() {
                bootbox.alert({
                    centerVertical: true,
                    size: "small",
                    message: "<span class=\'text-danger\'>ERROR! Try again</span>"
                });
            });
            </script>';
        }
    } catch (PDOException $e) {
        $error = '<script>$(function() {
            bootbox.alert({
                centerVertical: true,
                size: "small",
                message: "<span class=\'text-danger\'>System Error</span>"
            });
        });
        </script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Staff Management System | Login</title>
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
            document.getElementById('password').focus();	
        }

        $(document).ready(function() {
            $('#form1').validate({
                rules: {
                    password: {
                        required: true,
                        minlength: 5
                    },
                    confirm_password: {
                        required: true,
                        minlength: 5,
                        equalTo: "#password"
                    }
                },
                messages: {
                    password: {
                        required: "Enter Password"
                    },
                    confirm_password: {
                        required: "Confirm Password"
                    }
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });

            $('#save').on('click',function(e){
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
                        if(output) {
                            $('#form1').submit();
                        }
                    }
                });
            });
        });
        </script>
    </head>
    <body class="account-body accountbg">
        <?php
        // Displaying Notifications
        echo "{$success} {$error}";
        ?>
        <div class="container">
            <div class="row vh-100 d-flex justify-content-center">
                <div class="col-12 align-self-center">
                    <div class="row">
                        <div class="col-lg-5 mx-auto">
                            <div class="card">
                                <div class="card-body p-0 auth-header-box">
                                    <div class="text-center p-3">
                                        <a href="javascript: void(0);" class="logo logo-admin">
                                            <img src="template/assets/images/logo-sm.png" height="50" alt="School Logo" class="auth-logo">
                                        </a>
                                        <h4 class="mt-3 mb-1 font-weight-semibold text-white font-14">FEDERAL COLLEGE OF EDUCATION TECHNICAL, BICHI</h4>   
                                        <p class="text-white mb-0">Staff Management System</p>  
                                    </div>
                                </div>
                                <div class="card-body">
                                    <ul class="nav-border nav nav-pills" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active font-weight-semibold" data-toggle="tab" href="#Activation_Tab" role="tab">Activation</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link font-weight-semibold" href="../logout.php">Logout</a>
                                        </li>
                                    </ul>
                                        <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div class="tab-pane active p-3 pt-3" id="Activation_Tab" role="tabpanel">
                                            <form name="form1" id="form1" class="form-horizontal auth-form my-4" method="post" action="" autocomplete="off">
                                            <div class="form-group">
                                                    <label for="staff_id">Username</label>
                                                    <div class="input-group mb-3">                                                                                         
                                                        <input type="text" class="form-control" value="<?php echo $admin; ?>" readonly>
                                                    </div>                                    
                                                </div>
                                                <div class="form-group">
                                                    <label for="staff_id">Name</label>
                                                    <div class="input-group mb-3">                                                                                         
                                                        <input type="text" class="form-control" value="<?php echo $admin_fullname; ?>" readonly>
                                                    </div>                                    
                                                </div>
                                                <div class="form-group">
                                                    <label for="staff_id">Password</label>
                                                    <div class="input-group mb-3">                                                                                         
                                                        <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password">
                                                    </div>                                    
                                                </div>
                                                <div class="form-group">
                                                    <label for="dob">Confirm Password</label>                                      
                                                    <div class="input-group mb-3">                                  
                                                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
                                                    </div>                               
                                                </div>
                                                <div class="form-group mb-0 row">
                                                    <div class="col-12 mt-2">
                                                        <button class="btn btn-primary btn-block waves-effect waves-light" type="submit" name="save" id="save">Update</button>
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