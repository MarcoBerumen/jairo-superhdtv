<?php

use Imx\db;
use Imx\html;

html::head("Outgoing Inventory ");
html::bodyInit();
html::header("");
html::sidebar();


$formdata = db::dataQuery("select * from out_inventory where out_inventory_id ='$id'");
$formTitle = $formdata['name'];

html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Outgoing Inventory", "link" => "/back-office/outgoing-inventory"],
    ['text' => $formTitle, "link" => "/back-office/outgoing-inventory/$id"],
    ['text' => $status, "link" => "/back-office/outgoing-inventory/$id/{$status}"],
]);


$latte = new Latte\Engine;

    Imx\db::iquery("update out_inventory set status='$status' where out_inventory_id ='$id'");
    $form = "<h2>Outgoing Inventory status updated : {$status}</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/back-office/outgoing-inventory/\"'>Go back </button>
";
if($status =="Cancelled"){
    $item = $formdata['item_id'];
    Imx\db::iquery("update items set status =1 where item_id  = '{$item}'  ");
    $item = Imx\db::dataQuery("select * from items where item_id ='$item'");
    hdtv::inventory($item['product_id'],$item['store_id'],$item['status_id'],$item['grade_id']);
}



echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Outgoing Inventory Status update", "body" => $form]);
?>

    <script>
        function callbackForm(result) {
            window.location.href = '/back-office/outgoing-inventory';
        }
    </script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
