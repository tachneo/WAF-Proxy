<?php

// Check if a session is already active before starting one
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize audit checklist if not set
if (!isset($_SESSION['audit_checklist'])) {
    $_SESSION['audit_checklist'] = [
        'requests_inspected' => 0,
        'malicious_requests' => 0,
        'approved_requests' => 0
    ];
}

// Define attack patterns for SQLi, XSS, CSRF, RFI, etc.
$attack_patterns = [
    'SQL_INJECTION' => '/(union.*select.*|select.*from.*|insert.*into.*|drop.*table.*)/i',
    'XSS' => '/(<.*script.*>|javascript:.*|onload=.*|onerror=.*)/i',
    'CSRF' => '/(csrf_token|_csrf)/i',
    'RFI_LFI' => '/(file:\/\/|php:\/\/|data:\/\/|base64)/i',
    'HTTP_RESPONSE_SPLIT' => '/(\%0d\%0a|\r\n)/i',
    'CMD_INJECTION' => '/(;|&&|\|\||`|<|>|\\$)/' // Protect against shell commands
];

// Brute force protection: Limit login attempts per IP
function limit_login_attempts($ip) {
    if (!isset($_SESSION['login_attempts'][$ip])) {
        $_SESSION['login_attempts'][$ip] = 0;
    }
    $_SESSION['login_attempts'][$ip]++;
    
    if ($_SESSION['login_attempts'][$ip] > 5) {
        log_request($ip, 'Login Attempt Limit Exceeded', 'POST', 'Too many login attempts');
        header('HTTP/1.1 429 Too Many Requests');
        echo json_encode(["message" => "Too many login attempts. Try again later."]);
        exit;
    }
}

// Rate limiting: Prevent DDoS and high request frequency
function rate_limiting($ip) {
    if (!isset($_SESSION['rate_limit'][$ip])) {
        $_SESSION['rate_limit'][$ip] = ['count' => 0, 'last_time' => time()];
    }
    $_SESSION['rate_limit'][$ip]['count']++;

    $current_time = time();
    $time_difference = $current_time - $_SESSION['rate_limit'][$ip]['last_time'];

    if ($time_difference < 1 && $_SESSION['rate_limit'][$ip]['count'] > 10) {
        log_request($ip, 'Rate Limiting Exceeded', $_SERVER['REQUEST_METHOD'], 'Too many requests in a short time');
        header('HTTP/1.1 429 Too Many Requests');
        echo json_encode(["message" => "Rate limit exceeded. Try again later."]);
        exit;
    }

    if ($time_difference >= 1) {
        $_SESSION['rate_limit'][$ip] = ['count' => 0, 'last_time' => $current_time];
    }
}

// File upload protection: Validate file types and sizes
function validate_file_upload($file) {
    $allowed_file_types = ['image/jpeg', 'image/png', 'application/pdf'];
    $max_file_size = 2 * 1024 * 1024; // 2MB

    if (!in_array($file['type'], $allowed_file_types)) {
        log_request($_SERVER['REMOTE_ADDR'], 'Invalid File Type', 'POST', $file['name']);
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(["message" => "Invalid file type."]);
        exit;
    }

    if ($file['size'] > $max_file_size) {
        log_request($_SERVER['REMOTE_ADDR'], 'File Too Large', 'POST', $file['name']);
        header('HTTP/1.1 413 Payload Too Large');
        echo json_encode(["message" => "File size exceeds limit."]);
        exit;
    }
}

// Input sanitization: Remove potentially dangerous input
function sanitize_input($input) {
    return htmlspecialchars(strip_tags($input));
}

// Header injection protection: Validate common headers for potential attacks
function validate_headers($headers) {
    $header_patterns = [
        'Host' => '/[^a-zA-Z0-9\.\-]/',           // Allow only alphanumeric, dots, and hyphens in the Host header
        'Referer' => '/[^a-zA-Z0-9\.\-\:\/]/',    // Allow only alphanumeric, dots, hyphens, colons, and slashes in the Referer header
        // Updated regex to allow additional characters in the User-Agent header
        'User-Agent' => '/[^a-zA-Z0-9\.\-\(\)\/;:_ ]/' // Allow alphanumeric, dots, hyphens, parentheses, slashes, semicolons, colons, underscores, and spaces
    ];

    foreach ($header_patterns as $header => $pattern) {
        if (isset($headers[$header]) && preg_match($pattern, $headers[$header])) {
            // Log the specific header causing the issue
            log_request($_SERVER['REMOTE_ADDR'], 'Header Injection', 'HEADER', [$header => $headers[$header]]);
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(["message" => "Malicious header detected in $header: " . $headers[$header]]);
            exit;
        }
    }
}




// Protect against HTTP method tampering: Allow only specific HTTP methods
function validate_http_method($method) {
    $allowed_methods = ['GET', 'POST'];
    
    if (!in_array($method, $allowed_methods)) {
        log_request($_SERVER['REMOTE_ADDR'], 'Invalid HTTP Method', $method, 'Invalid method used');
        header('HTTP/1.1 405 Method Not Allowed');
        echo json_encode(["message" => "HTTP method not allowed."]);
        exit;
    }
}

// Log malicious requests
function log_request($ip, $endpoint, $method, $payload, $attack_type = 'Unknown') {
    $log_entry = date('Y-m-d H:i:s') . " - Attack Detected: $attack_type | IP: $ip | Endpoint: $endpoint | Method: $method | Payload: " . json_encode($payload) . "\n";
    file_put_contents('waf_audit.log', $log_entry, FILE_APPEND);
    
    $_SESSION['audit_checklist']['malicious_requests'] += 1;
}

// Log approved requests
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

    // Sanitize all incoming inputs
    $_GET = array_map('sanitize_input', $_GET);
    $_POST = array_map('sanitize_input', $_POST);
    $headers = getallheaders();
    $sanitized_headers = array_map('sanitize_input', $headers);

    // Validate headers for potential injection
    validate_headers($sanitized_headers);

    // Combine GET, POST, and headers data for inspection
    $payload = array_merge($_GET, $_POST, $sanitized_headers);

    // Validate HTTP method
    validate_http_method($_SERVER['REQUEST_METHOD']);

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

    // Apply rate limiting
    rate_limiting($_SERVER['REMOTE_ADDR']);
}

// Apply brute force protection for login attempts
if ($_SERVER['REQUEST_URI'] == '/login') {
    limit_login_attempts($_SERVER['REMOTE_ADDR']);
}

// Check file uploads
if ($_FILES) {
    foreach ($_FILES as $file) {
        validate_file_upload($file);
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
