<?php
use Spipu\Html2Pdf\Html2Pdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
dispatch('/public/notifications/expiring/send',function(){
    $stores = Imx\db::dataQueryMultiple("select * from stores where row_status = 1");
    foreach($stores as $store){
//        print_r($store);
        $emails = explode(",",$store['emailadmin']);
        if(count($emails)>0){
            // verify if there is item expiring
           $query = "SELECT
	 count(*)
	from items
WHERE
	items.store_id = '{$store['store_id']}'
    and
    items.status = '1'
    and datediff( provider_warranty_date,current_date()) <= '30'";
           $items = Imx\db::rquery($query);
           if($items > 0) {
               $path = "https://{$_SERVER['HTTP_HOST']}/public/notifications/expiring/?store={$store['store_id']}";
               $url = urlencode($path);
               $uri = "https://admin.hdtvoutlets.com/public/pdf/?pdf={$url}&file=Sales.pdf";
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
                   foreach ($emails as $email) {
                       $mail->addAddress($email);
                   }
//                   $mail->addAddress("josue@insist.com.mx");
                   $mail->addAttachment($tmppath, 'Expiring-items.pdf');

                   $mail->isHTML(true);
                   $date = date('m/d/Y');
                   $mail->Subject = 'Expiring Items Report ' . $date . ' ' . $store['name'];
                   $mail->Body = "Expiring Report  on {$store['name']}:<br>
There is {$items} items expiring withing 30 days or less";
                   $mail->send();
//        return json_encode(['status' => 'ok']);
               } catch (Exception $e) {
                   Imx\headers::error500();
                   return json_encode(['error' => $mail->ErrorInfo]);
               }

           }

        }
    }
//
    return "";
});
