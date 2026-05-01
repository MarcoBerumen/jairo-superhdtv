<?php
// * brands *
dispatch('/api/catalogs/brands', function () {
    $table = '(select * from brands where row_status = 1 ) as brands';


    // Table's primary key
    $primaryKey = 'brand_id';
    $columns = array(
        array('db' => 'brand_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        array('db' => 'country', 'dt' => 2),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});



dispatch_post('/api/catalogs/brands/:id', function ($id) {

    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "brands", "name");
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
        $id = Imx\db::e_post($data, "brands", $id, "name", 'brand_id');
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
