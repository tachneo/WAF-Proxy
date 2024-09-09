<?php
include('includes/header.php');
include('includes/db.php');
include('includes/functions.php');

// Fetch audit data from the database
$auditData = getAuditData($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WAF Audit Reports</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Audit Reports</h1>

        <!-- Audit Summary -->
        <div class="audit-summary">
            <div class="audit-box">
                <h3>Requests Inspected</h3>
                <p><?php echo $auditData['requests_inspected']; ?></p>
            </div>
            <div class="audit-box">
                <h3>Malicious Requests Detected</h3>
                <p><?php echo $auditData['malicious_requests']; ?></p>
            </div>
            <div class="audit-box">
                <h3>Approved (Safe) Requests</h3>
                <p><?php echo $auditData['approved_requests']; ?></p>
            </div>
        </div>

        <!-- Logs Link -->
        <div class="log-actions">
            <h3>More Details:</h3>
            <a href="logs.php" class="action-btn">View Logs</a>
        </div>
    </div>

    <script src="assets/js/scripts.js"></script>
</body>
</html>
