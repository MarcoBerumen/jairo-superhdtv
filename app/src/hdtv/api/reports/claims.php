<?php
dispatch_post('/api/reports/claims', function () {
    $store = $_POST[0]['data']['store']['value'];
    $status = $_POST[0]['data']['status']['value'];
    $skipdates = $_POST[0]['data']['skipdates']['value'];
    $end_date = Imx\utils::date2sql($_POST[0]['data']['end_date']['value']);
    $start_date = Imx\utils::date2sql($_POST[0]['data']['start_date']['value']);
    $ws = "";
    if($status){
        $ws = " and view_claims.status ='$status'";
    }
    $wd = "";
    $limit = "";
    if($skipdates){
        $limit = " limit 100 ";
    }
    else{
        $wd = "and  date(view_claims.date) between '$start_date' and '$end_date' ";

    }

    $query = "select 
view_claims.*,
1 as count,
view_invoices.provider,
date(items.sold_date) as sold_date,
items.sale_id,
view_invoices.date as purchase_date
 from view_claims        
 left join items on items.item_id = view_claims.item_id 
left join view_invoices on view_invoices.invoice_id = items.invoice_id
   where view_claims.store_id ='$store'
       {$wd}
{$ws}
{$limit}
 ";
//    echo $query;

//    return $query;
    $claims = Imx\db::dataQueryMultiple($query);
    if(count($claims)== 0){
        return "<h3>Empty report</h3>";
    }
    array_walk($claims,function(&$claim){
        $claim['sold_date'] = Imx\utils::sql2date($claim['sold_date']);
        $claim['purchase_date'] = Imx\utils::sql2date($claim['purchase_date']);
    });
//    print_r($claims);

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
                    "key" => "count",
                    "label" => "Claims",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,
                ],
                [
                    "key" => "credit",
                    "label" => "Credit",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,
                ],

            ], [

                [
                    "key" => "provider",
                    "label" => "Provider",
                    "proportion" => 8,
                    "type" => "string"
                ],
                [
                    "key" => "count",
                    "label" => "Claims",
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
                    "key" => "customer",
                    "label" => "Customer",
                    "proportion" => 3,
                    "type" => "string"
                ],
                [
                    "key" => "item",
                    "label" => "Serial Number",
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
                    "key" => "purchase_date",
                    "label" => "Purchase Date",
                    "proportion" => 2,
                    "type" => "text",
                    "align" => "right",
                ],
                [
                    "key" => "sold_date",
                    "label" => "Sold Date",
                    "proportion" => 2,
                    "type" => "text",
                    "align" => "right",
                ],
                [
                    "key" => "credit",
                    "label" => "Credit",
                    "proportion" => 2,
                    "type" => "text",
                    "align" => "right",
                ],
                [
                    "key" => "status",
                    "label" => "Status",
                    "proportion" => 2,
                    "type" => "text",
                    "align" => "right",
                ],
                [
                    "key" => "observations",
                    "label" => "Observations",
                    "proportion" => 4,
                    "type" => "text",
                    "align" => "right",
                ],

            ],
        ],
        'data' => $claims,
        'groupByData' => ["store", "provider"]

    ];
    $latte = new Latte\Engine;
    $data['latte'] = $latte;

    $report = $latte->renderToString('../app/templates/report.latte', $data);
    return " <div id='salesreport'>$report</div>";


});
