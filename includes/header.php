<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('includes/db.php');
include('includes/functions.php');

// Check if the user is logged in, else redirect to login page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Run WAF check on all incoming requests
$request = $_SERVER['QUERY_STRING'] . file_get_contents("php://input");
checkForMaliciousActivity($conn, $request);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WAF Management Tool</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header Section -->
    <header class="main-header">
        <div class="logo">
            <a href="index.php">WAF Management</a>
        </div>

        <!-- Navigation Menu -->
        <nav class="main-nav">
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="logs.php">Logs</a></li>
                <li><a href="rules.php">Rules</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="audit.php">Audit</a></li>
                <li><a href="export_logs.php">Export Logs</a></li>
                <li><a href="waf_management.php">WAF Management Settings</a></li> <!-- New Menu Item -->
            </ul>
        </nav>

        <!-- Profile Section -->
        <div class="profile">
            <span class="profile-name">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
            <div class="profile-menu">
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content Wrapper -->
    <div class="content-wrapper">
