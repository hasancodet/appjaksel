<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		// $this->load->view('welcome_message');
		//$this->load->view('home');
		echo "halo";
	}

	public function test(){
		$this->db->select('user.*');
		$this->db->join('bidang_pegawai', 'user.id_user = bidang_pegawai.id_pegawai');
		$this->db->join('bidang', 'bidang.id_kepala_bidang = 222');
		print_r($this->db->get('user')->result_array());
	}

	public function login(){

	}
}
