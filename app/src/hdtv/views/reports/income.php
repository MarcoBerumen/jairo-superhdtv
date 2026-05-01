<?php
error_reporting(0);

use Imx\db;
use Imx\html2 as html;

html::head(" Reports Income ");
html::bodyInit();
html::header("");
html::sidebar();
html::beginContent();

$latte = new Latte\Engine;



$store = Imx\db::rquery("select store_id from users where user_id ='{$_SESSION['user']['user_id']}'");

$params = [
    'title' => "Income",
    'name' => "pinvcome",
    'fields' => [
        ['name' => 'store', 'label' => 'Store','value'=>$store, 'type' => 'select', 'ajax' => "/api/sel2/stores", 'required' => true],
        ['name' => 'start_date', 'label' => 'Date', 'type' => 'date', 'value' => date('m/d/Y'), 'format' => 'm/d/Y', 'required' => true],
        ['name' => 'end_date', 'label' => 'Date', 'type' => 'date', 'value' => date('m/d/Y'), 'format' => 'm/d/Y', 'required' => true],
    ]

];
$html = '
<input type="button" class="btn btn-primary" onclick="validateForm();" value="Get Report">
<br>
<br>
<div id="invoicesdetail" class="col-12">

</div>
';
$form =  $latte->renderToString('../app/templates/form.latte', $params);
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Income Report ", "body" => $form . $html]);
?>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        $(document).ready(function() {
            // validateForm();
        });
    });


    function validateForm() {

        $('#invoicesdetail').html(`
        <div class="fa-3x">
  <i class="fas fa-spinner fa-spin"></i>
</div>
`);
        Imx.validaForm('/api/reports/income-statement', false, [], "", "", "", false);

    }


    function callbackForm(result) {
        if (result == "recarga") {
            validateForm();
        } else {

            $('#invoicesdetail').html(result);
        }
    }
</script>
<?php
html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
