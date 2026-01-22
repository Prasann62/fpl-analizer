<?php
/**
 * Rate Limiting Library
 * Implements sliding window rate limiting algorithm
 * Storage: File-based (upgradeable to Redis)
 */

class RateLimit {
    private static $storageDir = null;

    /**
     * Initialize rate limiter
     */
    private static function init() {
        if (self::$storageDir === null) {
            self::$storageDir = __DIR__ . '/../cache/ratelimit';
            
            if (!is_dir(self::$storageDir)) {
                mkdir(self::$storageDir, 0755, true);
            }
        }
    }

    /**
     * Get client identifier (IP address + User Agent hash)
     * @return string Client identifier
     */
    private static function getClientId() {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Combine IP with user agent hash for better uniqueness
        return $ip . '_' . substr(md5($userAgent), 0, 8);
    }

    /**
     * Get user-specific identifier if logged in
     * @return string|null User ID or null
     */
    private static function getUserId() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['email']) ? md5($_SESSION['email']) : null;
    }

    /**
     * Check if request is allowed
     * @param string $action Action identifier (e.g., 'login', 'api', 'register')
     * @param int $maxRequests Maximum requests allowed
     * @param int $windowSeconds Time window in seconds
     * @param bool $perUser If true, limit per user instead of per IP
     * @return array ['allowed' => bool, 'remaining' => int, 'reset' => int]
     */
    public static function check($action, $maxRequests = null, $windowSeconds = null, $perUser = false) {
        self::init();

        // Get limits from config if not provided
        if ($maxRequests === null || $windowSeconds === null) {
            list($maxRequests, $windowSeconds) = self::getConfigLimits($action);
        }

        // Determine identifier
        $identifier = $perUser && self::getUserId() ? self::getUserId() : self::getClientId();
        $key = self::getKey($action, $identifier);
        
        // Get current request log
        $requests = self::getRequests($key);
        $now = time();
        $windowStart = $now - $windowSeconds;

        // Remove old requests outside the window
        $requests = array_filter($requests, function($timestamp) use ($windowStart) {
            return $timestamp > $windowStart;
        });

        // Check if limit exceeded
        $currentCount = count($requests);
        $allowed = $currentCount < $maxRequests;

        if ($allowed) {
            // Add current request
            $requests[] = $now;
            self::saveRequests($key, $requests);
        }

        // Calculate remaining and reset time
        $remaining = max(0, $maxRequests - $currentCount - ($allowed ? 1 : 0));
        $reset = !empty($requests) ? min($requests) + $windowSeconds : $now + $windowSeconds;

        return [
            'allowed' => $allowed,
            'remaining' => $remaining,
            'reset' => $reset,
            'retry_after' => $allowed ? 0 : ($reset - $now)
        ];
    }

    /**
     * Enforce rate limit - throws 429 if exceeded
     * @param string $action Action identifier
     * @param int $maxRequests Maximum requests allowed
     * @param int $windowSeconds Time window in seconds
     * @param bool $perUser If true, limit per user
     */
    public static function enforce($action, $maxRequests = null, $windowSeconds = null, $perUser = false) {
        $result = self::check($action, $maxRequests, $windowSeconds, $perUser);

        if (!$result['allowed']) {
            // Log rate limit violation
            Security::logSecurityEvent('Rate limit exceeded', [
                'action' => $action,
                'client' => self::getClientId(),
                'retry_after' => $result['retry_after']
            ]);

            // Send 429 response
            http_response_code(429);
            header('Retry-After: ' . $result['retry_after']);
            header('X-RateLimit-Limit: ' . $maxRequests);
            header('X-RateLimit-Remaining: 0');
            header('X-RateLimit-Reset: ' . $result['reset']);
            header('Content-Type: application/json');
            
            echo json_encode([
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => $result['retry_after']
            ]);
            
            exit;
        }

        // Set rate limit headers
        header('X-RateLimit-Limit: ' . $maxRequests);
        header('X-RateLimit-Remaining: ' . $result['remaining']);
        header('X-RateLimit-Reset: ' . $result['reset']);
    }

    /**
     * Get configured limits for action
     * @param string $action Action identifier
     * @return array [maxRequests, windowSeconds]
     */
    private static function getConfigLimits($action) {
        $limits = [
            'login' => [
                Config::get('RATE_LIMIT_LOGIN_MAX', 5),
                Config::get('RATE_LIMIT_LOGIN_WINDOW', 900)
            ],
            'register' => [
                Config::get('RATE_LIMIT_REGISTER_MAX', 3),
                Config::get('RATE_LIMIT_REGISTER_WINDOW', 3600)
            ],
            'api' => [
                Config::get('RATE_LIMIT_API_MAX', 100),
                Config::get('RATE_LIMIT_API_WINDOW', 60)
            ],
            'general' => [
                Config::get('RATE_LIMIT_GENERAL_MAX', 200),
                Config::get('RATE_LIMIT_GENERAL_WINDOW', 60)
            ]
        ];

        return $limits[$action] ?? $limits['general'];
    }

    /**
     * Generate storage key
     * @param string $action Action identifier
     * @param string $identifier Client/User identifier
     * @return string Storage key
     */
    private static function getKey($action, $identifier) {
        return $action . '_' . $identifier;
    }

    /**
     * Get request log from storage
     * @param string $key Storage key
     * @return array Request timestamps
     */
    private static function getRequests($key) {
        $file = self::$storageDir . '/' . md5($key) . '.json';
        
        if (!file_exists($file)) {
            return [];
        }

        $data = file_get_contents($file);
        $requests = json_decode($data, true);
        
        return is_array($requests) ? $requests : [];
    }

    /**
     * Save request log to storage
     * @param string $key Storage key
     * @param array $requests Request timestamps
     */
    private static function saveRequests($key, $requests) {
        $file = self::$storageDir . '/' . md5($key) . '.json';
        file_put_contents($file, json_encode($requests));
    }

    /**
     * Clear old rate limit files (cleanup task)
     * Should be called periodically via cron
     */
    public static function cleanup() {
        self::init();
        
        $files = glob(self::$storageDir . '/*.json');
        $now = time();
        $maxAge = 86400; // 24 hours

        foreach ($files as $file) {
            if ($now - filemtime($file) > $maxAge) {
                unlink($file);
            }
        }
    }

    /**
     * Reset rate limit for specific identifier
     * @param string $action Action identifier
     * @param string $identifier Optional specific identifier
     */
    public static function reset($action, $identifier = null) {
        self::init();
        
        if ($identifier === null) {
            $identifier = self::getClientId();
        }

        $key = self::getKey($action, $identifier);
        $file = self::$storageDir . '/' . md5($key) . '.json';
        
        if (file_exists($file)) {
            unlink($file);
        }
    }
}

// Load dependencies
require_once __DIR__ . '/security.php';
