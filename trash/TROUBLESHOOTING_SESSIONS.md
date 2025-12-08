# Session Issues - Quick Troubleshooting Guide

## Common Issues and Solutions

### Issue 1: "Another session is active. End it and try again"

**Status:** ✅ FIXED

This error has been resolved. Users can now log in/out and switch users without this error.

**If still experiencing this:**

1. Clear browser cookies
2. Clear browser cache
3. Try in a private/incognito window
4. Check that `include/session_manager.php` exists
5. Verify `include/login.php` is updated

### Issue 2: Session expires too quickly

**Solution:**

1. Check `SESSION_TIMEOUT` setting in `include/authentication.php` (default: 30 minutes)
2. Check PHP `session.gc_maxlifetime` in php.ini (should be ≥ 1800 seconds)
3. Verify `session_start()` is being called with proper status checking

**To increase timeout:**
Edit `include/authentication.php`:

```php
define('SESSION_TIMEOUT', 45); // 45 minutes instead of 30
```

### Issue 3: Getting logged out unexpectedly

**Possible causes:**

1. Browser cache interfering - clear it
2. Session timeout reached - log back in
3. Another user logged in on same account - log back in with current credentials
4. Server session files corrupted

**Solution:**

1. Clear browser cache and cookies
2. Restart browser
3. Log in again

### Issue 4: PHP Warning about session_start()

**Status:** ✅ FIXED

All `session_start()` calls now use proper status checking:

```php
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
```

### Issue 5: Cart disappearing

**Related to:** Session handling
**Solution:**

1. Verify `include/customer_cart.php` is using proper session initialization
2. Check that `SESSION_CART` is being set properly
3. Clear cookies and try again

---

## Diagnostic Steps

### Check if Session Manager is Loaded

The file `/include/session_manager.php` should exist and contain session management functions.

**Verify:**

```bash
ls -la /var/www/html/pos/include/session_manager.php
```

### Check Session Files

```bash
# Find PHP session directory (usually /tmp or /var/lib/php/sessions)
php -r "echo ini_get('session.save_path');"
```

### Test Session Status

Create a test file at `/test_session.php`:

```php
<?php
require('config.php');
require('include/session_manager.php');

initialize_session();
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'ACTIVE' : 'INACTIVE') . "<br>";
echo "Session ID: " . session_id() . "<br>";
echo "Session Timeout: " . ini_get('session.gc_maxlifetime') . " seconds<br>";
echo "Session Storage: " . ini_get('session.save_path') . "<br>";

// Test setting/getting session variables
$_SESSION['test'] = 'working';
echo "Test Variable: " . (isset($_SESSION['test']) ? $_SESSION['test'] : 'NOT SET') . "<br>";
?>
```

Access it at: `http://yourdomain/pos/test_session.php`

### Enable Session Debugging

Add to `config.php` temporarily:

```php
// Session debugging (remove in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/pos/error.log');
```

---

## Performance Tips

1. **Use Redis for Sessions** (recommended for production)

   - Edit php.ini:

   ```
   session.save_handler = redis
   session.save_path = "tcp://127.0.0.1:6379"
   ```

2. **Session Cleanup**

   - PHP automatically cleans old sessions
   - Configure `session.gc_probability` and `session.gc_divisor`

3. **Monitor Session Files**

   ```bash
   # Check session file count
   ls /tmp/sess_* | wc -l

   # Delete old sessions manually
   find /tmp/sess_* -type f -atime +1 -delete
   ```

---

## Security Checklist

✅ Session IDs regenerated on login
✅ Session IDs regenerated on logout
✅ Old sessions cleared when switching users
✅ Session cookies have security flags
✅ Session timeout enforced (30 minutes)
✅ Activity logging implemented
✅ Cache headers set to prevent caching of sensitive pages

---

## Contact/Support

If issues persist:

1. Check the error logs at `/var/www/html/pos/error.log`
2. Review PHP error logs
3. Check system session directory permissions
4. Verify database connectivity
5. Review SESSION_FIXES.md for detailed information

---

**Last Updated:** December 3, 2025
