<?php
//die('System under maintenance');
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_DEPRECATED);
require "../init.php";
if ($_ENV['APP_DEBUG'] ?? 0 == 1) {
  ini_set('error_reporting', E_ALL);
}

// print_r($_SESSION);
if ($_ENV['APP_ROUTE'] == "") {
  die("Program setup is not complete");
}

//* vistas
$path = sprintf("../app/src/%s/router.php", $_ENV['APP_ROUTE']);
require $path;
// * menu
$path = sprintf("../app/src/%s/menu.php", $_ENV['APP_ROUTE']);
require $path;
//* api
// ? API ROUTES
$path = sprintf("../app/src/%s/api/", $_ENV['APP_ROUTE']);
// require "api/login.php";

foreach (scandir($path) as $file) {
  if (substr($file, -3) == "php") {
    $f = $path . $file;;
    require $f;
  }
}




run();


function before($route)
{
  if (strpos($route['pattern'], "public") === false && strpos($route['pattern'], "app"  ) === false  && strpos($route['webscrapper'], "app"  ) === false)  {
    if (!isset($_SESSION[$_ENV['APP_SESSIONNAME']])) { //! REQUERIMOS INICIO SE SESION

        if(str_contains($route['pattern'], "/api") ) {
            Imx\headers::unauthorized();
            die('Unathenticated');
        }
      include "views/login.php";
      exit;
    }
  }
}




function not_found($errno, $errstr, $errfile = null, $errline = null)
{
  header('HTTP/1.0 401 Unauthorized');

  require "views/notfound.php";
}
