<?php
ob_start();
require_once 'index.php';
ob_end_clean();
$CI =& get_instance();
$CI->load->database();

echo "Starting Repair...\n";

// 1. Update all archives to point to Template ID 1
$CI->db->set('template_id', 1);
$CI->db->update('tb_sk_archives');
echo "Updated " . $CI->db->affected_rows() . " archives to use Template ID 1.\n";

// 2. Delete all templates EXCEPT ID 1
$CI->db->where('id !=', 1);
$CI->db->delete('tb_templates');
echo "Deleted " . $CI->db->affected_rows() . " duplicate templates.\n";

echo "Repair Complete.\n";
