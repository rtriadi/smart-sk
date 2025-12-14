<?php
$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'db_smart_sk';

$mysqli = new mysqli($hostname, $username, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$res = $mysqli->query("SHOW VARIABLES LIKE 'max_allowed_packet'");
$row = $res->fetch_assoc();
if ($row) {
    $bytes = $row['Value'];
    echo "max_allowed_packet: " . $bytes . " bytes (" . round($bytes/1024/1024, 2) . " MB)\n";
} else {
    echo "Could not fetch max_allowed_packet\n";
}
// Also check wait_timeout
$res = $mysqli->query("SHOW VARIABLES LIKE 'wait_timeout'");
$row = $res->fetch_assoc();
if ($row) echo "wait_timeout: " . $row['Value'] . "\n";


// Clear the big log file
$logFile = 'debug_sk_save.txt';
if (file_exists($logFile)) {
    unlink($logFile);
    echo "Cleared debug_sk_save.txt\n";
}
?>
