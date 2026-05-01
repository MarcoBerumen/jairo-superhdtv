<?php

dispatch_post('/api/reports/inventory', function () {
//    print_r($_POST[0]);
    $store = $_POST[0]['data']['store']['value'];
    $category = $_POST[0]['data']['category_id']['value'];
    $brand = $_POST[0]['data']['brand']['value'];
    $status = $_POST[0]['data']['status']['value'];
    $feature = $_POST[0]['data']['feature_id']['value'];
    $feature_filter = $_POST[0]['data']['feature']['value'];
    $wbrand = "";
    $wstatus = "";
    $wfeatures = "";
    $wcat = "";
    if ($brand)
        $wbrand = " and brands.brand_id = '{$brand}'";
    if ($status)
        $wstatus = " and status.status_id = '{$status}'";
    if($category)
        $wcat = " and products.category_id ='{$category}'";
    if($feature &&$feature_filter){
        $slug = Imx\db::rquery("select slug from features where feature_id ='{$feature}'");
        $type = Imx\db::rquery("select type from features where feature_id ='{$feature}'");
        echo $type;
        switch($type){
            case "List":
                if(count($feature_filter)>1){

                $options = implode("\"','\"", $feature_filter);
                $wfeatures = " and JSON_EXTRACT(features, '$.{$slug}') IN ('\"$options\"') \n";
                }
                else
                {
                    $wfeatures = " and JSON_EXTRACT(features, '$.{$slug}') IN ('{$feature_filter[0]}') \n";
                }
                break;
            default:
                $options = implode(",", $feature_filter);
                $wfeatures = " and JSON_EXTRACT(features, '$.{$slug}') IN ({$options}) \n";

        }
//        echo $wfeatures;
    }
    $query = "SELECT
	stock.stock,
	products.product_id,
	products.name AS product,
	products.model,
	brands.name AS brand,
	status.name AS status,
	grades.name AS grade ,
       categories.name as category
FROM
	stock
	LEFT JOIN products ON products.product_id = stock.product_id
	LEFT JOIN categories ON categories.category_id = products.category_id
	LEFT JOIN brands ON brands.brand_id = products.brand_id
	LEFT JOIN status ON status.status_id = stock.status_id
	LEFT JOIN grades ON grades.grade_id = stock.grade_id 
WHERE
	stock.store_id = {$store}
    $wstatus
	    $wcat
	    $wfeatures
    $wbrand ";

    $inventory = Imx\db::dataQueryMultiple($query);
    $data = [
        'columnsData' => [
            [

                [
                    "key" => "brand",
                    "label" => "Brand",
                    "proportion" => 3,
                    "type" => "string"
                ],                [
                "key" => "stock",
                "label" => "Stock",
                "proportion" => 1,
                'totalKey'=>true,
                "align"=> "right",
                "type" => "number"
            ],
            ],           [

                [
                    "key" => "category",
                    "label" => "Category",
                    "proportion" => 3,
                    "type" => "string"
                ],                [
                "key" => "stock",
                "label" => "Stock",
                "proportion" => 1,
                'totalKey'=>true,
                "align"=> "right",
                "type" => "number"
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
                    "key" => "stock",
                    "label" => "Stock",
                    "proportion" => 1,
                    "type" => "number",
                    "align"=> "right",

                ],

            ]
        ],
        'data' => $inventory,
        'groupByData' => ["brand","category"]

    ];
   $latte = new Latte\Engine;
    $data['latte'] = $latte;
    return $latte->renderToString('../app/templates/report.latte', $data);

});
