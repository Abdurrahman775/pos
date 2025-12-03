<?php

/**
 * Role-Based Access Control (RBAC) System
 * This file defines user roles and permissions for the POS system
 */

// Define user roles
define('ROLE_ADMINISTRATOR', 1);
define('ROLE_MANAGER', 2);
define('ROLE_CASHIER', 3);

// Define permissions for each role
$role_permissions = [
    ROLE_ADMINISTRATOR => [
        'dashboard',
        'pos',
        'transactions',
        'refunds',
        'products',
        'categories',
        'suppliers',
        'customers',
        'employees',
        'reports',
        'settings',
        'users',
        'backup',
        'audit_log',
        'low_stock',
        'bulk_import',
        'employee_performance',
        'attendance',
        'system_settings'
    ],
    ROLE_MANAGER => [
        'dashboard',
        'pos',
        'transactions',
        'refunds',
        'products',
        'categories',
        'suppliers',
        'customers',
        'reports',
        'low_stock',
        'bulk_import'
    ],
    ROLE_CASHIER => [
        'pos',
        'view_own_transactions',
        'change_password'
    ]
];

/**
 * Check if a user has permission to access a resource
 * 
 * @param int $role_id User's role ID
 * @param string $permission Permission to check
 * @return bool True if user has permission, false otherwise
 */
function has_permission($role_id, $permission)
{
    global $role_permissions;

    if (!isset($role_permissions[$role_id])) {
        return false;
    }

    return in_array($permission, $role_permissions[$role_id]);
}

/**
 * Require permission to access a resource
 * Redirects to access denied page if permission is not granted
 * 
 * @param string $permission Permission required
 */
function require_permission($permission)
{
    if (!isset($_SESSION['admin_id']) || !isset($_SESSION['role_id'])) {
        // Store the current page as referrer for post-login redirect
        $_SESSION['login_referrer'] = basename($_SERVER['PHP_SELF']);
        if (!empty($_SERVER['QUERY_STRING'])) {
            $_SESSION['login_referrer'] .= '?' . $_SERVER['QUERY_STRING'];
        }
        header("Location: index.php");
        exit();
    }

    if (!has_permission($_SESSION['role_id'], $permission)) {
        header("Location: access_denied.php");
        exit();
    }
}

/**
 * Get role name from role ID
 * 
 * @param int $role_id Role ID
 * @return string Role name
 */
function get_role_name($role_id)
{
    switch ($role_id) {
        case ROLE_ADMINISTRATOR:
            return 'Administrator';
        case ROLE_MANAGER:
            return 'Manager';
        case ROLE_CASHIER:
            return 'Cashier';
        default:
            return 'Unknown';
    }
}

/**
 * Check if user is administrator
 * 
 * @return bool True if user is administrator
 */
function is_administrator()
{
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == ROLE_ADMINISTRATOR;
}

/**
 * Check if user is manager
 * 
 * @return bool True if user is manager
 */
function is_manager()
{
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == ROLE_MANAGER;
}

/**
 * Check if user is cashier
 * 
 * @return bool True if user is cashier
 */
function is_cashier()
{
    return isset($_SESSION['role_id']) && $_SESSION['role_id'] == ROLE_CASHIER;
}
