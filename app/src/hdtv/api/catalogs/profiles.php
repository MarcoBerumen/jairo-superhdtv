<?php
// * profiles *
dispatch('/api/catalogs/profiles', function () {
    $table = 'profiles';


    // Table's primary key
    $primaryKey = 'profile_id';
    $columns = array(
        array('db' => 'profile_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});



dispatch_post('/api/catalogs/profiles/:id', function ($id) {
//print_r($_POST);
$permissions = [];
foreach($_POST[1] as $p){
//    print_r($p);
    if(count($p['children'])){
        foreach($p['children'] as $c){
            if($c['state']['selected'])
                $permissions[trim($p['text'])][trim($c['text'])] =true;
        }
    }
    else
    {
        if($p['state']['selected'])
        $permissions[trim($p['text'])] = true;
    }
}
//print_r($permissions);
//exit;
$permissions = json_encode($permissions);
$_POST[0]['data']['permissions']['value'] = $permissions;
    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "profiles", "name");
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
        $id = Imx\db::e_post($data, "profiles", $id, "name", 'profile_id');
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
