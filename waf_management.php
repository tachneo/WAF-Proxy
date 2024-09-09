<?php
include('includes/header.php');
include('includes/db.php');
include('includes/functions.php');

// Fetch current WAF management settings
$settings = getWAFManagementSettings($conn);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ruleSourceUrl = $_POST['rule_source_url'];
    $alertEmail = $_POST['alert_email'];

    // Update settings
    updateWAFManagementSettings($conn, $ruleSourceUrl, $alertEmail);

    // Fetch updated settings after the update
    $settings = getWAFManagementSettings($conn);
    $successMessage = "Settings updated successfully!";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WAF Management Settings</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Manage WAF Settings</h1>

        <?php if (isset($successMessage)): ?>
            <p class="success"><?php echo $successMessage; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="rule_source_url">Rule Source URL</label>
                <input type="text" id="rule_source_url" name="rule_source_url" value="<?php echo htmlspecialchars($settings['rule_source_url']); ?>" required>
            </div>

            <div class="form-group">
                <label for="alert_email">Alert Email</label>
                <input type="email" id="alert_email" name="alert_email" value="<?php echo htmlspecialchars($settings['alert_email']); ?>" required>
            </div>

            <div class="form-group">
                <button type="submit" class="action-btn">Update Settings</button>
            </div>
        </form>

        <!-- Display the current settings below -->
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
                    <td>Rule Source URL</td>
                    <td><?php echo htmlspecialchars($settings['rule_source_url']); ?></td>
                    <td><?php echo date('Y-m-d H:i:s', strtotime($settings['updated_at'])); ?></td>
                </tr>
                <tr>
                    <td>Alert Email</td>
                    <td><?php echo htmlspecialchars($settings['alert_email']); ?></td>
                    <td><?php echo date('Y-m-d H:i:s', strtotime($settings['updated_at'])); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <script src="assets/js/scripts.js"></script>
</body>
</html>
