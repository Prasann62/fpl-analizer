<?php
/**
 * Security Library
 * Core security functions following OWASP best practices
 */

class Security {
    
    /**
     * Sanitize user input based on type
     * @param mixed $input Raw input value
     * @param string $type Type of sanitization (email, string, int, float, url)
     * @param int $maxLength Maximum length (optional)
     * @return mixed Sanitized value
     */
    public static function sanitizeInput($input, $type = 'string', $maxLength = null) {
        // Trim whitespace
        if (is_string($input)) {
            $input = trim($input);
        }

        // Apply length limit
        if ($maxLength !== null && is_string($input)) {
            $input = substr($input, 0, $maxLength);
        }

        switch ($type) {
            case 'email':
                return filter_var($input, FILTER_SANITIZE_EMAIL);
            
            case 'int':
                return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            
            case 'float':
                return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            
            case 'url':
                return filter_var($input, FILTER_SANITIZE_URL);
            
            case 'string':
            default:
                // Remove HTML tags and encode special characters
                return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
        }
    }

    /**
     * Validate email address (RFC compliant)
     * @param string $email Email address
     * @return bool True if valid
     */
    public static function validateEmail($email) {
        $email = self::sanitizeInput($email, 'email', 255);
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate password strength
     * Requirements: Minimum 8 characters, at least one uppercase, one lowercase, one number
     * @param string $password Password to validate
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function validatePassword($password) {
        $errors = [];
        $minLength = (int) Config::get('PASSWORD_MIN_LENGTH', 8);

        if (strlen($password) < $minLength) {
            $errors[] = "Password must be at least {$minLength} characters long";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Generate CSRF token
     * @return string CSRF token
     */
    public static function generateCSRFToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $tokenName = Config::get('CSRF_TOKEN_NAME', 'csrf_token');
        $token = bin2hex(random_bytes(32));
        $_SESSION[$tokenName] = $token;
        
        return $token;
    }

    /**
     * Verify CSRF token
     * @param string $token Token to verify
     * @return bool True if valid
     */
    public static function verifyCSRFToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $tokenName = Config::get('CSRF_TOKEN_NAME', 'csrf_token');
        
        if (!isset($_SESSION[$tokenName])) {
            return false;
        }

        // Constant-time comparison to prevent timing attacks
        return hash_equals($_SESSION[$tokenName], $token);
    }

    /**
     * Hash password using bcrypt
     * @param string $password Plain text password
     * @return string Hashed password
     */
    public static function hashPassword($password) {
        // Use bcrypt with cost factor of 12
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Verify password against hash
     * @param string $password Plain text password
     * @param string $hash Hashed password
     * @return bool True if password matches
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Check if password needs rehashing (algorithm updated)
     * @param string $hash Current password hash
     * @return bool True if needs rehashing
     */
    public static function needsRehash($hash) {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Generate secure random string
     * @param int $length Length of string
     * @return string Random string
     */
    public static function generateRandomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Validate string length
     * @param string $input Input string
     * @param int $min Minimum length
     * @param int $max Maximum length
     * @return bool True if valid
     */
    public static function validateLength($input, $min, $max) {
        $length = strlen($input);
        return $length >= $min && $length <= $max;
    }

    /**
     * Sanitize filename for safe file operations
     * @param string $filename Original filename
     * @return string Safe filename
     */
    public static function sanitizeFilename($filename) {
        // Remove any path components
        $filename = basename($filename);
        
        // Remove any non-alphanumeric characters except dots, hyphens, and underscores
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        
        return $filename;
    }

    /**
     * Prevent XSS by escaping output
     * @param string $output Output to escape
     * @return string Escaped output
     */
    public static function escapeOutput($output) {
        return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate Manager ID (numeric, 1-10 digits)
     * @param mixed $managerId Manager ID
     * @return bool True if valid
     */
    public static function validateManagerId($managerId) {
        return is_numeric($managerId) && preg_match('/^\d{1,10}$/', $managerId);
    }

    /**
     * Validate integer within range
     * @param mixed $value Value to validate
     * @param int $min Minimum value
     * @param int $max Maximum value
     * @return bool True if valid
     */
    public static function validateIntRange($value, $min, $max) {
        if (!is_numeric($value)) {
            return false;
        }
        $intVal = (int) $value;
        return $intVal >= $min && $intVal <= $max;
    }

    /**
     * Log security event
     * @param string $event Event description
     * @param array $context Additional context
     */
    public static function logSecurityEvent($event, $context = []) {
        $logFile = __DIR__ . '/../logs/security.log';
        $logDir = dirname($logFile);
        
        // Create logs directory if it doesn't exist
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $logEntry = sprintf(
            "[%s] %s | IP: %s | User-Agent: %s | Context: %s\n",
            $timestamp,
            $event,
            $ip,
            $userAgent,
            json_encode($context)
        );

        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}

// Load configuration
require_once __DIR__ . '/../config/config.php';
