<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// * invoices *
dispatch('/api/back-office/claims', function () {
    $filter = hdtv::storeFilter();
    $table = "(select * from view_claims where store_id in ($filter)) as vi";


    // Table's primary key
    $primaryKey = 'claim_id';
    $columns = array(
        array('db' => 'claim_id', 'dt' => 0),
        array(
            'db'        => 'date',
            'dt'        => 1,
            'formatter' => function ($d, $row) {
                return date('m/d/Y', strtotime($d));
            }
        ),
        array('db' => 'store', 'dt' => 2),
        array('db' => 'product', 'dt' => 3),
        array('db' => 'item', 'dt' => 4),
        array('db' => 'customer', 'dt' => 5),
        array('db' => 'status', 'dt' => 6),
        array('db' => 'status', 'dt' => 7),
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});


dispatch_post('/api/back-office/claims/:id', function ($id) {

    // validate existing items
    $_POST[0]['data']['date']['value'] = Imx\utils::date2sql($_POST[0]['data']['date']['value']);
    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "claims");
        // notify claim
        $user = $_SESSION['user']['name'];
        $store = Imx\db::dataQuery("select * from stores where store_id = '{$data['store_id']['value']}'");
        $customer = $data['customer_id']['label'];
        $emails = explode(",",$store['emailadmin']);
        if(count($emails)>0) {
            $date = date('m/d/Y', strtotime('yesterday'));
            $server = $_SERVER['SERVER_NAME'];
            $url = urlencode("https://{$server}/public/claims/{$id}/print");
            $uri = "https://{$server}/public/pdf/?pdf={$url}&file=Receipt.pdf";
            $pdf = file_get_contents($uri);
            $temp = tmpfile();
            $tmppath = stream_get_meta_data($temp)['uri'];
            fwrite($temp, $pdf);
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = $_ENV['APP_EMAIL_HOST'];
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['APP_EMAIL_USER'];
                $mail->Password = $_ENV['APP_EMAIL_PASSWORD'];
                $mail->SMTPSecure = ($_ENV['APP_EMAIL_SECURE'] == "ssl") ? 'ssl' : 'tsl';
                $mail->Port = $_ENV['APP_EMAIL_PORT'];
                $mail->setFrom($_ENV['APP_EMAIL'], $_ENV['APP_EMAIL']);
//                print_r($emails);
                foreach ($emails as $email) {
                    $mail->addAddress($email);
                }
                $mail->addAttachment($tmppath, 'Claim.pdf');

                $mail->isHTML(true);
                $mail->Subject = 'New Claim ' . $id . ' ' . $store['name'];
                $date = date('m/d/Y H:i:s');
                $mail->Body = "New Claim {$id} <br>
                 Store :{$store['name']} <br>
                 User : {$user}<br>
                 Customer : {$customer}<br>
                 Item : {$data['item_id']['label']}<br>
                 Observations : {$data['observations']['value']}<br>
                 Credit : {$data['credit']['value']}<br>
                 Date : {$date} ";
                $mail->send();
//                echo "Notification sent";

//        return json_encode(['status' => 'ok']);
            } catch (Exception $e) {
//                Imx\headers::error500();
                error_log("Cannot send notification, SMTP Error",0);
            }
        }




    } else {
        // Edit claim
        $data = $_POST[0]['data'];
        $orig_id = $id;
        $id = Imx\db::e_post($data, "claims", $id, "", 'claim_id');
        if (is_numeric($id)) {
            $id = $orig_id;

            // ! remove previous items
            Imx\db::iquery("update items set status = 2 , claim_id = null  where claim_id ='$id'");
            // ! delete transactions
            Imx\db::iquery("delete from transactions where transaction_type ='Claim' and reference_id ='$id'");
            // ! Delete credits
            Imx\db::iquery("delete from credits where credit_type ='Claim' and reference_id ='$id'");


            $user = $_SESSION['user']['name'];
            $store = Imx\db::dataQuery("select * from stores where store_id = '{$data['store_id']['value']}'");
            $customer = $data['customer_id']['label'];
            $emails = explode(",", $store['emailadmin']);
            if (count($emails) > 0) {
                $date = date('m/d/Y', strtotime('yesterday'));
                $server = $_SERVER['SERVER_NAME'];
                $url = urlencode("https://{$server}/public/claims/{$id}/print");
                $uri = "https://{$server}/public/pdf/?pdf={$url}&file=Receipt.pdf";
                $pdf = file_get_contents($uri);
                $temp = tmpfile();
                $tmppath = stream_get_meta_data($temp)['uri'];
                fwrite($temp, $pdf);
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = $_ENV['APP_EMAIL_HOST'];
                    $mail->SMTPAuth = true;
                    $mail->Username = $_ENV['APP_EMAIL_USER'];
                    $mail->Password = $_ENV['APP_EMAIL_PASSWORD'];
                    $mail->SMTPSecure = ($_ENV['APP_EMAIL_SECURE'] == "ssl") ? 'ssl' : 'tsl';
                    $mail->Port = $_ENV['APP_EMAIL_PORT'];
                    $mail->setFrom($_ENV['APP_EMAIL'], $_ENV['APP_EMAIL']);
//                print_r($emails);
                    foreach ($emails as $email) {
                        $mail->addAddress($email);
                    }
                    $mail->addAttachment($tmppath, 'Claim.pdf');

                    $mail->isHTML(true);
                    $mail->Subject = 'Claim modification ' . $id . ' ' . $store['name'];
                    $date = date('m/d/Y H:i:s');
                    $mail->Body = "Claim modification {$id} <br>
                 Store :{$store['name']} <br>
                 User : {$user}<br>
                 Customer : {$customer}<br>
                 Item : {$data['item_id']['label']}<br>
                 Observations : {$data['observations']['value']}<br>
                 Credit : {$data['credit']['value']}<br>
                 Date : {$date} ";
                    $mail->send();
//                echo "Notification sent";

//        return json_encode(['status' => 'ok']);
                } catch (Exception $e) {
//                Imx\headers::error500();
                    error_log("Cannot send notification, SMTP Error", 0);
                }


            }
        }
    }
    // ? Now we create the claim transaction
    $item = Imx\db::dataQuery("select * from items where item_id ='{$data['item_id']['value']}'");
