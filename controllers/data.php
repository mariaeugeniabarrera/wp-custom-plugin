<?php
	$servername = "200.110.135.126";
	$username = "tradem_user_et";
	$password = "2EeHrkIA";
	$dbname = "tradem_et";

	$conn = new mysqli($servername, $username, $password, $dbname);

	function utf8ize($d) {
		if (is_array($d)) {
			foreach ($d as $k => $v) {
				$d[$k] = utf8ize($v);
			}
		} else if (is_string ($d)) {
			return utf8_encode($d);
		}
		return $d;
	}
	
	switch ($_POST['data']) // ||   $_GET['data']
	{
		case 'clientes_detailed_data':
			$sql = "SELECT id,nombre,url,emails,count_espacio,count_design,count_style,habilitado_informe FROM tradem_et.nuke_clientes order by id asc";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$datos[] = $row;
				}
				echo json_encode(utf8ize($datos));
			} else {
				echo null;
			}
			break;
		
		case 'get_banners':
			$sql = "select cliente, contador_design, contador_espacio, contador_style, fecha_inicio, banner as no_banner FROM tradem_et.wp_stats_banners";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$datos[] = $row;
				}
				echo json_encode(utf8ize($datos));
			} else {
				echo null;
			}
			break;
		
		case 'login':
			if($_POST['usuario'] && $_POST['password'])
			{
				$usuario = $_POST['usuario'];
				$password = $_POST['password'];
				$admin = 0;
				$sql = "SELECT * FROM tradem_et.wp_users WHERE user_login = '$usuario' AND user_pass = '$password'";
				//echo $sql;
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$datos[] = $row;
						// save session cookie
						setcookie("session", $usuario, time() + (86400 * 30), "/");
					}
					echo true;
				} else {
					echo false;
				}
			}
			break;
		
		case 'logout':
			if($_COOKIE['session']){
				$session = $_COOKIE['session'];
				setcookie('session', null, -1, '/');
			}
			echo json_encode(0);
			break;
		
		case 'ver_informe_enviado':
			if($_POST['fecha'] && $_POST['cliente']){
				$fecha = $_POST['fecha'];
				$cliente = $_POST['cliente'];
				$sql = "SELECT fecha, informe FROM tradem_et.informes_enviados where fecha like '%$fecha%' and cliente like '%$cliente%'";
				//echo $sql;
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$datos[] = $row;
					}
					echo json_encode(utf8ize($datos));
					//echo json_encode($datos, JSON_HEX_QUOT | JSON_HEX_TAG);
				} else {
					echo null;
				}
			}
			break;
		
		case 'ver_informes_enviados':
			if($_POST['fecha']){
				$fecha = $_POST['fecha'];
				$sql = "SELECT fecha, informe,cliente FROM tradem_et.informes_enviados where fecha like '%$fecha%'";
				//echo $sql;
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$datos[] = $row;
					}
					echo json_encode(utf8ize($datos));
					//echo json_encode($datos, JSON_HEX_QUOT | JSON_HEX_TAG);
				} else {
					echo null;
				}
			}
			break;
		
		case 'update_habilitado_informe':
			if($_POST['id_cliente'] && $_POST['habilitado']){
				$id_cliente = $_POST['id_cliente'];
				$habilitado = $_POST['habilitado'];
				$sql = "update tradem_et.nuke_clientes set habilitado_informe = $habilitado where id = $id_cliente";
				$result = $conn->query($sql);
				if ($result != true) {
					echo $sql . '  ' . $conn->error . " $habilitado ";
				}else{
					echo json_encode($result . " $habilitado ");
				}
			}
			break;
		
		/*case 'clientes_detailed_data':
			if($_POST['page']){
				$page = $_POST['page'];
				$page_start = ($page - 1) * 10;
				$page_end = $page * 10;
				$sql = "SELECT id,nombre,url,emails,count_espacio,count_design,count_style FROM tradem_et.nuke_clientes WHERE id >= $page_start AND id <= $page_end order by id asc";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$datos[] = $row;
					}
					echo json_encode(utf8ize($datos));
				} else {
					echo null;
				}
			}
			break;*/
	
		case 'contador_banners':
			if($_POST['mes'] && $_POST['anio'] && $_POST['zona']){
				$mes = $_POST['mes'];
				$anio = $_POST['anio'];
				$zona = $_POST['zona'];
				$sql = "SELECT contador FROM tradem_et.wp_stats WHERE mes=$mes AND anio=$anio AND zona = '$zona'";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$datos[] = $row;
					}
					echo json_encode(utf8ize($datos));
				} else {
					echo null;
				}
			}
			break;
		
		case 'contador_banners_sin_zona':
			if($_POST['mes'] && $_POST['anio']){
				$mes = $_POST['mes'];
				$anio = $_POST['anio'];
				$sql = "SELECT * FROM tradem_et.wp_stats where mes = $mes and anio = $anio";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$datos[] = $row;
					}
					echo json_encode(utf8ize($datos));
				} else {
					echo null;
				}
			}
			break;
		
		case 'notas_sitio':
			if($_POST['cliente_id']){
				$cliente_id = $_POST['cliente_id'];
				$sql = "SELECT post_id,meta_key,meta_value FROM tradem_et.postmeta WHERE meta_key = 'cliente' and meta_value = '$cliente_id' ORDER BY post_id DESC";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$datos[] = $row;
					}
					echo json_encode(utf8ize($datos));
				} else {
					echo null;
				}
			}
			break;
		
		case 'notas_sitio_all':
			if($_POST['mes'] && $_POST['anio']){
				$mes = $_POST['mes'];
				$anio = $_POST['anio'];
				//echo "Nota Sitio All";
				$datos = '';
				$sql = "SELECT post_id,meta_key,meta_value,nombre,post_title,post_date,post_status FROM tradem_et.wp_postmeta pm left join tradem_et.nuke_clientes nc on pm.meta_value = nc.id left join tradem_et.wp_posts ps on ps.ID = pm.post_id WHERE pm.meta_key = 'cliente' AND post_date like '%$anio-$mes%'";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
							$datos[] = $row;
					}
					echo json_encode(utf8ize($datos));
				} else {
					echo null;
				}
			}
			break;
		
		case 'cantidad_vistas_notas_del_sitio':
			if($_POST['anio']){
				$anio = $_POST['anio'];
				$sql = "SELECT post_id, meta_value as cantidad_vistas, post_date, post_title, post_status, guid FROM tradem_et.wp_postmeta pm left join tradem_et.wp_posts ps on ps.ID = pm.post_id where meta_key = 'views' and ps.post_date like '$anio-%'";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
							$datos[] = $row;
					}
					echo json_encode(utf8ize($datos));
				} else {
					echo null;
				}
			}
			break;
			
		case 'estadisticas':
			if($_POST['cliente_id'] && $_POST['sitio']){
				$cliente_id = $_POST['cliente_id'];
				$sitio = $_POST['sitio'];
				$sql = "SELECT * FROM tradem_et.nuke_clientes_stats WHERE id_cliente='$cliente_id' AND sitio='$sitio' and titulo<>'' ORDER BY id DESC";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						$datos[] = $row;
					}
					echo json_encode(utf8ize($datos));
				} else {
					echo null;
				}
			}
			break;
			
		case 'enviar_informe':
			if($_POST['mes'] && $_POST['anio']){ // $_POST['mes'] && $_POST['anio'] && $_POST['template_html']
				$mes = $_POST['mes'];
				$anio = $_POST['anio'];
				$template = $_POST['template_html'];
				$title_informe = 'RRG - Reporte Resumen Global';
				/* Procesar todo el informe */
				$sql = "SELECT id,nombre,url,emails,count_espacio,count_design,count_style FROM tradem_et.nuke_clientes WHERE habilitado_informe = 1"; /* Recorro los clientes habilitados para enviar el informe */
				$result0 = $conn->query($sql);
				//echo $sql . '  ' . $conn->error;
				if ($result0->num_rows > 0) {
					while($row0 = $result0->fetch_assoc()) {
						//$datos[] = $row;
						
						/* Clientes habilitados */
							
							// Imprimimos Cabecera Informe
							$imagen = "url('../images/cabezal.jpg')";
							$cabecera = '<div id="cabecera" style="height:129px;width:615px;background-image:'.$imagen.';position:relative;">
							<div id="texto_cabecera" style="position:absolute;bottom:0px;left:0px;color:#fff;display:inline-block;">
							<div id="titulo_informe" style="display:inline-block;">'.$title_informe.'</div> - 
							<div id="cliente" style="font-weight:bold;display:inline-block;">'.$row0['nombre'].'</div>
							</div>
							<div id="mes_anio" style="position:absolute;bottom:0px;right:0px;color:#fff;display:inline-block;">'.$mes. ' ' .$anio.'</div>
							</div>';
							echo $cabecera;
							
							$reporte_apertura_notas = '<div id="reporte_apertura_notas" style="width:530px;padding:40px;" align="center"><span style="font-weight:bold;">RAN - Reporte Apertura de Notas</span>
							El siguiente listado expresa las notas del cliente que se encuentran activas y comunicando, junto con sus correspondientes aperturas obtenidas hasta el momento.</div>';
							echo $reporte_apertura_notas;
							
							$emails[] = $row0['emails'];
							$cliente = $row0['id'];
							echo 'Cliente Id ' . $row0['id'] . '<br/>';
							echo 'Cliente Email ' . $row0['emails'] . '<br/>';
							
							/* Contar Micropixeles */
							
								$micropixel = 0;
								$micropixel_home = 0;
								$q_micro_home = "SELECT contador FROM tradem_et.wp_stats WHERE mes=$mes AND anio =$anio AND zona = 'home'";
								$result = $conn->query($q_micro_home);
								if ($result->num_rows > 0) {
									while($row = $result->fetch_assoc()) {
										$micropixel_home = $row['contador'];
									}
								}
								$q_micro_footer = "SELECT contador FROM tradem_et.wp_stats WHERE mes=$mes AND anio=$anio AND zona = 'footer'";
								$micropixel_footer = 0;
								$result = $conn->query($q_micro_home);
								if ($result->num_rows > 0) {
									while($row = $result->fetch_assoc()) {
										$micropixel_footer = $row['contador'];
									}
								}
								echo 'Micropixel Home ' . $micropixel_home . '<br/>';
								echo 'Micropixel Footer ' . $micropixel_footer . '<br/>';
								
								$micropixel_total = $micropixel_home + $micropixel_footer;
								
								echo 'Micropixel Total ' . $micropixel_total . '<br/>';
								
							/* ./Contar Micropixeles */
							
							/* Obtener Notas y Cant. Visitas de Notas por Cliente */
								
								// ESPACIO TRADEM
								$articulos_espacio = '';
								$sql = "select *, meta_value as visitas from (select cli.id as cli_id, post_id, post.post_title from tradem_et.nuke_clientes cli right join tradem_et.wp_postmeta postmeta on postmeta.meta_value = cli.id right join tradem_et.wp_posts post on post.ID = postmeta.meta_value where postmeta.meta_key = 'cliente' and cli.id = $cliente) as tabla left join tradem_et.wp_postmeta pm on pm.post_id = tabla.post_id where pm.meta_key = 'views'";
								$result1 = $conn->query($sql);
								$total_visitas_espacio = 0;
								//echo $sql . '  ' . $conn->error;
								if ($result1->num_rows > 0) {
									while($row1 = $result1->fetch_assoc()) {
										
										$total_visitas_espacio += $row1['visitas'];
										
										/* Resultados */
										/*echo 'Cliente Id ' . $row1['cli_id'] . '</br>';
										echo 'Post Id ' . $row1['post_id'] . '</br>';
										echo 'Post Title ' . $row1['post_title'] . '</br>';
										echo 'Cant. Visitas ' . $row1['visitas'] . '</br>';*/
										
										$articulos_espacio .= '<tr>
										<td><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"> <strong>Espacio Tradem</strong></font></td>
										<td bgcolor="#EEEBEA"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#000" size="2">'.$row1["post_title"].'</font></td>
										<td style="text-align: center"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>'.$row1["visitas"].'</strong></font></td>
										</tr>';
									}
									
								}else{
									$articulos_espacio .= '<tr><td>No hay asignaci&oacute;n de art&iacute;culos</td></tr>';
								}
								
								$tabla_espacio = '<table id="tabla_espacio">
								<thead>
								<th>Sitio</th>
								<th>Nota</th>
								<th>Visitas</th>
								</thead>
								<tbody id="body_tabla_espacio">
								'.$articulos_espacio.'
								</tbody>
								</table>
								';
								echo $tabla_espacio;
								
								// TRADEM DESIGN
								$sql = 'select * from nuke_clientes_stats where id_cliente=$cliente and sitio="design2 and titulo<>""';
								$result2 = $conn->query($sql);
								if ($result1->num_rows > 0) {
									while($row1 = $result1->fetch_assoc()) {
										
										/* Resultados */
										echo 'Cliente Id ' . $row1['cli_id'] . '</br>';
										echo 'Post Id ' . $row1['post_id'] . '</br>';
										echo 'Post Title ' . $row1['post_title'] . '</br>';
										echo 'Cant. Visitas ' . $row1['visitas'] . '</br>';
										
										$articulos_espacio .= '<tr>
										<td><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"> <strong>Espacio Tradem</strong></font></td>
										<td bgcolor="#EEEBEA"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#000" size="2">$row1["post_title"]</font></td>
										<td style="text-align: center"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>$row1["visitas"]</strong></font></td>
										</tr>';
									}
								}
								
							/* ./Obtener Notas y Vistas de Notas por Cliente */
							
						/* ./Clientes habilitados */
						
					}
					//echo json_encode(utf8ize($datos));
				} else {
					echo null;
				}
			}
			break;
	}