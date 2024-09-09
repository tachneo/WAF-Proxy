<?php
include('includes/header.php');
include('includes/db.php');
include('includes/functions.php');

// Fetch current WAF settings
$wafStatus = getWAFStatus($conn);
$rateLimit = getRateLimit($conn);
$emailNotifications = getEmailNotificationStatus($conn);
$logLevel = getLogLevel($conn);

// Handle settings updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newStatus = isset($_POST['waf_status']) ? 1 : 0;
    $newRateLimit = $_POST['rate_limit'];
    $newEmailNotifications = isset($_POST['email_notifications']) ? 1 : 0;
    $newLogLevel = $_POST['log_level'];

    // Update WAF settings in the database
    updateWAFSettings($conn, $newStatus, $newRateLimit, $newEmailNotifications, $newLogLevel);

    // Reload the settings after update to reflect the changes
    $wafStatus = getWAFStatus($conn);
    $rateLimit = getRateLimit($conn);
    $emailNotifications = getEmailNotificationStatus($conn);
    $logLevel = getLogLevel($conn);

    $successMessage = "Settings updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WAF Settings</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>WAF Settings</h1>

        <!-- Display success message if settings updated -->
        <?php if (isset($successMessage)): ?>
            <p class="success-message"><?php echo $successMessage; ?></p>
        <?php endif; ?>

        <!-- Settings Form -->
        <form method="POST" action="settings.php">

            <!-- WAF Status Toggle -->
            <div class="form-group">
                <label for="waf_status">WAF Status:</label>
                <input type="checkbox" name="waf_status" id="waf_status" <?php echo $wafStatus ? 'checked' : ''; ?>>
                <span><?php echo $wafStatus ? 'Enabled' : 'Disabled'; ?></span>
            </div>

            <!-- Rate Limit Setting -->
            <div class="form-group">
                <label for="rate_limit">Rate Limit (requests per minute):</label>
                <input type="number" name="rate_limit" id="rate_limit" value="<?php echo $rateLimit; ?>" min="1" required>
            </div>

            <!-- Email Notification Setting -->
            <div class="form-group">
                <label for="email_notifications">Email Notifications for Attacks:</label>
                <input type="checkbox" name="email_notifications" id="email_notifications" <?php echo $emailNotifications ? 'checked' : ''; ?>>
                <span><?php echo $emailNotifications ? 'Enabled' : 'Disabled'; ?></span>
            </div>

            <!-- Log Level Setting -->
            <div class="form-group">
                <label for="log_level">Log Level:</label>
                <select name="log_level" id="log_level" required>
                    <option value="all" <?php echo $logLevel == 'all' ? 'selected' : ''; ?>>Log Everything</option>
                    <option value="malicious" <?php echo $logLevel == 'malicious' ? 'selected' : ''; ?>>Log Only Malicious Activity</option>
                    <option value="none" <?php echo $logLevel == 'none' ? 'selected' : ''; ?>>Disable Logging</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="form-group">
                <button type="submit" class="action-btn">Update Settings</button>
            </div>

        </form>

        <!-- Display the current settings below after form submission -->
        <h2>Current WAF Settings</h2>
        <table class="settings-table">
            <thead>
                <tr>
                    <th>Setting</th>
                    <th>Value</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>WAF Status</td>
                    <td><?php echo $wafStatus ? 'Enabled' : 'Disabled'; ?></td>
                    <td><?php echo date('Y-m-d H:i:s'); ?></td>
                </tr>
                <tr>
                    <td>Rate Limit</td>
                    <td><?php echo $rateLimit; ?></td>
                    <td><?php echo date('Y-m-d H:i:s'); ?></td>
                </tr>
                <tr>
                    <td>Email Notifications</td>
                    <td><?php echo $emailNotifications ? 'Enabled' : 'Disabled'; ?></td>
                    <td><?php echo date('Y-m-d H:i:s'); ?></td>
                </tr>
                <tr>
                    <td>Log Level</td>
                    <td><?php echo $logLevel; ?></td>
                    <td><?php echo date('Y-m-d H:i:s'); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- JavaScript Functionality -->
    <script src="assets/js/scripts.js"></script>
</body>
</html>
