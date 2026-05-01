<?php

use Imx\db;
use Imx\html;

html::head("HDTV - brands ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from brands where brand_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Brands", "link" => "/catalogs/brands"],
    ['text' => $formTitle, "link" => "/catalogs/brands/$id"],
]);


$latte = new Latte\Engine;



$params = [
    'title' => "brand",
    'name' => "verbrand",
    'cols' => "6",
    'fields' => [
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Name', 'required' => 'true'],
        ['name' => 'country', 'value' => $formdata['country'] ?? "", 'label' => 'Country', 'required' => 'true'],
    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);

$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/brands\'" value="Back">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/catalogs/brands/' . $id . '\')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Brand", "body" => $form]);
?>

<script>
    function callbackForm(result) {
        window.location.href = '/catalogs/brands';
    }
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
