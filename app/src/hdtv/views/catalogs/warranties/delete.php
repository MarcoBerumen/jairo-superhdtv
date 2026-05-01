<?php

use Imx\db;
use Imx\html;

html::head("Warranties ");
html::bodyInit();
html::header("");
html::sidebar();


$formdata = db::dataQuery("select * from warranties where warranty_id ='$id'");
$formTitle = $formdata['name'];

html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Warranties", "link" => "/catalogs/warranties"],
    ['text' => $formTitle, "link" => "/catalogs/warranties/$id"],
    ['text' => 'Delete', "link" => "/catalogs/warranties/$id/delete"],
]);


$latte = new Latte\Engine;
if(!isset($_GET['confirm']) && $formdata['row_status']){
    $form = "<h1>Are you sure to delete this warranty?</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/warranties/\"'>Cancel </button>
    <button class='btn btn-primary' onclick='window.location.href=\"/catalogs/warranties/$id/delete?confirm=true\"' >Yes, delete it </button>
    <br>
";
}
else
{
    Imx\db::iquery("update warranties set row_status = 0 where warranty_id ='$id'");
    $form = "<h2>Warranty Deleted</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/warranties/\"'>Go back </button>

";
}



echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle warranty", "body" => $form]);
?>

    <script>
        function callbackForm(result) {
            window.location.href = '/catalogs/warranties';
        }
    </script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
