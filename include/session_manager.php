<?php

/**
 * Session Manager
 * Centralized session handling to prevent conflicts and ensure proper initialization
 */

/**
 * Initialize session safely
 * Should be called at the beginning of each page that needs session data
 */
if (!function_exists('initialize_session')) {
    function initialize_session()
    {
        // Check if session is not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}

/**
 * Destroy session safely
 * Clears all session data and cookies
 */
if (!function_exists('destroy_session')) {
    function destroy_session()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Clear all session data
            session_unset();

            // Destroy the session
            session_destroy();

            // Clear session cookie
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }
        }
    }
}

/**
 * Regenerate session ID for security
 * Prevents session fixation attacks
 */
if (!function_exists('regenerate_session')) {
    function regenerate_session()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}

/**
 * Clear specific session variable
 * @param string $key The session key to clear
 */
if (!function_exists('clear_session_var')) {
    function clear_session_var($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
}

/**
 * Clear all login-related session variables
 * Used when switching users or logging out
 */
if (!function_exists('clear_login_session')) {
    function clear_login_session()
    {
        $login_vars = ['pos_admin', 'pos_admin_temp', 'pos_admin_basic', 'admin_id', 'role_name'];
        foreach ($login_vars as $var) {
            clear_session_var($var);
        }
    }
}

/**
 * Check if user is logged in
 * @return bool
 */
if (!function_exists('is_user_logged_in')) {
    function is_user_logged_in()
    {
        return isset($_SESSION['pos_admin']) && !empty($_SESSION['pos_admin']);
    }
}

/**
 * Check if user is in activation process
 * @return bool
 */
if (!function_exists('is_activation_pending')) {
    function is_activation_pending()
    {
        return isset($_SESSION['pos_admin_temp']) && !empty($_SESSION['pos_admin_temp']);
    }
}

/**
 * Get current username
 * @return string|null
 */
if (!function_exists('get_current_user')) {
    function get_current_user()
    {
        return isset($_SESSION['pos_admin']) ? $_SESSION['pos_admin'] : null;
    }
}

/**
 * Get session duration in seconds
 * @return int
 */
if (!function_exists('get_session_duration')) {
    function get_session_duration()
    {
        $timeout = ini_get('session.gc_maxlifetime');
        return $timeout ? (int)$timeout : 1440; // Default 24 minutes
    }
}
