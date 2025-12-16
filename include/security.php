<?php

/**
 * Security Helper Functions
 * CSRF Protection, Rate Limiting, XSS Prevention
 */

/**
 * Generate CSRF Token
 * Creates a unique token for form submission protection
 * 
 * @return string CSRF token
 */
function generate_csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF Token
 * Checks if submitted token matches session token
 * 
 * @param string $token Token to validate
 * @return bool True if valid, false otherwise
 */
function validate_csrf_token($token)
{
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate new CSRF token (for post-submission)
 * Regenerates token after successful form submission
 */
function regenerate_csrf_token()
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Get CSRF Token HTML Input
 * Returns hidden input field with CSRF token
 * 
 * @return string HTML input field
 */
function csrf_token_field()
{
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Sanitize Output for XSS Prevention
 * Wrapper for htmlspecialchars with secure defaults
 * 
 * @param string $string String to sanitize
 * @param int $flags Optional flags
 * @return string Sanitized string
 */
function escape_html($string)
{
    if (is_null($string)) {
        return '';
    }
    return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Sanitize for JavaScript context
 * 
 * @param string $string String to sanitize
 * @return string Sanitized string
 */
function escape_js($string)
{
    if (is_null($string)) {
        return '';
    }
    return json_encode($string, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

/**
 * Check Rate Limiting for Login Attempts
 * Prevents brute force attacks
 * 
 * @param PDO $dbh Database connection
 * @param string $username Username attempting login
 * @param string $ip_address IP address of request
 * @return array ['allowed' => bool, 'message' => string, 'wait_time' => int]
 */
function check_login_rate_limit($dbh, $username, $ip_address)
{
    // Configuration
    $max_attempts = 5; // Maximum failed attempts
    $lockout_time = 900; // 15 minutes in seconds
    $time_window = 300; // 5 minutes in seconds
    
    try {
        // Create table if not exists
        $create_table = "CREATE TABLE IF NOT EXISTS login_attempts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100),
            ip_address VARCHAR(45),
            attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP,
            success TINYINT(1) DEFAULT 0,
            INDEX idx_username (username),
            INDEX idx_ip (ip_address),
            INDEX idx_time (attempt_time)
        )";
        $dbh->exec($create_table);
        
        // Check for recent failed attempts
        $sql = "SELECT COUNT(*) as failed_count, MAX(attempt_time) as last_attempt
                FROM login_attempts 
                WHERE (username = :username OR ip_address = :ip_address)
                AND success = 0 
                AND attempt_time > DATE_SUB(NOW(), INTERVAL :time_window SECOND)";
        
        $query = $dbh->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
        $query->bindParam(':time_window', $time_window, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($result['failed_count'] >= $max_attempts) {
            $last_attempt = strtotime($result['last_attempt']);
            $time_passed = time() - $last_attempt;
            
            if ($time_passed < $lockout_time) {
                $wait_time = ceil(($lockout_time - $time_passed) / 60);
                return [
                    'allowed' => false,
                    'message' => "Too many failed login attempts. Please try again in {$wait_time} minute(s).",
                    'wait_time' => $wait_time
                ];
            }
        }
        
        return [
            'allowed' => true,
            'message' => '',
            'wait_time' => 0
        ];
        
    } catch (PDOException $e) {
        // On error, allow login (fail open for availability)
        error_log("Rate limit check error: " . $e->getMessage());
        return [
            'allowed' => true,
            'message' => '',
            'wait_time' => 0
        ];
    }
}

/**
 * Log Login Attempt
 * Records login attempt for rate limiting
 * 
 * @param PDO $dbh Database connection
 * @param string $username Username attempting login
 * @param string $ip_address IP address of request
 * @param bool $success Whether login was successful
 */
function log_login_attempt($dbh, $username, $ip_address, $success = false)
{
    try {
        $sql = "INSERT INTO login_attempts (username, ip_address, success) 
                VALUES (:username, :ip_address, :success)";
        
        $query = $dbh->prepare($sql);
        $query->bindParam(':username', $username, PDO::PARAM_STR);
        $query->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
        $success_int = $success ? 1 : 0;
        $query->bindParam(':success', $success_int, PDO::PARAM_INT);
        $query->execute();
        
        // Clean up old attempts (older than 24 hours)
        $cleanup_sql = "DELETE FROM login_attempts 
                       WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $dbh->exec($cleanup_sql);
        
    } catch (PDOException $e) {
        error_log("Login attempt logging error: " . $e->getMessage());
    }
}

/**
 * Validate Input Length
 * Ensures input doesn't exceed maximum length
 * 
 * @param string $input Input to validate
 * @param int $max_length Maximum allowed length
 * @param string $field_name Field name for error message
 * @return array ['valid' => bool, 'message' => string]
 */
function validate_input_length($input, $max_length, $field_name = 'Field')
{
    $length = mb_strlen($input, 'UTF-8');
    
    if ($length > $max_length) {
        return [
            'valid' => false,
            'message' => "{$field_name} must not exceed {$max_length} characters (current: {$length})"
        ];
    }
    
    return [
        'valid' => true,
        'message' => ''
    ];
}

/**
 * Sanitize filename for uploads
 * 
 * @param string $filename Original filename
 * @return string Sanitized filename
 */
function sanitize_filename($filename)
{
    // Remove path information
    $filename = basename($filename);
    
    // Remove special characters, keep only alphanumeric, dash, underscore, and dot
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    
    // Prevent double extensions
    $filename = preg_replace('/\.+/', '.', $filename);
    
    return $filename;
}

/**
 * Validate file upload
 * 
 * @param array $file $_FILES array element
 * @param array $allowed_types Allowed MIME types
 * @param int $max_size Maximum file size in bytes
 * @return array ['valid' => bool, 'message' => string]
 */
function validate_file_upload($file, $allowed_types = [], $max_size = 5242880) // 5MB default
{
    // Check if file was uploaded
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['valid' => false, 'message' => 'Invalid file upload'];
    }
    
    // Check for upload errors
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['valid' => false, 'message' => 'File size exceeds limit'];
        case UPLOAD_ERR_NO_FILE:
            return ['valid' => false, 'message' => 'No file uploaded'];
        default:
            return ['valid' => false, 'message' => 'Upload error occurred'];
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        $max_mb = round($max_size / 1048576, 2);
        return ['valid' => false, 'message' => "File size must not exceed {$max_mb}MB"];
    }
    
    // Check MIME type if specified
    if (!empty($allowed_types)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime_type, $allowed_types)) {
            return ['valid' => false, 'message' => 'Invalid file type'];
        }
    }
    
    return ['valid' => true, 'message' => ''];
}
?>
