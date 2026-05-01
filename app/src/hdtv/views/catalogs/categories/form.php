<?php

use Imx\db;
use Imx\html;

html::head("Categories ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from categories where category_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Categories", "link" => "/catalogs/categories"],
    ['text' => $formTitle, "link" => "/catalogs/categories/$id"],
]);


$latte = new Latte\Engine;



$params = [
    'title' => "category",
    'name' => "vercategory",
    'cols' => "6",
    'fields' => [
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Name', 'required' => 'true'],
    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);

$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/categories\'" value="Back">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/catalogs/categories/' . $id . '\')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Category", "body" => $form]);
?>

<script>
    function callbackForm(result) {
        window.location.href = '/catalogs/categories';
    }
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
