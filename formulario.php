<?php

    $secret = ""; //Google reCaptcha secret key

    $phpMailerHost = "";
    $phpMailerEmail = "";
    $phpMailerPassword = "";

	require_once "./scripts/recaptchalib.php";
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;

	require './scripts/vendor/phpmailer/phpmailer/src/Exception.php';
	require './scripts/vendor/phpmailer/phpmailer/src/PHPMailer.php';
	 
	// empty response
	$response = null;
	 
	// check secret key
	$reCaptcha = new ReCaptcha($secret);
	
	// if submitted check response
	if (isset($_POST["g-recaptcha-response"])) {
		$response = $reCaptcha->verifyResponse(
			$_SERVER["REMOTE_ADDR"],
			$_POST["g-recaptcha-response"]
		);
	}

    $errores = array();

	if (isset($_POST["submit"]))
	{
		if ($_POST['nombre'] == null)
		{
            array_push($errores, "Debe escribir un nombre.");
		}
				
		if ($_POST['contacto'] == null)
		{
            array_push($errores, "Debe escribir una forma de contacto.");
		}	
		
		if ($_POST['consulta'] == null)
		{
            array_push($errores, "Debe escribir una consulta.");
		}

        if (count($errores) == 0)
		{
            if ($response != null && $response->success)
            {
                $titulo = "CONSULTA WEB";
                
                $mensaje = $_POST['nombre'] .  " escribio lo siguiente:" . "\n\n" . $_POST['consulta'] . "\n\n\n" . "Datos de contacto: " . $_POST['contacto'] . "\n\n" . "NO RESPONDER ESTE MAIL, RESPONDER A LA FORMA DE CONTACTO QUE PROPORCIONÓ EL CLIENTE.";
                
                $mensajehtml = "<html><b>" . $_POST['nombre'] .  "</b> escribio lo siguiente:" . "<br><br>" . $_POST['consulta'] . "<br><br>" . "<b>Datos de contacto: </b>" . $_POST['contacto'] . "<br><br>NO RESPONDER ESTE MAIL, RESPONDER A LA FORMA DE CONTACTO QUE PROPORCIONO EL CLIENTE EN 'Datos de contacto'.</html>";

                require './scripts/vendor/autoload.php';
                
                $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
                try {
                    //Server settings
                    //$mail->SMTPDebug = 1;                               // Enable verbose debug output
                    $mail->isSMTP();                                      // Set mailer to use SMTP
                    $mail->Host = $phpMailerHost;  			              // Specify main and backup SMTP servers
                    $mail->SMTPAuth = true;                               // Enable SMTP authentication
                    $mail->Username = $phpMailerEmail;                    // SMTP username
                    $mail->Password = $phpMailerPassword;                 // SMTP password
                    $mail->SMTPAutoTLS = false;
                    $mail->SMTPSecure = '';                               // Enable TLS encryption, `ssl` also accepted
                    $mail->Port = 587;                                    // TCP port to connect to

                    //Recipients
                    $mail->setFrom($phpMailerEmail, 'Estudio Contable');

                    $mail->addAddress($phpMailerEmail);   	              // Name is optional

                    //Content
                    $mail->isHTML(true);                                  // Set email format to HTML
                    $mail->Subject = $titulo;
                    $mail->Body    = $mensajehtml;
                    $mail->AltBody = $mensaje;

                    $mail->send();
                } catch (Exception $e) {
                    array_push($errores, "El mensaje no pudo ser enviado debido a un error interno: " . $mail->ErrorInfo . ".");
                }
            }
            else
            {
                array_push($errores, "Debe completar el reCAPTCHA.");
            }
        }
	}
?>

<html lang="es">
    <head>
        <!-- METATAGS -->
        <meta charset="utf-8">
        <meta name="description" content="Estudio Contable BC & Asociados fue fundado por la Dra. Patricia Bonavota y la Dra. Paula Cirmi como un estúdio dedicado a proveer soluciones integrales a PYMES y a nuevos emprendimientos. La práctica de nuestro Estudio ofrece una amplia gama de servícios administrativos, contables y financieros al comercio y a la industria."/>
        <meta name="robots" content="NOINDEX,NOFOLLOW,NOARCHIVE,SNIPPET,ODP,YDIR">
        <meta name="keywords" content="estudio,contable,impositivo,asesoramiento,auditorias,impositivo,liquidacion,haberes,societarios,socios,devoto,contador,administrador">
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Ezequiel Castro" />
        <meta name="copyright" content="Estudio BC" />
        
        <!-- TITULO E ICONO -->
        <title>Contacto</title>
        <link rel="icon" type="image/png" href="./img/logo_bc.png">
        
        <!-- BOOTSTRAP & CUSTOM CSS -->
        <link rel="stylesheet" href="css/bootstrap/css/bootstrap.min.css">
    </head>
    <body class="container" style="margin-top: 100px;">
        <script>window.onload = function() { setTimeout(function() { window.location = "index.html";}, 5000); } </script>
        <?php if(count($errores) > 0) : ?>
            <div class="alert alert-danger">
                <p>Se detectaron los siguientes errores:</p>
                <br>
                <?php foreach($errores as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach ?>
                <br>
                <p>Será re-direccionado a la página principal en 5 segundos. De no ser así, pulse <a href="index.html" title="Página principal">aquí</a> para volver.</p>
            </div>
        <?php endif ?>
        
        <?php if(count($errores) == 0) : ?>
            <div class="alert alert-success">
                <p>Su consulta fue enviada exitosamente. Nuestos horarios de atención online son de Lunes a Viernes de 10 a 19 horas. Muchas gracias. Será re-direccionado a la página principal en 5 segundos. De no ser así, pulse <a href="index.html" title="Página principal">aquí</a> para volver.</p>
            </div>
        <?php endif ?>
    </body>
</html>