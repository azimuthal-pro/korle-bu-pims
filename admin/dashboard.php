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
    <style>
        .dropdown {
            position: relative;
        }

        .dropdown-content {
            display: none;
            flex-direction: column;
            padding-left: 15px;
        }

        .dropdown:hover .dropdown-content {
            display: flex;
        }

        .sidebar ul li a {
            display: block;
            padding: 10px 15px;
            color: #fff;
            text-decoration: none;
        }

        .sidebar ul li a:hover {
            background-color: #24bbee;
        }

        .dropdown-content a {
            font-size: 14px;
            padding: 8px 0;
        }
    </style>
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

            <li class="dropdown">
                <a href="#">👤 Patient</a>
                <div class="dropdown-content">
                    <a href="?page=add_patient">➕ Add Patient</a>
                    <a href="?page=view_patient">📁 View Patients</a>
                </div>
            </li>

            <li class="dropdown">
                <a href="#">📋 Medical History</a>
                <div class="dropdown-content">
                    <a href="?page=view_history">📖 View Medical History</a>
                </div>
            </li>

            <li class="dropdown">
                <a href="#">📊 Reports</a>
                <div class="dropdown-content">
                    <a href="?page=patients_report">👥 Patient Reports</a>
                    <a href="?page=medical_history_report">📚 Medical History Reports</a>
                </div>
            </li>

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
