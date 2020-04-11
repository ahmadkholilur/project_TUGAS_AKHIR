<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengguna extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('role_id') == 1) {
            redirect('auth/blocked');
        } elseif (!$this->session->userdata('email')) {
            redirect('auth');
        };


        $this->load->model('Informasi_model');
        $this->load->model('Pelayanan_model');
    }
    public function index()
    {
        $data['title'] = 'Halaman Utama';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $this->load->view('templates/header', $data);
        $this->load->view('templates/topbar_pengguna', $data);
        $this->load->view('pengguna/index', $data);
        $this->load->view('templates/footer_pengguna');
    }

    public function informasi()
    {
        $data['title'] = 'Informasi';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $config['base_url'] = site_url('pengguna/informasi');
        $config['total_rows'] = $this->Informasi_model->jumlah();
        $config['per_page'] = 6;

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
        $data['informasi'] = $this->Informasi_model->getInformasi($config['per_page'], $data['start']);
        $data['total_rows'] = $config['total_rows'];

        if ($this->input->post('cari')) {
            $data['informasi'] = $this->Informasi_model->cari();
        }

        // $data['informasi'] = $this->Informasi_model->informasi();
        $this->load->view('templates/header', $data);
        // $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar_pengguna', $data);
        $this->load->view('pengguna/informasi', $data);
        $this->load->view('templates/footer_pengguna');
    }

    public function detail($id_informasi)
    {
        $data['title'] = 'Informasi berita';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $data['informasi'] = $this->Informasi_model->detail($id_informasi);
        $this->load->view('templates/header', $data);
        // $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar_pengguna', $data);
        $this->load->view('pengguna/detail', $data);
        $this->load->view('templates/footer_pengguna');
    }



    public function user()
    {
        $data['title'] = 'Profil';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('templates/header', $data);
        // $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar_pengguna', $data);
        $this->load->view('pengguna/user', $data);
        $this->load->view('templates/footer_pengguna');
    }

    public function password()
    {
        $data['title'] = 'Ganti Password';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $this->form_validation->set_rules('current_password', 'Password Lama', 'required|trim');
        $this->form_validation->set_rules('password1', 'Password Baru', 'required|trim|min_length[6]|matches[password2]');
        $this->form_validation->set_rules('password2', 'Ulangi password', 'required|trim|min_length[6]|matches[password1]');

        if ($this->form_validation->run() == false) {

            $this->load->view('templates/header', $data);
            // $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar_pengguna', $data);
            $this->load->view('pengguna/password', $data);
            $this->load->view('templates/footer_pengguna');
        } else {
            $current_password = $this->input->post('current_password');
            $password1 = $this->input->post('password1');
            if (!password_verify($current_password, $data['user']['password'])) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
            Password salah
          </div>');
                redirect('pengguna/password');
            } else {
                if ($current_password == $password1) {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
            Password tidak sinkron
          </div>');
                    redirect('pengguna/password');
                } else {
                    //password sudah benar
                    $password_hash = password_hash($password1, PASSWORD_DEFAULT);

                    $this->db->set('password', $password_hash);
                    $this->db->where('email', $this->session->userdata('email'));
                    $this->db->update('user');

                    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Password berhasil di perbaharui
          </div>');
                    redirect('pengguna/user');
                }
            }
        }
    }

    public function edit()
    {
        $data['title'] = 'Edit Profil';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $this->form_validation->set_rules('name', 'Nama Lengkap', 'required|trim');
        if ($this->form_validation->run() == false) {

            $this->load->view('templates/header', $data);
            // $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar_pengguna', $data);
            $this->load->view('pengguna/edit', $data);
            $this->load->view('templates/footer_pengguna');
        } else {
            $name = $this->input->post('name');
            $email = $this->input->post('email');


            //jika ada gambar yang di upload
            $upload_image = $_FILES['image']['name'];
            if ($upload_image) {
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size']     = '2048';
                $config['upload_path'] = './assets/img/profil';


                $this->load->library('upload', $config);

                if ($this->upload->do_upload('image')) {
                    $old_image = $data['user']['image'];
                    if ($old_image != 'default.jpg') {

                        unlink(FCPATH . 'assets/img/profil/' . $old_image);
                    }


                    $new_image = $this->upload->data('file_name');
                    $this->db->set('image', $new_image);
                } else {
                    echo $this->upload->display_errors();
                }
            }

            $this->db->set('name', $name);
            $this->db->where('email', $email);
            $this->db->update('user');

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Data berhasil di perbaharui
          </div>');
            redirect('pengguna/user');
        }
    }

    public function pelayanan()
    {
        $data['title'] = 'Pelayanan';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        $data['pelayanan'] = $this->Pelayanan_model->pelayanan();
        $this->load->view('templates/header', $data);
        // $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar_pengguna', $data);
        $this->load->view('pengguna/pelayanan', $data);
        $this->load->view('templates/footer_pengguna');
    }

    public function tambah()
    {
        $data['title'] = 'Pelayanan';
        $this->form_validation->set_rules('nama', 'Nama', 'required', [
            'required' => 'nama harus di isi'
        ]);

        if ($this->form_validation->run() == false) {
            // $data['user'] = $this->db->get('user')->row_array();
            $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
            $data['pelayanan'] = $this->Pelayanan_model->pelayanan();
            // $data['user'] = $this->Informasi_model->informasi();
            $this->load->view('templates/header', $data);
            // $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar_pengguna', $data);
            $this->load->view('pengguna/pelayanan', $data);
            $this->load->view('templates/footer_pengguna');
        } else {
            $nama = $this->input->post('nama');
            $jenis = $this->input->post('jenis');
            $email = $this->input->post('email');
            $nomor = $this->input->post('nomor');
            $keterangan = $this->input->post('keterangan');
            $status = $this->input->post('status');
            // $isi = $this->input->post('isi');
            $id_pelayanan = $this->input->post('id_pelayanan');

            $config['allowed_types'] = 'gif|jpg|jpeg|png|rar|zip|pdf';
            $config['max_size']     = '4096';
            $config['upload_path'] = './assets/img/pelayanan';
            $config['file_name'] = $_FILES['berkas']['name'];

            $this->load->library('upload', $config);
            // $this->load->initialize($config);
            if (!empty($_FILES['berkas']['name'])) {
                if ($this->upload->do_upload('berkas')) {
                    $foto = $this->upload->data();
                    $data = array(
                        'nama' => $nama,
                        'jenis' => $jenis,
                        'email' => $email,
                        'nomor' => $nomor,
                        'keterangan' => $keterangan,
                        'status' => $status,
                        'berkas' => $foto['file_name'],
                        // 'isi' => $isi,
                    );
                    $this->Pelayanan_model->masuk($data);
                    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
                Data berhasil di simpan
              </div>');
                    redirect('pengguna/pelayanan');
                } else {
                    die('gagal upload');
                }
            } else {
                echo "tidak masuk";
            }

            $info = [
                'nama' => $nama,
                'jenis' => $jenis,
                'email' => $email,
                'nomor' => $nomor,
                'keterangan' => $keterangan,
                'status' => $status,
            ];
            $this->db->set($info);
            $this->db->where('id_pelayanan', $id_pelayanan);
            $this->db->insert('pelayanan');
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Data berhasil di simpan
          </div>');
            redirect('pengguna/pelayanan');
        }
    }

    public function hasil()
    {
        $data['title'] = 'Hasil Pelayanan';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $config['base_url'] = site_url('pengguna/hasil');
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
            $data['pelayanan'] = $this->Pelayanan_model->cari();
        }

        $this->load->view('templates/header', $data);
        // $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar_pengguna', $data);
        $this->load->view('pengguna/hasil', $data);
        $this->load->view('templates/footer_pengguna');
    }

    public function tentang()
    {
        $data['title'] = 'Tentang Lingkungan RT 01 ';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();
        // $data['pelayanan'] = $this->Pelayanan_model->pelayanan();
        $this->load->view('templates/header', $data);
        // $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar_pengguna', $data);
        $this->load->view('pengguna/tentang', $data);
        $this->load->view('templates/footer_pengguna');
    }
}
