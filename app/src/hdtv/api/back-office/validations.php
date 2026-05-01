<?php
// BACKEND VALIDATIONS FILE
/**
 * Validate a serial number that doesnt exist;
 */
dispatch("/api/validation/serials/:serial", function ($serial) {
    $id = $_GET['id'] ?? "";
    if (isset($_GET['exists'])) {
        $store = $_GET['store'];
        // We lookup for items that exists and belongs to the current store.
        $item = Imx\db::dataQuery("select * from items where serial_number ='$serial' and store_id = '$store' and status = 1");
        if (count($item) == 0) {
            header("HTTP/1.1 409 Conflict");
        } else {
            return "ok";
        }
    }
    $item = Imx\db::dataQuery("select * from items where serial_number ='$serial' and invoice_id <> '$id'");

    if (count($item) > 0) {
        header("HTTP/1.1 409 Conflict");
    } else {
        return "ok";
    }
});
/**
 * Price check endpoint 
 */
dispatch('/api/products/:id/prices', function ($id) {
    $grade = $_GET['grade'] ?? "";
    $status = $_GET['status'] ?? "";
    $store = $_GET['store'] ?? "";
    $price_list = Imx\db::rquery("select price_list_id from stores where row_status = 1 and store_id ='$store'");
    $stock_type = (Imx\db::rquery("select stock_type from products  where product_id = '{$id}'  ") == "1") ? "Bulk" : "Unique Serial";
    $query = "select price,min_price ,cost,'$stock_type' as stock_type from product_pricing where product_id = '{$id}' 
    and price_list_id = '{$price_list}'
    and status_id = '{$status}'
    and grade_id = '{$grade}'";
    $data = Imx\db::dataQuery($query);
    $items = Imx\db::dataQueryMultiple("select item_id,serial_number from items where product_id = '{$id}' 
    and store_id = '{$store}'
    and status_id = '{$status}'
    and grade_id = '{$grade}' and status =1 ");
    $stock = count($items);
    if (count($data)) {
        $data['items'] = $items;
        $data['stock'] = $stock;
        return json_encode($data);
    } else {
        return json_encode([
            "price" => 0.00,
            "min_price" => 0.00,
            "items" => [],
            "cost" => 0.00,
            "stock" => 0,
            "stock_type" => $stock_type
        ]);
    }
});
