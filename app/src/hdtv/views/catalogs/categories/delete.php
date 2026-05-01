<?php

use Imx\db;
use Imx\html;

html::head("Categories ");
html::bodyInit();
html::header("");
html::sidebar();


$formdata = db::dataQuery("select * from categories where category_id ='$id'");
$formTitle = $formdata['name'];

html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Categories", "link" => "/catalogs/categories"],
    ['text' => $formTitle, "link" => "/catalogs/categories/$id"],
    ['text' => 'Delete', "link" => "/catalogs/categories/$id/delete"],
]);


$latte = new Latte\Engine;
if(!isset($_GET['confirm']) && $formdata['row_status']){
    $form = "<h1>Are you sure to delete this category?</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/categories/\"'>Cancel </button>
    <button class='btn btn-primary' onclick='window.location.href=\"/catalogs/categories/$id/delete?confirm=true\"' >Yes, delete it </button>
    <br>
";
}
else
{
    Imx\db::iquery("update categories set row_status = 0 where category_id ='$id'");
    $form = "<h2>Category Deleted</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/categories/\"'>Go back </button>

";
}



echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle category", "body" => $form]);
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
