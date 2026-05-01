<?php

use Cocur\Slugify\Slugify;


// * features *
dispatch('/api/catalogs/features', function () {
    $table = 'features';


    // Table's primary key
    $primaryKey = 'feature_id';
    $columns = array(
        array('db' => 'feature_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        array('db' => 'type', 'dt' => 2),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});



dispatch_post('/api/catalogs/features/:id', function ($id) {
    $slugify = new Slugify();



    $_POST[0]['data']['slug']['value'] = $slugify->slugify($_POST[0]['data']['name']['value']);
    $_POST[0]['data']['categories']['value'] = implode(",", $_POST[0]['data']['categories']['value']);

    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "features", "slug");
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
        $id = Imx\db::e_post($data, "features", $id, "slug", 'feature_id');
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
