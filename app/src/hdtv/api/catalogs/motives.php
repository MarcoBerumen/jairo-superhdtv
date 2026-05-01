<?php
// * motives *
dispatch('/api/catalogs/motives', function () {
    $table = 'motives';


    // Table's primary key
    $primaryKey = 'motive_id';
    $columns = array(
        array('db' => 'motive_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});



dispatch_post('/api/catalogs/motives/:id', function ($id) {

    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "motives", "name");
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
        $id = Imx\db::e_post($data, "motives", $id, "name", 'motive_id');
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
