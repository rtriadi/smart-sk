<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Db_fix extends CI_Controller {
    public function index() {
        $this->load->dbforge();
        
        if (!$this->db->field_exists('updated_at', 'tb_sk_archives')) {
            $fields = [
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => TRUE
                ]
            ];
            $this->dbforge->add_column('tb_sk_archives', $fields);
            echo "Added updated_at column.";
        } else {
            echo "updated_at column already exists.";
        }
    }
}
