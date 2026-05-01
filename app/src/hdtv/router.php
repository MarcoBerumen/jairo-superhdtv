<?php
//? HOME

require "hdtv.class.php";
dispatch('/', function () {
    require "views/home.php";
});

dispatch('/pos', function () {
    require "views/pos.php";
});

dispatch('/system-settings', function () {
    require "views/settings.php";
});

dispatch('/dashboard', function () {
    require "views/home.php";
});

dispatch('/notificaciones', function () {
    require "views/notificaciones.php";
});

dispatch('/logout', function () {
    require "views/logout.php";
});

//** CATALOGS */
require "views/catalogs/router.php";
//** backoffice */
require "views/backoffice/router.php";
//** reportes */
require "views/reports/router.php";
dispatch('/login', function () {
    require "views/login.php";
});


dispatch('/logout', function () {
    require "views/logout.php";
});
