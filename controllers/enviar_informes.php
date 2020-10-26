<?php

require('../../wp-blog-header.php');
global $wpdb;


$q_clientes = "
    SELECT id as ID, nombre as NOMBRE, url, emails, count_espacio as ESPACIO, count_design as DESIGN, count_style as STYLE
    FROM nuke_clientes WHERE habilitado_informe = 1";

$buscarCliente = $wpdb->get_results($q_clientes);
$cuerpo = '';
$flag_espacio = false;
$flag_design = false;
$flag_style = false;
$sum_notas_espacio = 0;
$sum_notas_design = 0;
$sum_notas_style = 0;

$clientes_footer = array(94,176,82,120,267,294,198,219,283,79,291,300,89,298,290);
$clientes_home = array(56,301,189,32,146,67,83,5,13,151,187,65,64,63,223,138,178,295,236,71);
$clientes_ambos = array(205);

$mes = $_POST['mes'];
$anio = $_POST['anio'];
$mes_text = $_POST['mes_text'];
$emails_informe_enviado = '';
$clientes_informe_enviado = array('<button type="button" class="mi-boton">Informe Copia Enviado</button> <span class="titulo_destino">Reportes Tradem</span> reportetradem@gmail.com');

foreach ($buscarCliente as $key => $cliente) :

// Cambiar el mes
$q_micro_home = "SELECT contador FROM `wp_stats` WHERE mes=$mes AND anio=$anio AND zona = 'home'";
$q_micro_footer = "SELECT contador FROM `wp_stats` WHERE mes=$mes AND anio=$anio AND zona = 'footer'";

