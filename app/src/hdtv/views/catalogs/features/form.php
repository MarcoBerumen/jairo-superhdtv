<?php

use Imx\db;
use Imx\html;

html::head("HDTV - features ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from features where feature_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Features", "link" => "/catalogs/features"],
    ['text' => $formTitle, "link" => "/catalogs/features/$id"],
]);


$latte = new Latte\Engine;

$types = [

    [
        'text' => 'Boolean',
        'value' => 'Boolean'
    ],
    [
        'text' => 'List',
        'value' => 'List'
    ],
    [
        'text' => 'Numeric',
        'value' => 'Numeric'
    ],
];
if($formdata['categories']){
$categories = Imx\db::dataQueryMultiple("select name as text, category_id as value, 1 as selected from categories where category_id in({$formdata['categories']})");
} else{
    $categories= [];
}
$params = [
    'title' => "feature",
    'name' => "verfeature",
    'cols' => "6",
    'fields' => [
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Name', 'required' => 'true'],
        ['name' => 'type', 'value' => $formdata['type'] ?? "", 'label' => 'Type', 'required' => 'true', 'type' => 'select', 'data' => $types],
        ['name' => 'options', 'value' => $formdata['options'] ?? "", 'label' => 'Options',  'helper' => 'Comma separated values (only for list type)'],
        ['name' => 'categories', 'value' => "", 'data' => $categories, 'label' => 'Categories', 'required' => 'true', 'type' => 'multiple', 'ajax' => '/api/sel2/categories'],

    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);

$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/features\'" value="Back">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/catalogs/features/' . $id . '\')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Feature", "body" => $form]);
?>

<script>
    function callbackForm(result) {
        window.location.href = '/catalogs/features';
    }
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
