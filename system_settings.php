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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_settings'])) {
    try {
        $dbh->beginTransaction();
        
        // Handle logo upload
        if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] == UPLOAD_ERR_OK) {
            $file = $_FILES['company_logo'];
            $allowed_types = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
            $max_size = 2 * 1024 * 1024; // 2MB
            
            // Validate file type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mime_type, $allowed_types)) {
                throw new Exception('Invalid file type. Only PNG, JPG, JPEG, and GIF are allowed.');
            }
            
            // Validate file size
            if ($file['size'] > $max_size) {
                throw new Exception('File size exceeds 2MB limit.');
            }
            
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'logo_' . time() . '.' . $extension;
            $upload_path = 'uploads/logo/' . $new_filename;
            
            // Delete old logo if exists
            $old_logo_sql = "SELECT setting_value FROM system_settings WHERE setting_key = 'company_logo'";
            $old_logo_query = $dbh->prepare($old_logo_sql);
            $old_logo_query->execute();
            $old_logo = $old_logo_query->fetchColumn();
            
            if ($old_logo && file_exists($old_logo)) {
                unlink($old_logo);
            }
            
            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Save logo path to database
                $check_sql = "SELECT id FROM system_settings WHERE setting_key = 'company_logo'";
                $check_query = $dbh->prepare($check_sql);
                $check_query->execute();
                
                if ($check_query->fetch()) {
                    $sql = "UPDATE system_settings SET setting_value = :value, updated_by = :updated_by, updated_at = NOW() 
                            WHERE setting_key = 'company_logo'";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':value', $upload_path, PDO::PARAM_STR);
                    $query->bindParam(':updated_by', $_SESSION['pos_admin'], PDO::PARAM_STR);
                    $query->execute();
                } else {
                    $sql = "INSERT INTO system_settings (setting_key, setting_value, setting_type, updated_by, updated_at) 
                            VALUES ('company_logo', :value, 'text', :updated_by, NOW())";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':value', $upload_path, PDO::PARAM_STR);
                    $query->bindParam(':updated_by', $_SESSION['pos_admin'], PDO::PARAM_STR);
                    $query->execute();
                }
            } else {
                throw new Exception('Failed to upload file.');
            }
        }
        
        // Handle logo deletion
        if (isset($_POST['delete_logo']) && $_POST['delete_logo'] == '1') {
            $logo_sql = "SELECT setting_value FROM system_settings WHERE setting_key = 'company_logo'";
            $logo_query = $dbh->prepare($logo_sql);
            $logo_query->execute();
            $logo_path = $logo_query->fetchColumn();
            
            if ($logo_path && file_exists($logo_path)) {
                unlink($logo_path);
            }
            
            $del_sql = "DELETE FROM system_settings WHERE setting_key = 'company_logo'";
            $del_query = $dbh->prepare($del_sql);
            $del_query->execute();
        }
        
        $settings_to_update = [
            'store_name',
            'store_address',
            'store_phone',
            'store_email',
            'tax_rate',
            'currency_symbol',
            'currency_code',
            'receipt_footer',
            'session_timeout',
            'low_stock_threshold'
        ];

        foreach ($settings_to_update as $key) {
            if (isset($_POST[$key])) {
                $value = trim($_POST[$key]);
                
                // Check if setting exists
                $check_sql = "SELECT id FROM system_settings WHERE setting_key = :key";
                $check_query = $dbh->prepare($check_sql);
                $check_query->bindParam(':key', $key, PDO::PARAM_STR);
                $check_query->execute();
                
                if ($check_query->fetch()) {
                    // Update existing setting
                    $sql = "UPDATE system_settings SET setting_value = :value, updated_by = :updated_by, updated_at = NOW() 
                            WHERE setting_key = :key";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':value', $value, PDO::PARAM_STR);
                    $query->bindParam(':updated_by', $_SESSION['pos_admin'], PDO::PARAM_STR);
                    $query->bindParam(':key', $key, PDO::PARAM_STR);
                    $query->execute();
                } else {
                    // Insert new setting
                    $sql = "INSERT INTO system_settings (setting_key, setting_value, setting_type, updated_by, updated_at) 
                            VALUES (:key, :value, 'text', :updated_by, NOW())";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':key', $key, PDO::PARAM_STR);
                    $query->bindParam(':value', $value, PDO::PARAM_STR);
                    $query->bindParam(':updated_by', $_SESSION['pos_admin'], PDO::PARAM_STR);
                    $query->execute();
                }
            }
        }

        // Log activity
        if (isset($_SESSION['pos_admin'])) {
            log_activity($dbh, 'UPDATE', 'Updated system settings');
        }
        
        $dbh->commit();
        $success = 'Settings updated successfully!';
    } catch (Exception $e) {
        $dbh->rollBack();
        $error = 'Error updating settings: ' . $e->getMessage();
    }
}

// Get current settings
$settings = [];
$sql = "SELECT * FROM system_settings";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);
foreach ($results as $row) {
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
    <?php include('include/sidebar.php'); ?>
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

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success; ?>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-lg-10 mx-auto">
                        <form method="POST" action="" enctype="multipart/form-data">
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
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="company_logo">Company Logo</label>
                                                <?php if (!empty($settings['company_logo']) && file_exists($settings['company_logo'])): ?>
                                                    <div class="mb-2">
                                                        <img src="<?php echo htmlspecialchars($settings['company_logo']); ?>" 
                                                             alt="Current Logo" 
                                                             style="max-height: 100px; max-width: 200px; border: 1px solid #ddd; padding: 5px; background: #fff;">
                                                        <div class="mt-2">
                                                            <label class="custom-control custom-checkbox">
                                                                <input type="checkbox" name="delete_logo" value="1" class="custom-control-input">
                                                                <span class="custom-control-label">Delete current logo</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                                <input type="file" class="form-control-file" id="company_logo" name="company_logo" accept="image/png,image/jpeg,image/jpg,image/gif">
                                                <small class="form-text text-muted">Upload a new logo (PNG, JPG, JPEG, GIF. Max 2MB). Leave empty to keep current logo.</small>
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