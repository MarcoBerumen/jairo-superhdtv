<?php

use Imx\db;
use Imx\html;

html::head("Expenses Types ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from  expenses where expense_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Expenses", "link" => "/back-office/expenses"],
    ['text' => $formTitle, "link" => "/back-office/expenses/$id"],
]);


$latte = new Latte\Engine;
$store = Imx\db::rquery("select store_id from users where user_id ='{$_SESSION['user']['user_id']}'");

$params = [
    'title' => "expense",
    'name' => "verexpense",
    'cols' => "6",
    'fields' => [
    ['name' => 'date', 'value' => Imx\utils::sql2date($formdata['date'] ?? date('Y-m-d')), "locale" => "en", 'format' => 'm/d/Y', 'label' => 'Date', 'required' => 'true', 'type' => 'date'],
        [
            'name' => 'store_id', 'value' => $formdata['store_id'] ?? $store,
            'label' => 'Store', 'required' => 'true', 'type' => "select", "ajax" => "/api/sel2/stores/"
        ],
        ['name' => 'bank_account_id', 'value' => $formdata['bank_account_id'] ?? "", 'label' => 'Bank Account', 'type' => 'select', 'ajax' => '/api/sel2/bank-accounts', 'required' => 'true'],
        ['name' => 'expense_type_id', 'value' => $formdata['expense_type_id'] ?? "", 'label' => 'Expense', 'type' => 'select', 'ajax' => '/api/sel2/expenses-types', 'required' => 'true'],

        ['name' => 'subtotal', 'value' => $formdata['subtotal'] ?? "0", 'label' => 'Subtotal',  'required' => 'true', 'type' => 'numeric'],
        ['name' => 'tax', 'value' => $formdata['tax'] ?? "0", 'label' => 'TAX ',  'required' => 'true', 'type' => 'numeric'],
        ['name' => 'total', 'value' => $formdata['total'] ?? "0", 'label' => 'Total ',  'required' => 'true', 'type' => 'rnumeric'],
        ['name' => 'observations', 'col'=>12,'value' => $formdata['observations'] ?? "", 'label' => 'Observations', 'type' => 'textarea'],

    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);
$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/back-office/expenses\'" value="Back">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/back-office/expenses/' . $id . '\')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Expense", "body" => $form]);

?>
    <script>

        function validaForm() {


            Imx.validaForm('/api/back-office/expenses/<?php echo $id; ?>');

        }


        function callbackForm(result) {
            window.location.href = '/back-office/expenses';
        }
        document.addEventListener("DOMContentLoaded", function() {





            $(document).ready(function() {

                $('#subtotal,#tax').keyup(function() {
                    var total = parseFloat($('#subtotal').val()) +  parseFloat($('#tax').val());
                    $('#total').val(total??0);
                });

            });
        });
    </script>
<?php
html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
