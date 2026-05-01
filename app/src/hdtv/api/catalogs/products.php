<?php

use Cocur\Slugify\Slugify;

// * products *
dispatch('/api/catalogs/products', function () {
    $table = 'products';


    // Table's primary key
    $primaryKey = 'product_id';
    $columns = array(
        array('db' => 'product_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        array('db' => 'model', 'dt' => 2),
        array('db' => 'sku', 'dt' => 3),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});


dispatch('/api/catalogs/products/:id', function ($id) {
    return Imx\utils::safe_json_encode(Imx\db::dataQuery("select * from products where product_id ='$id'"));
});

dispatch_get('/api/catalogs/products/:id/images/delete/:key/:file', function ($id, $key, $file) {
    $file = "$key/$file";
    Imx\s3::removeFile($file);
    $pimg = trim(Imx\db::rquery("select images from products where product_id ='$id'"));
    $pimg = explode(",", $pimg);
    $nimg = [];
    foreach ($pimg as $img) {
        if ($img != "$file")
            $nimg[] = $img;
    }
    $nimg = implode(',', $nimg);
    Imx\db::iquery("update products set images ='$nimg' where product_id ='$id'");
    echo "<script>
    alert('File deleted');
    opener.location.href = opener.location.href;
    self.close();
    </script>";
});
dispatch_post('/api/catalogs/products/:id', function ($id) {
    $slugify = new Slugify();
    $img = $_POST[0]['data']['images']['data'];
    $files = [];
    $features = [];
    foreach ($_POST[1]['data'] as $key => $value) {
        $features[$key] = $value['value'];
    }
    $_POST[0]['data']['features']['value'] = json_encode($features);
    foreach ($img as &$archivo) {
        // GenerateCheckSum($archivo['data']) .
        $archivo['name'] =  substr($slugify->slugify($archivo['name']), 0, -4) . "-" . md5($archivo['data']) . "." .  pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $files[] = "products/{$archivo['name']}";
        $response = Imx\s3::storeString($archivo['name'], "products/", file_get_contents($archivo['data']));
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
    $_POST[0]['data']['images']['value'] = implode(",", $files);
    $_POST[0]['data']['images']['data'] = "";
    // print_r($img);
    // print_r($_POST);
    // exit;
    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "products", "name");
        $response = [];
        if (is_numeric($id)) {
            $response['status'] = "ok";
        } else {
            $response['status'] = "error";
            $response['text'] = $id;
        }
    } else {
        $data = $_POST[0]['data'];
        $pimg = trim(Imx\db::rquery("select images from products where product_id ='$id'"));
        if ($pimg != "" && $data['images']['value'] != "") {
            $data['images']['value'] = $pimg . "," . $data['images']['value'];
        }
        if ($pimg != "" && $data['images']['value'] == "") {
            $data['images']['value'] = $pimg;
        }
        $id = Imx\db::e_post($data, "products", $id, "name", 'product_id');
        $response = [];
        if (is_numeric($id)) {
            $response['status'] = "ok";
        } else {
            $response['status'] = "error";
            $response['text'] = $id;
        }
    }

    return json_encode($response);
});
