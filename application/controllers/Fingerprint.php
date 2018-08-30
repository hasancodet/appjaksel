<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fingerprint extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->library('Lib_fingerprint');
  } 
  
  public function index(){
    echo "string";
    $this->lib_fingerprint->tes();
  }

  public function tarikData($ip_address){
    $update = 0;
    $insert = 0;
    $data = $this->lib_fingerprint->getData($ip_address);
    for ($i=1; $i <count($data)-1 ; $i++) { 
      $datanew[$i]['id_pegawai'] = $data[$i]['id_pegawai'];
      $tanggaljam = explode(" ",$data[$i]['DateTime']);
      $datanew[$i]['tanggal'] = $tanggaljam[0];
      $tanggal = $datanew[$i]['tanggal'];
      if(isset($tanggaljam[1]) && $tanggaljam[1] < '12:00:00'){
        $datanew[$i]['jam_masuk'] = $tanggaljam[1];
      }elseif(isset($tanggaljam[1]) && $tanggaljam[1] > '12:00:00'){
        $datanew[$i]['jam_pulang'] = $tanggaljam[1];
      }
      //update jika tanggal sama
      $this->db->where('tanggal', $datanew[$i]['tanggal']);
      $this->db->where('id_pegawai', $datanew[$i]['id_pegawai']);
      $cek_tanggal = $this->db->get('absen')->result_array();
      if(count($cek_tanggal) > 0){
        $id_pegawai = $datanew[$i]['id_pegawai'];
        $this->db->update('absen', $datanew[$i], "tanggal = '$tanggal' and id_pegawai = '$id_pegawai'");
        $update++;
      }else{
        $this->db->insert('absen', $datanew[$i]);
        $insert++;
      }

      // print_r($data[$i]);
    }
    echo $update." ".$insert;
  }


  public function clearAbsenData(){
    $this->db->empty_table('absen');
    //$data['laporan_all'] = null;
    //redirect('/laporan/absenAll');
    echo "Clear Data Suskes<br><a href='../laporan/absenAll'>Back</a>";
  }
  public function tarikDataAll(){
    $this->db->empty_table('absen_temp');
    $mesin = $this->db->get('mesin_fingerprint')->result_array();
    for ($i=0; $i <count($mesin) ; $i++) { 
      $ip_address = $mesin[$i]['ip_address'];
      if($mesin[$i]['status'] == 1){
        $this->tarikDataPerMesin($ip_address);
      }
    }

    $this->db->query('delete from absen_temp where date(DateTime) != date(now())');
    // $this->db->query("delete from absen_temp where date(DateTime) != '2018-08-27'");
    $this->pindahDataToAbsen2();
  }

  public function tarikDataPerMesin($ip_address){
    $data = $this->lib_fingerprint->getData($ip_address);
    for ($i=1; $i <count($data)-1 ; $i++) { 
      $data_insert[$i]['id_pegawai'] = $data[$i]['id_pegawai'];
      $data_insert[$i]['DateTime'] = $data[$i]['DateTime'];
      $this->db->insert('absen_temp', $data_insert[$i]);
    }
  }

  public function pindahDataToAbsen(){
    // $this->db->order_by('id_pegawai', 'ASC');
    $absen_temp = $this->db->get('absen_temp')->result_array();
    for ($i=0; $i <count($absen_temp) ; $i++) {
      $data_update = array();
      $data_insert = array();
      $DateTime = explode(" ", $absen_temp[$i]['DateTime']);
      unset($absen_temp[$i]['DateTime']);
      $id_pegawai= $absen_temp[$i]['id_pegawai'];
      $tanggal = $DateTime[0];
      $jam = $DateTime[1];
      $this->db->where("id_pegawai = '$id_pegawai'");
      $this->db->where("tanggal = '$tanggal'");
      if($this->db->get('absen')->num_rows() == 0){
        $data_insert['id_pegawai'] = $id_pegawai;
        $data_insert['tanggal'] = $tanggal;
        if($jam < '12:00:00'){
          $data_insert['jam_masuk'] = $jam;
        }else{
          $data_insert['jam_pulang'] = $jam;
        }
        $this->db->insert('absen', $data_insert);
      }else{
        $data_insert['id_pegawai'] = $id_pegawai;
        $data_insert['tanggal'] = $tanggal;
        if($jam < '12:00:00'){
          $this->db->where($data_insert);
          $this->db->where("jam_masuk IS NOT NULL");
          if($this->db->get('absen')->num_rows() == 1){
            $jam_masuk = $this->db->get('absen')->result_array()[0]['jam_masuk'];
            if($jam < $jam_masuk){
              $data_update['jam_masuk'] = $jam;
            }else{
              $data_update['jam_masuk'] = $jam_masuk;
            }
          }else{
            $data_update['jam_masuk'] = $jam;
          }
        }else{
          $this->db->where($data_insert);
          $this->db->where("jam_pulang IS NOT NULL");
          if($this->db->get('absen')->num_rows() == 1){
            $jam_pulang = $this->db->get('absen')->result_array()[0]['jam_pulang'];
            if($jam > $jam_pulang){
              $data_update['jam_pulang'] = $jam;
            }else{
              $data_update['jam_pulang'] = $jam_pulang;
            }
          }else{
            $data_update['jam_pulang'] = $jam;
          }
        }
        // print_r($data_insert);
        // print_r($data_update);
        // echo "<br>";
        $this->db->where($data_insert);
        $this->db->update('absen', $data_update);
      }
      unset($data_insert);
      unset($data_update);
      unset($id_pegawai);
      unset($DateTime);
    }
    echo "sukses<br>";
  }

  public function pindahDataToAbsen2(){
    // $this->db->select('id_pegawai');
    $id_pegawai_arr = $this->db->get('absen_temp')->result_array();
    // $tanggal = date('2018-08-21');
    // $tanggal = date('Y-m-d');
    for ($i=0; $i <count($id_pegawai_arr) ; $i++) { 
      $id_pegawai = $id_pegawai_arr[$i]['id_pegawai'];
      $tanggal = explode(" ", $id_pegawai_arr[$i]['DateTime'])[0];
      $this->db->where("id_pegawai = '$id_pegawai' AND tanggal = '$tanggal'");
      if($this->db->get('absen')->num_rows() == 0){
        $this->db->where("id_pegawai = '$id_pegawai' AND date(DateTime) = '$tanggal'");
        $this->db->select("min(time(DateTime)) as jam_pertama , max(time(DateTime)) as jam_terakhir");
        $jam = $this->db->get('absen_temp')->result_array();
        if($jam[0]['jam_pertama'] < '12:00:00'){
          $jam_masuk = $jam[0]['jam_pertama'];
        }else{
          $jam_masuk = null;
        }
        if($jam[0]['jam_terakhir'] > '12:00:00'){
          $jam_pulang = $jam[0]['jam_terakhir'];
        }else{
          $jam_pulang = null;
        }
        $data_insert = array(
                      'id_pegawai' => $id_pegawai,
                      'tanggal' => $tanggal,
                      'jam_masuk' => $jam_masuk,
                      'jam_pulang' => $jam_pulang
        );
        $this->db->insert('absen', $data_insert);
      }else{
        $this->db->where("id_pegawai = '$id_pegawai' AND date(DateTime) = '$tanggal'");
        $this->db->select("min(time(DateTime)) as jam_pertama , max(time(DateTime)) as jam_terakhir");
        $jam = $this->db->get('absen_temp')->result_array();
        if($jam[0]['jam_pertama'] < '12:00:00'){
          $jam_masuk = $jam[0]['jam_pertama'];
        }else{
          $jam_masuk = null;
        }
        if($jam[0]['jam_terakhir'] > '12:00:00'){
          $jam_pulang = $jam[0]['jam_terakhir'];
        }else{
          $jam_pulang = null;
        }
        $data['id_pegawai'] = $id_pegawai;
        $data['tanggal'] = $tanggal;
        $data_update['jam_masuk'] = $jam_masuk;
        $data_update['jam_pulang'] = $jam_pulang;
        $this->db->where($data);
        $this->db->update('absen', $data_update);
      }
    }
  }
  public function pindahDataToAbsen3(){
    // $this->db->select('id_pegawai');
    $tanggal_awal = date('2018-07-21');
    $tanggal = date('Y-m-d');
    $this->db->where("waktu >= '$tanggal_awal' and  waktu <= '$tanggal'");
    $id_pegawai_arr = $this->db->get('data_absenmesin')->result_array();
    for ($i=0; $i <count($id_pegawai_arr) ; $i++) { 
      $id_pegawai = $id_pegawai_arr[$i]['id_user'];
      $tanggal = explode(" ", $id_pegawai_arr[$i]['waktu'])[0];
      $this->db->where("id_pegawai = '$id_pegawai' AND tanggal ='$tanggal'");
      if($this->db->get('absen')->num_rows() == 0){
        $this->db->where("id_user = '$id_pegawai' AND date(waktu) = '$tanggal'");
        $this->db->select("min(time(waktu)) as jam_pertama , max(time(waktu)) as jam_terakhir");
        $jam = $this->db->get('data_absenmesin')->result_array();
        if($jam[0]['jam_pertama'] < '12:00:00'){
          $jam_masuk = $jam[0]['jam_pertama'];
        }else{
          $jam_masuk = null;
        }
        if($jam[0]['jam_terakhir'] > '12:00:00'){
          $jam_pulang = $jam[0]['jam_terakhir'];
        }else{
          $jam_pulang = null;
        }
        $data_insert = array(
                      'id_pegawai' => $id_pegawai,
                      'tanggal' => $tanggal,
                      'jam_masuk' => $jam_masuk,
                      'jam_pulang' => $jam_pulang
        );
        $this->db->insert('absen', $data_insert);
      }else{
        $this->db->where("id_user = '$id_pegawai' AND date(waktu) = '$tanggal'");
        $this->db->select("min(time(waktu)) as jam_pertama , max(time(waktu)) as jam_terakhir");
        $jam = $this->db->get('data_absenmesin')->result_array();
        if($jam[0]['jam_pertama'] < '12:00:00'){
          $jam_masuk = $jam[0]['jam_pertama'];
        }else{
          $jam_masuk = null;
        }
        if($jam[0]['jam_terakhir'] > '12:00:00'){
          $jam_pulang = $jam[0]['jam_terakhir'];
        }else{
          $jam_pulang = null;
        }
        $data['id_pegawai'] = $id_pegawai;
        $data['tanggal'] = $tanggal;
        $data_update['jam_masuk'] = $jam_masuk;
        $data_update['jam_pulang'] = $jam_pulang;
        $this->db->where($data);
        $this->db->update('absen', $data_update);
      }
    }
  }
  
  public function cek(){
    $data_insert['id_pegawai'] = '112';
    $data_insert['tanggal'] = '2018-08-21';
    $this->db->where($data_insert);
    $jam_masuk = $this->db->get('absen')->result_array()[0]['jam_masuk'];
    echo $jam_masuk;
  }

  public function cek2(){
    print_r($this->db->get('absen_temp')->result_array());    
  }
}
