<?php
// * invoices *
dispatch('/api/back-office/outgoing-inventory', function () {
    $table = 'view_out_inventory';


    // Table's primary key
    $primaryKey = 'out_inventory_id';
    $columns = array(
        array('db' => 'out_inventory_id', 'dt' => 0),
        array(
            'db'        => 'date',
            'dt'        => 1,
            'formatter' => function ($d, $row) {
                return date('m/d/Y', strtotime($d));
            }
        ),
        array('db' => 'store', 'dt' => 2),
        array('db' => 'motive', 'dt' => 3),
        array('db' => 'total_cost', 'dt' => 4),
        array('db' => 'status', 'dt' => 5),
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});


dispatch_post('/api/back-office/outgoing-inventory/:id', function ($id) {

    // validate existing items
    $items = $_POST[1]['data'];
    foreach ($items as $item) {


        // * validate each item is stilll avaiable for sold
        $q = "select count(*) from items where item_id ='{$item['item_id']}' and out_inventory_id != '$id'";
        if (Imx\db::rquery($q) > 0) {
            $response = [];
            $response['status'] = "error";
            $response['text'] = "One Item you selected for outgoing inventory is already sold";
            return json_encode($response);
        }
    }

    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "out_inventory");
    } else {
        // Edit invoice
        $data = $_POST[0]['data'];
        $orig_id = $id;
        $id = Imx\db::e_post($data, "out_inventory", $id, "", 'out_inventory_id');
        if (is_numeric($id)) {
            $id = $orig_id;

            // ! remove previous items
            Imx\db::iquery("update items set status = 1 , out_inventory_id = null  where out_inventory_id ='$id'");
            // ! delete transactions
            Imx\db::iquery("delete from transactions where transaction_type ='Out of Inventory' and reference_id ='$id'");
            // * Update inventory of deleted items
            foreach ($items as $item) {
                hdtv::inventory(
                    $item['product_id']['value'],
                    $data['store_id']['value'],
                    $item['status_id']['value'],
                    $item['grade_id']['value']
                );
            }
        }
    }
    $response = [];
    if (is_numeric($id)) {
        // CREAMOS LOS ITEMS 
        $items = $_POST[1]['data'];
        $product = $data['product_id']['value'];
        foreach ($items as $item) {
            // *  process each item group and store on transactions table 

            $date = $_POST[0]['data']['date']['value'];
            Imx\db::iquery("
            insert into transactions
            (
            store_id,
            product_id,
            transaction_type,
            status_id,
            grade_id,
            date,
            item_id,
            reference_id,
            status,
            quantity,
            price,
            total
            )
            values
            (
            '{$_POST[0]['data']['store_id']['value']}',
            '{$item['product_id']['value']}',
            'Out of Inventory',
            '{$item['status_id']['value']}',
            '{$item['grade_id']['value']}',
            '$date',
            '{$item['item_id']['value']}',
            '{$id}',
            '1',
            '1',
            '{$item['cost']['value']}',
            '{$item['cost']['value']}'
            );
            ");
            // TODO append to taxes
            // ! Modify item status (remove from stock)
            Imx\db::iquery("update items set status = 4, out_inventory_id =  '$id'  where item_id ='{$item['item_id']['value']}'");
            // * create and update stock 
            hdtv::inventory(
                $item['product_id']['value'],
                $_POST[0]['data']['store_id']['value'],
                $item['status_id']['value'],
                $item['grade_id']['value']
            );
        }



        $response['status'] = "ok";
        return json_encode($response);
    } else {
        $response['status'] = "error";
        $response['text'] = $id;
    }
    return json_encode($response);
});
