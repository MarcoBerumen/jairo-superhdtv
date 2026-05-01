<?php

use Imx\db;
use Imx\html;
html::head("Physical inventory ");
html::bodyInit();
html::header("");
html::sidebar();
$formdata = db::dataQuery("select * from  out_inventory where out_inventory_id ='$id'");

html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Physical inventory", "link" => "/back-office/inventories"],
    ['text' => $id, "link" => "/back-office/inventories/$id"],
    ['text' => "Report", "link" => "/back-office/inventories/$id/report"],
]);


$latte = new Latte\Engine;

# TODO check if the inventory is pending
switch($status){
    case "cancel":
        Imx\db::iquery("update inventories set status = 'Cancelled' where inventory_id='{$id}'");
        break;
    case "apply":
        Imx\db::iquery("update inventories set status = 'Applied' where inventory_id='{$id}'");
        break;
}

$form = "inventory report";

echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Physical inventory $id Report", "body" => $form]);
html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
