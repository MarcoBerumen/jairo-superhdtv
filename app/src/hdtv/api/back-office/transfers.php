<?php
// * invoices *
dispatch('/api/back-office/transfers', function () {
    $table = 'view_transfers';


    // Table's primary key
    $primaryKey = 'transfer_id';
    $columns = array(
        array('db' => 'transfer_id', 'dt' => 0),
        array('db' => 'origin', 'dt' => 1),
        array('db' => 'destination', 'dt' => 1),
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


dispatch_post('/api/back-office/transfers/:id', function ($id) {

    $_POST[0]['data']['date']['value']= Imx\utils::date2sql($_POST[0]['data']['date']['value']);
    $_POST[0]['data']['user_id']['value'] = $_SESSION['user']['user_id'];
    $_POST[0]['data']['status']['value'] = 'Pending';
    unset($_POST[0]['data']['total_cost']);
    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "transfers");
    } else {
        // Edit invoice
        $data = $_POST[0]['data'];
        $orig_id = $id;
        $id = Imx\db::e_post($data, "transfers", $id, "", 'transfer_id');
        if (is_numeric($id)) {
            $id = $orig_id;
            // ! delete transactions
            Imx\db::iquery("delete from transactions where transaction_type ='Transfer' and reference_id ='$id'");
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
            '{$_POST[0]['data']['destination_id']['value']}',
            '{$item['product_id']['value']}',
            'Transfer',
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
