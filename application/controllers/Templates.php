<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Templates extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Template_model');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['templates'] = $this->Template_model->get_all_templates();
        $this->load->view('templates/manage_view', $data);
    }

    public function create() {
        $this->form_validation->set_rules('nama_sk', 'Nama SK', 'required');
        $this->form_validation->set_rules('kategori', 'Kategori', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/create_view');
        } else {
            $data = [
                'nama_sk' => $this->input->post('nama_sk'),
                'kategori' => $this->input->post('kategori'),
                'nomor_pattern' => $this->input->post('nomor_pattern'),
                'html_pattern' => $this->input->post('html_pattern'),
                'form_config' => $this->input->post('form_config')
            ];
            $this->Template_model->create_template($data);
            redirect('templates');
        }
    }

    public function edit($id) {
        $data['template'] = $this->Template_model->get_template_by_id($id);
        
        $this->form_validation->set_rules('nama_sk', 'Nama SK', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/edit_view', $data);
        } else {
             $update_data = [
                'nama_sk' => $this->input->post('nama_sk'),
                'kategori' => $this->input->post('kategori'),
                'nomor_pattern' => $this->input->post('nomor_pattern'),
                'html_pattern' => $this->input->post('html_pattern'),
                'form_config' => $this->input->post('form_config')
            ];
            $this->Template_model->update_template($id, $update_data);
            redirect('templates');
        }
    }

    public function delete($id) {
        $this->Template_model->delete_template($id);
        redirect('templates');
    }
}
