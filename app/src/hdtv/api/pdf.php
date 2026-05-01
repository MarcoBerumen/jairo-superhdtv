<?php
dispatch("/public/pdf",function(){
    if (isset($_GET['pdf'])) {
        $link = $_GET['pdf'];
        $cmd = "wkhtmltopdf --viewport-size 1024x768  \"{$link}\" documento.pdf ";
        $cmd = "chromium  --headless --disable-gpu --print-to-pdf-no-header --print-to-pdf=\"documento.pdf\" {$link} --no-sandbox";
        exec($cmd, $arr); //# para ver el comando
        $file = $_GET['file'] ?? 'documento.pdf';
//        header('Content-Description: File Transfer');
        header('Content-type: application/pdf');
//        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Disposition: inline; filename="documento.pdf"');
//        header('Content-Length: ' . filesize('documento.pdf'));
        readfile('documento.pdf');
    }

});
