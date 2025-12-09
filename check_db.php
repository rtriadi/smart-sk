<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully\n";

// List databases
$sql = "SHOW DATABASES";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Databases found:\n";
    while($row = $result->fetch_assoc()) {
        echo $row["Database"] . "\n";
    }
} else {
    echo "0 results";
}
$conn->close();
?>
