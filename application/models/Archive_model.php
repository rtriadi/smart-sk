<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Archive_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_archives() {
        $this->db->select('tb_sk_archives.*, tb_templates.nama_sk');
        $this->db->from('tb_sk_archives');
        $this->db->join('tb_templates', 'tb_templates.id = tb_sk_archives.template_id', 'left');
        $this->db->order_by('tb_sk_archives.id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_archive_by_id($id) {
        return $this->db->get_where('tb_sk_archives', ['id' => $id])->row();
    }

    public function create_archive($data) {
        $res = $this->db->insert('tb_sk_archives', $data);
        if (!$res) {
             $error = $this->db->error();
             log_message('error', "Model: DB Error: " . print_r($error, true));
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
