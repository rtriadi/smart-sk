<?php
define('BASEPATH', 'dummy'); 
include 'application/config/database.php';
$db = $db['default'];

$mysqli = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$res = $mysqli->query("SHOW VARIABLES LIKE 'max_allowed_packet'");
$row = $res->fetch_assoc();
$bytes = $row['Value'];
echo "max_allowed_packet: " . $bytes . " bytes (" . round($bytes/1024/1024, 2) . " MB)\n";

// Clear the big log file
$logFile = 'debug_sk_save.txt';
if (file_exists($logFile)) {
    unlink($logFile);
    echo "Cleared debug_sk_save.txt\n";
}
?>
