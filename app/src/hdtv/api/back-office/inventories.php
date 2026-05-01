<?php
// * invoices *
dispatch('/api/back-office/inventories', function () {
    $table = 'view_inventories';


    // Table's primary key
    $primaryKey = 'inventory_id';
    $columns = array(
        array('db' => 'inventory_id', 'dt' => 0),
        array('db' => 'store', 'dt' => 1),
        array('db' => 'date', 'dt' => 2,
            'formatter' => function ($d, $row) {
                return date('m/d/Y', strtotime($d));
            }),
        array('db' => 'total', 'dt' => 3),
        array('db' => 'user', 'dt' => 4),
        array('db' => 'status', 'dt' => 5),
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});


dispatch_post('/api/back-office/inventories/:id', function ($id) {

    $_POST[0]['data']['date']['value']= Imx\utils::date2sql($_POST[0]['data']['date']['value']);
    $_POST[0]['data']['total']['value']= $_POST[0]['data']['total_cost']['value'];
    $_POST[0]['data']['user_id']['value'] = $_SESSION['user']['user_id'];
    $_POST[0]['data']['status']['value'] = 'Pending';
    unset($_POST[0]['data']['total_cost']);

    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "inventories");
    } else {
        // Edit invoice
        $data = $_POST[0]['data'];
        $orig_id = $id;
        $id = Imx\db::e_post($data, "inventories", $id, "", 'inventory_id');
        if (is_numeric($id)) {
            $id = $orig_id;
            // ! delete transactions
            Imx\db::iquery("delete from transactions where transaction_type ='Inventory' and reference_id ='$id'");
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
            'Inventory',
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

        }



        $response['status'] = "ok";
        return json_encode($response);
    } else {
        $response['status'] = "error";
        $response['text'] = $id;
    }
    return json_encode($response);
});
