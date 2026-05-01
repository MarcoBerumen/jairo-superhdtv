<?php

use Imx\db;
use Imx\html;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

html::head("Cancel Sale");
html::bodyInit();
html::header("");
html::sidebar();


$formdata = db::dataQuery("select * from sales where sale_id ='$id'");
//print_r($formdata);
$formTitle = $formdata['name'];

html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Sales", "link" => "/back-office/sales"],
    ['text' => $id, "link" => "#"],
    ['text' => 'Cancel', "link" => "/back-office/sales/$id/cancel"],
]);

$sale = Imx\db::dataquery("select * from view_sales where sale_id ='$id'");
$latte = new Latte\Engine;
    $form = "
    <i class='fa fa-info fa-1x'></i> Sale info  <br>
    Sale Number : <a href='/api/app/sales/{$id}/receipt' target='_blank'>$id<a /> <br>
    Store : {$sale['store']} <br>
    Customer : {$sale['customer']} <br>
    Payment Method : {$sale['payment_method']} <br>
    Total : {$sale['total']} <br>
    Observations : {$sale['observation']} <br>
";

if(!isset($_GET['confirm']) && $formdata['status']){
    $form = "<h2>Are you sure to cancel this sale?</h2> {$form}    <button class='btn btn-danger' onclick='window.location.href=\"/back-office/sales/\"'>Go Back </button>
    <button class='btn btn-primary' onclick='window.location.href=\"/back-office/sales/$id/cancel?confirm=true\"' >Cancel this sale </button>
    <br>
";
}
else
{
    $user = $_SESSION['user']['name'];
    Imx\db::iquery("update sales set status = 0 where sale_id ='$id'");
    // create transactions of each item .
    $items = Imx\db::dataQueryMultiple("select 
    items.*,
     tax+price+warranty_price as total 
    from items where sale_id = '$id'");
    foreach($items as $item ){
        $tr = "
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
'Cancelation',
'{$item['status_id']}',
'{$item['grade_id']}',
'{$item['notes']}',
'$id',
'1',
'{$item['price']}',
'{$item['item_id']}',
'{$item['warranty_id']}',
'{$item['warranty_price']}',
'{$item['serial_number']}',
'{$item['price']}',
'1',
'{$item['total']}','{$_SESSION['user']['user_id']}')
        ";
        Imx\db::iquery($tr);
        // ?  release the item
        Imx\db::iquery("update items set status = '1' , sale_id = null where item_id ='{$item['item_id']}'");
        // now we update the inventory
        hdtv::inventory(
            $item['product_id'],
            $item['store_id'],
            $item['status_id'],
            $item['grade_id']
        );
    }
    // notify by email
    $store = Imx\db::rquery("select store_id from sales where sale_id = '$id'");
    $store = Imx\db::dataQuery("select * from stores where store_id = '{$store}'");
        $emails = explode(",",$store['emailadmin']);
        if(count($emails)>0) {
            $date = date('m/d/Y', strtotime('yesterday'));
            $server = $_SERVER['SERVER_NAME'];
            $url = urlencode("https://{$server}/api/app/sales/{$id}/receipt");
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
                $mail->addAttachment($tmppath, 'Sales.pdf');

                $mail->isHTML(true);
                $mail->Subject = 'Sale Cancelation ' . $id . ' ' . $store['name'];
                $date = date('m/d/Y H:i:s');
                $mail->Body = "Sale cancelation {$id} <br>
                 Store :{$store['name']} <br>
                 User : {$user}<br>
                 Date : {$date} ";
                $mail->send();
//                echo "Notification sent";

//        return json_encode(['status' => 'ok']);
            } catch (Exception $e) {
//                Imx\headers::error500();
                print_r($e);
                echo "<h1>Cannot send notification, SMTP Error</h1>";
            }
        }
//    print_r($items);
    $form = "<h2>Sale <?php echo $id;?> canceled</h2>  {$form}
    <button class='btn btn-danger' onclick='window.location.href=\"/back-office/sales/\"'>Go back </button>
";
}



echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Sale cancelation $formTitle ", "body" => $form]);
?>

    <script>
        function callbackForm(result) {
            window.location.href = '/back-office/sales';
        }
    </script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
