<?php

use Imx\db;
use Imx\html;

html::head("Warranties ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from warranties where warranty_id ='$id'");
    $formdata['password'] = Imx\utils::decrypt($formdata['password']);
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Warranties", "link" => "/catalogs/warranties"],
    ['text' => $formTitle, "link" => "/catalogs/warranties/$id"],
]);


$latte = new Latte\Engine;



$params = [
    'title' => "profile",
    'name' => "verprofile",
    'cols' => "6",
    'fields' => [
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Name', 'required' => 'true'],
        ['name' => 'warranty_days', 'value' => $formdata['warranty_days'] ?? "", 'label' => 'Warranty Days', 'required' => 'true'],
        ['name' => 'price', 'value' => $formdata['price'] ?? "", 'label' => 'Price', 'required' => 'true'],
        ['name' => 'under_price', 'value' => $formdata['under_price'] ?? "", 'label' => 'Under Price', 'required' => 'true'],
    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);

$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/warranties\'" value="Back">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/catalogs/warranties/' . $id . '\')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Warranty", "body" => $form]);
?>

<script>
    function callbackForm(result) {
        window.location.href = '/catalogs/warranties';
    }
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
