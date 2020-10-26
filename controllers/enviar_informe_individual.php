<?php
	require('../../wp-blog-header.php');
	global $wpdb;

	$servername = "000.000.000.000";
	$username = "xxxxxxxxx";
	$password = "xxxxxxxxx";
	$dbname = "xxxxxxxxx";

	$conn = new mysqli($servername, $username, $password, $dbname);
	
	if($_POST['cliente'] && $_POST['fecha']){
		$fecha = $_POST['fecha'];
		$cliente = $_POST['cliente'];
		$sql = "SELECT fecha, informe, cliente FROM xxxxxx.informes_enviados where fecha like '%$fecha%' and cliente like '%$cliente%'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$datos[] = $row;
					$asunto = 'IMPORTANTE : RRG - Reporte Resumen Global';
					$headers[] = 'Bcc: xxxxxx@gmail.com';
					//$headers = '';
					//wp_mail('alejandro@grupodeboss.com', $asunto, $row['informe'],$headers);
					wp_mail($row['cliente'], $asunto, $row['informe'],$headers);
					
				}
				echo true;
		} else {
			echo null;
		}
		
	}
?>
