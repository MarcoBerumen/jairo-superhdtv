<?php
dispatch_post('/api/reports/comissions', function () {
    $store = $_POST[0]['data']['store']['value'];
    $status = $_POST[0]['data']['status']['value'];
    $user = $_POST[0]['data']['user']['value'];
    $end_date = Imx\utils::date2sql($_POST[0]['data']['end_date']['value']);
    $start_date = Imx\utils::date2sql($_POST[0]['data']['start_date']['value']);
    $wu = "";
    if($user){
        $wu = " and view_sales.user_id ='$user'";
    }


    $query = "select 

view_sales.sale_id,
view_sales.store,
view_sales.comission as scomission,
view_items.comission,
date(view_sales.date),
view_items.product,
view_items.status,
view_items.grade,
view_items.cost,
view_items.price,
view_items.warranty_price,
view_items.customer,
view_sales.`user`,
view_invoices.shipping_item as shipping,
concat(view_invoices.provider,' ', view_invoices.document_number) as invoice,
brands.name as brand

 from view_sales        
  left join view_items on view_items.sale_id = view_sales.sale_id
     left join products on products.product_id = view_items.product_id
     left join brands on brands.brand_id = products.brand_id
 left join view_invoices on view_invoices.invoice_id = view_items.invoice_id

   where date(view_sales.date) between '$start_date' and '$end_date'
       and view_sales.store_id ='$store'
     and view_sales.status_code ='1'
{$wu}
 ";

//    echo nl2br($query);
    $comissions_report = Imx\db::dataQueryMultiple($query);
    if(count($comissions_report)== 0){
        return "<h3>Empty report</h3>";
    }
    $data = [
        'columnsData' => [
            [

                [
                    "key" => "store",
                    "label" => "Store",
                    "proportion" => 8,
                    "type" => "string"
                ],
                [
                    "key" => "cost",
                    "label" => "Cost",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,


                ],
                [
                    "key" => "shipping",
                    "label" => "Shipping",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,
                ],
                [
                    "key" => "price",
                    "label" => "Price",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,


                ], [                "key" => "warranty_price",
                "label" => "Warranty<br>Price",
                "proportion" => 1,
                "type" => "number",
                "align" => "right",
                'totalKey' => true,


            ], [
                "key" => "scomission",
                "label" => "Sale Comission",
                "proportion" => 1,
                "type" => "number",
                "align" => "right",
                'totalKey' => true,

            ],

            ], [

                [
                    "key" => "user",
                    "label" => "Seller",
                    "proportion" => 8,
                    "type" => "string"
                ],
                [
                    "key" => "cost",
                    "label" => "Cost",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,


                ],
                [
                    "key" => "shipping",
                    "label" => "Shipping",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,
                ],
                [
                    "key" => "price",
                    "label" => "Price",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,


                ],
                [                "key" => "warranty_price",
                    "label" => "Warranty<br>Price",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,



                ], [
                    "key" => "scomission",
                    "label" => "Comission",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,


                ],

            ],

            [

                [
                    "key" => "sale_id",
                    "label" => "Sale",
                    "proportion" => 1,
                    "type" => "string"
                ],
                [
                    "key" => "date",
                    "label" => "Date",
                    "proportion" => 1,
                    "type" => "string"
                ],
                [
                "key" => "customer",
                "label" => "Customer",
                "proportion" => 2,
                "type" => "string"
            ],

                [
                "key" => "scomission",
                "label" => "Comission",
                "proportion" => 1,
                "type" => "number",
                "align" => "right",
                    "totalkey" =>true

            ],

            ],
            [
                [
                    "key" => "brand",
                    "label" => "Brand",
                    "proportion" => 2,
                    "type" => "string"
                ],
               [
                "key" => "product",
                "label" => "Product",
                "proportion" => 4,
                "type" => "string"
            ],
                [
                    "key" => "grade",
                    "label" => "Grade",
                    "proportion" => 1,
                    "type" => "string"
                ],
                [
                    "key" => "cost",
                    "label" => "Cost",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",

                ],
                [
                    "key" => "shipping",
                    "label" => "Shipping",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,
                ],
                [
                    "key" => "price",
                    "label" => "Price",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",

                ], [                "key" => "warranty_price",
                "label" => "Warranty<br>Price",
                "proportion" => 1,
                "type" => "number",
                "align" => "right",
                'totalKey' => true,


            ], [
                "key" => "comission",
                "label" => "Comission",
                "proportion" => 1,
                "type" => "number",
                "align" => "right",

            ],

            ]
        ],
        'data' => $comissions_report,
        'groupByData' => ["store", "user","sale_id"]

    ];
    $latte = new Latte\Engine;
    $data['latte'] = $latte;

    $report = $latte->renderToString('../app/templates/report.latte', $data);
    return " <div id='salesreport'>$report</div>";


});
