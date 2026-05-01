<?php
dispatch("/api/items/:serial",function($serial){
    Imx\headers::json();
    $store = $_GET['store'];
    $item = Imx\db::dataQuery("select * from view_items where serial_number ='$serial'");
    if($item == [])
    {
        return json_encode([
            "item" => $serial,
            "error" => "Not found"
        ]);
    }
    else
    {
//        print_r($item);
//    return json_encode($item);
        $error = ($store == $item['store_id'])?"Ok":"Invalid store";
        if($item['status_code'] != "1")
            $error = "Item not available";
        $data = [
            "stock_type"=>"stock_type",
"product_id" => [
    "label"=> $item['product'],
    "type"=> "select",
    "value"=> $item['product_id']
],
"item_id" =>[
    "label"=> $item['serial_number'],
    "type"=> "select",
    "value"=> $item['item_id']
],
"status_id" =>[
    "label"=> $item['status'],
    "type"=> "select",
    "value"=> $item['status_id']
],
"grade_id" =>[
    "label"=> $item['grade'],
    "type"=> "select",
    "value"=> $item['grade_id']
],
"stock_type" =>[
    "label"=> $item['stock_type'],
    "type"=> "text",
    "label"=> $item['stock_type'],
],
"cost" =>[
    "label"=> $item['cost'],
    "type"=> "text",
    "value"=> $item['cost']
]
        ];
        return json_encode([
            "item" => $serial,
            "error" => $error,
            "data" => $data
        ]);
    }
});
