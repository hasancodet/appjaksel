<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Absen 
        <small>Realtime</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Level</a></li>
        <li class="active">Here</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">
      <!-- <a class="button" href="../fingerprint/clearAbsenData">Clear</a> -->
      <div class="box">
        <table class="table">
          <tr>
            <td>Id Pegawai</td>
            <td>Tanggal</td>
            <td>Jam Masuk</td>
            <td>Jam Pulang</td>
          </tr>
          <?php 
          if($laporan_all!=null){
            for ($i=0; $i <count($laporan_all) ; $i++) { ?>
          <tr>
            <td><?=$laporan_all[$i]['id_user']?></td>
            <td><?=$laporan_all[$i]['tanggal']?></td>
            <td><?=$laporan_all[$i]['jam_masuk']?></td>
            <td><?=$laporan_all[$i]['jam_pulang']?></td>
          </tr>
          <?php
           }
          } 
          ?>
        </table>
        
      </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->