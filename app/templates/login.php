<?php

use Imx\html;
use Imx\db;

html::head("Login");
$db = db::mycon();

if (db::mycon() == false) {
    die("Error de conexion a DB");
}

?>

<body class="pace-to    p">
    <!-- begin #page-loader -->
    <div id="page-loader" class="fade show">
        <span class="spinner"></span>
    </div>
    <!-- end #page-loader -->

    <!-- begin login-cover -->
    <div class="login-cover">
        <div class="login-cover-image" style="background-image: url(../assets/img/login-bg/login-bg-17.jpg)" data-id="login-cover-image"></div>
        <div class="login-cover-bg"></div>
    </div>
    <!-- end login-cover -->

    <!-- begin #page-container -->
    <div id="page-container" class="fade">
        <!-- begin login -->
        <div class="login login-v2" data-pageload-addclass="animated fadeIn">
            <!-- begin brand -->
            <div class="login-header">
                <div class="brand">
                    <span class="logo"></span> <b><?php echo $_ENV['APP_NAME']; ?></b>
                    <?php echo $_ENV['APP_VERSION']; ?>
                </div>
                <div class="icon">
                    <i class="fa fa-lock"></i>
                </div>
            </div>
            <!-- end brand -->
            <!-- begin login-content -->
            <div class="login-content">
                <div class="form-group m-b-20">
                    <input id='usuario' type="text" class="form-control form-control-lg" placeholder="Usuario" required />
                </div>
                <div class="form-group m-b-20">
                    <input id='clave' type="password" class="form-control form-control-lg" placeholder="Clave" required />
                </div>
                <div class="checkbox checkbox-css m-b-20">
                    <input type="checkbox" id="remember_checkbox" />
                    <label for="remember_checkbox">
                        Recordarme
                    </label>
                </div>
                <div class="login-buttons">
                    <button id='login' type="button" class="btn btn-success btn-block btn-lg">Acceder</button>
                </div>
            </div>
            <!-- end login-content -->
        </div>
        <!-- end login -->

        <!-- begin login-bg -->
        <ul class="login-bg-list clearfix">
            <li class="active"><a href="javascript:;" data-click="change-bg" data-img="../assets/img/login-bg/login-bg-17.jpg" style="background-image: url(../assets/img/login-bg/login-bg-17.jpg)"></a></li>
            <li><a href="javascript:;" data-click="change-bg" data-img="../assets/img/login-bg/login-bg-16.jpg" style="background-image: url(../assets/img/login-bg/login-bg-16.jpg)"></a></li>
            <li><a href="javascript:;" data-click="change-bg" data-img="../assets/img/login-bg/login-bg-15.jpg" style="background-image: url(../assets/img/login-bg/login-bg-15.jpg)"></a></li>
            <li><a href="javascript:;" data-click="change-bg" data-img="../assets/img/login-bg/login-bg-14.jpg" style="background-image: url(../assets/img/login-bg/login-bg-14.jpg)"></a></li>
            <li><a href="javascript:;" data-click="change-bg" data-img="../assets/img/login-bg/login-bg-13.jpg" style="background-image: url(../assets/img/login-bg/login-bg-13.jpg)"></a></li>
            <li><a href="javascript:;" data-click="change-bg" data-img="../assets/img/login-bg/login-bg-12.jpg" style="background-image: url(../assets/img/login-bg/login-bg-12.jpg)"></a></li>
        </ul>
        <!-- end login-bg -->


        <!-- begin scroll to top btn -->
        <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
        <!-- end scroll to top btn -->
    </div>
    <!-- end page container -->

    <?php
    $scripts = "<script src=\"/assets/js/app/login.js\"></script>";
    html::scripts(false, $scripts);
    html::bodyEnd();
