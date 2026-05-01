<?php

use Cocur\Slugify\Slugify;


// * customers * 
dispatch('/api/catalogs/customers', function () {
    $table = 'view_customers';


    // Table's primary key
    $primaryKey = 'customer_id';
    $columns = array(
        array('db' => 'customer_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        array('db' => 'email',  'dt' => 2),
        array('db' => 'profile',   'dt' => 3),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});


dispatch('/api/catalogs/customers/:id', function ($id) {
    return Imx\utils::safe_json_encode(Imx\db::dataQuery("select * from customers where customer_id ='$id'"));
});


dispatch_get('/api/catalogs/customers/:id/face/delete/:key/:file', function ($id, $key, $file) {
    $file = "$key/$file";
    Imx\s3::removeFile($file);
    $pimg = trim(Imx\db::rquery("select face from customers where product_id ='$id'"));
    $pimg = explode(",", $pimg);
    $nimg = [];
    foreach ($pimg as $img) {
        if ($img != "$file")
            $nimg[] = $img;
    }
    $nimg = implode(',', $nimg);
    Imx\db::iquery("update customers set face ='$nimg' where product_id ='$id'");

    echo "<script>
    alert('File deleted');
    opener.location.href = opener.location.href;
    self.close();
    </script>";
});

dispatch_post('/api/catalogs/customers/:id', function ($id) {



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
    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "customers", "email");
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

        $pimg = trim(Imx\db::rquery("select face from customers where customer_id ='$id'"));
        if ($pimg != "" && $data['face']['value'] != "") {
            $data['face']['value'] = $pimg . "," . $data['face']['value'];
        }
        if ($pimg != "" && $data['face']['value'] == "") {
            $data['face']['value'] = $pimg;
        }



        $id = Imx\db::e_post($data, "customers", $id, "email", 'customer_id');
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
