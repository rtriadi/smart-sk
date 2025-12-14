<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Archive_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_archives() {
        return $this->db->get('tb_sk_archives')->result();
    }

    public function get_archive_by_id($id) {
        return $this->db->get_where('tb_sk_archives', ['id' => $id])->row();
    }

    public function create_archive($data) {
        @file_put_contents(FCPATH . 'debug_sk_save.txt', date('Y-m-d H:i:s') . " - Model: create_archive called\n", FILE_APPEND);
        $res = $this->db->insert('tb_sk_archives', $data);
        @file_put_contents(FCPATH . 'debug_sk_save.txt', date('Y-m-d H:i:s') . " - Model: insert result: " . ($res ? 'TRUE' : 'FALSE') . "\n", FILE_APPEND);
        if (!$res) {
             $error = $this->db->error();
             @file_put_contents(FCPATH . 'debug_sk_save.txt', date('Y-m-d H:i:s') . " - Model: DB Error: " . print_r($error, true) . "\n", FILE_APPEND);
        }
        return $res;
    }

    public function update_archive($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tb_sk_archives', $data);
    }

    public function delete_archive($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tb_sk_archives');
    }
}
