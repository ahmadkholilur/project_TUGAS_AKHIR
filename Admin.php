<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        cek_login();
        $this->load->model('Informasi_model');
        $this->load->model('Pelayanan_model');
        $this->load->model('User_model');
    }

    public function index()
    {
        $data['title'] = 'Dashboard';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $data['total_rows'] = $this->Informasi_model->jumlah();
        $data['total_rows1'] = $this->Pelayanan_model->jumlah();
        $data['anggota'] = $this->db->get('user')->num_rows();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('templates/footer');
    }

    public function role()
    {
        $data['title'] = 'Role';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $data['role'] = $this->db->get('user_role')->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/role', $data);
        $this->load->view('templates/footer');
    }

    public function roleaccess($role_id)
    {
        $data['title'] = 'Role Access';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $data['role'] = $this->db->get_where('user_role', ['id' => $role_id])->row_array();
        $this->db->where('id!=', 1);
        $data['menu'] = $this->db->get('user_menu')->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/role-access', $data);
        $this->load->view('templates/footer');
    }

    public function changeaccess()
    {
        $menu_id = $this->input->post('menuId');
        $role_id = $this->input->post('roleId');

        $data = [
            'role_id' => $role_id,
            'menu_id' => $menu_id
        ];

        $result = $this->db->get_where('user_access_menu', $data);

        if ($result->num_rows() < 1) {
            $this->db->insert('user_access_menu', $data);
        } else {
            $this->db->delete('user_access_menu', $data);
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Akses Diubah
          </div>');
    }

    public function pengguna()
    {
        $data['title'] = 'Pengguna';

        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $config['base_url'] = site_url('admin/pengguna');
        $config['total_rows'] = $this->User_model->jumlah();
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
        $data['users'] = $this->User_model->getAll($config['per_page'], $data['start']);
        $data['anggota'] = $this->db->get('user')->num_rows();
        // $data['users'] = $this->db->get('user')->result_array();
        if ($this->input->post('keyword')) {
            $data['users'] = $this->User_model->cari();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/pengguna', $data);
        $this->load->view('templates/footer');
    }

    public function hapus($id)
    {
        $data = $this->User_model->getId($id)->row_array();
        $nama = './assets/img/profil/' . $data['image'];

        if (is_readable($nama) && unlink($nama)) {
            $this->User_model->hapus($id);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Data Pengguna berhasil di ubah
          </div>');
            redirect('admin/pengguna');
        } else {
            $this->User_model->hapus($id);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Data Pengguna berhasil di ubah
          </div>');
            redirect('admin/pengguna');
        }
    }
}
