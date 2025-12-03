<ul class="metismenu left-sidenav-menu">
    <li><a href="dashboard.php"><i data-feather="home" class="align-self-center menu-icon"></i><span>Dashboard</span></a></li>
    <li><a href="sales_window.php"><i data-feather="shopping-cart" class="align-self-center menu-icon"></i><span>POS / Sales</span></a></li>

    <!-- Transactions -->
    <li><a href="transactions.php"><i data-feather="file-text" class="align-self-center menu-icon"></i><span>Transactions</span></a></li>

    <!-- Products -->
    <li>
        <a href="javascript: void(0);"><i data-feather="shopping-bag" class="align-self-center menu-icon"></i><span>Inventory</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
        <ul class="nav-second-level" aria-expanded="false">
            <li class="nav-item"><a class="nav-link" href="add_product.php"><i class="ti-control-record"></i>Add Product</a></li>
            <li class="nav-item"><a class="nav-link" href="all_products.php"><i class="ti-control-record"></i>All Products</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_categories.php"><i class="ti-control-record"></i>Categories</a></li>
            <li class="nav-item"><a class="nav-link" href="manage_suppliers.php"><i class="ti-control-record"></i>Suppliers</a></li>
            <li class="nav-item"><a class="nav-link" href="low_stock_alerts.php"><i class="ti-control-record"></i>Low Stock Alerts</a></li>
        </ul>
    </li>

    <!-- Customers -->
    <li>
        <a href="javascript: void(0);"><i data-feather="users" class="align-self-center menu-icon"></i><span>Customers</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
        <ul class="nav-second-level" aria-expanded="false">
            <li class="nav-item"><a class="nav-link" href="customers.php"><i class="ti-control-record"></i>All Customers</a></li>
            <li class="nav-item"><a class="nav-link" href="add_customer.php"><i class="ti-control-record"></i>Add Customer</a></li>
        </ul>
    </li>

    <!-- Reports -->
    <li><a href="reports_dashboard.php"><i data-feather="bar-chart-2" class="align-self-center menu-icon"></i><span>Reports</span></a></li>

    <!-- User Management -->
    <li>
        <a href="javascript: void(0);"><i data-feather="user-check" class="align-self-center menu-icon"></i><span>User Management</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
        <ul class="nav-second-level" aria-expanded="false">
            <li class="nav-item"><a class="nav-link" href="add_admin.php"><i class="ti-control-record"></i>New User</a></li>
            <li class="nav-item"><a class="nav-link" href="employees.php"><i class="ti-control-record"></i>All Users</a></li>
        </ul>
    </li>

    <hr class="hr-dashed hr-menu">

    <!-- System Settings -->
    <li><a href="system_settings.php"><i data-feather="settings" class="align-self-center menu-icon"></i><span>System Settings</span></a></li>

    <!-- Change Password -->
    <li><a href="change_password.php"><i data-feather="lock" class="align-self-center menu-icon"></i><span>Change Password</span></a></li>

    <!-- Audit Log -->
    <li><a href="audit_log.php"><i data-feather="eye" class="align-self-center menu-icon"></i><span>Audit Log</span></a></li>

    <!-- Logout -->
    <li><a href="logout.php"><i data-feather="log-out" class="align-self-center menu-icon"></i><span>Logout</span></a></li>
</ul>