<?php
require("config.php");
require("include/functions.php");
require("include/admin_authentication.php");
// Try to redirect
header("Location: customers.php?test=1");
exit;
?>
