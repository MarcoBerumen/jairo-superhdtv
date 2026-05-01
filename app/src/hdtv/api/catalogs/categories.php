<?php
// * categories *
dispatch('/api/catalogs/categories', function () {
    $table = '(select * from categories where row_status = 1 )categories';


    // Table's primary key
    $primaryKey = 'category_id';
    $columns = array(
        array('db' => 'category_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});



dispatch_post('/api/catalogs/categories/:id', function ($id) {

    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "categories", "name");
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
        $id = Imx\db::e_post($data, "categories", $id, "name", 'category_id');
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
