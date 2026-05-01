<?php

use Imx\db;
use Imx\html;

html::head("Grades ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from grades where grade_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Grades", "link" => "/catalogs/grades"],
    ['text' => $formTitle, "link" => "/catalogs/grades/$id"],
]);


$latte = new Latte\Engine;

$formdata['status'] = $formdata['status'] ?? 0;
$status = Imx\db::dataQueryMultiple("select name as text, status_id as value, 1 as selected from status where status_id in({$formdata['status']})");

$params = [
    'title' => "grade",
    'name' => "vergrade",
    'cols' => "6",
    'fields' => [
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Name', 'required' => 'true'],
        ['name' => 'status', 'value' => "", 'data' => $status, 'label' => 'Status', 'required' => 'true', 'type' => 'multiple', 'ajax' => '/api/sel2/status'],

    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);

$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/grades\'" value="Back">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/catalogs/grades/' . $id . '\')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Grade", "body" => $form]);
?>

<script>
    function callbackForm(result) {
        window.location.href = '/catalogs/grades';
    }
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
