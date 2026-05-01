<?php
dispatch_post('/api/back-office/product-pricing/:list/:id', function ($list, $id) {


    // * PRICESS PRICES
    $data = $_POST[1]['data'];
    foreach ($data as $key => $price) {
        $sg = explode("_", $key);
        $field = $sg[0];
        if ($field == "min") {
            $field = "min_price";
            $status = $sg[2];
            $grade = $sg[3];
        } else {
            $status = $sg[1];
            $grade = $sg[2];
        }
        $price = $price['value'];
        if (intval($price) == 0) {
            continue;
        }
        $query = "select price,min_price  from product_pricing where product_id = '{$id}' 
        and status_id = '{$status}'
        and grade_id = '{$grade}'";
        $data = Imx\db::dataQuery($query);
        if (count($data)) {
            Imx\db::iquery("update product_pricing set {$field} ='$price'
            where product_id = '{$id}' 
            and status_id = '{$status}'
            and grade_id = '{$grade}'
            ");
        } else {
            Imx\db::iquery("insert into product_pricing 
            (product_id,status_id,grade_id,{$field})
            values('{$id}','{$status}','$grade','$price')");
        }
    }
    return "recarga";
});
