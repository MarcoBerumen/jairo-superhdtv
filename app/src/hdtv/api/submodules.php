<?php
// * CATALOGS
$files = glob('../app/src/hdtv/api/catalogs/*.php');
foreach ($files as $file) {
    $file = "catalogs/" . basename($file);
    require $file;
}
// * BACK-OFFICE
$files = glob('../app/src/hdtv/api/back-office/*.php');
foreach ($files as $file) {
    $file = "back-office/" . basename($file);
    require $file;
}
// * APP
$files = glob('../app/src/hdtv/api/app/*.php');
foreach ($files as $file) {
    $file = "app/" . basename($file);
    require $file;
}
// * REPORTS
$files = glob('../app/src/hdtv/api/reports/*.php');
foreach ($files as $file) {
    $file = "reports/" . basename($file);
    require $file;
}
