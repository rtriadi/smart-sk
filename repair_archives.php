<?php
ob_start();
require_once 'index.php';
ob_end_clean();
$CI =& get_instance();
$CI->load->database();

// Get valid template ID (prefer 1 or 2)
$valid_id = 1;
$query = $CI->db->get_where('tb_templates', ['id' => 2]);
if ($query->num_rows() > 0) {
    $valid_id = 2;
} else {
    $query = $CI->db->get_where('tb_templates', ['id' => 1]);
    if ($query->num_rows() > 0) {
        $valid_id = 1;
    }
}

echo "Repairing Archives to use Template ID: $valid_id\n";
$CI->db->set('template_id', $valid_id);
$CI->db->update('tb_sk_archives');
echo "Updated " . $CI->db->affected_rows() . " archives.\n";
