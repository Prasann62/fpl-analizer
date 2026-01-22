<?php
/**
 * Configuration Loader
 * Loads environment variables from .env file
 * Provides centralized access to configuration values
 */

class Config {
    private static $config = [];
    private static $loaded = false;

    /**
     * Load environment variables from .env file
     */
    public static function load() {
        if (self::$loaded) {
            return;
        }

        $envFile = __DIR__ . '/.env';
        
        if (!file_exists($envFile)) {
            // Fallback to example file in development
            $envFile = __DIR__ . '/.env.example';
            if (!file_exists($envFile)) {
                throw new Exception('Configuration file not found. Please create config/.env');
            }
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Store in config array
                self::$config[$key] = $value;
                
                // Also set as environment variable
                putenv("$key=$value");
            }
        }

        self::$loaded = true;
    }

    /**
     * Get configuration value
     * @param string $key Configuration key
     * @param mixed $default Default value if key not found
     * @return mixed Configuration value
     */
    public static function get($key, $default = null) {
        if (!self::$loaded) {
            self::load();
        }

        return isset(self::$config[$key]) ? self::$config[$key] : $default;
    }

    /**
     * Get database configuration
     * @return array Database configuration
     */
    public static function getDatabase() {
        return [
            'host' => self::get('DB_HOST', 'localhost'),
            'username' => self::get('DB_USERNAME'),
            'password' => self::get('DB_PASSWORD'),
            'database' => self::get('DB_NAME')
        ];
    }

    /**
     * Check if debug mode is enabled
     * @return bool
     */
    public static function isDebug() {
        return self::get('APP_DEBUG', 'false') === 'true';
    }

    /**
     * Get session timeout in seconds
     * @return int
     */
    public static function getSessionTimeout() {
        return (int) self::get('SESSION_TIMEOUT', 1800);
    }
}

// Auto-load configuration
Config::load();
