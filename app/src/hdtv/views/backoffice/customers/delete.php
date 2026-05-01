<?php

use Imx\db;
use Imx\html;

html::head("Customers ");
html::bodyInit();
html::header("");
html::sidebar();


$formdata = db::dataQuery("select * from customers where customer_id ='$id'");
$formTitle = $formdata['name'];

html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Customers", "link" => "/back-office/customers"],
    ['text' => $formTitle, "link" => "/back-office/customers/$id"],
    ['text' => 'Delete', "link" => "/back-office/customers/$id/delete"],
]);


$latte = new Latte\Engine;
if(!isset($_GET['confirm']) && $formdata['row_status']){
    $form = "<h1>Are you sure to delete this customer?</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/back-office/customers/\"'>Cancel </button>
    <button class='btn btn-primary' onclick='window.location.href=\"/back-office/customers/$id/delete?confirm=true\"' >Yes, delete it </button>
    <br>
";
}
else
{
    Imx\db::iquery("update customers set row_status = 0 where customer_id ='$id'");
    $form = "<h2>Customer Deleted</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/back-office/customers/\"'>Go back </button>

";
}



echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle customer", "body" => $form]);
?>

    <script>
        function callbackForm(result) {
            window.location.href = '/back-office/customers';
        }
    </script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
