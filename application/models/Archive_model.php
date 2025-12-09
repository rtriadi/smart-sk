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
        return $this->db->insert('tb_sk_archives', $data);
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
