<?php
dispatch_post('/api/reports/webscrapper', function () {
    $brand = $_POST[0]['data']['brand']['value'];
    $category = $_POST[0]['data']['category']['value'];
    $store = $_POST[0]['data']['store']['value'];
    $wbrand = "";
    $wcategory = "";
    if ($brand) {
        $wbrand = "and brand_id ='{$brand}'";
    }
    if ($category) {
        $wcategory = "and category_id ='{$category}'";
    }
    $query = "
    select 
        p.*,
    (select webscrapper_price_history.price from webscrapper_price_history
    where webscrapper_price_history.webscrapper_id = p.webscrapper_id
and webscrapper_price_history.product_id  = p.product_id 
order by webscrapper_price_history.date desc limit 1 
) as ws_price,
(select webscrapper_price_history.date from webscrapper_price_history
where webscrapper_price_history.webscrapper_id = p.webscrapper_id
and webscrapper_price_history.product_id  = p.product_id 
order by webscrapper_price_history.date desc limit 1 
) as ws_date,
(select webscrapper_price_history.price_url from webscrapper_price_history
where webscrapper_price_history.webscrapper_id = p.webscrapper_id
and webscrapper_price_history.product_id  = p.product_id 
order by webscrapper_price_history.date desc limit 1 
) as ws_link

 from (select 
view_products.name,brand,category,
view_products.product_id,
webscrapper.name as ws,
webscrapper.webscrapper_id
 from view_products , webscrapper 
     where
 product_id in(select product_id  from stock where store_id = '{$store}'  and stock > 0 )
 {$wbrand}
{$wcategory}
 ) as p
 
   ";

//    echo nl2br($queryz);
    $products = Imx\db::dataQueryMultiple($query);
    array_walk($products, function (&$product) use ($store) {
        $product['min_price'] = hdtv::getMinPrice($store, $product['product_id']);
        $product['ws_date'] = Imx\utils::sql2date($product['ws_date']);
        $product['difference'] = (($product['ws_price'] - $product['min_price'])/$product['min_price'])*100;
    });
//    Imx\utils::print_r_cool($products);


    $data = [
        'columnsData' => [
            [

                [
                    "key" => "brand",
                    "label" => "Brand",
                    "proportion" => 12,
                    "type" => "string"
                ],
            ], [

                [
                    "key" => "category",
                    "label" => "category",
                    "proportion" => 12,
                    "type" => "string"
                ],
            ],
            [

                [
                    "key" => "product_id",
                    "label" => "ID",
                    "proportion" => 2,
                    "type" => "string"
                ],
                [
                    "key" => "name",
                    "label" => "Product",
                    "proportion" => 12,
                    "type" => "string"
                ],
                [
                    "key" => "min_price",
                    "label" => "Price",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",
                ],
            ],
            [

                [
                    "key" => "ws",
                    "label" => "Store",
                    "proportion" => 6,
                    "type" => "string"
                ],
                [
                    "key" => "ws_date",
                    "label" => "Date Updated",
                    "proportion" => 3,
                    "type" => "string"
                ],
                [
                    "key" => "ws_link",
                    "label" => "Link",
                    "proportion" => 2,
                    "type" => "link"
                ],
                [
                    "key" => "difference",
                    "label" => "% Diff.",
                    "proportion" => 2,
                    "type" => "number"
                ],
                [
                    "key" => "ws_price",
                    "label" => "Price",
                    "proportion" => 2,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,
                ],
            ],
        ],
        'data' => $products,
        'groupByData' => ["brand", "category", "product_id"]

    ];
    $latte = new Latte\Engine;
    $data['latte'] = $latte;

    $report = $latte->renderToString('../app/templates/report.latte', $data);
    return " <div id='salesreport'>$report</div>";
});
dispatch_post('/api/reports/webscrapperh', function () {
    $product = $_POST[0]['data']['product']['value'];
    $end_date = Imx\utils::date2sql($_POST[0]['data']['end_date']['value']);
    $start_date = Imx\utils::date2sql($_POST[0]['data']['start_date']['value']);
    $query = "select 
h.date,
h.price_url,
h.price,
h.milliseconds,
date_format(h.date,'%m/%d/%Y %H:%i:%s') as date,
p.name as product,
p.model,
p.brand,
p.sku,
p.category,
w.name as ws
 from webscrapper_price_history h 
 left join webscrapper w on w.webscrapper_id = h.webscrapper_id
 left join view_products  p on p.product_id = h.product_id
where h.product_id = '$product'
and date(h.date) between '$start_date' and '$end_date'
   ";

//    echo nl2br($query);
    $products = Imx\db::dataQueryMultiple($query);
    if(count($products)==0)
    {
        return "<h3>Empty report</h3>";
    }
//print_r($products);

    $data = [
        'columnsData' => [
            [

                [
                    "key" => "brand",
                    "label" => "Brand",
                    "proportion" => 2,
                    "type" => "string"
                ],

                [
                    "key" => "category",
                    "label" => "category",
                    "proportion" => 2,
                    "type" => "string"
                ],
                [
                    "key" => "brand",
                    "label" => "Brand",
                    "proportion" => 2,
                    "type" => "string"
                ],

                [
                    "key" => "product",
                    "label" => "Product",
                    "proportion" => 6,
                    "type" => "string"
                ],

                [
                    "key" => "model",
                    "label" => "model",
                    "proportion" => 2,
                    "type" => "string"
                ],

                [
                    "key" => "sku",
                    "label" => "SKU",
                    "proportion" => 2,
                    "type" => "string"
                ],


            ],

            [

                [
                    "key" => "ws",
                    "label" => "Store",
                    "proportion" => 6,
                    "type" => "string"
                ],

            ],

            [


                [
                    "key" => "date",
                    "label" => "Date Updated",
                    "proportion" => 3,
                    "type" => "string"
                ],
                [
                    "key" => "price_url",
                    "label" => "Link",
                    "proportion" => 2,
                    "type" => "link"
                ],
                [
                    "key" => "price",
                    "label" => "Price",
                    "proportion" => 2,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,
                ],
                [
                    "key" => "milliseconds",
                    "label" => "Ms.",
                    "proportion" => 2,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,
                ],
            ],
        ],
        'data' => $products,
        'groupByData' => ["product", "ws"]

    ];
    $latte = new Latte\Engine;
    $data['latte'] = $latte;

    $report = $latte->renderToString('../app/templates/report.latte', $data);
    return " <div id='salesreport'>$report</div>";
});
