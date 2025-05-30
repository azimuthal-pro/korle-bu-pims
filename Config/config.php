<?php
/**
 * General Configuration File
 * Korle-Bu Patient Information Management System
 * Local Development Environment
 */

// Prevent direct access
if (!defined('INCLUDED')) {
    define('INCLUDED', true);
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Error Reporting (Enable for development, disable for production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Application Configuration
define('APP_NAME', 'Korle-Bu Patient Information Management System');
define('APP_VERSION', '1.0.0');
define('APP_AUTHOR', 'Korle-Bu Manufacturing Unit');

// Server Configuration
define('BASE_URL', 'http://localhost/korle-bu-pims/');
define('ASSETS_URL', BASE_URL . 'assets/');
define('MODULES_URL', BASE_URL . 'modules/');

// File Paths
define('ROOT_PATH', dirname(dirname(__FILE__)) . '/');
define('CONFIG_PATH', ROOT_PATH . 'config/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('MODULES_PATH', ROOT_PATH . 'modules/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');

// Upload Configuration
define('UPLOAD_PATH', ROOT_PATH . 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_DOC_TYPES', ['pdf', 'doc', 'docx', 'txt']);

// Security Configuration
define('ENCRYPTION_KEY', 'korle_bu_pims_2024_secure_key_change_in_production');
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Pagination
define('RECORDS_PER_PAGE', 10);
define('MAX_PAGINATION_LINKS', 5);

// Date and Time Configuration
define('DEFAULT_TIMEZONE', 'Africa/Accra');
define('DATE_FORMAT', 'Y-m-d');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('DISPLAY_DATE_FORMAT', 'd/m/Y');
define('DISPLAY_DATETIME_FORMAT', 'd/m/Y H:i');

// Set default timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// User Roles (Admin Only System)
define('ROLE_ADMIN', 'admin');

// User Role Permissions (All admins have full access)
$userPermissions = [
    ROLE_ADMIN => [
        'manage_users',
        'manage_patients', 
        'manage_appointments',
        'manage_medical_records',
        'manage_prescriptions',
        'view_reports',
        'system_settings',
        'audit_logs',
        'manage_departments',
        'full_access'
    ]
];

// System Messages
define('MSG_SUCCESS', 'success');
define('MSG_ERROR', 'error');
define('MSG_WARNING', 'warning');
define('MSG_INFO', 'info');

// Database table names (for consistency)
define('TBL_USERS', 'users');
define('TBL_PATIENTS', 'patients');
define('TBL_APPOINTMENTS', 'appointments');
define('TBL_MEDICAL_RECORDS', 'medical_records');
define('TBL_DEPARTMENTS', 'departments');
define('TBL_PRESCRIPTIONS', 'prescriptions');
define('TBL_SYSTEM_LOGS', 'system_logs');

/**
 * Autoloader function for including required files
 */
function autoloadFile($filename) {
    $paths = [
        INCLUDES_PATH,
        CONFIG_PATH,
        MODULES_PATH
    ];
    
    foreach ($paths as $path) {
        $fullPath = $path . $filename;
        if (file_exists($fullPath)) {
            require_once $fullPath;
            return true;
        }
    }
    return false;
}

/**
 * Include database configuration
 */
require_once CONFIG_PATH . 'db.php';

/**
 * Get user permissions for a role
 */
function getUserPermissions($role) {
    global $userPermissions;
    return isset($userPermissions[$role]) ? $userPermissions[$role] : [];
}

/**
 * Check if user has permission
 */
function hasPermission($userRole, $permission) {
    $permissions = getUserPermissions($userRole);
    return in_array($permission, $permissions);
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Format date for display
 */
function formatDate($date, $format = DISPLAY_DATE_FORMAT) {
    if (empty($date) || $date === '0000-00-00') {
        return 'N/A';
    }
    return date($format, strtotime($date));
}

/**
 * Format datetime for display
 */
function formatDateTime($datetime, $format = DISPLAY_DATETIME_FORMAT) {
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
        return 'N/A';
    }
    return date($format, strtotime($datetime));
}

/**
 * Calculate age from date of birth
 */
function calculateAge($dateOfBirth) {
    if (empty($dateOfBirth) || $dateOfBirth === '0000-00-00') {
        return 'N/A';
    }
    
    $today = new DateTime();
    $dob = new DateTime($dateOfBirth);
    $age = $today->diff($dob);
    
    return $age->y . ' years';
}

/**
 * Generate unique patient number
 */
function generatePatientNumber() {
    $db = getDB();
    
    // Get the latest patient number
    $sql = "SELECT patient_number FROM " . TBL_PATIENTS . " ORDER BY patient_id DESC LIMIT 1";
    $result = $db->fetchRow($sql);
    
    if ($result) {
        // Extract number and increment
        $lastNumber = intval(substr($result['patient_number'], 3));
        $newNumber = $lastNumber + 1;
    } else {
        $newNumber = 1;
    }
    
    return 'PAT' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
}

/**
 * Log system activity
 */
function logSystemActivity($userId, $action, $tableAffected = null, $recordId = null, $oldValues = null, $newValues = null) {
    try {
        $db = getDB();
        
        $sql = "INSERT INTO " . TBL_SYSTEM_LOGS . " 
                (user_id, action, table_affected, record_id, old_values, new_values, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $userId,
            $action,
            $tableAffected,
            $recordId,
            $oldValues ? json_encode($oldValues) : null,
            $newValues ? json_encode($newValues) : null,
            $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ];
        
        $db->query($sql, $params);
        
    } catch (Exception $e) {
        error_log("Failed to log system activity: " . $e->getMessage());
    }
}

/**
 * Redirect to a page
 */
function redirect($url, $permanent = false) {
    if (!headers_sent()) {
        header('Location: ' . $url, true, $permanent ? 301 : 302);
        exit();
    } else {
        echo "<script>window.location.href = '$url';</script>";
        exit();
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user info
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    if (!isset($_SESSION['user_data'])) {
        // Fetch user data from database
        $db = getDB();
        $sql = "SELECT * FROM " . TBL_USERS . " WHERE user_id = ? AND status = 'active'";
        $user = $db->fetchRow($sql, [$_SESSION['user_id']]);
        
        if ($user) {
            $_SESSION['user_data'] = $user;
        }
    }
    
    return $_SESSION['user_data'] ?? null;
}

/**
 * Require login for protected pages
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect(BASE_URL . 'modules/auth/login.php');
    }
}

/**
 * Check if user has admin access (simplified for admin-only system)
 */
function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['role'] === ROLE_ADMIN;
}

/**
 * Require admin access for all protected pages
 */
function requireAdmin() {
    requireLogin();
    
    if (!isAdmin()) {
        die('Access denied. Admin access required.');
    }
}
?>