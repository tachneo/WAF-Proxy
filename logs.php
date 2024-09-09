<?php
include('includes/header.php');
include('includes/db.php');
include('includes/functions.php');

// Fetch logs from the database
$logs = getWAFLogs($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View WAF Logs</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>WAF Logs</h1>

        <!-- Logs Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>IP Address</th>
                    <th>Endpoint</th>
                    <th>Method</th>
                    <th>Payload</th>
                    <th>Attack Type</th>
                    <th>Blocked</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log) { ?>
                    <tr>
                        <td><?php echo $log['id']; ?></td>
                        <td><?php echo $log['ip_address']; ?></td>
                        <td><?php echo $log['endpoint']; ?></td>
                        <td><?php echo $log['method']; ?></td>
                        <td><?php echo $log['payload']; ?></td>
                        <td><?php echo $log['attack_type']; ?></td>
                        <td><?php echo $log['is_blocked'] ? 'Yes' : 'No'; ?></td>
                        <td><?php echo $log['timestamp']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="assets/js/scripts.js"></script>
</body>
</html>
