<?php
// ! categories
dispatch('/api/app/catalogs', function () {
    hdtv::checkToken();

    Imx\headers::json();
    $categories = Imx\db::dataQueryMultiple("select * from categories where row_status = 1");

    $features = Imx\db::dataQueryMultiple("select 
    features.*,
    1 as range_min,
    10 as range_max
    from features");
    $brands = Imx\db::dataQueryMultiple("select 
    brands.*
    from brands where row_status = 1 ");
    $payment_methods = Imx\db::dataQueryMultiple("select 
    *
    from payment_methods where row_status = 1");
    array_walk($payment_methods, function (&$payment_method){
        $payment_method['credit'] = $payment_method['credit'] *1;
    });

    return json_encode([
        "brands" => $brands,
        "categories" => $categories,
        "features" => $features,
        "payment_methods" => $payment_methods
    ], JSON_PRETTY_PRINT);
});
