<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Define groups for parent menu activation
$inventory_pages = ['add_product.php', 'all_products.php', 'manage_categories.php', 'manage_suppliers.php', 'low_stock_alerts.php', 'edit_product.php'];
$customer_pages = ['customers.php', 'add_customer.php', 'edit_customer.php', 'customer_details.php'];
$user_pages = ['add_admin.php', 'employees.php', 'edit_admin.php'];

// Check active groups
$is_inventory_active = in_array($current_page, $inventory_pages);
$is_customer_active = in_array($current_page, $customer_pages);
$is_user_active = in_array($current_page, $user_pages);
?>
<ul class="metismenu left-sidenav-menu">
    <li class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
        <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i data-feather="home" class="align-self-center menu-icon"></i><span>Dashboard</span>
        </a>
    </li>
    <li class="<?php echo ($current_page == 'sales_window.php') ? 'active' : ''; ?>">
        <a href="sales_window.php" class="<?php echo ($current_page == 'sales_window.php') ? 'active' : ''; ?>">
            <i data-feather="shopping-cart" class="align-self-center menu-icon"></i><span>POS / Sales</span>
        </a>
    </li>

    <!-- Transactions -->
    <li class="<?php echo ($current_page == 'transactions.php') ? 'active' : ''; ?>">
        <a href="transactions.php" class="<?php echo ($current_page == 'transactions.php') ? 'active' : ''; ?>">
            <i data-feather="file-text" class="align-self-center menu-icon"></i><span>Transactions</span>
        </a>
    </li>

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
            <li class="nav-item <?php echo ($current_page == 'manage_categories.php') ? 'active' : ''; ?>">
                <a class="nav-link <?php echo ($current_page == 'manage_categories.php') ? 'active' : ''; ?>" href="manage_categories.php"><i class="ti-control-record"></i>Categories</a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'manage_suppliers.php') ? 'active' : ''; ?>">
                <a class="nav-link <?php echo ($current_page == 'manage_suppliers.php') ? 'active' : ''; ?>" href="manage_suppliers.php"><i class="ti-control-record"></i>Suppliers</a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'low_stock_alerts.php') ? 'active' : ''; ?>">
                <a class="nav-link <?php echo ($current_page == 'low_stock_alerts.php') ? 'active' : ''; ?>" href="low_stock_alerts.php"><i class="ti-control-record"></i>Low Stock Alerts</a>
            </li>
        </ul>
    </li>

    <!-- Customers -->
    <li class="<?php echo $is_customer_active ? 'active mm-active' : ''; ?>">
        <a href="javascript: void(0);" class="<?php echo $is_customer_active ? 'active' : ''; ?>">
            <i data-feather="users" class="align-self-center menu-icon"></i><span>Customers</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span>
        </a>
        <ul class="nav-second-level <?php echo $is_customer_active ? 'in mm-show' : ''; ?>" aria-expanded="<?php echo $is_customer_active ? 'true' : 'false'; ?>">
            <li class="nav-item <?php echo ($current_page == 'customers.php' || $current_page == 'edit_customer.php' || $current_page == 'customer_details.php') ? 'active' : ''; ?>">
                <a class="nav-link <?php echo ($current_page == 'customers.php' || $current_page == 'edit_customer.php' || $current_page == 'customer_details.php') ? 'active' : ''; ?>" href="customers.php"><i class="ti-control-record"></i>All Customers</a>
            </li>
            <li class="nav-item <?php echo ($current_page == 'add_customer.php') ? 'active' : ''; ?>">
                <a class="nav-link <?php echo ($current_page == 'add_customer.php') ? 'active' : ''; ?>" href="add_customer.php"><i class="ti-control-record"></i>Add Customer</a>
            </li>
        </ul>
    </li>

    <!-- Reports -->
    <li class="<?php echo ($current_page == 'reports_dashboard.php') ? 'active' : ''; ?>">
        <a href="reports_dashboard.php" class="<?php echo ($current_page == 'reports_dashboard.php') ? 'active' : ''; ?>">
            <i data-feather="bar-chart-2" class="align-self-center menu-icon"></i><span>Reports</span>
        </a>
    </li>

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

    <hr class="hr-dashed hr-menu">

    <!-- System Settings -->
    <li class="<?php echo ($current_page == 'system_settings.php') ? 'active' : ''; ?>">
        <a href="system_settings.php" class="<?php echo ($current_page == 'system_settings.php') ? 'active' : ''; ?>">
            <i data-feather="settings" class="align-self-center menu-icon"></i><span>System Settings</span>
        </a>
    </li>

    <!-- Change Password -->
    <li class="<?php echo ($current_page == 'change_password.php') ? 'active' : ''; ?>">
        <a href="change_password.php" class="<?php echo ($current_page == 'change_password.php') ? 'active' : ''; ?>">
            <i data-feather="lock" class="align-self-center menu-icon"></i><span>Change Password</span>
        </a>
    </li>

    <!-- Audit Log -->
    <li class="<?php echo ($current_page == 'audit_log.php') ? 'active' : ''; ?>">
        <a href="audit_log.php" class="<?php echo ($current_page == 'audit_log.php') ? 'active' : ''; ?>">
            <i data-feather="eye" class="align-self-center menu-icon"></i><span>Audit Log</span>
        </a>
    </li>

    <!-- Logout -->
    <li><a href="logout.php"><i data-feather="log-out" class="align-self-center menu-icon"></i><span>Logout</span></a></li>
</ul>