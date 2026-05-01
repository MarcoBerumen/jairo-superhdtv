<?php
error_reporting(0);

use Imx\db;
use Imx\html;

html::head(" Reports / Expenses ");
html::bodyInit();
html::header("");
html::sidebar();
html::beginContent();

$latte = new Latte\Engine;


$options = [
    [
        'value' => '0',
        'text' => ' All',
    ],
    [
        'value' => 'FaceLogin',
        'text' => 'FaceLogin',
    ],
    [
        'value' => 'Open Shift',
        'text' => 'Open Shift',
    ],
    [
        'value' => 'Close Shift',
        'text' => 'Close Shift',
    ],
];
$store = Imx\db::rquery("select store_id from users where user_id ='{$_SESSION['user']['user_id']}'");

$params = [
    'title' => "expenses",
    'name' => "pexpenses",
    'fields' => [
        ['name' => 'store', 'label' => 'Store','value'=>$store, 'type' => 'select', 'ajax' => "/api/sel2/stores", 'required' => true],
        ['name' => 'type', 'label' => 'Expense type', 'value' => '0', 'type' => 'select', 'ajax'=>'/api/sel2/expenses-types'],
        ['name' => 'start_date', 'label' => 'Date', 'type' => 'date', 'value' => date('m/d/Y'), 'format' => 'm/d/Y', 'required' => true],
        ['name' => 'end_date', 'label' => 'Date', 'type' => 'date', 'value' => date('m/d/Y'), 'format' => 'm/d/Y', 'required' => true],
    ]

];
$html = '
<input type="button" class="btn btn-primary" onclick="validateForm();" value="Request report">
<br>
<br>
<div id="expensesdetail" class="col-12">

</div>
';
$form =  $latte->renderToString('../app/templates/form.latte', $params);
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Expenses Report", "body" => $form . $html]);
?>

    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            $(document).ready(function() {
                // validateForm();
            });
        });



        function validateForm() {

            $('#expensesdetail').html(`
        <div class="fa-3x">
  <i class="fas fa-spinner fa-spin"></i>
</div>
`);
            Imx.validaForm('/api/reports/expenses', false, [], "", "", "", false);

        }


        function callbackForm(result) {
            if (result == "recarga") {
                validateForm();
            } else {

                $('#expensesdetail').html(result);
            }
        }
    </script>
<?php
html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
