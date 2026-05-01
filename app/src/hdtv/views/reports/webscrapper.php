<?php
error_reporting(0);

use Imx\db;
use Imx\html;

html::head(" Web Scraper ");
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
$store = Imx\db::rquery("select store_id from users where user_id ='{$_SESSION['user']['user_id']}'");

$params = [
    'title' => "Web Scraper Report",
    'name' => "webscrapper",
    'fields' => [
        ['name' => 'store', 'label' => 'Store','value'=>$store, 'type' => 'select', 'ajax' => "/api/sel2/stores",'required'=>true],
        ['name' => 'brand', 'label' => 'Brand', 'type' => 'select', 'ajax' => "/api/sel2/brands"],
        ['name' => 'category_id', 'value' => $formdata['category_id'] ?? '', 'label' => 'Category'
            , 'type' => 'select', 'ajax' => '/api/sel2/categories'],
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
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Web Scraper Report", "body" => $form . $html]);
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
            Imx.validaForm('/api/reports/webscrapper', false, [], '', '', '', false);
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
