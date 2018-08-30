<html>
<head>
	<title></title>
</head>
<body>
<table border="1">
	<tr>
		<td>Nama Pegawai</td>
		<td>Jumlah hari telat</td>
		<td>Jumlah hari telat diganti</td>
		<td>Detail</td>
	</tr>
	<?php for ($i=0; $i <count($user) ; $i++) { ?>
		<tr>
			<td><?=$user[$i]['id_user']; ?></td>
			<td><?=$user[$i]['nama_user']; ?></td>
			<td><?=$user[$i]['nama_user']; ?></td>
			<td><?=$user[$i]['nama_user']; ?></td>
		</tr>
	<?php } ?>
</table>
</body>
</html>