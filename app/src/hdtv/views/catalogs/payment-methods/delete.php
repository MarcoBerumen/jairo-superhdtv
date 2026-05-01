<?php

use Imx\db;
use Imx\html;

html::head("Payment Methods ");
html::bodyInit();
html::header("");
html::sidebar();


$formdata = db::dataQuery("select * from payment_methods where payment_method_id ='$id'");
$formTitle = $formdata['name'];

html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Payment Methods", "link" => "/catalogs/payment-methods"],
    ['text' => $formTitle, "link" => "/catalogs/payment-methods/$id"],
    ['text' => 'Delete', "link" => "/catalogs/payment-methods/$id/delete"],
]);


$latte = new Latte\Engine;
if(!isset($_GET['confirm']) && $formdata['row_status']){
    $form = "<h1>Are you sure to delete this payment method?</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/payment-methods/\"'>Cancel </button>
    <button class='btn btn-primary' onclick='window.location.href=\"/catalogs/payment-methods/$id/delete?confirm=true\"' >Yes, delete it </button>
    <br>
";
}
else
{
    Imx\db::iquery("update payment_methods set row_status = 0 where payment_method_id ='$id'");
    $form = "<h2>Payment method  Deleted</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/payment-methods/\"'>Go back </button>

";
}



echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle payment method", "body" => $form]);
?>

    <script>
        function callbackForm(result) {
            window.location.href = '/catalogs/payment-methods';
        }
    </script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
