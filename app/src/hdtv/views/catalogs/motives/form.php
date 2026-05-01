<?php

use Imx\db;
use Imx\html;

html::head("Outgoing Inventory Motives ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from motives where motive_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Outgoing Inventory Motives", "link" => "/catalogs/motives"],
    ['text' => $formTitle, "link" => "/catalogs/motives/$id"],
]);


$latte = new Latte\Engine;



$params = [
    'title' => "motive",
    'name' => "vermotive",
    'cols' => "6",
    'fields' => [
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Name', 'required' => 'true'],
    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);

$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/motives\'" value="Back">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/catalogs/motives/' . $id . '\')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Motive", "body" => $form]);
?>

<script>
    function callbackForm(result) {
        window.location.href = '/catalogs/motives';
    }
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
