<?php

// Start a session to track the audit checklist
session_start();

// Initialize audit checklist if not set
if (!isset($_SESSION['audit_checklist'])) {
    $_SESSION['audit_checklist'] = [
        'requests_inspected' => 0,
        'malicious_requests' => 0,
        'approved_requests' => 0
    ];
}

// Define attack patterns for SQLi, XSS, CSRF, etc.
$attack_patterns = [
    'SQL_INJECTION' => '/(union.*select.*|select.*from.*|insert.*into.*|drop.*table.*)/i',
    'XSS' => '/(<.*script.*>|javascript:.*)/i',
    'CSRF' => '/(csrf_token|_csrf)/i',
    'RFI_LFI' => '/(file:\/\/|php:\/\/|data:\/\/)/i',
    'HTTP_RESPONSE_SPLIT' => '/(\%0d\%0a|\r\n)/i'
];

// Log malicious requests
function log_request($ip, $endpoint, $method, $payload, $attack_type) {
    $log_entry = date('Y-m-d H:i:s') . " - Attack Detected: $attack_type | IP: $ip | Endpoint: $endpoint | Method: $method | Payload: " . json_encode($payload) . "\n";
    file_put_contents('waf_audit.log', $log_entry, FILE_APPEND);

    $_SESSION['audit_checklist']['malicious_requests'] += 1;
}

// Audit log for green check - approved requests
function log_audit($ip, $endpoint, $method, $status) {
    $log_entry = date('Y-m-d H:i:s') . " - Audit Passed: IP: $ip | Endpoint: $endpoint | Method: $method | Status: $status\n";
    file_put_contents('waf_audit.log', $log_entry, FILE_APPEND);

    $_SESSION['audit_checklist']['approved_requests'] += 1;
}

// Function to inspect requests for attack patterns
function waf_rules($payload) {
    global $attack_patterns;
    foreach ($attack_patterns as $attack_type => $pattern) {
        if (preg_match($pattern, json_encode($payload))) {
            return $attack_type;
        }
    }
    return null;
}

// Function to inspect incoming requests
function inspect_request() {
    $_SESSION['audit_checklist']['requests_inspected'] += 1;

    // Combine GET, POST, and headers data for inspection
    $payload = array_merge($_GET, $_POST, getallheaders());

    // Check for malicious patterns
    $attack_type = waf_rules($payload);

    if ($attack_type) {
        log_request($_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $payload, $attack_type);
        header('HTTP/1.1 403 Forbidden');
        echo json_encode(["message" => "Request blocked by WAF", "attack_type" => $attack_type]);
        exit;
    } else {
        log_audit($_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], "Approved");
    }
}

// Call the inspect request function for every request
inspect_request();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secured PHP Application</title>
</head>
<body>
    <h1>Welcome to the Secured PHP Application!</h1>
</body>
</html>

<?php
// Endpoint to view the audit checklist
if ($_SERVER['REQUEST_URI'] == '/audit') {
    header('Content-Type: application/json');
    echo json_encode($_SESSION['audit_checklist']);
    exit;
}

// Endpoint to download logs
if ($_SERVER['REQUEST_URI'] == '/audit/logs') {
    header('Content-Type: application/json');
    $logs = file_get_contents('waf_audit.log');
    echo json_encode(['log_contents' => $logs]);
    exit;
}

// Endpoint to export audit as JSON
if ($_SERVER['REQUEST_URI'] == '/audit/export') {
    header('Content-Type: application/json');
    echo json_encode($_SESSION['audit_checklist']);
    exit;
}
?>
