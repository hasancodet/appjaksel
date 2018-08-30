<html>
<head>
	<title></title>
</head>
<body>
<h2>Nama :<?=$absen[0]['nama_user'];?></h2>
<table border="1">
	<tr>
		<td>tanggal</td>
		<td>jam masuk</td>
		<td>jam pulang</td>
		<td>Keterangan</td>
	</tr>
	<?php
		for ($i=0; $i <count($absen) ; $i++) {
				$id_hari = date('w', strtotime($absen[$i]['tanggal']));
				$tanggal = date('Y-m-d', strtotime($absen[$i]['tanggal']));
				$jam_masuk = date('H:i:s', strtotime($absen[$i]['jam_masuk']));
				$jam_pulang = date('H:i:s', strtotime($absen[$i]['jam_pulang']));
	?>
	<tr>
		<td><?=$tanggal;?></td>
		<td><?=$jam_masuk;?></td>
		<td><?=$jam_pulang;?></td>
		<td><?php if($jam_masuk > '07:30:00'){echo "telat";};?></td>
	</tr>
	<?php			
				// print_r($this->db->query("select nama_hari from rule_hari where id_hari = '$tanggal'")->result_array());
			}
	?>
	
</table>
</body>
</html>