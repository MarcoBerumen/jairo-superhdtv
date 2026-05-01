<?php

/**
 * Products Pagination endpoint 
 */
dispatch_post('/api/app/products', function () {
    $token = hdtv::checkToken(); // ? User validation
    $store = Imx\db::rquery("select store_id from users where token ='$token'");
    $pricelist = Imx\db::rquery("select price_list_id from stores where store_id ='$store'");
    $data =  file_get_contents('php://input');
    $data =  json_decode($data, true);
    $brand = $data['brand'] ?? "";
    $category = $data['category'] ?? "";
    $stock = $data['stock'] ?? false;
    $wb = "";
    $wc = "";
    $ws = "";
    $qf = "";
    if ($brand)
        $wb = " and brand_id ='$brand'";
    if ($category) {

        $wc = " and category_id ='$category'";
        // If we filter category then we listen for features filters
        $features = $data['features'];
        foreach ($features as $feature) {
            if (isset($feature['options'])) {
                // print_r($feature);
                if (count($feature['options']) == 1) {
                    $qf .= " and JSON_EXTRACT(features, '$.system') = '{$feature['options'][0]}' \n";
                } else {

                    $options = implode("\"','\"", $feature['options']);
                    $qf .= " and JSON_EXTRACT(features, '$.{$feature['feature']}') IN ('\"$options\"') \n";
                }
            }
            // ! only receive min range
            if (isset($feature['range_min']) && !isset($feature['range_max'])) {
                $qf .= " and JSON_EXTRACT(features, '$.{$feature['feature']}')  >= {$feature['range_min']} \n";
            }
            if (!isset($feature['range_min']) && isset($feature['range_max'])) {
                $qf .= " and JSON_EXTRACT(features, '$.{$feature['feature']}')  <= {$feature['range_max']} \n";
            }

            if (isset($feature['range_min']) && isset($feature['range_max'])) {
                $qf .= " and JSON_EXTRACT(features, '$.{$feature['feature']}') between {$feature['range_min']} and  {$feature['range_max']} \n";
            }
            if (isset($feature['bool'])) {
                $bool = 0;
                if ($feature['bool']) {
                    $qf .= " and JSON_EXTRACT(features, '$.{$feature['feature']}') = '1' \n";
                } else {
                    $qf .= " and 
                (
                    JSON_EXTRACT(features, '$.{$feature['feature']}') = '0' 
                    or
                    JSON_EXTRACT(features, '$.{$feature['feature']}') is null 
                    )
                    \n";
                }
            }
        }
    }
    if ($stock)
        $ws = " and  (select sum(stock.stock)
        from stock where stock.store_id ='$store'
        and stock.product_id = products.product_id
        ) > 0 ";
    $table = "
    (
        select
        product_id,
        name,
        model,
        sku,
        images,
        (select max(price) from product_pricing pp 
        left join stock on stock.product_id = pp.product_id
and stock.status_id = pp.status_id
and stock.grade_id = pp.grade_id
        where pp.product_id = products.product_id
        and stock.store_id = '$store'
         and stock.stock > 0 
        and price_list_id = '$pricelist' ) as price,
        (select min(min_price) from product_pricing pp 
                left join stock on stock.product_id = pp.product_id
and stock.status_id = pp.status_id
and stock.grade_id = pp.grade_id
        where pp.product_id = products.product_id
        and stock.store_id = '$store'
         and stock.stock > 0 
        and price_list_id = '$pricelist') as min_price,
        screen_size,
        (select brands.name from  brands where brands.brand_id = products.brand_id) as brand ,
        (select sum(stock.stock)
        from stock where stock.store_id ='$store'
        and stock.product_id = products.product_id
        ) as local_stock ,
        (select sum(stock.stock)
        from stock where stock.product_id = products.product_id
        ) as global_stock 
        from products
        where 
        (select price from product_pricing pp where pp.product_id = products.product_id
        and price_list_id = '$pricelist' order by price desc limit 1) > 0
        $wb
        $wc
        $ws
        $qf
        )
        as 

        products";
//    echo nl2br($table);
    // Table's primary key
    $primaryKey = 'product_id';
    $columns = array(
        array('db' => 'product_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        array('db' => 'model', 'dt' => 2),
        array('db' => 'images', 'dt' => 3),
        array('db' => 'price', 'dt' => 4),
        array('db' => 'screen_size', 'dt' => 5),
        array('db' => 'brand', 'dt' => 6),
        array('db' => 'local_stock', 'dt' => 7),
        array('db' => 'global_stock', 'dt' => 8),
        array('db' => 'sku', 'dt' => 9),
        array('db' => 'min_price', 'dt' => 10),
        // a
    );
    Imx\headers::json();

    return Imx\utils::safe_json_encode(Imx\jsonapi::simple($_GET, $table, $primaryKey, $columns));
});

