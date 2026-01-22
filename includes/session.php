<?php
/**
 * Secure Session Handler
 * Implements secure session management with timeout and fingerprinting
 */

class SecureSession {
    
    /**
     * Start secure session
     */
    public static function start() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        // Configure session security
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 1 : 0);
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);

        session_start();

        // Check session timeout
        self::checkTimeout();

        // Validate session fingerprint
        self::validateFingerprint();
    }

    /**
     * Check if session has timed out
     */
    private static function checkTimeout() {
        $timeout = Config::getSessionTimeout();

        if (isset($_SESSION['LAST_ACTIVITY'])) {
            if (time() - $_SESSION['LAST_ACTIVITY'] > $timeout) {
                // Session timed out
                self::destroy();
                return;
            }
        }

        $_SESSION['LAST_ACTIVITY'] = time();
    }

    /**
     * Validate session fingerprint to prevent session hijacking
     */
    private static function validateFingerprint() {
        $currentFingerprint = self::generateFingerprint();

        if (isset($_SESSION['FINGERPRINT'])) {
            if ($_SESSION['FINGERPRINT'] !== $currentFingerprint) {
                // Fingerprint mismatch - possible session hijacking
                Security::logSecurityEvent('Session fingerprint mismatch', [
                    'expected' => $_SESSION['FINGERPRINT'],
                    'actual' => $currentFingerprint
                ]);
                
                self::destroy();
                return;
            }
        } else {
            $_SESSION['FINGERPRINT'] = $currentFingerprint;
        }
    }

    /**
     * Generate session fingerprint based on user agent and IP
     * @return string Fingerprint hash
     */
    private static function generateFingerprint() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        
        // Use first 3 octets of IP for some flexibility with DHCP
        $ipParts = explode('.', $ip);
        $ipPrefix = implode('.', array_slice($ipParts, 0, 3));

        return hash('sha256', $userAgent . $ipPrefix);
    }

    /**
     * Regenerate session ID (call after privilege changes like login)
     */
    public static function regenerate() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
            $_SESSION['FINGERPRINT'] = self::generateFingerprint();
        }
    }

    /**
     * Destroy session completely
     */
    public static function destroy() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            
            // Delete session cookie
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 42000, '/');
            }

            session_destroy();
        }
    }

    /**
     * Check if user is authenticated
     * @return bool True if authenticated
     */
    public static function isAuthenticated() {
        self::start();
        return isset($_SESSION['access']) && $_SESSION['access'] === true;
    }

    /**
     * Check if user has specific role
     * @param string $role Role to check
     * @return bool True if user has role
     */
    public static function hasRole($role) {
        self::start();
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }

    /**
     * Require authentication (redirect if not authenticated)
     * @param string $redirectTo URL to redirect to if not authenticated
     */
    public static function requireAuth($redirectTo = 'loginform.php') {
        if (!self::isAuthenticated()) {
            header('Location: ' . $redirectTo);
            exit;
        }
    }

    /**
     * Require specific role (redirect if not authorized)
     * @param string $role Required role
     * @param string $redirectTo URL to redirect to if not authorized
     */
    public static function requireRole($role, $redirectTo = 'index.php') {
        self::requireAuth();
        
        if (!self::hasRole($role)) {
            Security::logSecurityEvent('Unauthorized access attempt', [
                'required_role' => $role,
                'user_role' => $_SESSION['role'] ?? 'none'
            ]);

            header('Location: ' . $redirectTo);
            exit;
        }
    }
}

// Load dependencies
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/security.php';
