<?php

use Imx\db;
use Imx\html;

html::head("Expenses types ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from expenses_types where expense_type_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Expenses types", "link" => "/catalogs/expenses-types"],
    ['text' => $formTitle, "link" => "/catalogs/expenses-types/$id"],
]);


$latte = new Latte\Engine;


$options = [
    [
        'value' => 'Fixed',
        'text' => 'Fixed'
    ],
    [
        'value' => 'Variable',
        'text' => 'Variable'
    ]
    ,
    [
        'value' => 'Cost of sales',
        'text' => 'Cost of sales'
    ]
];

$params = [
    'title' => "expensetype",
    'name' => "verexpensetype",
    'cols' => "6",
    'fields' => [
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Name', 'required' => 'true'],
        ['name' => 'type', 'value' => $formdata['type'] ?? "", 'label' => 'Type', 'required' => 'true', 'type' => 'select', 'data' => $options],
    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);

$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/expenses-types\'" value="Back">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/catalogs/expenses-types/' . $id . '\')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Expense type", "body" => $form]);
?>

<script>
    function callbackForm(result) {
        window.location.href = '/catalogs/expenses-types';
    }
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