//    print_r($item);
    $query = "
    insert into transactions
    (store_id,
        product_id,
        date,
        transaction_type,
        status_id,
        grade_id,
        notes,
        reference_id,
        quantity,
        price,
        item_id,
        warranty_id,
        warranty_price,
        serials,
        sale_price,
        `status`,
        total,user_id)
                            values
                            ('{$item['store_id']}',
                '{$item['product_id']}',
                 now(),
                'Claim',
                '{$item['status_id']}',
                '{$item['grade_id']}',
                '{$data['observations']['values']}',
                '$id',
                '1',
                '{$data['credit']['value']}',
                '{$item['item_id']}',
                '{$item['warranty_id']}',
                '{$item['warranty_price']}',
                '{$item['serial_number']}',
                '{$data['credit']['value']}',
                '1',
                '{$item['total']}','{$_SESSION['user']['user_id']}')
                        ";
    Imx\db::iquery($query);
    $customer = $data['customer_id']['value'];
    $credit = $data['credit']['value'];
    if($credit){
        Imx\db::iquery("insert into credits (customer_id,credit,credit_type,reference_id,observations)
values 
    ('{$customer}','{$credit}','Claim','{$id}','Claim {$id}');
");
    }
    hdtv::updateCredit($customer);
    $response = [];
    if (is_numeric($id)) {

        $response['status'] = "ok";
        return json_encode($response);
    } else {
        $response['status'] = "error";
        $response['text'] = $id;
    }
    return json_encode($response);
});
