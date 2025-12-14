<?php
ob_start();
require_once 'index.php';
ob_end_clean();
$CI =& get_instance();
$CI->load->database();

echo "Checking database schema for 'tb_pejabat'...\n";

if (!$CI->db->field_exists('is_default', 'tb_pejabat')) {
    echo "Column 'is_default' MISSING. Attempting to add...\n";
    
    // Try Direct SQL
    $sql = "ALTER TABLE `tb_pejabat` ADD COLUMN `is_default` TINYINT(1) DEFAULT 0";
    if ($CI->db->query($sql)) {
        echo "SUCCESS: Column 'is_default' added via SQL.\n";
    } else {
        echo "ERROR: Failed to add column. Error: " . json_encode($CI->db->error()) . "\n";
    }
} else {
    echo "Column 'is_default' ALREADY EXISTS.\n";
}

// Verification
if ($CI->db->field_exists('is_default', 'tb_pejabat')) {
    echo "VERIFIED: Column 'is_default' is present in the table.\n";
} else {
    echo "FAILED: Column 'is_default' is still missing.\n";
}
?>
