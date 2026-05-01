<?php
error_reporting(0);

use Imx\db;
use Imx\html;

html::head(" Customers Report ");
html::bodyInit();
html::header("");
html::sidebar();
html::beginContent();

$latte = new Latte\Engine;



$opts =[
    ["value"=>"","text"=>"Any"],
    ["value"=>"1","text"=>"Yes"],
    ["value"=>"0","text"=>"No"],
];

$params = [
    'title' => "Customers",
    'name' => "pcustomers",
    'fields' => [
        ['name' => 'credit', 'label' => 'Has Credit', 'type' => 'select','data'=>$opts,'value'=>''],
        ['name' => 'start_date', 'label' => 'Start Date', 'type' => 'date', 'value' => date('m/01/Y'), 'format' => 'm/d/Y', 'required' => true,'helper'=>'Registry Date'],
        ['name' => 'end_date', 'label' => 'End date', 'type' => 'date', 'value' => date('m/d/Y'), 'format' => 'm/d/Y', 'required' => true],
        ['name' => 'skipdates', 'label' => 'Skip Dates','value'=>1, 'type' => 'select', 'data'=>[ ['value'=>1,'text'=>'Yes'], ['value'=>0,'text'=>'No']],'helper'=>'Limited to the  last 100 records'],

    ]

];
$html = '
<input type="button" class="btn btn-primary" onclick="validateForm();" value="Get Customers">
<br>
<br>
<div id="customersdetail" class="col-12">

</div>
';
$form =  $latte->renderToString('../app/templates/form.latte', $params);
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Customers Report ", "body" => $form . $html]);
?>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        $(document).ready(function() {
            // validateForm();
        });
    });


    function validateForm() {

        $('#customersdetail').html(`
        <div class="fa-3x">
  <i class="fas fa-spinner fa-spin"></i>
</div>
`);
        Imx.validaForm('/api/reports/customers', false, [], "", "", "", false);

    }


    function callbackForm(result) {
        if (result == "recarga") {
            validateForm();
        } else {

            $('#customersdetail').html(result);
        }
    }
</script>
<?php
html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
