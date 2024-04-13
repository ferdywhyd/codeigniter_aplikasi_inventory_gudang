<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Barangmasuk extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        cek_login();

        $this->load->model('Admin_model', 'admin');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $data['title'] = "BARANG MASUK";
        $data['barangmasuk'] = $this->admin->getBarangMasuk();
        $data['barangmasuk_user'] = $this->admin->barangmasuk_user();
        $this->template->load('templates/dashboard', 'barang_masuk/data', $data);
    }

    private function _validasi()
    {
        $this->form_validation->set_rules('tanggal_masuk', 'Tanggal Masuk', 'required|trim');
        $this->form_validation->set_rules('supplier_id', 'Supplier', 'required');
        $this->form_validation->set_rules('barang_id', 'Barang', 'required');
        $this->form_validation->set_rules('jumlah_masuk', 'Jumlah Masuk', 'required|trim|numeric|greater_than[0]');
    }

    public function add()
    {
        $this->_validasi();
        if (is_admin()) {
            if ($this->form_validation->run() == false) {
                $data['title'] = "BARANG MASUK";
                $data['supplier'] = $this->admin->get('supplier');
                $joinuser = $this->db->join('user', 'user_id = id_user');
                $sort = $this->db->order_by('id_user', 'DESC');
                $data['barang'] = $this->admin->get('barang',null ,['stok ==' == 0], $sort, $joinuser);
    
                // Mendapatkan dan men-generate kode transaksi barang masuk
                $kode = 'T-BM-' . date('ymd');
                $kode_terakhir = $this->admin->getMax('barang_masuk', 'id_barang_masuk', $kode);
                $kode_tambah = substr($kode_terakhir, -5, 5);
                $kode_tambah++;
                $number = str_pad($kode_tambah, 5, '0', STR_PAD_LEFT);
                $data['id_barang_masuk'] = $kode . $number;
    
                $this->template->load('templates/dashboard', 'barang_masuk/add', $data);
            } else {
                $input = $this->input->post(null, true);
                $insert = $this->admin->insert('barang_masuk', $input);
    
                if ($insert) {
                    set_pesan('data berhasil disimpan.');
                    redirect('barangmasuk');
                } else {
                    set_pesan('Opps ada kesalahan!');
                    redirect('barangmasuk/add');
                }
            }
        } else {
            if ($this->form_validation->run() == false) {
                $data['title'] = "BARANG MASUK";
                $data['supplier'] = $this->admin->get('supplier');
                $userId = $this->session->userdata('login_session')['user'];
                $joinuser = $this->db->join('user', 'user_id = id_user');
                $sort = $this->db->order_by('id_user', 'DESC');
                $login = $this->db->where('id_user', $userId);
                $data['barang'] = $this->admin->get('barang',null ,['stok =' == 0], $login, $sort, $joinuser);
    
                // Mendapatkan dan men-generate kode transaksi barang masuk
                $kode = 'T-BM-' . date('ymd');
                $kode_terakhir = $this->admin->getMax('barang_masuk', 'id_barang_masuk', $kode);
                $kode_tambah = substr($kode_terakhir, -5, 5);
                $kode_tambah++;
                $number = str_pad($kode_tambah, 5, '0', STR_PAD_LEFT);
                $data['id_barang_masuk'] = $kode . $number;
    
                $this->template->load('templates/dashboard', 'barang_masuk/add', $data);
            } else {
                $input = $this->input->post(null, true);
                $insert = $this->admin->insert('barang_masuk', $input);
    
                if ($insert) {
                    set_pesan('data berhasil disimpan.');
                    redirect('barangmasuk');
                } else {
                    set_pesan('Opps ada kesalahan!');
                    redirect('barangmasuk/add');
                }
            }
        }
    }

    public function delete($getId)
    {
        $id = encode_php_tags($getId);
        if ($this->admin->delete('barang_masuk', 'id_barang_masuk', $id)) {
            set_pesan('data berhasil dihapus.');
        } else {
            set_pesan('data gagal dihapus.', false);
        }
        redirect('barangmasuk');
    }
}
