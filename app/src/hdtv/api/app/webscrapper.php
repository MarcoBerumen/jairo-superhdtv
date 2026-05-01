<?php
dispatch('/api/public/webscrapper',function(){
    return "";
});

dispatch('/api/webscrapper/sites',function() {
    hdtv::checkTokenW();
    Imx\headers::json();
    $webscrappers = Imx\db::dataQueryMultiple("select  * from webscrapper");;
    return json_encode($webscrappers);

});

dispatch('/api/webscrapper/products',function() {
//    hdtv::checkTokenW();
    Imx\headers::json();
    $products = Imx\db::dataQueryMultiple("select name,
       product_id,
description,
model,
sku,
tags,
prices
from products");
    array_walk($products,function(&$product){
        $product['prices'] = explode(",",$product['prices']);
        $product['tags'] = explode(",",$product['tags']);
        $product['blacklist'] = Imx\db::dataQueryMultiple("select 
webscrapper_id,
price_url
from 
    webscrapper_price_history
where
webscrapper_price_history.product_id = '{$product['product_id']}'
and webscrapper_price_history.blacklist = 1
");
        $product['history']= Imx\db::dataQueryMultiple("select 
webscrapper.name as store ,
webscrapper.webscrapper_id,
(select 
date
from webscrapper_price_history
where webscrapper_price_history.webscrapper_id = 
webscrapper.webscrapper_id
and webscrapper_price_history.product_id = '{$product['product_id']}'
order by date desc limit 1 
) as date,
(select 
price_url
from webscrapper_price_history
where webscrapper_price_history.webscrapper_id = 
webscrapper.webscrapper_id
and webscrapper_price_history.product_id = '{$product['product_id']}'

order by date desc limit 1 
) as link,
(select 
price
from webscrapper_price_history
where webscrapper_price_history.webscrapper_id = 
webscrapper.webscrapper_id
and webscrapper_price_history.product_id = '{$product['product_id']}'
order by date desc limit 1 
) as price
 from 
 webscrapper 
");
        return $product;
    });
    return json_encode($products);
});

dispatch_post('/api/webscrapper/products/price',function() {
    hdtv::checkTokenW();
    $data =  file_get_contents('php://input');
    $data = json_decode($data,true);
    $data['date'] =  date('Y-m-d H:i:s');
    $data['price'] = $data['price']??0;
    $data['milliseconds'] = $data['milliseconds']??0;
    $q = "insert into webscrapper_price_history
(webscrapper_id,
product_id,
date,
price,
price_url,
milliseconds)
values
('{$data['webscrapper_id']}',
'{$data['product_id']}',
'{$data['date']}',
'{$data['price']}',
'{$data['price_url']}',
'{$data['milliseconds']}')
";
    return Imx\db::iquery($q);
});
