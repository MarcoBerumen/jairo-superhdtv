<?php
// * bank_accounts *
dispatch('/api/catalogs/bank-accounts', function () {
    $table = 'bank_accounts';


    // Table's primary key
    $primaryKey = 'bank_account_id';
    $columns = array(
        array('db' => 'bank_account_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        array('db' => 'number', 'dt' => 2),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});



dispatch_post('/api/catalogs/bank-accounts/:id', function ($id) {

    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "bank_accounts", "number");
        $response = [];
        if (is_numeric($id)) {
            $response['status'] = "ok";
        } else {
            $response['status'] = "error";
            $response['text'] = $id;
        }
        return json_encode($response);
    } else {
        $data = $_POST[0]['data'];
        $id = Imx\db::e_post($data, "bank_accounts", $id, "number", 'bank_account_id');
        $response = [];
        if (is_numeric($id)) {
            $response['status'] = "ok";
        } else {
            $response['status'] = "error";
            $response['text'] = $id;
        }
        return json_encode($response);
    }
});
