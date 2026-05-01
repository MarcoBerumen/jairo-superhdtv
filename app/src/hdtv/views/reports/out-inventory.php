<?php
error_reporting(0);

use Imx\db;
use Imx\html;

html::head(" Outgoing Inventory ");
html::bodyInit();
html::header("");
html::sidebar();
html::beginContent();

$latte = new Latte\Engine;


$status = [
    [
        "text" => "Any",
        "value" => ""
    ],
    [
        "text" => "Received",
        "value" => "Received"
    ],
    [
        "text" => "Sent",
        "value" => "Sent"
    ],
    [
        "text" => "Credit",
        "value" => "Credit"
    ],
    [
        "text" => "Reject",
        "value" => "Reject"
    ]
];

$params = [
    'title' => "Outgoing Inventory",
    'name' => "psales",
    'fields' => [
        ['name' => 'store', 'label' => 'Store', 'type' => 'select', 'ajax' => "/api/sel2/stores", 'required' => true],
        ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'data'=>$status],
        ['name' => 'start_date', 'label' => 'Start Date', 'type' => 'date', 'value' => date('m/d/Y'), 'format' => 'm/d/Y', 'required' => true],
        ['name' => 'end_date', 'label' => 'End Date', 'type' => 'date', 'value' => date('m/d/Y'), 'format' => 'm/d/Y', 'required' => true],
        ['name' => 'skipdates', 'label' => 'Skip Dates','value'=>1, 'type' => 'select', 'data'=>[ ['value'=>1,'text'=>'Yes'], ['value'=>0,'text'=>'No']],'helper'=>'Limited to the  last 100 records'],

    ]

];
$html = '
<input type="button" class="btn btn-primary" onclick="validateForm();" value="Get Outgoing Inventory">
<br>
<br>
<div id="salesdetail" class="col-12">

</div>
';
$form =  $latte->renderToString('../app/templates/form.latte', $params);
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Outgoing Inventory Report", "body" => $form . $html]);
?>

    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            $(document).ready(function() {
                // validateForm();
            });
        });


        function validateForm() {

            $('#salesdetail').html(`
        <div class="fa-3x">
  <i class="fas fa-spinner fa-spin"></i>
</div>
`);
            Imx.validaForm('/api/reports/outgoing-inventory', false, [], "", "", "", false);

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
