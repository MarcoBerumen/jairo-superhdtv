<?php

use Spipu\Html2Pdf\Html2Pdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

dispatch("/api/app/sales/:id/receipt/signature", function ($id) {
    // header("Content-type: image/png");

    $key = Imx\s3::getAsLink("/sales/$id/signatures/Signature.webp");
    $signature =  Imx\s3::getAsLink($key);
    $file = file_get_contents($signature);
    echo $signature;
    echo base64_encode($file);
    echo "fin";
    exit;

    exit;
    if (filter_var($signature, FILTER_VALIDATE_URL) === false) {
        $signature = str_replace("data:image/png;base64,", "", $signature);
        $key =  Imx\s3::storeString("Signature.png", "sales/$id/signatures/", base64_decode($signature))['key'];

        $signature =  Imx\s3::getAsLink($key);
        if (filter_var($signature, FILTER_VALIDATE_URL)) {
            echo $signature;
            $q = "update sales set signature ='$key' where sale_id ='$id'";
            echo $q;
            Imx\db::iquery($q);
        } else {
            echo "invalid signature";
        }
        exit;

        echo base64_decode($signature);
    } else {
        echo Imx\s3::getAsLink("/sales/$id/signatures/Signature.png");
    }
});

dispatch("/api/app/sales/:id/receipt", function ($id) {

    $latte = new Latte\Engine;

    // $html2pdf = new Html2Pdf();
    // $html2pdf->setTestTdInOnePage(false);
    ob_clean();
    $data = Imx\db::dataQuery("select * from view_sales where sale_id ='$id'");
    $signature = Imx\s3::getAsLink(Imx\db::rquery("select signature from sales where sale_id ='$id'"));
    $salestatus = Imx\db::rquery("select status from sales where sale_id ='$id'");

    $customer = Imx\db::dataQuery("select * from customers where customer_id ='{$data['customer_id']}'");
    $store = Imx\db::dataQuery("select * from stores where store_id ='{$data['store_id']}'");
    $store['policy'] = nl2br($store['policy']);
    // print_r($data);
    if ($salestatus == 1) {
        $items = Imx\db::dataQueryMultiple("select 
    products.name,
    products.model,
    items.price,
    items.warranty_price,
    1 as quantity,
    items.price as amount,
    case when products.stock_type = 2 then
    i.serial_number
    else
    ''
    end
    as serial,
    status.name as status,
    grades.name as grade,
    warranties.name as warranty,
    warranties.warranty_days
     from transactions items
    left join items i on i.item_id = items.item_id
    left join products on products.product_id = items.product_id
    left join status on status.status_id = items.status_id
    left join grades on grades.grade_id = items.grade_id
    left join warranties on warranties.warranty_id  = items.warranty_id 
    where items.reference_id = $id 
    and items.transaction_type='Sale'
    ");
}
else
    {
        $items = Imx\db::dataQueryMultiple("select 
    products.name,
    products.model,
    items.price,
    items.warranty_price,
    1 as quantity,
    items.price as amount,
    case when products.stock_type = 2 then
    items.serials
    else
    ''
    end
    as serial,
    status.name as status,
    grades.name as grade,
    warranties.name as warranty,
    warranties.warranty_days
     from transactions items
    left join products on products.product_id = items.product_id
    left join status on status.status_id = items.status_id
    left join grades on grades.grade_id = items.grade_id
    left join warranties on warranties.warranty_id  = items.warranty_id 
    where items.reference_id = $id 
    and items.transaction_type='Cancelation'
    ");
        $data['observation'].=" <br><h1 style='color:red;'>Sale Canceled</h1>";

    }
    // print_r($items);
    // print_r($items);
    if ($data['contact']) {
        $data['contact'] = "<br>" . $data['contact'];
    }

    $params = [
        "customer" => $data['customer'],
        "customer_id" => $data['customer_id'],
        "store" => $store,
        "signature" => $signature,
        "total" => $data['total'],
        "subtotal" => $data['subtotal'],
        "tax" => $data['tax'] ?? 0,
        "observation" => $data['observation'],
        "payment_method" => $data['payment_method'],
        "address" => $customer['address'],
        "phone" => $customer['phone_number'],
        "email" => $customer['email'],
        "date" => date('m/d/Y H:i:s', strtotime($data['date'])),
        "contact" => $data['contact'],
        "id" => $id,
        "items" => $items,
        "banner" => $store['banner']
    ];

    $receipt = $latte->renderToString("../app/src/hdtv/templates/receipt.latte", $params);
    echo $receipt;
    // $html2pdf->writeHTML($receipt);
    // $html2pdf->Output();
});


dispatch_post("/api/app/sales/:id/mail",  function ( String $id) {
    $input = file_get_contents('php://input');
    $data =  json_decode($input, true);
    if (json_last_error_msg() == "No error") {
        $email = $data['email'];
        // now we call the HTML2PDF API
        // header("Content-type: application/pdf");
        // header("Content-Disposition: inline; filename=Invoice.pdf");
        $server = $_SERVER['SERVER_NAME'];
        $url = urlencode("https://{$server}/api/app/sales/{$id}/receipt");

        $uri = "https://$server/public/pdf/?pdf={$url}&file=Receipt.pdf";
        $pdf = file_get_contents($uri);
        $temp = tmpfile();
        $tmppath =   stream_get_meta_data($temp)['uri'];
        fwrite($temp, $pdf);


        $mail = new PHPMailer(true);
        try {
//             $mail->SMTPDebug = 2;
            $mail->isSMTP();
//            $mail->Host       = "email-smtp.us-west-1.amazonaws.com";
//            $mail->SMTPAuth   = true;
//            $mail->Username   = "AKIART5MKUTJZRTEIWEI";
//            $mail->Password   = "BCv+y16liNaKbXWyBD2w68vEmGhoA0dO5Lk0xXVxyOic";
//            $mail->SMTPSecure = 'tsl';
//            $mail->Port       = "587";

            $mail->Host       = $_ENV['APP_EMAIL_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['APP_EMAIL_USER'];
            $mail->Password   = $_ENV['APP_EMAIL_PASSWORD'];
            $mail->SMTPSecure = ($_ENV['APP_EMAIL_SECURE'] == "ssl")?'ssl':'tsl';
            $mail->Port       = $_ENV['APP_EMAIL_PORT'];
            $mail->setFrom($_ENV['APP_EMAIL'], $_ENV['APP_EMAIL']);
            $mail->addAddress($email);
            $mail->addAttachment($tmppath, 'Invoice.pdf');

            $mail->isHTML(true);
            $mail->Subject = 'Your HDTV purchase Invoice';
            $mail->Body    = "Thanks for your preference. <b>Invoice  $id</b>";
            $mail->AltBody = 'Thanks for your preference. <b>Invoice  $id</b>';
            $mail->send();
            return json_encode(['status' => 'ok']);
        } catch (Exception $e) {
            Imx\headers::error500();
            return json_encode(['error' => $mail->ErrorInfo]);
        }

        return $pdf;
        error_log("Delivery $email", 0);
        return json_encode(['status' => 'ok']);
    } else {
        Imx\headers::conflict();
        return json_encode(['error' => 'Invalid Json Payload']);
    }
});
