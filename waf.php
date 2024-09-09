<?php
// Increase memory limit to 512MB
ini_set('memory_limit', '512M');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files
include('includes/header.php');
include('includes/db.php');
include('includes/functions.php');

// Ensure session is started before inspecting requests
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Call the inspectRequest function from functions.php to inspect the incoming request
inspectRequest($conn);
