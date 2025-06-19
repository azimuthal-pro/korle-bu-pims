<?php
session_start();
require_once '../includes/session.php'; // checks if admin is logged in
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Korle-Bu PIMS</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

    <!-- Top Navigation Bar -->
    <header class="topbar">
        <div class="logo">Korle-Bu PIMS</div>
        <div class="admin-info">
            Logged in as: <strong><?= $_SESSION['admin_name'] ?? 'Admin' ?></strong>
            | <a href="../logout.php" class="logout">Logout</a>
        </div>
    </header>

    <!-- Sidebar + Main Content Wrapper -->
    <div class="wrapper">
        <!-- Sidebar Navigation -->
        <nav class="sidebar">
            <ul>
                <li><a href="?page=home">🏠 Home</a></li>
                <li><a href="?page=patient">👤 Patient</a></li>
                <li><a href="?page=history">📋 Medical History</a></li>
                <li><a href="?page=reports">📊 Reports</a></li>
                <li><a href="../logout.php">🚪 Logout</a></li>
            </ul>
        </nav>

        <!-- Dynamic Page Content -->
        <main class="main-content">
            <?php
            $page = $_GET['page'] ?? 'home';
            $pageFile = "../modules/{$page}.php";
            if (file_exists($pageFile)) {
                include $pageFile;
            } else {
                echo "<h2>Welcome to the Admin Dashboard</h2><p>Select a module from the sidebar.</p>";
            }
            ?>
        </main>
    </div>

</body>
</html>
