<?php
// * customers *
dispatch('/api/back-office/customers', function () {
    $table = '(select * from customers where row_status = 1 ) as  customers';


    // Table's primary key
    $primaryKey = 'customer_id';
    $columns = array(
        array('db' => 'customer_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        array('db' => 'credit', 'dt' => 2),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});



dispatch_post('/api/back-office/customers/:id', function ($id) {
    unset($_POST[0]['data']['credit']); //!! avoid credit modifications
    if ($id == "new") {
        $data = $_POST[0]['data'];
        $data['member_since']['value'] = date('Y-m-d');
        $id = Imx\db::i_post($data, "customers", "name");
        $response = [];
        if (is_numeric($id)) {
            $response['status'] = "ok";
        } else {
            $response['status'] = "error";
            $response['text'] = $id;
        }
        return json_encode($response);
    } else {
        // update creditwhen editing
        hdtv::updateCredit($id);
        $data = $_POST[0]['data'];
        $id = Imx\db::e_post($data, "customers", $id, "name", 'customer_id');
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
