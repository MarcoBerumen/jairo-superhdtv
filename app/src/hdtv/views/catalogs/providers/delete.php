<?php

use Imx\db;
use Imx\html;

html::head("Providers ");
html::bodyInit();
html::header("");
html::sidebar();


$formdata = db::dataQuery("select * from providers where provider_id ='$id'");
$formTitle = $formdata['name'];

html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Providers", "link" => "/catalogs/providers"],
    ['text' => $formTitle, "link" => "/catalogs/providers/$id"],
    ['text' => 'Delete', "link" => "/catalogs/providers/$id/delete"],
]);


$latte = new Latte\Engine;
if(!isset($_GET['confirm']) && $formdata['row_status']){
    $form = "<h1>Are you sure to delete this provider?</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/providers/\"'>Cancel </button>
    <button class='btn btn-primary' onclick='window.location.href=\"/catalogs/providers/$id/delete?confirm=true\"' >Yes, delete it </button>
    <br>
";
}
else
{
    Imx\db::iquery("update providers set row_status = 0 where provider_id ='$id'");
    $form = "<h2>Provider Deleted</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/providers/\"'>Go back </button>

";
}



echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle provider", "body" => $form]);
?>

    <script>
        function callbackForm(result) {
            window.location.href = '/catalogs/providers';
        }
    </script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
