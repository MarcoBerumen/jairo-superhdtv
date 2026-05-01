<?php
// * profiles *
dispatch('/api/catalogs/price-lists', function () {
    $table = 'price_lists';


    // Table's primary key
    $primaryKey = 'price_list_id';
    $columns = array(
        array('db' => 'price_list_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});



dispatch_post('/api/catalogs/price-lists/:id', function ($id) {

    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "price_lists", "name");
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
        $id = Imx\db::e_post($data, "price_lists", $id, "name", 'price_list_id');
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
