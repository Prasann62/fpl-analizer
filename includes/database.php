<?php
/**
 * Secure Database Connection Wrapper
 * Provides centralized database access with prepared statements
 */

class Database {
    private static $instance = null;
    private $connection = null;

    /**
     * Private constructor (Singleton pattern)
     */
    private function __construct() {
        $dbConfig = Config::getDatabase();
        
        $this->connection = new mysqli(
            $dbConfig['host'],
            $dbConfig['username'],
            $dbConfig['password'],
            $dbConfig['database']
        );

        if ($this->connection->connect_error) {
            // Log error without exposing details
            Security::logSecurityEvent('Database connection failed', [
                'error' => $this->connection->connect_error
            ]);

            if (Config::isDebug()) {
                throw new Exception('Database connection failed: ' . $this->connection->connect_error);
            } else {
                throw new Exception('Database connection failed. Please contact support.');
            }
        }

        // Set charset to UTF-8
        $this->connection->set_charset('utf8mb4');
    }

    /**
     * Get database instance (Singleton)
     * @return Database Database instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get mysqli connection
     * @return mysqli Connection object
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Execute prepared statement with parameters
     * @param string $query SQL query with placeholders
     * @param string $types Parameter types (s=string, i=int, d=double, b=blob)
     * @param array $params Parameters array
     * @return mysqli_result|bool Result object or success boolean
     */
    public function execute($query, $types = '', $params = []) {
        $stmt = $this->connection->prepare($query);
        
        if (!$stmt) {
            Security::logSecurityEvent('SQL prepare failed', [
                'query' => $query,
                'error' => $this->connection->error
            ]);

            if (Config::isDebug()) {
                throw new Exception('SQL prepare failed: ' . $this->connection->error);
            } else {
                throw new Exception('Database query failed');
            }
        }

        // Bind parameters if provided
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        // Execute statement
        if (!$stmt->execute()) {
            Security::logSecurityEvent('SQL execute failed', [
                'query' => $query,
                'error' => $stmt->error
            ]);

            $stmt->close();

            if (Config::isDebug()) {
                throw new Exception('SQL execute failed: ' . $stmt->error);
            } else {
                throw new Exception('Database operation failed');
            }
        }

        // Get result for SELECT queries
        $result = $stmt->get_result();
        $stmt->close();

        return $result !== false ? $result : true;
    }

    /**
     * Execute SELECT query and return all rows
     * @param string $query SQL query
     * @param string $types Parameter types
     * @param array $params Parameters
     * @return array Rows array
     */
    public function selectAll($query, $types = '', $params = []) {
        $result = $this->execute($query, $types, $params);
        
        if ($result === true || $result === false) {
            return [];
        }

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Execute SELECT query and return single row
     * @param string $query SQL query
     * @param string $types Parameter types
     * @param array $params Parameters
     * @return array|null Row array or null
     */
    public function selectOne($query, $types = '', $params = []) {
        $result = $this->execute($query, $types, $params);
        
        if ($result === true || $result === false) {
            return null;
        }

        return $result->fetch_assoc();
    }

    /**
     * Get last inserted ID
     * @return int Last insert ID
     */
    public function getLastInsertId() {
        return $this->connection->insert_id;
    }

    /**
     * Get number of affected rows
     * @return int Affected rows
     */
    public function getAffectedRows() {
        return $this->connection->affected_rows;
    }

    /**
     * Escape string (additional safety layer)
     * @param string $value Value to escape
     * @return string Escaped value
     */
    public function escape($value) {
        return $this->connection->real_escape_string($value);
    }

    /**
     * Close connection
     */
    public function close() {
        if ($this->connection) {
            $this->connection->close();
            $this->connection = null;
        }
    }

    /**
     * Prevent cloning (Singleton)
     */
    private function __clone() {}

    /**
     * Prevent unserialization (Singleton)
     */
    public function __wakeup() {
        throw new Exception('Cannot unserialize singleton');
    }
}

// Load dependencies
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/security.php';
