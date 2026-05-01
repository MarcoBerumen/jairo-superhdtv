<?php
error_reporting(0);

use Imx\db;
use Imx\html;

html::head(" Reports / Attendance ");
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
    [
        'value' => 'Worked Hours',
        'text' => 'Worked Hours',
    ],
];
$store = Imx\db::rquery("select store_id from users where user_id ='{$_SESSION['user']['user_id']}'");

$params = [
    'title' => "attendance",
    'name' => "pattendance",
    'fields' => [
        ['name' => 'store', 'label' => 'Store','value'=>$store, 'type' => 'select', 'ajax' => "/api/sel2/stores", 'required' => true],
        ['name' => 'user', 'label' => 'User', 'type' => 'select', 'ajax' => "/api/sel2/users"],
        ['name' => 'type', 'label' => 'Record Type', 'value' => '0', 'type' => 'select', 'data' => $options],
        ['name' => 'start_date', 'label' => 'Date', 'type' => 'date', 'value' => date('m/d/Y'), 'format' => 'm/d/Y', 'required' => true],
        ['name' => 'end_date', 'label' => 'Date', 'type' => 'date', 'value' => date('m/d/Y'), 'format' => 'm/d/Y', 'required' => true],
    ]

];
$html = '
<input type="button" class="btn btn-primary" onclick="validateForm();" value="Request report">
<br>
<br>
<div id="attendancedetail" class="col-12">

</div>
';
$form =  $latte->renderToString('../app/templates/form.latte', $params);
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Attendance Report", "body" => $form . $html]);
?>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        $(document).ready(function() {
            // validateForm();
        });
    });



    function validateForm() {

        $('#attendancedetail').html(`
        <div class="fa-3x">
  <i class="fas fa-spinner fa-spin"></i>
</div>
`);
        Imx.validaForm('/api/reports/attendance', false, [], "", "", "", false);

    }


    function callbackForm(result) {
        if (result == "recarga") {
            validateForm();
        } else {

            $('#attendancedetail').html(result);
        }
    }
</script>
<?php
html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
