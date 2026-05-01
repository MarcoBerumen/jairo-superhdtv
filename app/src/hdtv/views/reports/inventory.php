<?php
error_reporting(0);

use Imx\db;
use Imx\html;

html::head(" Inventory ");
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
    'title' => "Inventory Report",
    'name' => "inventory",
    'fields' => [
        ['name' => 'store', 'label' => 'Store','value'=>$store, 'type' => 'select', 'ajax' => "/api/sel2/stores", 'required' => true],
        ['name' => 'brand', 'label' => 'Brand', 'type' => 'select', 'ajax' => "/api/sel2/brands"],
        ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'ajax' => "/api/sel2/status"],
        ['name' => 'stock', 'label' => 'Filter only stock items', 'type' => 'select', 'data' => $stock],
        ['name' => 'category_id', 'value' => $formdata['category_id'] ?? '', 'label' => 'Category'
            , 'type' => 'select', 'ajax' => '/api/sel2/categories'],
        ['name' => 'feature_id', 'value' =>  '', 'label' => 'Feature', 'type' => 'select'],
        ['name' => 'feature', 'value' =>  '', 'label' => 'Feature Filter','type'=>'multiple' ],

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
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Inventory Report", "body" => $form . $html]);
?>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        $(document).ready(function() {
            $("#feature_id").select2({
                ajax: {
                    url: function(){
                        return "/api/sel2/features/?category=" + $('#category_id').val()
                    },
                    dataType: 'json'
                }
            });

            $("#feature").select2({
                ajax: {
                    url: function(){
                        return "/api/sel2/feature/" + $('#feature_id').val()
                    },
                    dataType: 'json'
                }
            });

            $('#category_id').change(function() {
                $('#feature_id').val(null).trigger("change");
            });
            $('#feature_id').change(function() {
                $('#feature').val(null).trigger("change");
            });
            // validateForm();
        });
    });

    function validateForm() {
        $('#reporte').html(`
        <div class="fa-3x">
  <i class="fas fa-spinner fa-spin"></i>
</div>
`);
        Imx.validaForm('/api/reports/inventory', false, [], '', '', '', false);
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
