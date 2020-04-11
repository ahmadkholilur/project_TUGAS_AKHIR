<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Informasi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        cek_login();
        $this->load->model('Informasi_model');
        // $this->load->library('pagination');
        //$this->load->library('upload');
    }
    public function index()
    {

        $data['title'] = 'Informasi';
        // $data['user'] = $this->db->get('user')->row_array();
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        //config
        $config['base_url'] = site_url('informasi/index');
        $config['total_rows'] = $this->Informasi_model->jumlah();
        $config['per_page'] = 5;
        // $config['num_link'] = 3;   digunakan untuk merubah jumlah page kiri dan kanan


        //design pagination
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

        //inisialisasi config
        $this->pagination->initialize($config);


        $data['start'] = $this->uri->segment(3);
        $data['informasi'] = $this->Informasi_model->getInformasi($config['per_page'], $data['start']);
        $data['total_rows'] = $config['total_rows'];

        if ($this->input->post('cari')) {
            $data['informasi'] = $this->Informasi_model->cari();
        }
        //data informasi
        // $data['informasi'] = $this->Informasi_model->informasi();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('informasi/index', $data);
        $this->load->view('templates/footer');
    }

    public function tambah()
    {
        $data['title'] = 'Tambah Informasi';
        $this->form_validation->set_rules('judul', 'Judul', 'required', [
            'required' => 'nama harus di isi'
        ]);
        // $this->form_validation->set_rules('gambar', 'Gambar', 'required');
        $this->form_validation->set_rules('isi', 'Isi', 'required', [
            'required' => 'wajib isi content'
        ]);
        if ($this->form_validation->run() == false) {
            // $data['user'] = $this->db->get('user')->row_array();
            $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
            $data['informasi'] = $this->Informasi_model->informasi();
            // $data['user'] = $this->Informasi_model->informasi();
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('informasi/tambah', $data);
            $this->load->view('templates/footer');
        } else {
            $judul = $this->input->post('judul');
            $isi = $this->input->post('isi');
            $id_informasi = $this->input->post('id_informasi');

            $config['allowed_types'] = 'gif|jpg|jpeg|png';
            $config['max_size']     = '2048';
            $config['upload_path'] = './assets/img/informasi';
            $config['file_name'] = $_FILES['gambar']['name'];

            $this->load->library('upload', $config);
            // $this->load->initialize($config);
            if (!empty($_FILES['gambar']['name'])) {
                if ($this->upload->do_upload('gambar')) {
                    $foto = $this->upload->data();
                    $data = array(
                        'judul' => $judul,
                        'gambar' => $foto['file_name'],
                        'isi' => $isi,
                    );
                    $this->Informasi_model->masuk($data);
                    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Data berhasil di simpan
          </div>');
                    redirect('informasi');
                } else {
                    die('gagal upload');
                }
            } else {
                echo "tidak masuk";
            }

            $info = [
                'judul' => $judul,
                'isi' => $isi
            ];
            $this->db->set($info);
            $this->db->where('id_informasi', $id_informasi);
            $this->db->insert('informasi');
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Data berhasil di simpan
          </div>');
            redirect('informasi');
        }
    }

    public function hapus($id_informasi)
    {
        // $this->Informasi_model->hapus($id_informasi);
        // redirect('informasi');

        $data = $this->Informasi_model->getId($id_informasi)->row_array();
        $nama = './assets/img/informasi/' . $data['gambar'];

        if (is_readable($nama) && unlink($nama)) {
            $this->Informasi_model->hapus($id_informasi);
            redirect('informasi');
        } else {
            $this->Informasi_model->hapus($id_informasi);
            redirect('informasi');
        }
    }

    public function detail($id_informasi)
    {

        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $data['informasi'] = $this->Informasi_model->detail($id_informasi);
        $data['title'] = 'Detail Informasi';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('informasi/detail', $data);
        $this->load->view('templates/footer');
    }

    public function ubah($id_informasi)
    {
        $kondisi = array('id_informasi' => $id_informasi);
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $data['informasi'] = $this->Informasi_model->ambil($kondisi);
        $data['title'] = 'Ubah Informasi';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('informasi/ubah', $data);
        $this->load->view('templates/footer');
    }

    public function update()
    {
        $id_informasi = $this->input->post('id_informasi');
        $judul = $this->input->post('judul');
        $isi = $this->input->post('isi');

        $path = './assets/img/informasi/';
        $kondisi = array('id_informasi' => $id_informasi);

        //ambil data foto
        $config['allowed_types'] = 'gif|jpg|jpeg|png';
        $config['max_size']     = '4096';
        $config['upload_path'] = './assets/img/informasi';
        $config['file_name'] = $_FILES['gambar']['name'];

        $this->load->library('upload', $config);

        if (!empty($_FILES['gambar']['name'])) {
            if ($this->upload->do_upload('gambar')) {

                $foto = $this->upload->data();
                $data = array(
                    'judul' => $judul,
                    'isi' => $isi,
                    'gambar' => $foto['file_name']
                );
                //hapus gambar pada direktori

                @unlink(FCPATH . $path . $this->input->post('filelama'));

                $this->Informasi_model->ubah($data, $kondisi);
                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Data berhasil di perbaharui
          </div>');
                redirect('informasi');
            } else {
                die('gagal update');
            }
        } else {
            echo "tidak masuk";
        }
        //ambil informasi pada input an
        $info = [
            'judul' => $judul,
            'isi' => $isi
        ];
        $this->db->set($info);
        $this->db->where('id_informasi', $id_informasi);
        $this->db->update('informasi');
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
        Data berhasil di perbaharui
      </div>');
        redirect('informasi');
    }
}
