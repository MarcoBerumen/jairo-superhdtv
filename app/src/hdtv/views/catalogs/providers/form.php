<?php

use Imx\db;
use Imx\html;

html::head("HDTV - Providers ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from providers where provider_id ='$id'");
    $formdata['password'] = Imx\utils::decrypt($formdata['password']);
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "providers", "link" => "/catalogs/providers"],
    ['text' => $formTitle, "link" => "/catalogs/providers/$id"],
]);


$latte = new Latte\Engine;



$formdata['fechaIngreso'] = Imx\utils::fechamex($formdata['fechaIngreso']);
$params = [
    'title' => "Provider",
    'name' => "viewprovider",
    'cols' => "6",
    'fields' => [
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Name',  'required' => 'true'],
        ['name' => 'address', 'value' => $formdata['address'] ?? "", 'label' => 'Address', 'required' => 'true'],
        ['name' => 'contact_name', 'value' => $formdata['contact_name'] ?? "", 'label' => 'Contact name', 'required' => 'true'],
        ['name' => 'email', 'value' => $formdata['email'] ?? "", 'label' => 'Email', 'helper' => 'Login ID', 'required' => 'true', 'type' => 'email'],
        ['name' => 'phone_number', 'value' => $formdata['phone_number'] ?? "", 'label' => 'Phone number', 'required' => 'true'],
        ['name' => 'credit_line', 'value' => $formdata['credit_line'] ?? "", 'label' => 'Credit Line', 'required' => 'true', 'type' => "numeric"],
        ['name' => 'credit_days', 'value' => $formdata['credit_days'] ?? "", 'label' => 'Credit Days', 'required' => 'true', 'type' => "numeric"],
        ['name' => 'warranty_days', 'value' => $formdata['warranty_days'] ?? "", 'label' => 'Warranty Days', 'required' => 'true', 'type' => "numeric"],
    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);

$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/providers\'" value="Back">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/catalogs/providers/' . $id . '\')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Provider", "body" => $form]);
?>

<script>
    function callbackForm(result) {
        window.location.href = '/catalogs/providers';
    }
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
