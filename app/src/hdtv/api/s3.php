<?php

use Imx\s3;

dispatch("/api/s3/:key/:file", function ($key, $file) {
    $f = sys_get_temp_dir() . "/" . Imx\utils::get_guid() . $file;
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    $file =  s3::getAsLink("{$key}/{$file}");
    switch ($extension) {
        case "jpg":
            $mime = 'image/jpeg';
            if (isset($_GET['mini'])) {

                $file = file_get_contents($file);
                file_put_contents($f, $file);
                Imx\utils::resizer($f, $f . ".min.jpg", 20, 60);

                $file = $f . ".min.jpg";
            }
            break;
        case "pdf":
            $mime = 'application/pdf';
            break;
        case "png":
            $mime = 'image/png';
            break;
        default:
            $mime = "application/octet-stream";
            break;
    }
    header('Content-Type:' . $mime);
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    return file_get_contents($file);
});
dispatch("/api/public/s3/:key/:file", function ($key, $file) {
    $f = sys_get_temp_dir() . "/" . Imx\utils::get_guid() . $file;
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    $file =  s3::getAsLink("{$key}/{$file}");
    switch ($extension) {
        case "jpg":
            $mime = 'image/jpeg';
            if (isset($_GET['mini'])) {

                $file = file_get_contents($file);
                file_put_contents($f, $file);
                Imx\utils::resizer($f, $f . ".min.jpg", 20, 60);

                $file = $f . ".min.jpg";
            }
            break;
        case "pdf":
            $mime = 'application/pdf';
            break;
        case "png":
            $mime = 'image/png';
            break;
        default:
            $mime = "application/octet-stream";
            break;
    }

    header('Content-Type:' . $mime);
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    return file_get_contents($file);
});
dispatch("/api/s3/:key/:file/delete", function ($key, $file) {
    s3::removeFile("{$key}/{$file}", false);
});
