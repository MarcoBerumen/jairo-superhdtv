<?php

use Imx\db;
use Imx\html;

html::head("Claims ");
html::bodyInit();
html::header("");
html::sidebar();


$formdata = db::dataQuery("select * from claims where claim_id ='$id'");
$formTitle = $formdata['name'];

html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Claims", "link" => "/back-office/claims"],
    ['text' => $formTitle, "link" => "/back-office/claims/$id"],
    ['text' => $status, "link" => "/back-office/claims/$id/{$status}"],
]);


$latte = new Latte\Engine;

    Imx\db::iquery("update claims set status='$status' where claim_id ='$id'");
    $form = "<h2>Claim status updated : {$status}</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/back-office/claims/\"'>Go back </button>
";
if($status =="Return to Inventory"){
    $item = $formdata['item_id'];
    Imx\db::iquery("update items set status =1 where item_id  = '{$item}'  ");
    $item = Imx\db::dataQuery("select * from items where item_id ='$item'");
    hdtv::inventory($item['product_id'],$item['store_id'],$item['status_id'],$item['grade_id']);
}



echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Claim status update", "body" => $form]);
?>

    <script>
        function callbackForm(result) {
            window.location.href = '/back-office/claims';
        }
    </script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
