<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_smart_sk";

// Create connection
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: " . $conn->error . "\n";
}

$conn->select_db($dbname);

// Read schema file
$schema = file_get_contents('application/sql/schema.sql');
if ($schema === false) {
    die("Error reading schema file");
}

// Execute multi query
if ($conn->multi_query($schema)) {
    do {
        /* store first result set */
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    echo "Schema applied successfully\n";
} else {
    echo "Error applying schema: " . $conn->error . "\n";
}

$conn->close();
?>
