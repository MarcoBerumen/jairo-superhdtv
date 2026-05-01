<?php

use Imx\db;
use Imx\html;

html::head("Status ");
html::bodyInit();
html::header("");
html::sidebar();


$formdata = db::dataQuery("select * from status where status_id ='$id'");
$formTitle = $formdata['name'];

html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Status", "link" => "/catalogs/status"],
    ['text' => $formTitle, "link" => "/catalogs/status/$id"],
    ['text' => 'Delete', "link" => "/catalogs/status/$id/delete"],
]);


$latte = new Latte\Engine;
if(!isset($_GET['confirm']) && $formdata['row_status']){
    $form = "<h1>Are you sure to delete this grade?</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/status/\"'>Cancel </button>
    <button class='btn btn-primary' onclick='window.location.href=\"/catalogs/status/$id/delete?confirm=true\"' >Yes, delete it </button>
    <br>
";
}
else
{
    Imx\db::iquery("update status set row_status = 0 where status_id ='$id'");
    $form = "<h2>Grade Deleted</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/status/\"'>Go back </button>

";
}



echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle grade", "body" => $form]);
?>

    <script>
        function callbackForm(result) {
            window.location.href = '/catalogs/status';
        }
    </script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
