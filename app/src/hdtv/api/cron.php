<?php

use PHPMailer\PHPMailer\PHPMailer;

dispatch("/api/public/cron", function () {
    set_time_limit(15); // set the time limit to 120 seconds


    $notificaciones = Imx\db::dataQueryMultiple("select 
    notificaciones.id,
    notificaciones.titulo,
    usuarios.correo,
    notificaciones.asunto
    
     from notificaciones
     left join usuarios on usuarios.id = notificaciones.usuario
     where notificaciones.correo = 1 ");
    foreach ($notificaciones as $notificacion) {
        // print_r($notificacion);
        // exit;
        $mail = new PHPMailer();
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $user = $_ENV['APP_EMAIL_USER'];
        $password = $_ENV['APP_EMAIL_PASSWORD'];
        $mailfrom = $_ENV['APP_EMAIL'];
        $mailfromname = $_ENV['APP_EMAIL'];

        // $mail->SMTPDebug = 2; //Alternative to above constant
        $mail->isSMTP(); // enable SMTP
        $mail->Timeout       =   15; // set the timeout (seconds)
        $mail->Host = $_ENV['APP_EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $user; // SMTP server;
        $mail->Password = $password; // SMTP server
        if ($_ENV['APP_EMAIL_SECURE']) {
            $mail->SMTPSecure = 'SSL';
        } // SMTP server
        $mail->Port =  $_ENV['APP_EMAIL_PORT'];
        $mail->setFrom($mailfrom, $mailfromname); // correo y usuario que envia el correo
        $mail->isHTML(true);
        //# calculamos nuestras variables :
        $mail->Subject = $notificacion['asunto'];
        $mail->Body = $notificacion['titulo'];
        $mail->addAddress($notificacion['correo']);
        $mail->addBCC("jorge.valenzuela@dtc.mx");
        // $mail->addBCC("josue@insist.com.mx");
        //# adjuntamos xml y pdf
        if (!$mail->send()) {
            //aciertos("Mensaje enviado con exito","Imprimir","window.location.href='$formato.php?id=$id&t=$tipo'");
            echo 'No se pudo enviar el reporte diario :<br>  Error de sistema : ' . $mail->ErrorInfo;
        } else {
            echo "nOTIFICACION ENVIADA<br>";
        }
        Imx\db::iquery("update notificaciones set correo ='0' where id ='{$notificacion['id']}'");
    }
});