dispatch('/api/app/products/:id', function ($id) {
    $token = hdtv::checkToken(); // ? User validation
    $user = Imx\db::dataQuery("select store_id,user_id from users where token ='$token'");
    $profile = Imx\db::rquery("select profile from view_users where user_id ='{$user['user_id']}'");
    $store = $user['store_id'];
    $pricelist = Imx\db::rquery("select price_list_id from stores where store_id ='$store'");
    $producto = Imx\db::dataQuery("
        select 
    p.product_id,
    p.name,
    p.description,
    p.sku,
    b.name as brand,
    c.name as category,
    p.images,
    w.name as warranty,
    w.warranty_id,
    (select max(price) from product_pricing pp where pp.product_id = p.product_id
    and price_list_id = '$pricelist' ) as max_price,
    (select price from product_pricing pp where pp.product_id = p.product_id
    and price_list_id = '$pricelist'  and price > 0  order by price asc limit 1 ) as min_price,
    stock_type,
    features
    from products  p
    left join brands b on b.brand_id = p.brand_id
    left join categories c on c.category_id = p.category_id
    left join warranties w on w.warranty_id = p.warranty_id
    where product_id = '$id'
    and w.row_status =1
    ");

    $underprice = Imx\db::rquery("select 
    min(under_price) 
     from warranties 
    where under_price >=   '{$producto['max_price']}' and row_status =1 ;");

    $producto['warranties'] = Imx\db::dataQueryMultiple("
SELECT 
    warranty_id,
    name,
    under_price, 
    warranty_days,
    price  FROM `warranties`
    where 
             warranty_id = '{$producto['warranty_id']}' 

    union 
    SELECT 
    warranty_id,
    name,
    under_price, 
    warranty_days,
    price  FROM `warranties`
       where row_status = 1 
and under_price >=  '{$underprice}'
group by warranty_days
 order by warranty_days,price desc
     
    ");


    $producto['prices'] = Imx\db::dataQueryMultiple("select 
webscrapper.name as store ,
(select 
date
from webscrapper_price_history
where webscrapper_price_history.webscrapper_id = 
webscrapper.webscrapper_id
and webscrapper_price_history.product_id = '{$id}'
and webscrapper_price_history.price > 0 
order by date desc limit 1 
) as date,
(select 
price_url
from webscrapper_price_history
where webscrapper_price_history.webscrapper_id = 
webscrapper.webscrapper_id
and webscrapper_price_history.product_id = '{$id}'
and webscrapper_price_history.price > 0 

order by date desc limit 1 
) as link,
(select 
price
from webscrapper_price_history
where webscrapper_price_history.webscrapper_id = 
webscrapper.webscrapper_id
and webscrapper_price_history.product_id = '{$id}'
and webscrapper_price_history.price > 0 
order by date desc limit 1 
) as price
 from 
 webscrapper 
");

    $producto['features'] = json_decode($producto['features']);
    $q = "select 
    stock.store_id,
    stock.product_id,
    stock.status_id,
    stock.grade_id,
    product_pricing.price,
    product_pricing.min_price,
    grades.name as grade,
    stores.name as store,
    status.name as status ,
    stock.stock as quantity,
    (select group_concat(concat(items.item_id,'|',serial_number,'|',provider_warranty_date)) from items
    where items.product_id =  stock.product_id
    and items.store_id = stock.store_id
    and items.grade_id = stock.grade_id
    and items.status_id = stock.status_id
    and items.status = 1
    ) as items
     from stock 
     left join stores on stores.store_id = stock.store_id
     left join product_pricing on
    (
        product_pricing.product_id = stock.product_id
        and product_pricing.grade_id = stock.grade_id
        and  product_pricing.status_id = stock.status_id
        and  product_pricing.price_list_id = stores.price_list_id
    )

    left join grades on grades.grade_id = stock.grade_id
    left join status on status.status_id = stock.status_id
    where stock.product_id =  '{$id}'  and   stock  > 0 
    and stock.store_id ='$store'";
    // echo $q;
    $producto['stock'] = Imx\db::dataQueryMultiple($q);

    foreach ($producto['stock'] as &$stock) {
        $s = explode(",", $stock['items']);
        $stock['items'] = [];
        if($profile =="Super Admin"){
                $stock['min_price']="1.00";
        }
        foreach ($s as $i) {
            $it = explode("|", $i);
            $it[2] = Imx\utils::sql2date($it[2]);
            $stock['items'][] = [
                "item_id" => $it[0],
                "serial" => $it[1],
                "warranty_date" => $it[2],
            ];
        }
    }
    Imx\headers::json();
    return json_encode($producto);
});


dispatch('/api/app/products-ws', function () {
    $productos = Imx\db::dataQueryMultiple("select 
product_id,
products.name,
model,
sku,
brands.name as brand,
tags from products
left join brands on brands.brand_id = products.brand_id
");

    return json_encode($productos);
});
