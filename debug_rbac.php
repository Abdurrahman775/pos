<?php
session_start();
require_once('config.php');

echo "<h2>RBAC Debug</h2>";
echo "<h3>Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Role ID from Session:</h3>";
$user_role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 0;
echo "user_role_id = " . $user_role_id . "<br>";
echo "Type: " . gettype($user_role_id) . "<br>";

echo "<h3>Role Permissions Array (from rbac.php):</h3>";
if (isset($role_permissions)) {
    echo "<pre>";
    print_r($role_permissions);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>ERROR: \$role_permissions is not defined!</p>";
}

echo "<h3>Testing has_permission() function:</h3>";
if (function_exists('has_permission')) {
    echo "has_permission function EXISTS<br>";
    echo "has_permission(1, 'dashboard') = " . (has_permission(1, 'dashboard') ? 'TRUE' : 'FALSE') . "<br>";
    echo "has_permission(\$user_role_id, 'dashboard') = " . (has_permission($user_role_id, 'dashboard') ? 'TRUE' : 'FALSE') . "<br>";
    echo "has_permission(\$user_role_id, 'pos') = " . (has_permission($user_role_id, 'pos') ? 'TRUE' : 'FALSE') . "<br>";
    echo "has_permission(\$user_role_id, 'products') = " . (has_permission($user_role_id, 'products') ? 'TRUE' : 'FALSE') . "<br>";
} else {
    echo "<p style='color: red;'>ERROR: has_permission() function NOT defined!</p>";
}

echo "<h3>Global Variables:</h3>";
echo "<pre>";
print_r($GLOBALS['role_permissions'] ?? 'NOT SET');
echo "</pre>";

echo "<h3>Direct Test:</h3>";
// Define permissions directly to test
$test_permissions = [
    1 => ['dashboard', 'pos', 'transactions', 'products', 'customers'],
    2 => ['dashboard', 'pos', 'transactions'],
    3 => ['dashboard', 'pos']
];

echo "Test array:<br>";
echo "<pre>";
print_r($test_permissions);
echo "</pre>";

echo "in_array('dashboard', \$test_permissions[1]) = " . (in_array('dashboard', $test_permissions[1]) ? 'TRUE' : 'FALSE') . "<br>";
?>
