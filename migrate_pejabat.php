<?php
ob_start();
require_once 'index.php';
ob_end_clean();
$CI =& get_instance();
$CI->load->database();

// Check if is_default exists
if (!$CI->db->field_exists('is_default', 'tb_pejabat')) {
    $fields = array(
        'is_default' => array(
            'type' => 'TINYINT',
            'constraint' => 1,
            'default' => 0
        )
    );
    $CI->load->dbforge();
    $CI->dbforge->add_column('tb_pejabat', $fields);
    echo "Column 'is_default' added to tb_pejabat.\n";
} else {
    echo "Column 'is_default' already exists.\n";
}
?>
