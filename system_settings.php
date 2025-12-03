<?php
/**
 * System Settings Page
 * Configure system-wide settings (Administrator only)
 */
require("config.php");
require("include/functions.php");
require_once("include/pos_functions.php");
require("include/authentication.php");
require("include/admin_constants.php");

// Require system_settings permission (Administrator only)
require_permission('system_settings');

$error = '';
$success = '';

// Handle form submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_settings'])) {
    try {
        $settings_to_update = [
            'store_name', 'store_address', 'store_phone', 'store_email',
            'tax_rate', 'currency_symbol', 'currency_code', 'receipt_footer',
            'session_timeout', 'low_stock_threshold'
        ];
        
        foreach($settings_to_update as $key) {
            if(isset($_POST[$key])) {
                $value = trim($_POST[$key]);
                update_setting($dbh, $key, $value, $_SESSION['pos_admin']);
            }
        }
        
        log_activity($dbh, 'UPDATE_SETTINGS', 'Updated system settings');
        $success = 'Settings updated successfully!';
    } catch(Exception $e) {
        $error = 'Error updating settings: ' . $e->getMessage();
    }
}

// Get current settings
$settings = [];
$sql = "SELECT * FROM system_settings";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);
foreach($results as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>System Settings | POS System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel="shortcut icon" href="template/assets/images/favicon.ico">
    <link href="template/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/metisMenu.min.css" rel="stylesheet" type="text/css" />
    <link href="template/assets/css/app.min.css" rel="stylesheet" type="text/css" />
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
                            <h4 class="page-title">System Settings</h4>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                <li class="breadcrumb-item active">System Settings</li>
                            </ol>
                        </div>
                    </div>
                </div>
                
                <?php if($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                <?php endif; ?>
                
                <?php if($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success; ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-lg-10 mx-auto">
                        <form method="POST" action="">
                            <!-- Store Information -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-store mr-2"></i>Store Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="store_name">Store Name</label>
                                                <input type="text" class="form-control" id="store_name" name="store_name" 
                                                       value="<?php echo htmlspecialchars($settings['store_name'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="store_phone">Store Phone</label>
                                                <input type="text" class="form-control" id="store_phone" name="store_phone"
                                                       value="<?php echo htmlspecialchars($settings['store_phone'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="store_email">Store Email</label>
                                                <input type="email" class="form-control" id="store_email" name="store_email"
                                                       value="<?php echo htmlspecialchars($settings['store_email'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="store_address">Store Address</label>
                                                <input type="text" class="form-control" id="store_address" name="store_address"
                                                       value="<?php echo htmlspecialchars($settings['store_address'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Currency & Tax Settings -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-coins mr-2"></i>Currency & Tax Settings</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="currency_symbol">Currency Symbol</label>
                                                <input type="text" class="form-control" id="currency_symbol" name="currency_symbol"
                                                       value="<?php echo htmlspecialchars($settings['currency_symbol'] ?? '₦'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="currency_code">Currency Code</label>
                                                <input type="text" class="form-control" id="currency_code" name="currency_code"
                                                       value="<?php echo htmlspecialchars($settings['currency_code'] ?? 'NGN'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="tax_rate">Tax Rate (%)</label>
                                                <input type="number" step="0.01" class="form-control" id="tax_rate" name="tax_rate"
                                                       value="<?php echo htmlspecialchars($settings['tax_rate'] ?? '7.5'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Receipt Settings -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-receipt mr-2"></i>Receipt Settings</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="receipt_footer">Receipt Footer Message</label>
                                        <textarea class="form-control" id="receipt_footer" name="receipt_footer" rows="3"><?php echo htmlspecialchars($settings['receipt_footer'] ?? ''); ?></textarea>
                                        <small class="form-text text-muted">This message will appear at the bottom of all receipts</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- System Preferences -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-cog mr-2"></i>System Preferences</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="session_timeout">Session Timeout (minutes)</label>
                                                <input type="number" class="form-control" id="session_timeout" name="session_timeout"
                                                       value="<?php echo htmlspecialchars($settings['session_timeout'] ?? '30'); ?>">
                                                <small class="form-text text-muted">Users will be logged out after this period of inactivity</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="low_stock_threshold">Default Low Stock Threshold</label>
                                                <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold"
                                                       value="<?php echo htmlspecialchars($settings['low_stock_threshold'] ?? '10'); ?>">
                                                <small class="form-text text-muted">Minimum stock level for new products</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group text-right">
                                <button type="submit" name="save_settings" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save mr-2"></i> Save Settings
                                </button>
                            </div>
                        </form>
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
    <script src="template/assets/js/app.js"></script>
</body>
</html>
