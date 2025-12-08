<?php
/**
 * Access Denied Page
 * Shown when a user tries to access a resource they don't have permission for
 */
require("config.php");
require("include/functions.php");

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

$username = isset($_SESSION['pos_admin']) ? $_SESSION['pos_admin'] : 'Unknown';
$role_name = isset($_SESSION['role_name']) ? $_SESSION['role_name'] : 'Unknown';

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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Access Denied | <?php echo htmlspecialchars($company_name); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="template/assets/images/favicon.ico">
    <!-- App css -->
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
</head>
<body class="account-body accountbg">
    <div class="container">
        <div class="row vh-100 d-flex justify-content-center">
            <div class="col-12 align-self-center">
                <div class="row">
                    <div class="col-lg-6 mx-auto">
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
                                    <h4 class="mt-3 mb-1 font-weight-semibold text-white font-18"><?php echo htmlspecialchars($company_name); ?> - Access Denied</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="text-center">
                                    <i class="dripicons-lock text-danger" style="font-size: 72px;"></i>
                                    <h3 class="mt-4 font-weight-semibold">Permission Denied</h3>
                                    <p class="text-muted mb-4">You do not have permission to access this resource.</p>
                                    
                                    <div class="alert alert-info" role="alert">
                                        <strong>User:</strong> <?php echo htmlspecialchars($username); ?><br>
                                        <strong>Role:</strong> <?php echo htmlspecialchars($role_name); ?>
                                    </div>
                                    
                                    <p class="text-muted">If you believe you should have access to this resource, please contact your administrator.</p>
                                    
                                    <div class="mt-4">
                                        <a href="dashboard.php" class="btn btn-primary mr-2"><i class="fas fa-home mr-1"></i> Go to Dashboard</a>
                                        <a href="logout.php" class="btn btn-secondary"><i class="fas fa-sign-out-alt mr-1"></i> Logout</a>
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
