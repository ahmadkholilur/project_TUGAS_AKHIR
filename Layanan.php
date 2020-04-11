<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Layanan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('email')) {
            redirect('auth');
        }
        $this->load->model('Informasi_model');
    }
    public function index()
    {
        $data['title'] = 'Halaman Utama';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        // $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar_pengguna', $data);
        $this->load->view('pengguna/index', $data);
        $this->load->view('templates/footer_pengguna');
    }
}
