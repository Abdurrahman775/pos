<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);

// Load RBAC functions
require_once('rbac.php');

// Ensure $role_permissions is accessible in this scope
global $role_permissions;

// Get user's role from session
$user_role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 0;

// Fallback: If role_id is not in session, fetch it from database
// This handles cases where user logged in before role_id was added to session
if ($user_role_id == 0 && isset($_SESSION['pos_admin'])) {
    $username = $_SESSION['pos_admin'];
    $sql = "SELECT role_id FROM admins WHERE username = :username";
    $query = $dbh->prepare($sql);
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $user_role_id = $result['role_id'];
        // Store in session for next time
        $_SESSION['role_id'] = $user_role_id;
    }
}

// Define groups for parent menu activation
$inventory_pages = ['add_product.php', 'all_products.php', 'manage_categories.php', 'manage_suppliers.php', 'low_stock_alerts.php', 'edit_product.php'];
$customer_pages = ['customers.php', 'edit_customer.php', 'customer_details.php'];
$user_pages = ['add_admin.php', 'employees.php', 'edit_admin.php'];

// Check active groups
$is_inventory_active = in_array($current_page, $inventory_pages);
$is_customer_active = in_array($current_page, $customer_pages);
$is_user_active = in_array($current_page, $user_pages);
?>
<ul class="metismenu left-sidenav-menu">
    <?php if (has_permission($user_role_id, 'dashboard')): ?>
    <li class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
        <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i data-feather="home" class="align-self-center menu-icon"></i><span>Dashboard</span>
        </a>
    </li>
    <?php endif; ?>
    
    <?php if (has_permission($user_role_id, 'pos')): ?>
    <li class="<?php echo ($current_page == 'sales_window.php') ? 'active' : ''; ?>">
        <a href="sales_window.php" class="<?php echo ($current_page == 'sales_window.php') ? 'active' : ''; ?>">
            <i data-feather="shopping-cart" class="align-self-center menu-icon"></i><span>POS / Sales</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (has_permission($user_role_id, 'transactions')): ?>
    <!-- Transactions -->
    <li class="<?php echo ($current_page == 'transactions.php') ? 'active' : ''; ?>">
        <a href="transactions.php" class="<?php echo ($current_page == 'transactions.php') ? 'active' : ''; ?>">
            <i data-feather="file-text" class="align-self-center menu-icon"></i><span>Transactions</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (has_permission($user_role_id, 'virtual_account')): ?>
    <!-- Virtual Account -->
    <li class="<?php echo ($current_page == 'virtual_account.php') ? 'active' : ''; ?>">
        <a href="virtual_account.php" class="<?php echo ($current_page == 'virtual_account.php') ? 'active' : ''; ?>">
            <i data-feather="credit-card" class="align-self-center menu-icon"></i><span>Virtual Account</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (has_permission($user_role_id, 'products')): ?>
    <!-- Products -->
    <li class="<?php echo $is_inventory_active ? 'active mm-active' : ''; ?>">
        <a href="javascript: void(0);" class="<?php echo $is_inventory_active ? 'active' : ''; ?>">
            <i data-feather="shopping-bag" class="align-self-center menu-icon"></i><span>Inventory</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="nav-second-level <?php echo $is_inventory_active ? 'in mm-show' : ''; ?>" aria-expanded="<?php echo $is_inventory_active ? 'true' : 'false'; ?>">
            <li class="nav-item <?php echo ($current_page == 'add_product.php') ? 'active' : ''; ?>">
                <a class="nav-link <?php echo ($current_page == 'add_product.php') ? 'active' : ''; ?>" href="add_product.php"><i class="ti-control-record"></i>Add Product</a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'all_products.php' || $current_page == 'edit_product.php') ? 'active' : ''; ?>">
                <a class="nav-link <?php echo ($current_page == 'all_products.php' || $current_page == 'edit_product.php') ? 'active' : ''; ?>" href="all_products.php"><i class="ti-control-record"></i>All Products</a>
            </li>
            <?php if (has_permission($user_role_id, 'categories')): ?>
            <li class="nav-item <?php echo ($current_page == 'manage_categories.php') ? 'active' : ''; ?>">
                <a class="nav-link <?php echo ($current_page == 'manage_categories.php') ? 'active' : ''; ?>" href="manage_categories.php"><i class="ti-control-record"></i>Categories</a>
            </li>
            <?php endif; ?>
            <?php if (has_permission($user_role_id, 'suppliers')): ?>
            <li class="nav-item <?php echo ($current_page == 'manage_suppliers.php') ? 'active' : ''; ?>">
                <a class="nav-link <?php echo ($current_page == 'manage_suppliers.php') ? 'active' : ''; ?>" href="manage_suppliers.php"><i class="ti-control-record"></i>Suppliers</a>
            </li>
            <?php endif; ?>
            <?php if (has_permission($user_role_id, 'low_stock')): ?>
            <li class="nav-item <?php echo ($current_page == 'low_stock_alerts.php') ? 'active' : ''; ?>">
                <a class="nav-link <?php echo ($current_page == 'low_stock_alerts.php') ? 'active' : ''; ?>" href="low_stock_alerts.php"><i class="ti-control-record"></i>Low Stock Alerts</a>
            </li>
            <?php endif; ?>
        </ul>
    </li>
    <?php endif; ?>

    <?php if (has_permission($user_role_id, 'customers')): ?>
    <!-- Customers -->
    <li class="<?php echo $is_customer_active ? 'active mm-active' : ''; ?>">
        <a href="javascript: void(0);" class="<?php echo $is_customer_active ? 'active' : ''; ?>">
            <i data-feather="users" class="align-self-center menu-icon"></i><span>Customers</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="nav-second-level <?php echo $is_customer_active ? 'in mm-show' : ''; ?>" aria-expanded="<?php echo $is_customer_active ? 'true' : 'false'; ?>">
            <li class="nav-item <?php echo ($current_page == 'customers.php' || $current_page == 'edit_customer.php' || $current_page == 'customer_details.php') ? 'active' : ''; ?>">
                <a class="nav-link <?php echo ($current_page == 'customers.php' || $current_page == 'edit_customer.php' || $current_page == 'customer_details.php') ? 'active' : ''; ?>" href="customers.php"><i class="ti-control-record"></i>All Customers</a>
            </li>
        </ul>
    </li>
    <?php endif; ?>

    <?php if (has_permission($user_role_id, 'reports')): ?>
    <!-- Reports -->
    <li class="<?php echo ($current_page == 'reports_dashboard.php') ? 'active' : ''; ?>">
        <a href="reports_dashboard.php" class="<?php echo ($current_page == 'reports_dashboard.php') ? 'active' : ''; ?>">
            <i data-feather="bar-chart-2" class="align-self-center menu-icon"></i><span>Reports</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if (has_permission($user_role_id, 'employees') || has_permission($user_role_id, 'users')): ?>
    <!-- User Management -->
    <li class="<?php echo $is_user_active ? 'active mm-active' : ''; ?>">
        <a href="javascript: void(0);" class="<?php echo $is_user_active ? 'active' : ''; ?>">
            <i data-feather="user-check" class="align-self-center menu-icon"></i><span>User Management</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="nav-second-level <?php echo $is_user_active ? 'in mm-show' : ''; ?>" aria-expanded="<?php echo $is_user_active ? 'true' : 'false'; ?>">
            <li class="nav-item <?php echo ($current_page == 'add_admin.php') ? 'active' : ''; ?>">
                <a class="nav-link <?php echo ($current_page == 'add_admin.php') ? 'active' : ''; ?>" href="add_admin.php"><i class="ti-control-record"></i>New User</a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'employees.php' || $current_page == 'edit_admin.php') ? 'active' : ''; ?>">
                <a class="nav-link <?php echo ($current_page == 'employees.php' || $current_page == 'edit_admin.php') ? 'active' : ''; ?>" href="employees.php"><i class="ti-control-record"></i>All Users</a>
            </li>
        </ul>
    </li>
    <?php endif; ?>

    <hr class="hr-dashed hr-menu">

    <?php if (has_permission($user_role_id, 'system_settings')): ?>
    <!-- System Settings -->
    <li class="<?php echo ($current_page == 'system_settings.php') ? 'active' : ''; ?>">
        <a href="system_settings.php" class="<?php echo ($current_page == 'system_settings.php') ? 'active' : ''; ?>">
            <i data-feather="settings" class="align-self-center menu-icon"></i><span>System Settings</span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Change Password (available to all users) -->
    <li class="<?php echo ($current_page == 'change_password.php') ? 'active' : ''; ?>">
        <a href="change_password.php" class="<?php echo ($current_page == 'change_password.php') ? 'active' : ''; ?>">
            <i data-feather="lock" class="align-self-center menu-icon"></i><span>Change Password</span>
        </a>
    </li>

    <?php if (has_permission($user_role_id, 'audit_log')): ?>
    <!-- Audit Log -->
    <li class="<?php echo ($current_page == 'audit_log.php') ? 'active' : ''; ?>">
        <a href="audit_log.php" class="<?php echo ($current_page == 'audit_log.php') ? 'active' : ''; ?>">
            <i data-feather="eye" class="align-self-center menu-icon"></i><span>Audit Log</span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Logout (available to all users) -->
    <li><a href="logout.php"><i data-feather="log-out" class="align-self-center menu-icon"></i><span>Logout</span></a></li>
</ul>