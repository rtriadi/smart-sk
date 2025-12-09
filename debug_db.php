<?php
// Load CodeIgniter framework
ob_start();
require_once 'index.php';
ob_end_clean();

$CI =& get_instance();
$CI->load->database();

echo "<h1>Debug Database Content</h1>";

// Check tb_templates
echo "<h2>Table: tb_templates</h2>";
$query = $CI->db->get('tb_templates');
if ($query->num_rows() > 0) {
    echo "<table border='1'><tr>";
    foreach ($query->list_fields() as $field) {
        echo "<th>$field</th>";
    }
    echo "</tr>";
    
    foreach ($query->result_array() as $row) {
        echo "<tr>";
        foreach ($row as $val) {
            echo "<td>" . htmlspecialchars($val) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Table is empty.";
}
