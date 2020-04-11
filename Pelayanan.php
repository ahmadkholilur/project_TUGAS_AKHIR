<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pelayanan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        cek_login();
        $this->load->model('Pelayanan_model');
    }
    public function index()
    {
        $data['title'] = 'Pelayanan';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $config['base_url'] = site_url('pelayanan/index');
        $config['total_rows'] = $this->Pelayanan_model->jumlah();
        $config['per_page'] = 5;

        $config['full_tag_open'] = '<nav><ul class="pagination justify-content-center">';
        $config['full_tag_close'] = ' </ul></nav>';

        $config['first_link'] = 'awal';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';

        $config['last_link'] = 'akhir';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';

        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';

        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';

        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';

        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';

        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);


        $data['start'] = $this->uri->segment(3);
        $data['pelayanan'] = $this->Pelayanan_model->getAll($config['per_page'], $data['start']);
        $data['total_rows'] = $config['total_rows'];


        if ($this->input->post('keyword')) {
            #code..
            $data['pelayanan'] = $this->Pelayanan_model->cari();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('pelayanan/index', $data);
        $this->load->view('templates/footer');
    }

    public function detail($id_pelayanan)
    {
        $data['title'] = 'Detail Pelayanan';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $data['pelayanan'] = $this->Pelayanan_model->detail($id_pelayanan);
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('pelayanan/detail', $data);
        $this->load->view('templates/footer');
    }

    public function hapus($id_pelayanan)
    {
        $data = $this->Pelayanan_model->getId($id_pelayanan)->row_array();
        $nama = './assets/img/pelayanan/' . $data['berkas'];

        if (is_readable($nama) && unlink($nama)) {
            $this->Pelayanan_model->hapus($id_pelayanan);
            redirect('pelayanan');
        } else {
            $this->Pelayanan_model->hapus($id_pelayanan);
            redirect('pelayanan');
        }
    }

    public function status($id_pelayanan)
    {
        $data['title'] = 'Status Pelayanan';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $data['pelayanan'] = $this->Pelayanan_model->detail($id_pelayanan);
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('pelayanan/status', $data);
        $this->load->view('templates/footer');
    }

    public function update()
    {
        $status = $this->input->post('status');
        $pesan = $this->input->post('pesan');
        $id_pelayanan = $this->input->post('id_pelayanan');

        $data = [
            'status' => $status,
            'pesan' => $pesan
        ];

        $this->db->set($data);
        $this->db->where('id_pelayanan', $id_pelayanan);
        $this->db->update('pelayanan');
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
        Data berhasil di perbaharui
      </div>');
        redirect('pelayanan');
    }
}
