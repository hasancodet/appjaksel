<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Laporan extends CI_Controller {

	public function index()
	{
		// $this->db->get();
    // echo asset_url()."<br>";
    // echo base_url()."assets/Admin";
		$this->load->view('template/header');
    $this->load->view('template/sidebar');
    $this->load->view('view_laporan_all');
		$this->load->view('template/footer');
	}

	public function hari(){
		$this->db->order_by('id_hari','asc');
		return $this->db->get('rule_hari')->result_array();
	}
	public function test(){
		$hari = $this->hari();
		$this->db->select('*');
		$this->db->join('bidang_pegawai', 'user.id_user = bidang_pegawai.id_pegawai');
		$this->db->join('bidang', 'bidang.id_kepala_bidang = 222');
		// $this->db->join('absen', 'absen.id_pegawai = user.id_user');
		$data['user'] = $this->db->get('user')->result_array();
		
		for ($i=0; $i <count($data['user']) ; $i++) { 
			$data['user'][$i]['jumlah_telat_hari'] = 0;
			$this->db->where('id_pegawai', $data['user'][$i]['id_pegawai']);
			$absen = $this->db->get('absen')->result_array();

			$id_hari = date('w', strtotime($absen[$i]['tanggal']));
			$tanggal = date('Y-m-d', strtotime($absen[$i]['tanggal']));
			$jam_masuk = date('H:i:s', strtotime($absen[$i]['jam_masuk']));
			$jam_pulang = date('H:i:s', strtotime($absen[$i]['jam_pulang']));
			
			if($jam_masuk > $hari[$id_hari]['jam_masuk']){
				$data['user'][$i]['jumlah_telat_hari']++;
			}

			// $data['user'][$i]['absen'] =$this->db->get('absen')->result_array();
		}
		print_r($data['user']);
		// $this->load->view('laporan', $data);
	}

	public function test2($id_pegawai, $tanggal_awal, $tanggal_akhir){
		$absen = $this->db->query("SELECT absen.*, nama_user FROM `absen`, user where id_pegawai = '$id_pegawai' and user.id_user = absen.id_pegawai and tanggal >= '$tanggal_awal' and tanggal <= '$tanggal_akhir' order by tanggal asc")->result_array();
		for ($i=0; $i <count($absen) ; $i++) {
			$id_hari = date('w', strtotime($absen[$i]['tanggal']));
			$tanggal = date('Y-m-d', strtotime($absen[$i]['tanggal']));
			$jam_masuk = date('H:i:s', strtotime($absen[$i]['jam_masuk']));
			$jam_pulang = date('H:i:s', strtotime($absen[$i]['jam_pulang']));
			
			// print_r($this->db->query("select nama_hari from rule_hari where id_hari = '$tanggal'")->result_array());
		}
		$data['absen'] = $absen;
		$this->load->view('laporan_pegawai',$data);
	}

  public function absenAll(){
    $this->db->order_by('tanggal', 'DESC');
    $data['laporan_all'] = $this->db->get('absen')->result_array();
    $this->load->view('template/header');
    $this->load->view('template/sidebar');
    $this->load->view('view_laporan_all', $data);
    $this->load->view('template/footer');
  }
}
