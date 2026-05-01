<?php
// * grades *
dispatch('/api/catalogs/grades', function () {
    $table = '(select * from grades where row_status = 1) as g';
    // Table's primary key
    $primaryKey = 'grade_id';
    $columns = array(
        array('db' => 'grade_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});



dispatch_post('/api/catalogs/grades/:id', function ($id) {
    $_POST[0]['data']['status']['value'] = implode(",", $_POST[0]['data']['status']['value']);

    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "grades", "name");
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
        $id = Imx\db::e_post($data, "grades", $id, "name", 'grade_id');
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
