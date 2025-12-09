<?php

class Fungsi
{
	protected $ci;

	function __construct()
	{
		$this->ci = &get_instance();
	}

	function user_login()
	{
		$id_user = $this->ci->session->userdata('id_user');
		$user_data = $this->ci->db->query("SELECT * FROM tbl_users WHERE id_user = '$id_user'")->row();
		return $user_data;
	}
}
