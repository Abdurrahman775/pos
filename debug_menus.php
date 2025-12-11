<?php
// Create a debug page to test the exact inclusion scenario
require('config.php');
require('include/functions.php');

echo "<h2>Menus.php Debug</h2>";

echo "<h3>Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Role ID:</h3>";
$user_role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 0;
echo "user_role_id = " . $user_role_id . "<br>";

echo "<h3>\$role_permissions in main scope:</h3>";
if (isset($role_permissions)) {
    echo "<pre>";
    print_r($role_permissions);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>NOT SET in main scope</p>";
}

echo "<h3>Global \$role_permissions:</h3>";
global $role_permissions;
if (isset($role_permissions)) {
    echo "<pre>";
    print_r($role_permissions);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>NOT SET even after global declaration</p>";
}

echo "<h3>Testing has_permission:</h3>";
echo "has_permission(1, 'dashboard') = " . (has_permission(1, 'dashboard') ? 'TRUE' : 'FALSE') . "<br>";
echo "has_permission(\$user_role_id, 'dashboard') = " . (has_permission($user_role_id, 'dashboard') ? 'TRUE' : 'FALSE') . "<br>";

echo "<h3>Now including menus.php...</h3>";
ob_start();
include('include/menus.php');
$menu_html = ob_get_clean();

echo "<h3>Generated Menu HTML:</h3>";
echo "<textarea style='width:100%; height:400px;'>" . htmlspecialchars($menu_html) . "</textarea>";

echo "<h3>Count menu items:</h3>";
$count = substr_count($menu_html, '<li');
echo "Number of &lt;li&gt; tags: " . $count;
?>
