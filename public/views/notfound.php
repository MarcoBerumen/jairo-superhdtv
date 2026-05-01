<?php

use Imx\html;

html::head("Not Found");
html::bodyInit();
$latte = new Latte\Engine;
if (isset($_SESSION[$_ENV['APP_SESSIONNAME']])) {
    html::header("");
    html::sidebar();
}

echo $latte->renderToString('../app/templates/notfound.latte', []);


html::endContent();
html::containerEnd();
html::scripts(false, "");
html::bodyEnd();
