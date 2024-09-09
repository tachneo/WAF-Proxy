<?php
include('includes/header.php');
include('includes/db.php');
include('includes/functions.php');

// Fetch data for stats
$requestsInspected = getInspectedRequests($conn);
$maliciousRequests = getMaliciousRequests($conn);
$approvedRequests = getApprovedRequests($conn);
$wafStatus = getWAFStatus($conn);

// Calculate threat level based on the percentage of malicious requests
$threatLevel = 'Normal';
if ($maliciousRequests > 0) {
    $maliciousPercentage = ($maliciousRequests / $requestsInspected) * 100;

    if ($maliciousPercentage >= 50) {
        $threatLevel = 'High Alert';
    } elseif ($maliciousPercentage >= 20) {
        $threatLevel = 'Medium';
    } else {
        $threatLevel = 'Normal';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WAF Dashboard</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .threat-normal {
            color: green;
        }
        .threat-medium {
            color: orange;
        }
        .threat-high {
            color: red;
        }
        .status-enabled {
            color: green;
            font-weight: bold;
        }
        .status-disabled {
            color: red;
            font-weight: bold;
        }
        .stat-box {
            padding: 15px;
            background-color: #f4f4f4;
            margin: 10px;
            text-align: center;
            border-radius: 8px;
        }
        .stat-box h3 {
            margin-bottom: 10px;
        }
        .waf-status, .log-actions {
            margin-top: 20px;
        }
        .threat-level {
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-top: 20px;
            font-size: 1.2em;
        }
        .action-btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
        }
        .action-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>WAF Dashboard</h1>

        <!-- Stats Section -->
        <div class="stats">
            <div class="stat-box">
                <h3>Total Requests Inspected</h3>
                <p><?php echo $requestsInspected; ?></p>
            </div>
            <div class="stat-box">
                <h3>Malicious Requests Blocked</h3>
                <p><?php echo $maliciousRequests; ?></p>
            </div>
            <div class="stat-box">
                <h3>Approved (Safe) Requests</h3>
                <p><?php echo $approvedRequests; ?></p>
            </div>
        </div>

        <!-- Threat Level Indicator -->
        <div class="threat-level <?php echo ($threatLevel == 'High Alert') ? 'threat-high' : (($threatLevel == 'Medium') ? 'threat-medium' : 'threat-normal'); ?>">
            <h3>Threat Level: 
                <span class="threat-label">
                    <?php if ($threatLevel == 'High Alert'): ?>
                        <i class="fas fa-exclamation-circle"></i> High Alert
                    <?php elseif ($threatLevel == 'Medium'): ?>
                        <i class="fas fa-exclamation-triangle"></i> Medium
                    <?php else: ?>
                        <i class="fas fa-check-circle"></i> Normal
                    <?php endif; ?>
                </span>
            </h3>
        </div>

        <!-- WAF Status Section -->
        <div class="waf-status">
            <h3>Current WAF Status: 
                <span class="<?php echo $wafStatus ? 'status-enabled' : 'status-disabled'; ?>">
                    <?php echo $wafStatus ? 'Enabled' : 'Disabled'; ?>
                </span>
            </h3>
            <button id="toggleWAF" class="action-btn" onclick="toggleWAFStatus()">Toggle WAF</button>
        </div>

        <!-- Log Actions -->
        <div class="log-actions">
            <h3>Logs & Actions</h3>
            <a href="logs.php" class="action-btn">View Logs</a>
            <a href="rules.php" class="action-btn">Manage WAF Rules</a>
        </div>
    </div>

    <!-- JavaScript Functionality -->
    <script src="assets/js/scripts.js"></script>
    <script>
        // Function to toggle the WAF status (enable/disable)
        function toggleWAFStatus() {
            let confirmToggle = confirm("Are you sure you want to toggle the WAF status?");
            if (confirmToggle) {
                window.location.href = "settings.php?action=toggleWAF";
            }
        }
    </script>

    <?php include('includes/footer.php'); ?>
</body>
</html>
