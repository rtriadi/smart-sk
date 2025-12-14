<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_smart_sk"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$result = $conn->query("SHOW CREATE TABLE tb_sk_archives");
if ($row = $result->fetch_assoc()) {
    echo "<pre>" . print_r($row, true) . "</pre>";
}
$conn->close();
?>
