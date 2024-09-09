<?php
include('includes/db.php');
include('includes/functions.php');

// Fetch WAF rules from the database
$wafRules = getWAFRules($conn);

// Prepare the array to hold the JSON data
$rulesArray = [];

foreach ($wafRules as $rule) {
    $rulesArray[] = [
        'id' => $rule['id'],
        'rule_name' => $rule['rule_name'],
        'pattern' => $rule['pattern'],
        'action' => $rule['action'],
        'created_at' => $rule['created_at']
    ];
}

// Convert the array to JSON format
$jsonData = json_encode($rulesArray, JSON_PRETTY_PRINT);

// Define the path where the JSON file will be saved
$jsonFilePath = 'waf-rules.json';

// Save the JSON data to the file
file_put_contents($jsonFilePath, $jsonData);

// Output the JSON data (you can return this if needed or redirect after generation)
header('Content-Type: application/json');
echo $jsonData;
?>
