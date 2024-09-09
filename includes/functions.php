<?php

// =================== WAF LOG FUNCTIONS =====================

// Fetch WAF logs from the database
if (!function_exists('getWAFLogs')) {
    function getWAFLogs($conn) {
        $query = "SELECT * FROM waf_logs ORDER BY timestamp DESC";
        $result = $conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

// Log a request in the WAF logs
if (!function_exists('logRequest')) {
    function logRequest($conn, $ip_address, $endpoint, $method, $payload, $attack_type, $is_blocked) {
        $stmt = $conn->prepare("INSERT INTO waf_logs (ip_address, endpoint, method, payload, attack_type, is_blocked, timestamp) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssssi", $ip_address, $endpoint, $method, $payload, $attack_type, $is_blocked);
        $stmt->execute();
    }
}

// =================== WAF RULE FUNCTIONS =====================

// Fetch all WAF rules
if (!function_exists('getWAFRules')) {
    function getWAFRules($conn) {
        $query = "SELECT * FROM waf_rules ORDER BY id ASC";
        $result = $conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

// Add a new WAF rule
if (!function_exists('addWAFRule')) {
    function addWAFRule($conn, $rule_name, $pattern, $action) {
        $stmt = $conn->prepare("INSERT INTO waf_rules (rule_name, pattern, action, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $rule_name, $pattern, $action);
        $stmt->execute();
        $stmt->close();
    }
}

// Delete a WAF rule by its ID
if (!function_exists('deleteWAFRule')) {
    function deleteWAFRule($conn, $id) {
        $stmt = $conn->prepare("DELETE FROM waf_rules WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

// Process WAF rules to check for malicious activity
if (!function_exists('processWAFRules')) {
    function processWAFRules($conn, $request) {
        $rules = getWAFRules($conn);
        foreach ($rules as $rule) {
            if (@preg_match("/" . preg_quote($rule['pattern'], '/') . "/", $request)) {
                if ($rule['action'] == 'block') {
                    logRequest($conn, $_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], json_encode($request), $rule['rule_name'], 1);
                    die("Access Denied. Detected: " . $rule['rule_name']);
                } elseif ($rule['action'] == 'log') {
                    logRequest($conn, $_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], json_encode($request), $rule['rule_name'], 0);
                }
            }
        }
    }
}

// =================== WAF SETTINGS FUNCTIONS =====================

// Fetch the current WAF status
if (!function_exists('getWAFStatus')) {
    function getWAFStatus($conn) {
        $query = "SELECT waf_status FROM waf_settings LIMIT 1";
        $result = $conn->query($query);
        return $result->fetch_assoc()['waf_status'];
    }
}

// Fetch the current rate limit setting
if (!function_exists('getRateLimit')) {
    function getRateLimit($conn) {
        $query = "SELECT rate_limit FROM waf_settings LIMIT 1";
        $result = $conn->query($query);
        return $result->fetch_assoc()['rate_limit'];
    }
}

// Fetch the email notifications setting
if (!function_exists('getEmailNotificationStatus')) {
    function getEmailNotificationStatus($conn) {
        $query = "SELECT email_notifications FROM waf_settings LIMIT 1";
        $result = $conn->query($query);
        return $result->fetch_assoc()['email_notifications'];
    }
}

// Fetch the logging level setting
if (!function_exists('getLogLevel')) {
    function getLogLevel($conn) {
        $query = "SELECT log_level FROM waf_settings LIMIT 1";
        $result = $conn->query($query);
        return $result->fetch_assoc()['log_level'];
    }
}

// Update WAF settings in the database
if (!function_exists('updateWAFSettings')) {
    function updateWAFSettings($conn, $status, $rateLimit, $emailNotifications, $logLevel) {
        $stmt = $conn->prepare("UPDATE waf_settings SET waf_status = ?, rate_limit = ?, email_notifications = ?, log_level = ?");
        $stmt->bind_param("iiis", $status, $rateLimit, $emailNotifications, $logLevel);
        $stmt->execute();
    }
}

// =================== WAF MANAGEMENT SETTINGS FUNCTIONS =====================

// Fetch rule source URL and alert email from management settings
if (!function_exists('getWAFManagementSettings')) {
    function getWAFManagementSettings($conn) {
        $query = "SELECT * FROM waf_management_settings WHERE id = 1 LIMIT 1";
        $result = $conn->query($query);
        return $result->fetch_assoc();
    }
}

// Update rule source URL and alert email in the management settings
if (!function_exists('updateWAFManagementSettings')) {
    function updateWAFManagementSettings($conn, $ruleSourceUrl, $alertEmail) {
        $query = "SELECT COUNT(*) as count FROM waf_management_settings";
        $result = $conn->query($query);
        $row = $result->fetch_assoc();

        if ($row['count'] == 0) {
            $stmt = $conn->prepare("INSERT INTO waf_management_settings (rule_source_url, alert_email) VALUES (?, ?)");
            $stmt->bind_param("ss", $ruleSourceUrl, $alertEmail);
        } else {
            $stmt = $conn->prepare("UPDATE waf_management_settings SET rule_source_url = ?, alert_email = ?, updated_at = NOW() WHERE id = 1");
            $stmt->bind_param("ss", $ruleSourceUrl, $alertEmail);
        }
        $stmt->execute();
    }
}

// =================== AUDIT FUNCTIONS =====================

// Fetch audit data for reporting
if (!function_exists('getAuditData')) {
    function getAuditData($conn) {
        $query = "SELECT SUM(requests_inspected) as requests_inspected, SUM(malicious_requests) as malicious_requests, SUM(approved_requests) as approved_requests FROM audit_logs";
        $result = $conn->query($query);
        return $result->fetch_assoc();
    }
}

// Log audit data after each request
if (!function_exists('logAuditData')) {
    function logAuditData($conn, $requests_inspected, $malicious_requests, $approved_requests) {
        $stmt = $conn->prepare("INSERT INTO audit_logs (requests_inspected, malicious_requests, approved_requests, timestamp) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iii", $requests_inspected, $malicious_requests, $approved_requests);
        $stmt->execute();
    }
}

// =================== ADDITIONAL SECURITY FUNCTIONS =====================

// Sanitize input to prevent SQL injection
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($input) {
        return htmlspecialchars(strip_tags($input));
    }
}

// Check if a request matches a WAF rule
if (!function_exists('checkForMaliciousActivity')) {
    function checkForMaliciousActivity($conn, $request) {
        $rules = getWAFRules($conn);
        foreach ($rules as $rule) {
            if (@preg_match("/" . preg_quote($rule['pattern'], '/') . "/", $request)) {
                logRequest($conn, $_SERVER['REMOTE_ADDR'], $_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], json_encode($request), $rule['rule_name'], 1);
                if ($rule['action'] == 'block') {
                    die("Access Denied");
                }
            }
        }
    }
}

// =================== CSRF PROTECTION =====================

// Generate a CSRF token and store it in session
if (!function_exists('generateCSRFToken')) {
    function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

// Validate the CSRF token on form submission
if (!function_exists('validateCSRFToken')) {
    function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

// =================== ALERTING FUNCTIONS =====================

// Function to send email alerts
if (!function_exists('sendAlertEmail')) {
    function sendAlertEmail($conn, $subject, $message) {
        $settings = getWAFManagementSettings($conn);
        $alertEmail = $settings['alert_email'];
        $headers = "From: waf@example.com\r\n";
        mail($alertEmail, $subject, $message, $headers);
    }
}

// =================== AUTOMATED UPDATES =====================

// Function to update rules (e.g., from a JSON file or an API)
if (!function_exists('updateSecurityRules')) {
    function updateSecurityRules($conn) {
        $settings = getWAFManagementSettings($conn);
        $rulesJsonUrl = $settings['rule_source_url'];
        $rulesJson = file_get_contents($rulesJsonUrl);
        $rules = json_decode($rulesJson, true);

        foreach ($rules as $rule) {
            $pattern = $rule['pattern'];
            $action = $rule['action'];
            $stmt = $conn->prepare("REPLACE INTO waf_rules (pattern, action) VALUES (?, ?)");
            $stmt->bind_param("ss", $pattern, $action);
            $stmt->execute();
        }
    }
}
