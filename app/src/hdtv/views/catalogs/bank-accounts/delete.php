<?php

use Imx\db;
use Imx\html;

html::head("Bank Accounts ");
html::bodyInit();
html::header("");
html::sidebar();


$formdata = db::dataQuery("select * from bank_accounts where bank_account_id ='$id'");
$formTitle = $formdata['name'];

html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Bank Accounts", "link" => "/catalogs/bank-accounts"],
    ['text' => $formTitle, "link" => "/catalogs/bank-accounts/$id"],
    ['text' => 'Delete', "link" => "/catalogs/bank-accounts/$id/delete"],
]);


$latte = new Latte\Engine;
if(!isset($_GET['confirm']) && $formdata['row_status']){
    $form = "<h1>Are you sure to delete this bank account?</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/bank-accounts/\"'>Cancel </button>
    <button class='btn btn-primary' onclick='window.location.href=\"/catalogs/bank-accounts/$id/delete?confirm=true\"' >Yes, delete it </button>
    <br>
";
}
else
{
    Imx\db::iquery("update bank_accounts set row_status = 0 where bank_account_id ='$id'");
    $form = "<h2>Bank Account Deleted</h2>
    <button class='btn btn-danger' onclick='window.location.href=\"/catalogs/bank-accounts/\"'>Go back </button>

";
}



echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle bank account", "body" => $form]);
?>

    <script>
        function callbackForm(result) {
            window.location.href = '/catalogs/bank-accounts';
        }
    </script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
