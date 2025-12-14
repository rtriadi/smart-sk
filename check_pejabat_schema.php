<?php
ob_start();
require_once 'index.php';
ob_end_clean();
$CI =& get_instance();
$CI->load->database();
$fields = $CI->db->list_fields('tb_pejabat');
echo "Columns in tb_pejabat:\n";
foreach ($fields as $field) {
    echo "- " . $field . "\n";
}
?>
