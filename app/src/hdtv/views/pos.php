<?php

use Imx\html2;

html2::head("POS");
html2::bodyInit();
html2::header("");
html2::sidebar();
html2::beginContent();
$latte = new Latte\Engine;
$form =  "
Please install Expo Go App on <a href='https://play.google.com/store/apps/details?id=host.exp.exponent&hl=es_MX&gl=US'>Play store</a> or <a href='https://apps.apple.com/cl/app/expo-go/id982107779'>App Store</a> then scan this QR Code :<br>
<img src='/assets/img/expo/{$_ENV['APP_NAME']}.jpg'>
<a href=''></a>
";

echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Expo POS", "body" => $form ]);

html2::endContent();
html2::containerEnd();
html2::scripts();
html2::bodyEnd();
