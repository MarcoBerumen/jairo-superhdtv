<?php

use Imx\db;
use Imx\html;

html::head("Grades ");
html::bodyInit();
html::header("");
html::sidebar();


    $formdata = db::dataQuery("select * from grades where grade_id ='$id'");
    $formTitle = $formdata['name'];

html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Grades", "link" => "/catalogs/grades"],
    ['text' => $formTitle, "link" => "/catalogs/grades/$id"],
    ['text' => 'Delete', "link" => "/catalogs/grades/$id/delete"],
]);


$latte = new Latte\Engine;
if(!isset($_GET['confirm']) && $formdata['row_status']){
    $form = "<h1>Are you sure to delete this grade?</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/grades/\"'>Cancel </button>
    <button class='btn btn-primary' onclick='window.location.href=\"/catalogs/grades/$id/delete?confirm=true\"' >Yes, delete it </button>
    <br>
";
}
else
{
    Imx\db::iquery("update grades set row_status = 0 where grade_id ='$id'");
    $form = "<h2>Grade Deleted</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/grades/\"'>Go back </button>

";
}



echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle grade", "body" => $form]);
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
