<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('m_model');
		$this->load->helper('my_helper');
	}

	public function index()
	{
		$this->load->view('auth/login');
	}

	public function login()
	{
		$username  = $this->input->post('username', true);
		$password  = $this->input->post('password', true);

		$data = ['username' => $username];
		$query = $this->m_model->get_where('user', $data);
		$result = $query->row_array();

		if (empty($result)) {
			$this->session->set_flashdata('error', 'Username tidak ditemukan!');
			redirect(base_url());
			return;
		}

		if (md5($password) !== $result['password']) {
			$this->session->set_flashdata('error', 'Password salah!');
			redirect(base_url());
			return;
		}

		$lokasi = $this->db->get_where('lokasi', ['id' => $result['lokasi_id']])->row_array();

		$data = [
			'logged_in'     => TRUE,
			'username'      => $result['username'],
			'jabatan'       => $result['jabatan'],
			'role'          => $result['role'],
			'id'            => $result['id'],
			'lokasi_id'     => $result['lokasi_id'],
			'jam_kerja'     => $result['jam_kerja'],
			'lokasi_nama'   => $lokasi['nama_lokasi'] ?? null,
			'lokasi_lat'    => $lokasi['latitude'] ?? null,
			'lokasi_lng'    => $lokasi['longitude'] ?? null,
			'lokasi_radius' => $lokasi['radius_meter'] ?? null,
		];

		$this->session->set_userdata($data); 
		redirect(base_url('user'));
	}

	public function register()
	{
		$data['lokasi'] = $this->m_model->get_data('lokasi')->result();
		$this->load->view('auth/register', $data);
	}

	public function aksi_register() {
		$nama      = $this->input->post('nama', true);
		$username  = $this->input->post('username', true);
		$password  = $this->input->post('password', true);
		$jam_kerja = $this->input->post('jam_kerja', true);
		$posisi    = $this->input->post('posisi', true);
		$lokasi_id = $this->input->post('lokasi', true);

		$data = ['username' => $username];
		$query = $this->m_model->get_where('user', $data);

		if ($query->num_rows() > 0) {
			$this->session->set_flashdata('error', 'Username sudah digunakan!');
			redirect(base_url('auth/register'));
			return;
		}

		$data = [
			'nama'      => $nama,
			'username'  => $username,
			'password'  => md5($password),
			'jam_kerja' => $jam_kerja,
			'jabatan'      => $posisi,
			'role'      => 'user',
			'lokasi_id' => $lokasi_id
		];

		$this->m_model->tambah_data('user', $data);
		redirect(base_url('auth'));
	}

	function logout(){
		$this->session->sess_destroy();
		redirect(base_url('auth'));
	}
}