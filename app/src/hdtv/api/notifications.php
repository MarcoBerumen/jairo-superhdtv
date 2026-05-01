<?php
use Cocur\Slugify\Slugify;

dispatch('/api/notifications', function(){
    $usuario = $_SESSION['user']['id'];
    $notificaciones = Imx\db::dataQueryMultiple("select * from notificaciones
    where usuario = '$usuario' and estatus = 1");
    $notificaciones = 
    [
        'notificaciones' => count($notificaciones),
        'data'=>$notificaciones
    ];
    echo json_encode($notificaciones);
});

dispatch('/api/notifications/clear', function(){
    $notificaciones = $_GET['id'];
    Imx\db::iquery("update notificaciones set
    estatus = 0,
    fechaNotificacion = now()
    where id in ($notificaciones);
    ");
    header("location: ".$_SERVER['HTTP_REFERER']);
});
