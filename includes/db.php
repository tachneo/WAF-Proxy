<?php
$host = 'localhost';
$dbname = 'waf_management';
$username = 't';
$password = '7';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>
