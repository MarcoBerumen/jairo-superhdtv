<?php
error_reporting(0);

use Imx\db;
use Imx\html;

html::head(" Comissions ");
html::bodyInit();
html::header("");
html::sidebar();
html::beginContent();

$latte = new Latte\Engine;


$status = [
    ['text'=> 'Active','value'=>1],
    ['text'=> 'Canceled','value'=>0],
];
$store = Imx\db::rquery("select store_id from users where user_id ='{$_SESSION['user']['user_id']}'");

$params = [
    'title' => "Sales",
    'name' => "psales",
    'fields' => [
        ['name' => 'store', 'label' => 'Store','value'=>$store, 'type' => 'select', 'ajax' => "/api/sel2/stores", 'required' => true],
        ['name' => 'user', 'label' => 'User', 'type' => 'select', 'ajax' => "/api/sel2/users"],
        ['name' => 'start_date', 'label' => 'Date', 'type' => 'date', 'value' => date('m/d/Y'), 'format' => 'm/d/Y', 'required' => true],
        ['name' => 'end_date', 'label' => 'Date', 'type' => 'date', 'value' => date('m/d/Y'), 'format' => 'm/d/Y', 'required' => true],
    ]

];
$html = '
<input type="button" class="btn btn-primary" onclick="validateForm();" value="Get Comissions">
<br>
<br>
<div id="salesdetail" class="col-12">

</div>
';
$form =  $latte->renderToString('../app/templates/form.latte', $params);
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Comissions Report", "body" => $form . $html]);
?>

    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            $(document).ready(function() {
                // validateForm();
            });
        });

        function validatePrice(pricelist, productid) {
            Imx.validaForm('/api/back-office/product-pricing/' + pricelist + '/' + productid, false);
        }

        function validateForm() {

            $('#salesdetail').html(`
        <div class="fa-3x">
  <i class="fas fa-spinner fa-spin"></i>
</div>
`);
            Imx.validaForm('/api/reports/comissions', false, [], "", "", "", false);

        }


        function callbackForm(result) {
            if (result == "recarga") {
                validateForm();
            } else {

                $('#salesdetail').html(result);
            }
        }
    </script>
<?php
html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
