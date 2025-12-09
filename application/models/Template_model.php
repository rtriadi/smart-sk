<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Template_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_templates() {
        return $this->db->get('tb_templates')->result();
    }

    public function get_template_by_id($id) {
        return $this->db->get_where('tb_templates', ['id' => $id])->row();
    }

    public function create_template($data) {
        return $this->db->insert('tb_templates', $data);
    }

    public function update_template($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tb_templates', $data);
    }

    public function delete_template($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tb_templates');
    }
}
