<?php
error_reporting(0);

use Imx\db;
use Imx\html;

html::head(" Items ");
html::bodyInit();
html::header('');
html::sidebar();
html::beginContent();

$latte = new Latte\Engine;
$status = [
    ["text" =>"Available","value"=>"1"],
    ["text" =>"Sold","value"=>"2"],
    ["text" =>"Claim","value"=>"3"],
    ["text" =>"Out of Inventory","value"=>"4"],
    ["text" =>"Transfer","value"=>"5"],


];
$store = Imx\db::rquery("select store_id from users where user_id ='{$_SESSION['user']['user_id']}'");

$params = [
    'title' => "Items Report",
    'name' => "items",
    'fields' => [
        ['name' => 'store', 'label' => 'Store', 'value'=>$store,'type' => 'select', 'ajax' => "/api/sel2/stores", 'required' => true],
        ['name'=>'status','label'=>'Status','type'=>'select','data'=>$status]
    ]

];
$html = '
<input type="button" class="btn btn-primary" onclick="validateForm();" value="View Report">
<br>
<br>
<div id="reporte" class="col-12">

</div>
';
$form =  $latte->renderToString('../app/templates/form.latte', $params);
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Items Report", "body" => $form . $html]);
?>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        $(document).ready(function() {
            // validateForm();
        });
    });

    function validateForm() {
        $('#reporte').html(`
        <div class="fa-3x">
  <i class="fas fa-spinner fa-spin"></i>
</div>
`);
        Imx.validaForm('/api/reports/items', false, [], '', '', '', false);
    }


    function callbackForm(result) {
        $('#reporte').html(result);
    }
</script>
<?php
html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
