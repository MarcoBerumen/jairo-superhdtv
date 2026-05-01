<?php

use Imx\db;
use Imx\html;

html::head("Users ");
html::bodyInit();
html::header("");
html::sidebar();


$formdata = db::dataQuery("select * from users where user_id ='$id'");
$formTitle = $formdata['name'];

html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Users", "link" => "/catalogs/users"],
    ['text' => $formTitle, "link" => "/catalogs/users/$id"],
    ['text' => 'Delete', "link" => "/catalogs/users/$id/delete"],
]);


$latte = new Latte\Engine;
if(!isset($_GET['confirm']) && $formdata['row_status']){
    $form = "<h1>Are you sure to delete this user?</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/users/\"'>Cancel </button>
    <button class='btn btn-primary' onclick='window.location.href=\"/catalogs/users/$id/delete?confirm=true\"' >Yes, delete it </button>
    <br>
";
}
else
{
    Imx\db::iquery("update users set row_status = 0 where user_id ='$id'");
    $form = "<h2>User Deleted</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/users/\"'>Go back </button>

";
}



echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle user", "body" => $form]);
?>

    <script>
        function callbackForm(result) {
            window.location.href = '/catalogs/users';
        }
    </script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
