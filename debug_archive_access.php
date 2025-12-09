<?php
ob_start();
require_once 'index.php';
ob_end_clean();
$CI =& get_instance();
$CI->load->database();
$CI->load->model('Archive_model');
$CI->load->model('Template_model');

$ids = [2, 3, 4, 5];

echo "Checking Archives...\n";

foreach ($ids as $id) {
    echo "--------------------------------\n";
    echo "Checking Archive ID: $id\n";
    $archive = $CI->Archive_model->get_archive_by_id($id);
    
    if (!$archive) {
        echo "RESULT: Archive NOT FOUND.\n";
        continue;
    }
    
    echo "Found Archive. Template ID: " . $archive->template_id . "\n";
    
    $template = $CI->Template_model->get_template_by_id($archive->template_id);
    
    if ($template) {
        echo "RESULT: Template FOUND (ID: " . $template->id . ", Name: " . $template->nama_sk . ")\n";
    } else {
        echo "RESULT: Template NOT FOUND for ID " . $archive->template_id . "\n";
    }
}
