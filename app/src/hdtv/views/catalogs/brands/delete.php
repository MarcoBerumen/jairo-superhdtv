<?php

use Imx\db;
use Imx\html;

html::head("Brands ");
html::bodyInit();
html::header("");
html::sidebar();


$formdata = db::dataQuery("select * from brands where brand_id ='$id'");
$formTitle = $formdata['name'];

html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Brands", "link" => "/catalogs/brands"],
    ['text' => $formTitle, "link" => "/catalogs/brands/$id"],
    ['text' => 'Delete', "link" => "/catalogs/brands/$id/delete"],
]);


$latte = new Latte\Engine;
if(!isset($_GET['confirm']) && $formdata['row_status']){
    $form = "<h1>Are you sure to delete this brand?</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/brands/\"'>Cancel </button>
    <button class='btn btn-primary' onclick='window.location.href=\"/catalogs/brands/$id/delete?confirm=true\"' >Yes, delete it </button>
    <br>
";
}
else
{
    Imx\db::iquery("update brands set row_status = 0 where brand_id ='$id'");
    $form = "<h2>Brand Deleted</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/brands/\"'>Go back </button>

";
}



echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle brand", "body" => $form]);
?>

    <script>
        function callbackForm(result) {
            window.location.href = '/catalogs/brands';
        }
    </script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
