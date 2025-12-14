<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_smart_sk';

echo "Database Fixer - Manual Mode\n";
echo "Connecting to $db at $host...\n";

try {
    $mysqli = new mysqli($host, $user, $pass, $db);
    echo "Connected successfully.\n";

    // 1. Check if column exists
    echo "Checking schema for tb_pejabat...\n";
    $result = $mysqli->query("SHOW COLUMNS FROM tb_pejabat LIKE 'is_default'");
    
    if ($result->num_rows > 0) {
        echo "[INFO] Column 'is_default' already exists.\n";
    } else {
        echo "[ACTION] Column missing. Adding 'is_default' column...\n";
        $mysqli->query("ALTER TABLE tb_pejabat ADD COLUMN is_default TINYINT(1) DEFAULT 0");
        echo "[SUCCESS] Column added.\n";
    }

    // 2. Verify availability
    $verify = $mysqli->query("SHOW COLUMNS FROM tb_pejabat LIKE 'is_default'");
    if ($verify->num_rows > 0) {
        echo "[VERIFIED] Table schema is correct.\n";
    } else {
         echo "[FAILED] Column check returned false after ALTER.\n";
    }

} catch (mysqli_sql_exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
}
?>
