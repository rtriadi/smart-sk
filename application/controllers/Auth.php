<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // check_already_login();
        $this->load->model('user_model');
    }

    public function login()
    {
        if ($this->input->post()) {
            $username = $this->input->post('username');
            $password = sha1($this->input->post('password'));

            $user = $this->user_model->get_by_username($username);

            if ($user) {
                // Jika username ditemukan, cek password
                if ($password == $user['password']) {
                    // Jika password cocok, simpan sesi pengguna dan arahkan ke halaman dashboard atau halaman setelah login berhasil
                    $this->session->set_userdata('id_user', $user['id_user']);
                    redirect('dashboard'); // Ganti 'dashboard' dengan halaman setelah login berhasil
                } else {
                    // Password salah
                    $this->session->set_flashdata('error', 'Password salah');
                    redirect('auth/login'); // Ganti 'auth/login' dengan halaman login
                }
            } else {
                // Username tidak ditemukan
                $this->session->set_flashdata('error', 'Username tidak ditemukan');
                redirect('auth/login'); // Ganti 'auth/login' dengan halaman login
            }
        }
        $this->load->view('login');
    }

    public function logout()
    {
        // $this->session->unset_userdata('id_user');
        $this->session->sess_destroy();
        redirect('auth/login'); // Ganti 'auth/login' dengan halaman login
    }
}
