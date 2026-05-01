<?php
use Spipu\Html2Pdf\Html2Pdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
dispatch('/public/notifications/salesm/send',function(){
    $stores = Imx\db::dataQueryMultiple("select * from stores where row_status = 1");
    foreach($stores as $store){
//        print_r($store);
        $emails = explode(",",$store['emailadmin']);
        if(count($emails)>0){
            $date = date('F Y',strtotime('last day of last month'));
            $path = "https://{$_SERVER['HTTP_HOST']}/public/notifications/salesm/?store={$store['store_id']}/";
            $url = urlencode($path);
            $uri = "https://admin.hdtvoutlets.com/public/pdf/?pdf={$url}&file=Monthly-Sales.pdf";
            $pdf = file_get_contents($uri);
            $temp = tmpfile();
            $tmppath =   stream_get_meta_data($temp)['uri'];
            fwrite($temp, $pdf);

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = $_ENV['APP_EMAIL_HOST'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['APP_EMAIL_USER'];
                $mail->Password   = $_ENV['APP_EMAIL_PASSWORD'];
                $mail->SMTPSecure = ($_ENV['APP_EMAIL_SECURE'] == "ssl")?'ssl':'tsl';
                $mail->Port       = $_ENV['APP_EMAIL_PORT'];
                $mail->setFrom($_ENV['APP_EMAIL'], $_ENV['APP_EMAIL']);
                foreach($emails as $email){
                    $mail->addAddress($email);
                }
                $mail->addAttachment($tmppath, 'Sales.pdf');

                $mail->isHTML(true);
                $mail->Subject = 'Monthly Sales Report '.$date .' '.$store['name'];
                $mail->Body    = 'Monthly Sales Report';
                $mail->send();
//        return json_encode(['status' => 'ok']);
            } catch (Exception $e) {
                Imx\headers::error500();
                return json_encode(['error' => $mail->ErrorInfo]);
            }

        }
    }
//
    return "";
});
