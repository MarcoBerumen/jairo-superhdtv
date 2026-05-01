<?php
// * stores *
dispatch('/api/catalogs/stores', function () {
    $table = '(select * from stores where row_status = 1) as store';


    // Table's primary key
    $primaryKey = 'store_id';
    $columns = array(
        array('db' => 'store_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        array('db' => 'address', 'dt' => 2),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});



dispatch_post('/api/catalogs/stores/:id', function ($id) {

    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "stores", "name");
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
        $id = Imx\db::e_post($data, "stores", $id, "name", 'store_id');
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
