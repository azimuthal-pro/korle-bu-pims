<?php
/**
 * Database Connection Test Script
 * Korle-Bu Patient Information Management System
 * 
 * Place this file in the root directory (korle-bu-pims/)
 * Access via: http://localhost/korle-bu-pims/test_connection.php
 */

// Include configuration files
require_once 'config.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test - <?php echo APP_NAME; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .test-item {
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #ccc;
        }
        .success {
            border-left-color: #28a745;
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            border-left-color: #dc3545;
            background-color: #f8d7da;
            color: #721c24;
        }
        .warning {
            border-left-color: #ffc107;
            background-color: #fff3cd;
            color: #856404;
        }
        .info {
            border-left-color: #17a2b8;
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status {
            font-weight: bold;
            font-size: 18px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php echo APP_NAME; ?></h1>
            <h2>Database Connection Test</h2>
            <p>Version: <?php echo APP_VERSION; ?></p>
        </div>

        <?php
        // Test 1: PHP Configuration
        echo '<div class="test-item info">';
        echo '<h3>1. PHP Configuration</h3>';
        echo '<table>';
        echo '<tr><th>Setting</th><th>Value</th></tr>';
        echo '<tr><td>PHP Version</td><td>' . PHP_VERSION . '</td></tr>';
        echo '<tr><td>PDO Available</td><td>' . (extension_loaded('pdo') ? 'Yes' : 'No') . '</td></tr>';
        echo '<tr><td>PDO MySQL Available</td><td>' . (extension_loaded('pdo_mysql') ? 'Yes' : 'No') . '</td></tr>';
        echo '<tr><td>Default Timezone</td><td>' . date_default_timezone_get() . '</td></tr>';
        echo '<tr><td>Current Time</td><td>' . date('Y-m-d H:i:s') . '</td></tr>';
        echo '</table>';
        echo '</div>';

        // Test 2: File Structure
        echo '<div class="test-item info">';
        echo '<h3>2. File Structure Check</h3>';
        $requiredDirs = ['Config', 'includes', 'modules', 'assets'];
        $allDirsExist = true;
        
        foreach ($requiredDirs as $dir) {
            $exists = is_dir($dir);
            $allDirsExist = $allDirsExist && $exists;
            echo '<p>' . $dir . '/ directory: ' . ($exists ? '✓ Exists' : '✗ Missing') . '</p>';
        }
        
        if ($allDirsExist) {
            echo '<p class="status" style="color: green;">All required directories found!</p>';
        } else {
            echo '<p class="status" style="color: red;">Some directories are missing!</p>';
        }
        echo '</div>';

        // Test 3: Database Connection
        try {
            $connectionTest = testDatabaseConnection();
            
            if ($connectionTest['status'] === 'success') {
                echo '<div class="test-item success">';
                echo '<h3>3. Database Connection</h3>';
                echo '<p class="status">✓ ' . $connectionTest['message'] . '</p>';
                
                // Get database info
                $db = getDB();
                $info = $db->fetchRow("SELECT VERSION() as version, DATABASE() as database_name");
                
                echo '<table>';
                echo '<tr><th>Database Info</th><th>Value</th></tr>';
                echo '<tr><td>MySQL Version</td><td>' . ($info['version'] ?? 'Unknown') . '</td></tr>';
                echo '<tr><td>Database Name</td><td>' . ($info['database_name'] ?? 'Unknown') . '</td></tr>';
                echo '<tr><td>Host</td><td>' . DB_HOST . '</td></tr>';
                echo '<tr><td>Username</td><td>' . DB_USERNAME . '</td></tr>';
                echo '</table>';
                echo '</div>';
                
                // Test 4: Database Tables
                echo '<div class="test-item info">';
                echo '<h3>4. Database Tables Check</h3>';
                
                $requiredTables = [
                    'users', 'patients', 'appointments', 'medical_records', 
                    'departments', 'prescriptions', 'system_logs'
                ];
                
                $existingTables = [];
                $tablesResult = $db->fetchAll("SHOW TABLES");
                
                foreach ($tablesResult as $table) {
                    $existingTables[] = array_values($table)[0];
                }
                
                $allTablesExist = true;
                foreach ($requiredTables as $table) {
                    $exists = in_array($table, $existingTables);
                    $allTablesExist = $allTablesExist && $exists;
                    echo '<p>' . $table . ' table: ' . ($exists ? '✓ Exists' : '✗ Missing') . '</p>';
                }
                
                if ($allTablesExist) {
                    echo '<p class="status" style="color: green;">All required tables found!</p>';
                } else {
                    echo '<p class="status" style="color: red;">Some tables are missing! Please run the database schema script.</p>';
                }
                echo '</div>';
                
                // Test 5: Sample Data
                if ($allTablesExist) {
                    echo '<div class="test-item info">';
                    echo '<h3>5. Sample Data Check</h3>';
                    
                    $userCount = $db->fetchRow("SELECT COUNT(*) as count FROM users")['count'];
                    $deptCount = $db->fetchRow("SELECT COUNT(*) as count FROM departments")['count'];
                    $patientCount = $db->fetchRow("SELECT COUNT(*) as count FROM patients")['count'];
                    
                    echo '<table>';
                    echo '<tr><th>Table</th><th>Record Count</th></tr>';
                    echo '<tr><td>Users</td><td>' . $userCount . '</td></tr>';
                    echo '<tr><td>Departments</td><td>' . $deptCount . '</td></tr>';
                    echo '<tr><td>Patients</td><td>' . $patientCount . '</td></tr>';
                    echo '</table>';
                    
                    if ($userCount > 0 && $deptCount > 0) {
                        echo '<p class="status" style="color: green;">Sample data is available!</p>';
                        echo '<p><strong>Default Admin Login Credentials:</strong></p>';
                        echo '<ul>';
                        echo '<li>System Admin: username = <code>admin</code>, password = <code>admin123</code></li>';
                        echo '<li>Department Admin: username = <code>admin2</code>, password = <code>admin123</code></li>';
                        echo '<li>Unit Manager: username = <code>manager</code>, password = <code>admin123</code></li>';
                        echo '</ul>';
                        echo '<p><em>Note: This is an admin-only system. All users have full administrative access.</em></p>';
                    } else {
                        echo '<p class="status" style="color: orange;">No sample data found. Please run the database schema script with sample data.</p>';
                    }
                    echo '</div>';
                }
                
            } else {
                echo '<div class="test-item error">';
                echo '<h3>3. Database Connection</h3>';
                echo '<p class="status">✗ ' . $connectionTest['message'] . '</p>';
                echo '<p><strong>Troubleshooting:</strong></p>';
                echo '<ul>';
                echo '<li>Make sure XAMPP/WAMP is running</li>';
                echo '<li>Check if MySQL service is started</li>';
                echo '<li>Verify database credentials in config/database.php</li>';
                echo '<li>Ensure the database "korle_bu_pims" exists</li>';
                echo '</ul>';
                echo '</div>';
            }
            
        } catch (Exception $e) {
            echo '<div class="test-item error">';
            echo '<h3>3. Database Connection</h3>';
            echo '<p class="status">✗ Connection Failed</p>';
            echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }
        ?>

        <div class="test-item info">
            <h3>Next Steps</h3>
            <p>If all tests pass, you're ready to proceed with building the authentication system!</p>
            <p>If there are any errors, please fix them before continuing.</p>
            
            <a href="<?php echo BASE_URL; ?>" class="btn">Go to Application</a>
            <a href="<?php echo BASE_URL; ?>modules/auth/login.php" class="btn">Go to Login</a>
        </div>
    </div>
</body>
</html>