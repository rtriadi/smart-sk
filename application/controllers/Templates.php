<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Templates extends CI_Controller {

    public function __construct() {
        parent::__construct();
        check_not_login();
        $this->load->model('Template_model');
        $this->load->model('Category_model');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['templates'] = $this->Template_model->get_all_templates();
        
        // Wrap in enterprise layout
        $layout_data['page_content'] = $this->load->view('templates/manage_view', $data, TRUE);
        $this->load->view('layout/enterprise_layout', $layout_data);
    }

    public function create() {
        $data['categories'] = $this->Category_model->get_all();
        
        // Wrap in enterprise layout
        $layout_data['page_content'] = $this->load->view('templates/create_view', $data, TRUE);
        $this->load->view('layout/enterprise_layout', $layout_data);
    }

    public function store() {
        $this->form_validation->set_rules('nama_sk', 'Nama SK', 'required');
        $this->form_validation->set_rules('kategori', 'Kategori', 'required');

        if ($this->form_validation->run() === FALSE) {
            // Redirect back to create with error
            $this->session->set_flashdata('error', validation_errors());
            redirect('templates/create');
        } else {
            $data = [
                'nama_sk' => $this->input->post('nama_sk'),
                'kategori' => $this->input->post('kategori'),
                'nomor_pattern' => $this->input->post('nomor_pattern'),
                'html_pattern' => $this->input->post('html_pattern'),
                'form_config' => $this->input->post('form_config')
            ];
            $this->Template_model->create_template($data);
            $this->session->set_flashdata('success', 'Template berhasil dibuat!');
            redirect('templates');
        }
    }

    public function edit($id) {
        $data['template'] = $this->Template_model->get_template_by_id($id);
        
        if (!$data['template']) {
            show_404();
        }
        
        $data['categories'] = $this->Category_model->get_all();

        // Wrap in enterprise layout
        $layout_data['page_content'] = $this->load->view('templates/edit_view', $data, TRUE);
        $this->load->view('layout/enterprise_layout', $layout_data);
    }

    public function update($id) {
        $this->form_validation->set_rules('nama_sk', 'Nama SK', 'required');
        $this->form_validation->set_rules('kategori', 'Kategori', 'required');

        if ($this->form_validation->run() === FALSE) {
            // Redirect back to edit with error
            $this->session->set_flashdata('error', validation_errors());
            redirect('templates/edit/' . $id);
        } else {
            $update_data = [
                'nama_sk' => $this->input->post('nama_sk'),
                'kategori' => $this->input->post('kategori'),
                'nomor_pattern' => $this->input->post('nomor_pattern'),
                'html_pattern' => $this->input->post('html_pattern'),
                'form_config' => $this->input->post('form_config')
            ];
            $this->Template_model->update_template($id, $update_data);
            $this->session->set_flashdata('success', 'Template berhasil diperbarui!');
            redirect('templates');
        }
    }

    public function delete($id) {
        $this->Template_model->delete_template($id);
        $this->session->set_flashdata('success', 'Template berhasil dihapus!');
        redirect('templates');
    }
}
