<?php

use Imx\db;
use Imx\html;

html::head("Stores ");
html::bodyInit();
html::header("");
html::sidebar();


$formdata = db::dataQuery("select * from stores where store_id ='$id'");
$formTitle = $formdata['name'];

html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Stores", "link" => "/catalogs/stores"],
    ['text' => $formTitle, "link" => "/catalogs/stores/$id"],
    ['text' => 'Delete', "link" => "/catalogs/stores/$id/delete"],
]);


$latte = new Latte\Engine;
if(!isset($_GET['confirm']) && $formdata['row_status']){
    $form = "<h1>Are you sure to delete this store?</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/stores/\"'>Cancel </button>
    <button class='btn btn-primary' onclick='window.location.href=\"/catalogs/stores/$id/delete?confirm=true\"' >Yes, delete it </button>
    <br>
";
}
else
{
    Imx\db::iquery("update stores set row_status = 0 where store_id ='$id'");
    $form = "<h2>Store Deleted</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/stores/\"'>Go back </button>

";
}



echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle store+", "body" => $form]);
?>

    <script>
        function callbackForm(result) {
            window.location.href = '/catalogs/stores';
        }
    </script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
