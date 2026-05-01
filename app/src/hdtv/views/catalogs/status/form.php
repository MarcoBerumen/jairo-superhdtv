<?php

use Imx\db;
use Imx\html;

html::head("Status ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from status where status_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Product Status", "link" => "/catalogs/status"],
    ['text' => $formTitle, "link" => "/catalogs/status/$id"],
]);


$latte = new Latte\Engine;



$params = [
    'title' => "status",
    'name' => "verstatus",
    'cols' => "6",
    'fields' => [
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Name', 'required' => 'true'],
        ['name_app' => 'name_app', 'value' => $formdata['name_app'] ?? "", 'label' => 'Name on App', 'required' => 'true'],
    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);

$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/status\'" value="Back">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/catalogs/status/' . $id . '\')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Product Status", "body" => $form]);
?>

<script>
    function callbackForm(result) {
        window.location.href = '/catalogs/status';
    }
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
