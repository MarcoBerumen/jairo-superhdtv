<?php

use Cocur\Slugify\Slugify;


// * USERS * 
dispatch('/api/catalogs/users', function () {
    $filter = hdtv::storeFilter();
    $table = "( select * from view_users where store_id in ($filter)) as f ";
    // Table's primary key
    $primaryKey = 'user_id';
    $columns = array(
        array('db' => 'user_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        array('db' => 'store', 'dt' => 2),
        array('db' => 'email',  'dt' => 3),
        array('db' => 'profile',   'dt' => 4),
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});


dispatch('/api/catalogs/users/:id', function ($id) {
    return Imx\utils::safe_json_encode(Imx\db::dataQuery("select * from users where user_id ='$id'"));
});

dispatch_get('/api/catalogs/users/:id/face/delete/:key/:file', function ($id, $key, $file) {
    $file = "$key/$file";
    Imx\s3::removeFile($file);
    $pimg = trim(Imx\db::rquery("select face from users where product_id ='$id'"));
    $pimg = explode(",", $pimg);
    $nimg = [];
    foreach ($pimg as $img) {
        if ($img != "$file")
            $nimg[] = $img;
    }
    $nimg = implode(',', $nimg);
    Imx\db::iquery("update users set face ='$nimg' where product_id ='$id'");

    echo "<script>
    alert('Face deleted');
    opener.location.href = opener.location.href;
    self.close();
    </script>";
});

dispatch_post('/api/catalogs/users/:id', function ($id) {


    $_POST[0]['data']['bo_stores']['value'] = implode(",",$_POST[0]['data']['bo_stores']['value']);

    $slugify = new Slugify();
    $img = $_POST[0]['data']['face']['data'];
    $files = [];
    $features = [];

    foreach ($img as &$archivo) {
        // GenerateCheckSum($archivo['data']) .
        $archivo['name'] =  substr($slugify->slugify($archivo['name']), 0, -4) . "-" . md5($archivo['data']) . "." .  pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $files[] = "face/{$archivo['name']}";
        $response = Imx\s3::storeString($archivo['name'], "face/", file_get_contents($archivo['data']));
        if (!isset($response['valid'])) {
            $response['status'] = "error";
            $response['text'] = $response['error'];
            return json_encode($response);
        }

        // $files[] = $archivo['name'];
        // file_put_contents("./files/" . $archivo['name'], file_get_contents($archivo['data']));
        unset($archivo['data']);
    }
    // print_r($files);
    $_POST[0]['data']['face']['value'] = implode(",", $files);
    $_POST[0]['data']['face']['data'] = "";
    $_POST[0]['data']['password']['value'] = Imx\utils::encrypt($_POST[0]['data']['password']['value']);
    $_POST[0]['data']['workdays']['value'] = json_encode($_POST[0]['data']['workdays']['value']);
    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "users", "email");
        $response = [];
        if (is_numeric($id)) {
            hdtv::indexFace($id);
            $response['status'] = "ok";
        } else {
            $response['status'] = "error";
            $response['text'] = $id;
        }
    } else {
        $data = $_POST[0]['data'];
        $pimg = trim(Imx\db::rquery("select face from users where user_id ='$id'"));
        if ($pimg != "" && $data['face']['value'] != "") {
            $data['face']['value'] = $pimg . "," . $data['face']['value'];
        }
        if ($pimg != "" && $data['face']['value'] == "") {
            $data['face']['value'] = $pimg;
        }

        $valid = Imx\db::e_post($data, "users", $id, "email", 'user_id');
        $response = [];
        if (is_numeric($valid)) {
        hdtv::updateCredit($id);
            hdtv::indexFace($id);
            $response['status'] = "ok";
        } else {
            $response['status'] = "error";
            $response['text'] = $id;
        }
    }
    // * clear previous comissions 
    $q = "delete from users_comissions where user_id ='$id'";
    Imx\db::iquery($q);
    // * Store user comission payload  [Category, Comission Type, Start Range $, Comission]
    $comissions = $_POST[1]['data'];
    foreach ($comissions as $comission) {
        $comission['user_id']['value'] = $id;
        Imx\db::i_post($comission, "users_comissions");
    }

    return json_encode($response);
});
