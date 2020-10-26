<?php
	require('../../wp-blog-header.php');
	global $wpdb;

	$servername = "200.110.135.126";
	$username = "tradem_user_et";
	$password = "2EeHrkIA";
	$dbname = "tradem_et";

	$conn = new mysqli($servername, $username, $password, $dbname);
	
	if($_POST['cliente'] && $_POST['fecha']){
		$fecha = $_POST['fecha'];
		$cliente = $_POST['cliente'];
		$sql = "SELECT fecha, informe, cliente FROM tradem_et.informes_enviados where fecha like '%$fecha%' and cliente like '%$cliente%'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$datos[] = $row;
					$asunto = 'IMPORTANTE : RRG - Reporte Resumen Global';
					$headers[] = 'Bcc: reportetradem@gmail.com';
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
