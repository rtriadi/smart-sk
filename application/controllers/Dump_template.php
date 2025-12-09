<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dump_template extends CI_Controller {
    public function index() {
        $this->load->database();
        $template = $this->db->get_where('tb_templates', ['id' => 1])->row();
        
        header('Content-Type: application/json');
        echo json_encode([
            'form_config' => json_decode($template->form_config),
            'html_pattern' => $template->html_pattern
        ], JSON_PRETTY_PRINT);
    }
}
