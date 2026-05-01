<?php

dispatch_post('/api/reports/items', function () {
    $store = $_POST[0]['data']['store']['value'];
    $status = $_POST[0]['data']['status']['value'];
    $query = "SELECT
	view_items.*,
	1 as cnt,
	case status_code
when 1 then 'Available'
when 2 then 'Sold'
when 3 then 'Claim'
when 4 then 'Out'
when 5 then 'Transfer'
end as status_str
	from view_items
WHERE
	view_items.store_id = '{$store}'
	and 
	view_items.status_code = '{$status}'
order by category,product
";

    $inventory = Imx\db::dataQueryMultiple($query);
//    print_r($inventory);
    $data = [
        'columnsData' => [
            [

                [
                    "key" => "category",
                    "label" => "Category",
                    "proportion" => 8,
                    "type" => "string"
                ],
                [
                    "key" => "cnt",
                    "label" => "Total items",
                    "proportion" => 1,
                    "type" => "numeric",
                    'totalKey' => true,
                    "align"=>"right"
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
                    "key" => "cnt",
                    "label" => "Total items",
                    "proportion" => 1,
                    "type" => "numeric",
                    'totalKey' => true,
                    "align"=>"right"
                ],
            ],
            [

                [
                    "key" => "product",
                    "label" => "Product",
                    "proportion" => 3,
                    "type" => "string"
                ],
                [
                    "key" => "model",
                    "label" => "Model",
                    "proportion" => 2,
                    "type" => "string"
                ],
                [
                    "key" => "serial_number",
                    "label" => "Serial Number",
                    "proportion" => 3,
                    "type" => "string"
                ],

                [
                    'key' => 'status',
                    'label' => 'Status',
                    "proportion" => 2,
                    "type" => "string"
                ],

                [
                    "key" => "grade",
                    "label" => "Grade",
                    "proportion" => 1,
                    "type" => "string"
                ],
                [
                    "key" => "status_str",
                    "label" => "Item Status",
                    "proportion" => 1,
                    "type" => "string",
                    "align"=> "right",

                ],

            ]
        ],
        'data' => $inventory,
        'groupByData' => ["category","brand"]

    ];
   $latte = new Latte\Engine;
    $data['latte'] = $latte;
    return $latte->renderToString('../app/templates/report.latte', $data);

});
