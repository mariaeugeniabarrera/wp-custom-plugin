<?php
	require '../libs/phpmailer/PHPMailerAutoload.php';
	require '../libs/phpmailer/class.phpmailer.php';
	require '../libs/phpmailer/class.smtp.php';
	
	$name = $_POST['name'];
	$email = $_POST['email'];
	$inquiry = $_POST['inquiry'];
	
	/*
	$name = "Testing";
	$email = "testing@testing.com";
	$designation = "Designation";
	$department = "Depto";
	$company = "My Company";
	$phone = "My phone";
	$address = "My Address";
	$inquiry = "My Inquiry";
	*/
	
	$mail = new PHPMailer;
	$mail->SMTPDebug = 3;                               // Enable verbose debug output

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'mail.gspiedras.com';  			// Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'info@gspiedras.com.ar';                 // SMTP username
	$mail->Password = 'inf369';                           // SMTP password
	//$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 25;                                    // TCP port to connect to

	$mail->setFrom('info@gspiedras.com.ar', 'GSPiedras Company');
	
	//$mail->addAddress('Eugenia@gmail.com', 'Eugenia');
	
	$mail->addAddress('eugenia@grupodeboss.com', 'Eugenia');     // Add a recipient
	//$mail->addAddress('Eugenia@gmail.com', 'Eugenia');               // Name is optional
	//$mail->addAddress('Eugenia@gmail.com', 'Eugenia');               // Name is optional
	//$mail->addReplyTo('mail@gmail.com', 'Eugenia');
	//$mail->addCC('Eugenia@gmail.com');
	//$mail->addBCC('Eugenia@example.com');

	//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	
	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = "Nuevo Mensaje de $name";
	$mail->Body    = "<p><b>Full name: </b>$name</p> <p><b>Email: </b>$email</p> <p><b>Inquiry: </b>$inquiry</p>";
	$mail->AltBody = 'Mensaje enviado desde la web de gspiedras.';

	if(!$mail->send()) {
		echo 'Su mensaje no pude ser enviado. Por favor intente de nuevo.';
		echo 'Error: ' . $mail->ErrorInfo;
	} else {
		echo 'Su mensaje ha sido enviado con éxito.';
	}
 ?>