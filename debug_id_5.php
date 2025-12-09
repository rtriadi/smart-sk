<?php
ob_start();
require_once 'index.php';
ob_end_clean();
$CI =& get_instance();
$CI->load->database();

echo "Checking ID 5:\n";
$row = $CI->db->get_where('tb_sk_archives', ['id' => 5])->row();
if ($row) {
    echo "Archive Found. Template ID: " . $row->template_id . "\n";
    $tmpl = $CI->db->get_where('tb_templates', ['id' => $row->template_id])->row();
    if ($tmpl) {
        echo "Template Found: " . $tmpl->nama_sk . " (ID: " . $tmpl->id . ")\n";
    } else {
        echo "Template NOT FOUND! (ID: " . $row->template_id . ")\n";
    }
} else {
    echo "Archive 5 NOT FOUND.\n";
}
