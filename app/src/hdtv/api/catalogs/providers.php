<?php
// * providers *
dispatch('/api/catalogs/providers', function () {
    $table = '(select * from providers where row_status =1 ) as providers';


    // Table's primary key
    $primaryKey = 'provider_id';
    $columns = array(
        array('db' => 'provider_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        array('db' => 'email', 'dt' => 2),
        array('db' => 'credit_line', 'dt' => 3),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});


dispatch('/api/catalogs/providers/:id', function ($id) {
    return Imx\utils::safe_json_encode(Imx\db::dataQuery("select * from providers where provider_id ='$id'"));
});

dispatch_post('/api/catalogs/providers/:id', function ($id) {

    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "providers", "name");
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
        $id = Imx\db::e_post($data, "providers", $id, "name", 'provider_id');
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
