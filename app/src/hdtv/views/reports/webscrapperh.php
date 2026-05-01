<?php
error_reporting(0);

use Imx\db;
use Imx\html;

html::head(" Webscraper Price history ");
html::bodyInit();
html::header('');
html::sidebar();
html::beginContent();

$latte = new Latte\Engine;

$stock = [
    [
        "text" =>"Yes",
        "value"=>"1"
    ],
    [
        "text" =>"Everything",
        "value"=>"0"
    ]
];
$params = [
    'title' => "Webscraper Price History",
    'name' => "inventory",
    'fields' => [
        ['name' => 'product', 'label' => 'Product', 'type' => 'select','required'=>true,'ajax'=>'/api/sel2/products','cols'=>12],
        ['name' => 'start_date', 'label' => 'Date', 'type' => 'date', 'value' => date('m/01/Y'), 'format' => 'm/d/Y', 'required' => true],
        ['name' => 'end_date', 'label' => 'Date', 'type' => 'date', 'value' => date('m/d/Y'), 'format' => 'm/d/Y', 'required' => true],

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
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Webscraper Price History", "body" => $form . $html]);

?>

    <script>


                    document.addEventListener("DOMContentLoaded", function(event) {
            $(document).ready(function() {

            });
        });

        function validateForm() {
            $('#reporte').html(`
        <div class="fa-3x">
  <i class="fas fa-spinner fa-spin"></i>
</div>
`);
            Imx.validaForm('/api/reports/webscrapperh', false, [], '', '', '', false);
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
