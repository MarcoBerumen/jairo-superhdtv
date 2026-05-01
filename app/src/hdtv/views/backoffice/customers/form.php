<?php

use Imx\db;
use Imx\html;

html::head("Customers ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from customers where customer_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Customers", "link" => "/back-office/customers"],
    ['text' => $formTitle, "link" => "/back-office/customers/$id"],
]);


$latte = new Latte\Engine;



$params = [
    'title' => "customer",
    'name' => "vercustomer",
    'cols' => "6",
    'fields' => [
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Name', 'required' => 'true'],
        ['name' => 'address', 'value' => $formdata['address'] ?? "", 'label' => 'Address'],
        ['name' => 'email', 'value' => $formdata['email'] ?? "", 'label' => 'Email'],
        ['name' => 'phone_number', 'value' => $formdata['phone_number'] ?? "", 'label' => 'Phone Number', 'required' => 'true'],
        ['name' => 'credit', 'value' => $formdata['credit'] ?? "0", 'label' => 'Credit','type'=>'rnumeric'],
    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);

$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/back-office/customers\'" value="Back">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/back-office/customers/' . $id . '\')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Customer", "body" => $form]);
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
