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
                                    
<a href="dashboard.php" class="logo">
    <?php if ($company_logo && file_exists($company_logo)): ?>
                                            <img src="<?php echo htmlspecialchars($company_logo); ?>" height="50" alt="Company Logo" class="auth-logo">
                                        <?php else: ?>
                                            <img src="template/assets/images/logo-sm.png" height="50" alt="Logo" class="auth-logo">
                                        <?php endif; ?>
</a>



