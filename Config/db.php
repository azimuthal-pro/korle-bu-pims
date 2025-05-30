<?php
/**
 * Database Configuration File
 * Korle-Bu Patient Information Management System
 * Local Development Environment
 */

// Prevent direct access
if (!defined('INCLUDED')) {
    define('INCLUDED', true);
}

// Database Configuration Constants
define('DB_HOST', 'localhost');           // MySQL server host
define('DB_USERNAME', 'root');            // MySQL username (default for XAMPP/WAMP)
define('DB_PASSWORD', '');                // MySQL password (usually empty for local)
define('DB_NAME', 'korle_bu_pims');      // Database name
define('DB_CHARSET', 'utf8mb4');         // Character set

/**
 * Database Connection Class
 */
class Database {
    private $host = DB_HOST;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $database = DB_NAME;
    private $charset = DB_CHARSET;
    
    private $connection = null;
    private static $instance = null;
    
    /**
     * Singleton pattern - only one database connection
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->connect();
    }
    
    /**
     * Create database connection using PDO
     */
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
            // Set timezone to Ghana time
            $this->connection->exec("SET time_zone = '+00:00'");
            
        } catch (PDOException $e) {
            // Log error and show user-friendly message
            error_log("Database Connection Error: " . $e->getMessage());
            die("Database connection failed. Please check your configuration.");
        }
    }
    
    /**
     * Get the database connection
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Execute a query with parameters
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Query Error: " . $e->getMessage());
            throw new Exception("Database query failed");
        }
    }
    
    /**
     * Fetch single row
     */
    public function fetchRow($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Fetch multiple rows
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Insert data and return last insert ID
     */
    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->connection->lastInsertId();
    }
    
    /**
     * Update/Delete and return affected rows count
     */
    public function execute($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->connection->rollback();
    }
    
    /**
     * Check if connection is active
     */
    public function isConnected() {
        return $this->connection !== null;
    }
    
    /**
     * Close connection
     */
    public function close() {
        $this->connection = null;
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization
     */
    public function __wakeup() {}
}

/**
 * Quick access function to get database instance
 */
function getDB() {
    return Database::getInstance();
}

/**
 * Test database connection
 */
function testDatabaseConnection() {
    try {
        $db = Database::getInstance();
        
        if ($db->isConnected()) {
            // Test with a simple query
            $result = $db->fetchRow("SELECT 1 as test");
            
            if ($result && $result['test'] == 1) {
                return [
                    'status' => 'success',
                    'message' => 'Database connection successful!'
                ];
            }
        }
        
        return [
            'status' => 'error',
            'message' => 'Database connection test failed'
        ];
        
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Connection error: ' . $e->getMessage()
        ];
    }
}

// Auto-include this file when needed
if (!function_exists('autoloadDatabase')) {
    function autoloadDatabase() {
        static $loaded = false;
        if (!$loaded) {
            require_once __DIR__ . '/database.php';
            $loaded = true;
        }
    }
}
?>