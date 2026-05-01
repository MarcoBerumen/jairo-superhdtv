<?php

use Imx\db;
use Imx\html;

html::head("Transfers ");
html::bodyInit();
html::header("");
html::sidebar();


$formdata = db::dataQuery("select * from transfers where transfer_id ='$id'");

html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Transfers", "link" => "/back-office/transfers"],
    ['text' => $id, "link" => "/back-office/transfers/$id"],
    ['text' => $status, "link" => "/back-office/transfers/$id/{$status}"],
]);


$latte = new Latte\Engine;
// prevent to move any sold item
$solditems = Imx\db::rquery("
select count(*) from items where status not in(1,5) 
and item_id in(select item_id from transactions where transaction_type='Transfer' and reference_id='$id')
");
if($solditems > 0){
    $form = "<h2>Cannot change status</h2> 
Some items are not available : <br>

<br>
    <button class='btn btn-danger' onclick='window.location.href=\"/back-office/transfers/\"'>Go back </button>
    ";
}
else
{
    if($status == "sent")
    {
        Imx\db::iquery("update transfers set status='$status' where transfer_id ='$id'");
        $q = "update items set status='5',store_id='{$formdata['destination_id']}' where item_id in 
(select item_id from transactions where transaction_type='Transfer' and reference_id='$id') ";
    }    if($status == "received")
    {
        Imx\db::iquery("update transfers set status='$status' where transfer_id ='$id'");
        $q = "update items set status='1',store_id='{$formdata['destination_id']}' where item_id in 
(select item_id from transactions where transaction_type='Transfer' and reference_id='$id') ";
    }
Imx\db::iquery($q);
$form = "<h2>Transfer status updated</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/back-office/transfers/\"'>Go back </button>

";
}




echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Transfer status update", "body" => $form]);
?>

    <script>
        function callbackForm(result) {
            window.location.href = '/back-office/transfers';
        }
    </script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
