<?php
ob_start();
require_once 'index.php';
ob_end_clean();
$CI =& get_instance();
$CI->load->database();

echo "Listing All Archives:\n";
$query = $CI->db->get('tb_sk_archives');
if ($query->num_rows() > 0) {
    foreach ($query->result() as $row) {
        echo "Archive ID: " . $row->id . " | Template ID: " . $row->template_id . " | No Surat: " . $row->no_surat . "\n";
    }
} else {
    echo "No archives found in database.\n";
}
