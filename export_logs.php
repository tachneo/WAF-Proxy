<?php
include('includes/header.php');
include('includes/db.php');
include('includes/functions.php');

// Fetch logs to export
$logs = getWAFLogs($conn);

// Handle export in different formats
if (isset($_GET['format'])) {
    $format = $_GET['format'];

    if ($format === 'json') {
        // Export logs as JSON
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="waf_logs.json"');
        echo json_encode($logs, JSON_PRETTY_PRINT);
        exit;
    } elseif ($format === 'csv') {
        // Export logs as CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="waf_logs.csv"');
        
        // Create CSV file
        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'IP Address', 'Endpoint', 'Method', 'Payload', 'Attack Type', 'Blocked', 'Timestamp'));

        foreach ($logs as $log) {
            fputcsv($output, $log);
        }
        
        fclose($output);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export WAF Logs</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Export WAF Logs</h1>

        <!-- Export Buttons -->
        <div class="export-actions">
            <h3>Download Logs:</h3>
            <a href="export_logs.php?format=json" class="action-btn">Download JSON</a>
            <a href="export_logs.php?format=csv" class="action-btn">Download CSV</a>
        </div>
    </div>

    <script src="assets/js/scripts.js"></script>
</body>
</html>
