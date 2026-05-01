<?php
use Imx\html;
html::head("Finalizacion de sesion");

$latte = new Latte\Engine;

echo $latte->renderToString('../app/templates/login.latte', ["title"=> APP_NAME . " | Login ","app"=> APP_NAME]);
