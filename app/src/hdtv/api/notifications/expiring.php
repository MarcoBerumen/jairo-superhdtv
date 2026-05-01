<?php
dispatch('/public/notifications/expiring', function () {
    $store = $_GET['store']??"0";
    $expires = 30;
    $query = "SELECT
	view_items.*,
	 @days:=datediff( provider_warranty_date,current_date()) as days,
	case
	    when  @days >90  then
	    'More than 3 months'
	    when  @days >=60  then
	    'More than Two Month'
	    when  @days >=30  then
	    'More than one Month'
	    when  @days >=14  then
	        'More than 2 week'
	    when  @days >=7  then
	        'More than  week'
	    	    when  @days  <8   then
	        'Less than a week'
            when  @days  = 1  then
	        'One day'
	                when  @days  <= 0  then
	        'Expired'
	        end  as warranty_days,
	1 as cnt	from view_items
WHERE
	view_items.store_id = '{$store}'
	and 
	view_items.status_code = '1'
and datediff( provider_warranty_date,current_date()) <= '{$expires}'
order by datediff( provider_warranty_date,current_date()) desc ,brand,product
";

    $inventory = Imx\db::dataQueryMultiple($query);

    $data = [
        'columnsData' => [
            [

                [
                    "key" => "warranty_days",
                    "label" => "Warranty Days left",
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
                    "key" => "days",
                    "label" => "Days Left",
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


            ]
        ],
        'data' => $inventory,
        'groupByData' => ["warranty_days","brand"]

    ];
    $latte = new Latte\Engine;
    if(count($data)== 0)
    return "<h1>There is no  expiring  items in {$expires} days </h1>";
        $data['latte'] = $latte;
    $report = $latte->renderToString('../app/templates/report.latte', $data);
    return ($report)?$report:"Empty report";


});
