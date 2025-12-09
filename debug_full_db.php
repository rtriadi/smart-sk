<?php
ob_start();
require_once 'index.php';
ob_end_clean();
$CI =& get_instance();
$CI->load->database();

echo "=== TEMPLATES ===\n";
$query = $CI->db->get('tb_templates');
foreach ($query->result() as $row) {
    echo "ID: " . $row->id . " | Name: " . $row->nama_sk . " | Pattern: " . $row->nomor_pattern . "\n";
}

echo "\n=== ARCHIVES ===\n";
$query = $CI->db->get('tb_sk_archives');
foreach ($query->result() as $row) {
    echo "ID: " . $row->id . " | No Surat: " . $row->no_surat . " | Template ID: " . $row->template_id . "\n";
}
