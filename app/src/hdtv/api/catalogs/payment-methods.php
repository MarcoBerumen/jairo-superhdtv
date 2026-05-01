<?php
// * payment_methods *
dispatch('/api/catalogs/payment-methods', function () {
    $table = 'view_payment_methods';


    // Table's primary key
    $primaryKey = 'payment_method_id';
    $columns = array(
        array('db' => 'payment_method_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        array('db' => 'bank', 'dt' => 2),
        array('db' => 'contact', 'dt' => 3),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});



dispatch_post('/api/catalogs/payment-methods/:id', function ($id) {

    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "payment_methods", "name");
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
        $id = Imx\db::e_post($data, "payment_methods", $id, "name", 'payment_method_id');
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
