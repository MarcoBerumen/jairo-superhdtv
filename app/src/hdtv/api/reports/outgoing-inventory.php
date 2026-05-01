<?php
dispatch_post('/api/reports/outgoing-inventory', function () {
    $store = $_POST[0]['data']['store']['value'];
    $status = $_POST[0]['data']['status']['value'];
    $skipdates = $_POST[0]['data']['skipdates']['value'];
    $end_date = Imx\utils::date2sql($_POST[0]['data']['end_date']['value']);
    $start_date = Imx\utils::date2sql($_POST[0]['data']['start_date']['value']);
    $ws = "";
    if($status){
        $ws = " and view_out_inventory.status ='$status'";
    }
    $wd = "";
    $limit = "";
    if($skipdates){
        $limit = " limit 100 ";
    }
    else{
        $wd = "and  date(view_out_inventory.date) between '$start_date' and '$end_date' ";

    }

    $query = "select 
view_out_inventory.*,
1 as count,
view_invoices.provider,
view_items.product,
view_items.serial_number,
view_invoices.date as purchase_date
 from view_out_inventory        
     	left join transactions on transactions.reference_id = view_out_inventory.out_inventory_id and transactions.transaction_type='Out of Inventory'
	LEFT JOIN view_items ON view_items.item_id = transactions.item_id
	LEFT JOIN view_invoices ON view_invoices.invoice_id = view_items.invoice_id 
   where view_out_inventory.store_id ='$store'
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

            ], [

                [
                    "key" => "out_inventory_id",
                    "label" => "Out Inv. Id",
                    "proportion" => 2,
                    "type" => "string"
                ],
                [
                    "key" => "provider",
                    "label" => "Provider",
                    "proportion" => 2,
                    "type" => "string"
                ],




                [
                    "key" => "serial_number",
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
                    "key" => "status",
                    "label" => "Status",
                    "proportion" => 2,
                    "type" => "text",
                    "align" => "right",
                ],
                [
                    "key" => "motive",
                    "label" => "Motive",
                    "proportion" => 4,
                    "type" => "text",
                    "align" => "right",
                ],

            ],
        ],
        'data' => $claims,
        'groupByData' => ["store"]

    ];
    $latte = new Latte\Engine;
    $data['latte'] = $latte;

    $report = $latte->renderToString('../app/templates/report.latte', $data);
    return " <div id='salesreport'>$report</div>";


});
