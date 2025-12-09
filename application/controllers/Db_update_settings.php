<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Db_update_settings extends CI_Controller {
    public function index() {
        $this->load->dbforge();
        
        if (!$this->db->field_exists('settings_json', 'tb_sk_archives')) {
            $fields = [
                'settings_json' => [
                    'type' => 'TEXT',
                    'null' => TRUE
                ]
            ];
            $this->dbforge->add_column('tb_sk_archives', $fields);
            echo "Added settings_json column.";
        } else {
            echo "settings_json column already exists.";
        }
    }
}
