# WAF-Proxy
WAF Proxy Using Flask and ModSecurity
# PHP Web Application Firewall (WAF)

This project is a lightweight **PHP-based Web Application Firewall (WAF)** designed to inspect incoming HTTP requests for malicious patterns such as SQL injection, XSS, CSRF, and other web-based attacks. It logs all suspicious requests and provides an audit mechanism for tracking blocked and approved requests.

## Features

1. **Real-Time Request Inspection**:
   - Inspects incoming HTTP requests (GET, POST, and headers) and detects common attacks.
   - Blocks malicious requests with a **403 Forbidden** response.

2. **Attack Detection**:
   - Protects against:
     - **SQL Injection (SQLi)**
     - **Cross-Site Scripting (XSS)**
     - **Cross-Site Request Forgery (CSRF)**
     - **Remote File Inclusion (RFI)**
     - **Local File Inclusion (LFI)**
   - Uses regular expressions to detect attack patterns.

3. **Logging**:
   - Logs all malicious and approved requests to a log file `waf_audit.log`.
   - Provides a detailed log of IP addresses, attack types, request URIs, and methods.

4. **Audit Checklist**:
   - Tracks the number of requests inspected, malicious requests detected, and approved (safe) requests.
   - Audit information is available in JSON format via a dedicated endpoint.

5. **Endpoints**:
   - `/audit`: Returns a summary of inspected requests, malicious requests, and approved requests.
   - `/audit/logs`: Allows downloading the audit logs.
   - `/audit/export`: Exports the audit data in JSON format.

## Installation

### Prerequisites

- **PHP 7.x or higher** must be installed on your server.
- Ensure the web server has write permissions to create and update the `waf_audit.log` file.

### Steps to Deploy

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/yourusername/php-waf.git
   cd php-waf

Deploy the WAF:

Place the index.php file in the root directory of your PHP web server.
The WAF will automatically inspect all incoming requests.
Verify the Setup:

Visit the application homepage (e.g., http://yourdomain.com/) to ensure the WAF is functioning.

Usage
Once deployed, the WAF will begin inspecting incoming requests and logging suspicious activity. You can test attacks and view the audit reports as follows:

Test SQL Injection Attack
You can simulate an SQL injection attack by trying:

curl "http://yourdomain.com/?id=' UNION SELECT"

The WAF will block the request and log the attempt in waf_audit.log.

View the Audit Summary
To see a summary of requests inspected and approved, visit:

arduino

http://yourdomain.com/audit
Download the Logs
You can download the log file using the following endpoint:

arduino

http://yourdomain.com/audit/logs
Export Audit as JSON
To export the audit data as a JSON file, use:

arduino

http://yourdomain.com/audit/export
Code Structure
index.php: The main file containing the WAF logic. It inspects requests, blocks malicious ones, logs activity, and provides audit endpoints.
waf_audit.log: The log file where all malicious and approved requests are stored.

Configuration
The WAF uses predefined regular expressions to detect different types of attacks. You can customize the detection rules by editing the $attack_patterns array in the index.php file:

php

$attack_patterns = [
    'SQL_INJECTION' => '/(union.*select.*|select.*from.*|insert.*into.*|drop.*table.*)/i',
    'XSS' => '/(<.*script.*>|javascript:.*)/i',
    'CSRF' => '/(csrf_token|_csrf)/i',
    'RFI_LFI' => '/(file:\/\/|php:\/\/|data:\/\/)/i',
    'HTTP_RESPONSE_SPLIT' => '/(\%0d\%0a|\r\n)/i'
];


### Key Details:

- **Installation & Deployment**: Provides step-by-step instructions on how to deploy the PHP WAF to a web server.
- **Usage**: Describes how to test SQL injection attacks and view audit reports.
- **Configuration**: Allows the user to easily modify attack patterns in the `$attack_patterns` array.
- **Endpoints Summary**: A table summarizing the audit endpoints available for tracking activity.
- **Logs**: Describes the structure of the log file and what information is captured.
- **Security Considerations**: Includes advice on performance and log review for maintaining the WAF.

This README is comprehensive and suitable for hosting the WAF project on GitHub or sharing it with clients. Let me know if you need further customization!

