<?php
dispatch_post('/api/reports/sales', function () {
    $store = $_POST[0]['data']['store']['value'];
    $status = $_POST[0]['data']['status']['value'];
    $user = $_POST[0]['data']['user']['value'];
    $customer = $_POST[0]['data']['customer']['value'];
    $end_date = Imx\utils::date2sql($_POST[0]['data']['end_date']['value']);
    $start_date = Imx\utils::date2sql($_POST[0]['data']['start_date']['value']);
    $wu = "";
    if($user){
        $wu = " and view_sales.user_id ='$user'";
    }

    $skipdates = $_POST[0]['data']['skipdates']['value'];

    $wd = "";
    $limit = "";
    if($skipdates){
        $limit = " limit 100 ";
    }
    else{
        $wd = " and date(view_sales.date) between '$start_date' and '$end_date'";

    }



    $wc = "";
    if($customer){
        $wc = " and view_sales.customer_id ='$customer'";
    }
    if($status){

    $query = "select 

view_sales.sale_id,
view_sales.store,
date_format(view_sales.date,'%m/%d/%Y') as date,
view_items.product,
view_items.status,
view_items.grade,
view_items.cost,
view_items.price,
view_items.warranty_price,
view_items.utility - view_invoices.shipping_item as utility,
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

   where view_sales.store_id ='$store'
     and view_sales.status_code ='$status'

{$wc}
{$wu}
{$wd}
{$limit}
 ";
    }
    else{

$query = "select 
view_sales.sale_id,
view_sales.store,
date_format(view_sales.date,'%m/%d/%Y') as date,
view_items.product,
view_items.status,
view_items.grade,
view_items.cost,
view_items.price,
view_items.warranty_price,
view_items.utility - view_invoices.shipping_item as utility,
view_items.customer,
view_sales.`user`,
view_invoices.shipping_item as shipping,
concat(view_invoices.provider,' ', view_invoices.document_number) as invoice,
brands.name as brand

 from view_sales        
  left join transactions on transactions.reference_id = view_sales.sale_id and transaction_type='Cancelation'
     left join view_items on view_items.item_id = transactions.item_id
     left join products on products.product_id = transactions.product_id
     left join brands on brands.brand_id = products.brand_id
 left join view_invoices on view_invoices.invoice_id = view_items.invoice_id
   where
        view_sales.store_id ='$store'
     and view_sales.status_code ='$status'
     {$wd}

{$wc}
{$wu}
{$limit}
 ";
    }
//        echo nl2br($query);

    $inventory = Imx\db::dataQueryMultiple($query);

    if(count($inventory)== 0){
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
                "key" => "utility",
                "label" => "Utility",
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
                    "key" => "utility",
                    "label" => "Utility",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,


                ],

            ],
             [

                [
                    "key" => "brand",
                    "label" => "Brand",
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
                [  "key" => "warranty_price",
                    "label" => "Warranty<br>Price",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,



                ], [
                    "key" => "utility",
                    "label" => "Utility",
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
                "key" => "product",
                "label" => "Product",
                "proportion" => 2,
                "type" => "string"
            ], [
                "key" => "customer",
                "label" => "Customer",
                "proportion" => 2,
                "type" => "string"
            ],

                [
                    'key' => 'invoice',
                    'label' => 'Invoice ',
                    "proportion" => 2,
                    "type" => "string"
                ],                [
                    'key' => 'status',
                    'label' => 'Status',
                    "proportion" => 1,
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
                "key" => "utility",
                "label" => "Utility",
                "proportion" => 1,
                "type" => "number",
                "align" => "right",

            ],

            ]
        ],
        'data' => $inventory,
        'groupByData' => ["store", "user","brand"]

    ];
    $latte = new Latte\Engine;
    $data['latte'] = $latte;

    $report = $latte->renderToString('../app/templates/report.latte', $data);
    return " <div id='salesreport'>$report</div>";


});
