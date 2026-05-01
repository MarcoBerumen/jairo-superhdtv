<?php
session_start(); 
//* requerimos librerias de composer
require_once 'vendor/autoload.php';
require_once 'functions.php';
//** inicializamos CONSTANTES de .env */
// echo strpos($_SERVER['REQUEST_URI'],"/api");
if( is_numeric(strpos($_SERVER['REQUEST_URI'],"/api")))
{
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
}
else
{

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
}
$dotenv->load();
// * cargamos configuracion de .env a CONSTANTES
require 'const.php';
//** Incluimos clases de Imx */
require 'app/loader.php';