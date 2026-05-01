<?php

use Imx\html2;

html2::head("POS");
html2::bodyInit();
html2::header("");
html2::sidebar();
html2::beginContent();
$parameters = file_get_contents('../.env');
echo nl2br($parameters);
html2::endContent();
html2::containerEnd();
html2::scripts();
html2::bodyEnd();
