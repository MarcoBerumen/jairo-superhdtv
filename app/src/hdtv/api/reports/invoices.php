<?php
dispatch_post('/api/reports/invoices', function () {
    $store = $_POST[0]['data']['store']['value'];
    $status = $_POST[0]['data']['status']['value'];
    $provider = $_POST[0]['data']['provider']['value'];
    $end_date = Imx\utils::date2sql($_POST[0]['data']['end_date']['value']);
    $start_date = Imx\utils::date2sql($_POST[0]['data']['start_date']['value']);
    $wp = "";
    if($provider){
        $wp = " and provider_id ='$provider'";
    }
    $wc = "";



        $query = "select 
*
 from view_invoices        

   where date(view_invoices.date) between '$start_date' and '$end_date'
       and view_invoices.store_id ='$store'

{$wp}
 ";

    $invoices = Imx\db::dataQueryMultiple($query);
    if(count($invoices)== 0){
        return "<h3>Empty report</h3>";
    }
    array_walk($invoices,function(&$invoice){
        $invoice['date'] = Imx\utils::sql2date($invoice['date']);
    });
    $data = [
        'columnsData' => [
            [

                [
                    "key" => "store",
                    "label" => "Store",
                    "proportion" => 4,
                    "type" => "string"
                ],
                [
                    "key" => "total_items",
                    "label" => "Items",
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
                ],
                [
                    "key" => "total_price",
                    "label" => "Total",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",


                ]
            ], [

                [
                    "key" => "provider",
                    "label" => "Provider",
                    "proportion" => 4,
                    "type" => "string"
                ],

                [
                    "key" => "total_items",
                    "label" => "Items",
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
                    "key" => "total_price",
                    "label" => "Total",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",
                    'totalKey' => true,


                ]
            ],

            [

                [
                    "key" => "document_number",
                    "label" => "Document Number",
                    "proportion" => 2,
                    "type" => "string"
                ], [
                "key" => "date",
                "label" => "Date",
                "proportion" => 2,
                "type" => "string"
            ],
                [
                    "key" => "total_items",
                    "label" => "Items",
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
                ],
                [
                    "key" => "total_price",
                    "label" => "Total",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",


                ]

            ]
        ],
        'data' => $invoices,
        'groupByData' => ["store", "provider"]

    ];
    $latte = new Latte\Engine;
    $data['latte'] = $latte;

    $report = $latte->renderToString('../app/templates/report.latte', $data);
    return " <div id='salesreport'>$report</div>";


});
