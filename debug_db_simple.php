<?php
ob_start();
require_once 'index.php';
ob_end_clean();
$CI =& get_instance();
$CI->load->database();
$query = $CI->db->select('id, nama_sk')->get('tb_templates');
foreach ($query->result() as $row) {
    echo "ID: " . $row->id . " - " . $row->nama_sk . "\n";
}
