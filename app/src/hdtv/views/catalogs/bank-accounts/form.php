<?php

use Imx\db;
use Imx\html;

html::head("Bank accounts ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from bank_accounts where bank_account_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Bank accounts", "link" => "/catalogs/bank-accounts"],
    ['text' => $formTitle, "link" => "/catalogs/bank-accounts/$id"],
]);


$latte = new Latte\Engine;



$params = [
    'title' => "bank",
    'name' => "verbank",
    'cols' => "6",
    'fields' => [
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Bank Name', 'required' => 'true'],
        ['name' => 'number', 'value' => $formdata['number'] ?? "", 'label' => 'Number', 'required' => 'true'],
    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);

$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/bank-accounts\'" value="Back">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/catalogs/bank-accounts/' . $id . '\')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Bank account", "body" => $form]);
?>

<script>
    function callbackForm(result) {
        window.location.href = '/catalogs/bank-accounts';
    }
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
