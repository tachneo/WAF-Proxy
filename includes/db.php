<?php
$host = 'localhost';
$dbname = 'waf_management';
$username = 'rohit';
$password = 'Rohit@1eb357';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>
