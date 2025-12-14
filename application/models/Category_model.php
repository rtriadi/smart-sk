<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all() {
        return $this->db->get('tb_sk_categories')->result();
    }

    public function get_by_id($id) {
        return $this->db->get_where('tb_sk_categories', ['id' => $id])->row();
    }

    public function insert($data) {
        return $this->db->insert('tb_sk_categories', $data);
    }

    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tb_sk_categories', $data);
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tb_sk_categories');
    }
}
