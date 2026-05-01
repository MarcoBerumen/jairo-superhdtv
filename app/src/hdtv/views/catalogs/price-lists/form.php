<?php

use Imx\db;
use Imx\html;

html::head("Price lists ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from price_lists where price_list_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Price lists", "link" => "/catalogs/price-lists"],
    ['text' => $formTitle, "link" => "/catalogs/price-lists/$id"],
]);


$latte = new Latte\Engine;



$params = [
    'title' => "pricelist",
    'name' => "verpricelist",
    'cols' => "6",
    'fields' => [
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Name', 'required' => 'true'],
    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);

$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/price-lists\'" value="Back">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/catalogs/price-lists/' . $id . '\')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Price list", "body" => $form]);
?>

<script>
    function callbackForm(result) {
        window.location.href = '/catalogs/price-lists';
    }
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
