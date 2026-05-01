<?php

use Imx\db as db;
use Imx\utils as utils;

dispatch_post('/api/public/login', 'postLogin');


function postLogin()
{

  // echo utils::encrypt("cr0kjego");
  //# Recogemos los datos del post para autentificar
  $db = db::mycon(); // establecemos la conexion al servidor
  // recogemos las variables del post
  $usuario = $_POST['usuario']??$_POST['user'];
  $clave =  utils::encrypt($_POST['clave']??$_POST['password']);
  $db_query = "SELECT
   *
  FROM
    users
      where email = '{$usuario}' and password ='{$clave}' limit 1";
  $result = db::dataQuery($db_query);

  if (empty($result)) {
    return json_encode(['estatus' => false]);
  } else {
      $token = Imx\utils::get_guid();
      $user['token'] = $token;
      Imx\db::iquery("update users set token ='$token' where email = '{$usuario}' and password ='{$clave}'");
      $_SESSION[APP_SESSIONNAME] = true;
    $_SESSION['user'] = $result;
    return json_encode(['estatus' => true, 'usuario' => $result['name'],'user' => $result['name'],'token'=> $token]);
  }
}
