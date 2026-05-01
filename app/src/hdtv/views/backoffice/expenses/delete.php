<?php

use Imx\db;
use Imx\html;

html::head("Expenses ");
html::bodyInit();
html::header("");
html::sidebar();


$formdata = db::dataQuery("select * from expenses where expense_id ='$id'");
$formTitle = $formdata['name'];

html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Expenses", "link" => "/back-office/expenses"],
    ['text' => $id, "link" => "/back-office/expenses/$id"],
    ['text' => 'Delete', "link" => "/back-office/expenses/$id/delete"],
]);


$latte = new Latte\Engine;
if(!isset($_GET['confirm']) && $formdata['row_status']){
    $form = "<h1>Are you sure to delete this expense?</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/back-office/expenses/\"'>Cancel </button>
    <button class='btn btn-primary' onclick='window.location.href=\"/back-office/expenses/$id/delete?confirm=true\"' >Yes, delete it </button>
    <br>
";
}
else
{
    Imx\db::iquery("update expenses set row_status = 0 where expense_id ='$id'");
    $form = "<h2>Expense Deleted</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/back-office/expenses/\"'>Go back </button>

";
}



echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle expense", "body" => $form]);
?>

    <script>
        function callbackForm(result) {
            window.location.href = '/back-office/expenses';
        }
    </script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
