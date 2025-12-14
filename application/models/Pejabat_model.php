<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pejabat_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_all() {
        return $this->db->get('tb_pejabat')->result();
    }

    public function get_active() {
        $this->db->order_by('is_default', 'DESC');
        $this->db->order_by('nama', 'ASC');
        return $this->db->get_where('tb_pejabat', ['status' => 'aktif'])->result();
    }

    public function set_default($id) {
        // Reset all to 0
        $this->db->update('tb_pejabat', ['is_default' => 0]);
        
        // Set selected to 1
        $this->db->where('id', $id);
        return $this->db->update('tb_pejabat', ['is_default' => 1]);
    }

    public function get_by_id($id) {
        return $this->db->get_where('tb_pejabat', ['id' => $id])->row();
    }

    public function insert($data) {
        return $this->db->insert('tb_pejabat', $data);
    }

    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tb_pejabat', $data);
    }

    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('tb_pejabat');
    }
}
