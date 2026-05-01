<?php
// * warranties *
dispatch('/api/catalogs/warranties', function () {
    $table = '(select * from warranties where row_status = 1 ) as warranties';


    // Table's primary key
    $primaryKey = 'warranty_id';
    $columns = array(
        array('db' => 'warranty_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        array('db' => 'warranty_days', 'dt' => 2),
        array('db' => 'price', 'dt' => 3),
        array('db' => 'under_price', 'dt' => 4),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});



dispatch_post('/api/catalogs/warranties/:id', function ($id) {

    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "warranties");
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
        $id = Imx\db::e_post($data, "warranties", $id, "", 'warranty_id');
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
