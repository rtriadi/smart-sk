<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    public function get_by_username($username)
    {
        $this->db->where('username', $username);
        return $this->db->get('tb_users')->row_array();
    }

    public function get_by_id($id)
    {
        $this->db->where('id_user', $id);
        return $this->db->get('tb_users')->row_array();
    }
}