$micropixel = 0;
$micropixel_home = $wpdb->get_var($q_micro_home);
$micropixel_footer = $wpdb->get_var($q_micro_footer);
$micropixel_total = $micropixel_home + $micropixel_footer;

    // Destinatarios
    $destinatarios = array_map('trim',explode(",",$cliente->emails));
    foreach($destinatarios as $email) {
        // Comentar para pruebas
        $emails_destinatarios[] = $email;
    }

	// Busco Notas por sitio
	// ET
	$idPostET = array();
	$wpdb->query("SELECT `post_id`,`meta_key`, `meta_value` FROM $wpdb->postmeta 
        WHERE `meta_key` = 'cliente' and meta_value = '".$cliente->ID."' ORDER BY `post_id` DESC ");
    foreach($wpdb->last_result as $k => $v){
    	$idPostET[] = $v->post_id;
    }
    $articulos_espacio = "";
    if (!empty($idPostET)) :
    	// Buscamos las notas ET
    	query_posts( array( 'post_type' => 'post', 'post__in' => $idPostET ) );
    	if (have_posts()) :
    		while (have_posts()) : 
    			the_post();
    			$sum_notas_espacio = $sum_notas_espacio + get_post_meta(get_the_ID(),'views',true);
    			$articulos_espacio .= '<tr>
                <td><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"> <strong>Espacio Tradem</strong></font></td>
                <td bgcolor="#EEEBEA"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#000" size="2">'.get_the_title().'</font></td>
                <td style="text-align: center"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>'.get_post_meta(get_the_ID(),'views',true).'</strong></font></td>
                </tr>';
    		endwhile;
    		$flag_espacio = true;
    	else:
    		$articulos_espacio .= '<tr><td>No hay asignaci&oacute;n de art&iacute;culos</td></tr>';
    	endif;
    	wp_reset_query();


    endif;

    // Buscamos stats de los clientes para td
    $wpdb->query("SELECT * FROM nuke_clientes_stats
            WHERE id_cliente='".$cliente->ID."' AND sitio='design' and titulo<>'' ORDER BY id DESC");
    $sum_notas_design = 0;
    $articulos_design = "";
    //$cant_stats = $db->sql_numrows($buscar_clientes_stats);
    foreach($wpdb->last_result as $kdesign => $vdesign){
        $flag_design = true;
        $cont_design = ($vdesign->contador_leermas==0) ? '--': $vdesign->contador_leermas;
        $sum_notas_design = $sum_notas_design + $vdesign->contador_leermas;
        $articulos_design .= '<tr>
                            <td><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"> <strong>Tradem Design</strong></font></td>
                            <td bgcolor="#EEEBEA"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#000" size="2">'.utf8_decode($vdesign->titulo).'</font></td>
                            <td style="text-align: center"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>'.$cont_design.'</strong></font></td>
                            </tr>';
        unset($cont_design);
    }


    // Buscamos stats de los clientes para style
    $wpdb->query("SELECT * FROM nuke_clientes_stats
            WHERE id_cliente='".$cliente->ID."' AND sitio='style' ORDER BY id DESC");
    $sum_notas_style = 0;
    $articulos_style = "";
    //$cant_stats = $db->sql_numrows($buscar_clientes_stats);
    foreach($wpdb->last_result as $kstyle => $vstyle){
        $flag_style = true;
        $cont_style = ($vstyle->contador_leermas==0) ? '--': $vstyle->contador_leermas;
        $sum_notas_style = $sum_notas_style + $vstyle->contador_leermas;
        $articulos_style .= '<tr>
                            <td><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"> <strong>Tradem Style</strong></font></td>
                            <td bgcolor="#EEEBEA"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#000" size="2">'.utf8_decode($vstyle->titulo).'</font></td>
                            <td style="text-align: center"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>'.$cont_style.'</strong></font></td>
                            </tr>';
        unset($cont_style);
    }

$cuerpo = '
    <html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    </head>
    <body text="#666666" bgcolor="#E7E3E2" alink="#CCCCCC" vlink="#CCCCCC" link="#CCCCCC">
    <table border="1" bordercolor="#535353" cellspacing="0" cellpadding="0" width="610">
    <tr><td>
    <table width="600" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#FFFFFF" bgcolor="#FFFFFF">
    <tr>
    <td height="129" background="http://www.espaciotradem.com/reportes/cabezal.jpg"><table width="600" border="0" cellspacing="0" cellpadding="5">
    <tr>
    <td height="80" colspan="2">&nbsp;</td>
    </tr>
    <tr>
    <td width="476"><font face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCCC" size="4" ><strong>RRG</strong></font><font face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCCC" size="2" > - Reporte Resumen Global - </font><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="4" ><strong>'.strtoupper($cliente->NOMBRE).'</strong></font></td>
    <td width="104"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="1" >'.$mes_text.' '.$anio.'</font></td>
    </tr>
    </table></td>
    </tr>
    <tr>
    <td><table width="580" border="0" align="center" cellpadding="10" cellspacing="0">
    <tr>
    <td><font face="Verdana, Arial, Helvetica, sans-serif" color="#6699CC" size="3" ><strong>RAN - Reporte Apertura de Notas</strong></font></td>
    </tr>
    <tr>
    <td><font face="Verdana, Arial, Helvetica, sans-serif "size="2" >
El siguiente listado expresa las notas del cliente que se encuentran activas y comunicando,
junto con sus correspondientes aperturas obtenidas hasta el momento.</font></td>
    </tr>
    ';

    if ($flag_espacio) {
    $cuerpo .= '
    <tr>
    <td>
    <table width="585" border="0" cellpadding="5" cellspacing="0" bgcolor="#F8F8F8">
        <tr>
        <td bgcolor="#95958E"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"> Sitio </font>    </td>
        <td bgcolor="#95958E"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"> Notas </font>    </td>
        <td bgcolor="#95958E" style="text-align: center"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"> Visitas </font>    </td>
        </tr>
             '.$articulos_espacio.'
    </table>
        
    </td>
    </tr>
    
    <tr>
    <td></td>
    </tr>
    
    ';
    }


    if ($flag_design) {
    $cuerpo .= '
    <tr>
    <td>
    
    <table width="585" border="0" cellpadding="5" cellspacing="0" bgcolor="#F8F8F8">
        <tr>
        <td bgcolor="#95958E" colspan="1"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"> Sitio </font>    </td>
        <td bgcolor="#95958E" colspan="1"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"> Notas </font>    </td>
        <td bgcolor="#95958E" colspan="1" style="text-align: center"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"> Visitas </font>    </td>
        </tr>
             '.$articulos_design.'
    </table>
    
    </td>
    </tr>
    ';
    }



    if ($flag_style) {
        $cuerpo .= '
        <tr>
        <td>
        
        <table width="585" border="0" cellpadding="5" cellspacing="0" bgcolor="#F8F8F8">
            <tr>
            <td bgcolor="#95958E" colspan="1"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"> Sitio </font>    </td>
            <td bgcolor="#95958E" colspan="1"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"> Notas </font>    </td>
            <td bgcolor="#95958E" colspan="1" style="text-align: center"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2"> Visitas </font>    </td>
            </tr>
                 '.$articulos_style.'
        </table>
        
        </td>
        </tr>
        ';
    }



$apertura_links_espacio = $cliente->ESPACIO;
$apertura_links_espacio = (ceil($apertura_links_espacio) ==0) ? '--' : ceil($apertura_links_espacio);

$apertura_links_design = $cliente->DESIGN;
$apertura_links_design = (ceil($apertura_links_design) ==0) ? '--': ceil($apertura_links_design);


$apertura_links_style = $cliente->STYLE;
$apertura_links_style = (ceil($apertura_links_style) ==0) ? '--': ceil($apertura_links_style);

$total = $sum_notas_espacio+$sum_notas_design+$sum_notas_style+$apertura_links_espacio+$apertura_links_design+$apertura_links_style;
$total = ($total == 0) ? '--':$total;


if (in_array($cliente->ID, $clientes_home)) {
    $micropixel = $micropixel_home;
}

if (in_array($cliente->ID, $clientes_footer)) {
    $micropixel = $micropixel_footer;
}

if (in_array($cliente->ID, $clientes_ambos)) {
    $micropixel = $micropixel_total;
}


    $cuerpo .= '
    </table>
    <div align="center"></div></td>
    </tr>
    <tr>
    <td><table width="580" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr>
    <td><strong><font color="#6699CC" size="3" face="Verdana, Arial, Helvetica, sans-serif">VIE - Valor de Inter&eacute;s Especifico</font></strong></td>
    </tr>
    <tr>
    <td><font face="Verdana, Arial, Helvetica, sans-serif "size="2" >
    El siguiente valor expresa la sumatoria de todas las aperturas de notas activas y comunicando en cada site
    junto con los ingresos a la web generados totales y acumulados.
    </font></td>    
    </tr>
    <tr>
    <td><table width="570" border="0" cellpadding="5" cellspacing="0" bgcolor="#F8F8F8">
    <tr>
    <td bgcolor="#95958E"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2">Sitio</font> </td>
    <td bgcolor="#95958E"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2">Descripci&oacute;n</font> </td>
    <td bgcolor="#95958E" style="text-align: center"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="2">Visitas</font> </td>
    </tr>
    <tr>
    <td><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>Espacio Tradem</strong></font></td>
    <td bgcolor="#EEEBEA"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#000" size="2"> Cantidad de visitas de Notas</font></td>
    <td style="text-align: center"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>'.$sum_notas_espacio.'</strong></font></td>
    </tr>
    <tr>
    <td><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>Tradem Design</strong></font></td>
    <td bgcolor="#EEEBEA"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#000" size="2">Cantidad de visitas de Notas</font></td>
    <td style="text-align: center"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>'.$sum_notas_design  .'</strong></font></td>
    </tr>
    <tr>
    <td><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>Tradem Style</strong></font></td>
    <td bgcolor="#EEEBEA"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#000" size="2">Cantidad de visitas de Notas</font></td>
    <td style="text-align: center"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>'.$sum_notas_style  .'</strong></font></td>
    </tr>
    <tr>
    <td><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>Espacio Tradem</strong></font></td>
    <td bgcolor="#EEEBEA"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#000" size="2">Cantidad de Ingresos a la Web</font></td>
    <td style="text-align: center"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>'.$apertura_links_espacio.'</strong></font></td>
    </tr>
    <tr>
    <td><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>Tradem Design</strong></font></td>
    <td bgcolor="#EEEBEA"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#000" size="2">Cantidad de Ingresos a la Web</font></td>
    <td style="text-align: center"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>'.$apertura_links_design.'</strong></font></td>
    </tr>
    <tr>
    <td><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>Tradem Style</strong></font></td>
    <td bgcolor="#EEEBEA"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#000" size="2">Cantidad de Ingresos a la Web</font></td>
    <td style="text-align: center"><font face="Verdana, Arial, Helvetica, sans-serif"  color="#990000" size="2"><strong>'.$apertura_links_style.'</strong></font></td>
    </tr>
    <tr>
    <td bgcolor="#95958E">&nbsp;</td>
    <td bgcolor="#95958E"><div align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF" size="3"><strong>TOTAL</strong></font></div></td>
    <td bgcolor="#FFE1DF" style="text-align: center"><strong><font color="#990000" size="3" face="Verdana, Arial, Helvetica, sans-serif">'.$total .'</font></strong></td>
    </tr>
    </table></td>
    </tr>
    
    </table></td>
    </tr>';


if ($micropixel<>0):

$cuerpo .= '
<tr>
    <td><table width="580" border="0" align="center" cellpadding="0" cellspacing="0">
    <tr><td><br></td></tr>
    <tr>
    <td><strong><font color="#6699CC" size="3" face="Verdana, Arial, Helvetica, sans-serif">RVM - Reporte de Visibilidad de la Marca por micropixel </font></strong></td>
    </tr>
    <tr>
    <td><font face="Verdana, Arial, Helvetica, sans-serif "size="2" >
    Expresa la sumatoria exacta de veces que se muestra ( en forma completa y se hace visible ) la marca o denominación comercial de la empresa, estudio o consultora a la derecha del home de Espacio Tradem o las marcas inferiores al pie del mencionado sitio. Esta medición exacta se realiza mediante el prendido de un micropixel instalado única y exclusivamente en los baners de la derecha del home o las marcas inferiores al pie del sitio. Cabe aclarar que en caso que dichos elementos por razones de conectividad o permanencia en el sitio no se muestren de forma plena, no reportaran visibilidad de marca alguna. 
    </font><br><br>
    La Visibilidad de la marca para <strong>'.strtoupper($cliente->NOMBRE).'</strong> en los últimos 30 días es de '.$micropixel.'    
    </td>    
    </tr>
    <tr><td><br></td></tr>
  
    <tr><td>
    <font face="Verdana, Arial, Helvetica, sans-serif "size="2" >
    Importante : <em>Los ingresos a la Web indicados en acumulado son unicamente lo contabilizado a partir del 1 Agosto del 2011,  fecha de implementaci&oacute;n del presente RRG - Reporte Resumen Global. Los ingresos a la web anteriores a la mencionada fecha fueron enviados oportunamente en tiempo real,  uno a uno a travez de Reporte Tradem</em>
    </font>
    </td></tr>    
    </table></td>
    </tr>

<tr><td><br></td></tr>';

endif;


$cuerpo .= '

    <tr>
    <td>
    <table width="580" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td><strong><font color="#6699CC" size="3" face="Verdana, Arial, Helvetica, sans-serif">Acerca TrademMedia.com</font></strong></td>
      </tr>
      <tr>
        <td><font face="Verdana, Arial, Helvetica, sans-serif "size="2" >
        <p style="color:color:#666; font-size: 11px;">
        <strong>TrademMedia.com</strong> es el espacio on line orientado a la arquitectura, el diseño, la vanguardia, que posee dentro de su universo tres plataformas temáticas de comunicación (EspacioTradem, TrademDesign & TrademStyle) y redes sociales integradas (Comunidad Tradem).<br/><br/>
        

        
        <strong>EspacioTradem.com</strong> Plataforma de comunicaci&oacute;n l&iacute;der de la Arquitectura Comercial y Corporativa para empresas y profesionales.
        <br /><br /><strong>TrademDesign.com</strong> Espacio de comunicaci&oacute;n para Productos y Profesionales del Dise&ntilde;o, la Arquitectura y la Decoraci&oacute;n.
        <br/><br/><strong>TrademStyle.com</strong> Desarrollos & Emprendimientos. Paisajismo, life style y tecnología.
        <br/><br/><strong>Comunidad Tradem</strong> Red social de integración y participación.
        </p>
    </font></td>
      </tr>
   
   
    
    <tr><td> </td></tr>
    
    <tr>
    <td bgcolor="#333333">
    <table width="610" border="0" cellspacing="0" cellpadding="0">
    <tr>
    
    <td height="100">
    <img usemap="#map" src="http://www.espaciotradem.com/reportes/pie-reporte.jpg">
    
    <map name="map">
<area shape="rect" coords="18,22,72,73" alt="TW" href="http://twitter.com/ComunidadTradem" />
<area shape="rect" coords="82,21,136,74" alt="FAcebook" href="http://www.facebook.com/ComunidadTradem" />
    
    </map>
    
    </td></tr>
    <tr>
    <td colspan="3" style="text-align: center"><font face="Verdana, Arial, Helvetica, sans-serif" color="#CCCCCC"; size="1" >Si Ud. no puede visualizar este comunicado, por favor, envienos un email a <a href="mailto:reportes@espaciotradem.com">reportes@espaciotradem.com</a></font></td>
    </tr>
    </table>
    </td>
    </tr>
    </table>
    </td></tr>
    </table>
    </body>
    </html>';
	//echo $cuerpo;
	
	$month = date("m");
	$year = date("Y");
	$month_year = $year . '-' . $month;
	
	$q = "ver_informe('$month_year','$emails_destinatarios[0]')";
	$cliente_valor = ' <button type="button" class="mi-boton" onclick="'.$q.'">Ver Informe</button>' . '<span class="titulo_destino"> '. $cliente->NOMBRE . '</span> ' . $emails_destinatarios[0];
	array_push($clientes_informe_enviado, $cliente_valor);
	//$wpdb->insert('informes_enviados',array('informe' => $cuerpo, 'cliente' => $emails_destinatarios[0]));
	
	$asunto = "IMPORTANTE : RRG - Reporte Resumen Global - ".$cliente->NOMBRE;
	$headers[] = 'Bcc: reportetradem@gmail.com';
	//print_r($emails_destinatarios);
	//echo $asunto;
	 if($_POST['email_destino']){
		 $emails_destinatarios = $_POST['email_destino'];
		 $headers = '';
	 }

	
	//$emails_destinatarios = 'alejandro@grupodeboss.com';
	//wp_mail($emails_destinatarios, $asunto, $cuerpo,$headers );


	//echo "<br>".$cliente->emails."<br>";
/*
	//echo "<pre>";
	var_dump($idPostET);
	//echo "</pre>";*/

	unset($emails_destinatarios,$cuerpo,$sum_notas_espacio,$flag_design,$flag_espacio,$flag_style,$sum_notas_design,$cont_style,$sum_notas_style,
    $total,$apertura_links_style,$apertura_links_design,$apertura_links_espacio,$micropixel);
	
endforeach;

	echo json_encode($clientes_informe_enviado);
?>
