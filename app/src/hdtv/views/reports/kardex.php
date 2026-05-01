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
    'title' => "Kardex",
    'name' => "inventory",
    'fields' => [
        ['name' => 'store', 'label' => 'Store','value'=>$store, 'type' => 'select', 'ajax' => "/api/sel2/stores", 'required' => true],
        ['name' => 'item', 'label' => 'Item', 'type' => 'select','required'=>true],
        ['name' => 'customer', 'label' => 'Customer',  'type' => 'select', 'ajax' => "/api/sel2/customers",'cols'=>12],

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
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Kardex", "body" => $form . $html]);
?>

    <script>


                    document.addEventListener("DOMContentLoaded", function(event) {
            $(document).ready(function() {
                            $("#item").select2({
                                ajax: {
                                    url: function(){
                                        return "/api/sel2/items/?store=" + $('#store').val() +"&customer="+ $('#customer').val()
                                    },
                                    dataType: 'json'
                                }
                            });
                $('#store,#customer').change(function() {
                    $('#item').val(null).trigger("change");
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
            Imx.validaForm('/api/reports/kardex', false, [], '', '', '', false);
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
