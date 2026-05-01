<?php
error_reporting(0);

use Imx\db;
use Imx\html;

html::head(" Back Office / Sales ");
html::bodyInit();
html::header("");
html::sidebar();
html::beginContent();

$latte = new Latte\Engine;

$user = $_SESSION['user']['user_id'];
$store = Imx\db::rquery("select store_id from users where user_id ='$user'");
$daterange = date('m/d/Y') . " to " . date('m/d/Y');
$status = [

        ["text"=>"Any",'value'=>'2'],
        ["text"=>"Active",'value'=>1],
        ["text"=>"Canceled",'value'=>0]
];
$params = [
    'title' => "Sales",
    'name' => "salesform",
    'fields' => [
        ['name' => 'store', 'label' => 'Store', 'value' => $store, 'type' => 'select', 'ajax' => "/api/sel2/stores", 'required' => true],
        ['name' => 'date', 'range' => true, 'label' => 'Date', 'type' => 'date', 'value' => $daterange, 'format' => 'm/d/Y', 'required' => true],
        ['name' => 'customer', 'label' => 'Customer',  'type' => 'select', 'ajax' => "/api/sel2/customers"],
        ['name' => 'user', 'label' => 'User', 'type' => 'select', 'ajax' => "/api/sel2/users"],
        ['name' => 'status', 'label' => 'Status', 'value'=>'2','type' => 'select', 'data' => $status],
        ['name' => 'skipdates', 'label' => 'Skip Dates','value'=>0, 'type' => 'select', 'data'=>[ ['value'=>1,'text'=>'Yes'], ['value'=>0,'text'=>'No']],'helper'=>'Limited to the  last 100 records'],

    ]

];
$html = '
<input type="button" class="btn btn-primary" onclick="validateForm();" value="Get Sales">
<br>
<br>
<div id="salesdetail" class="col-12">

</div>
';
$form =  $latte->renderToString('../app/templates/form.latte', $params);
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Sales information", "body" => $form . $html]);
?>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        $(document).ready(function() {

            $(document).on("click", ".print", function () {
                document.getElementById("receipts").contentWindow.print();
            });

            // class input small  
            $(':input').addClass('form-control-sm');

            // parsley new validations

            $('#subtotal').attr("data-parsley-validate-if-empty", "true");
            $('#subtotal').attr("data-parsley-equalto", "#sale_subtotal");
            $('#subtotal').attr("data-parsley-equalto-message", "Value must match Subtotal of sale");
            // Listen for keyup events
            $('.payment_method').keyup(function() {
                var target = $(this).attr('id');
                console.log(target);
                var tax = parseFloat($('#paymentsModal').attr('data-tax'));
                var taxable = parseFloat($(this).attr('data-tax'));
                var basec = parseFloat($(this).val());
                var taxc = basec * (tax / 100) * taxable;
                $('.' + target).val(taxc.toFixed(2));
                calculateAmounts();
            });

            // Receipt modal click listener event
            $('#receiptModal').on('show.bs.modal', async function(event) {
                var saleid = $(event.relatedTarget).attr('data-id'); // Button that triggered the modal
                $('#receipts').attr('src', '/api/app/sales/' + saleid + '/receipt');

            });
            // Email modal click listener event
            $('#mailReceipt').on('show.bs.modal', async function(event) {
                var saleid = $(event.relatedTarget).attr('data-id'); // Button that triggered the modal
                var customeremail = $(event.relatedTarget).attr('data-email'); // Button that triggered the modal
                $('#mailReceipt').attr('data-id', saleid);

                $('#mailsaleid').html(saleid);
                $('#email').val(customeremail);

            });
            // Payment modal click listener event
            $('#paymentsModal').on('show.bs.modal', async function(event) {
                var saleid = $(event.relatedTarget).attr('data-id'); // Button that triggered the modal
                // fetch payments detail 
                $('.payment_method').val(0);
                await axios.get("/api/sales/" + saleid + "/payments").then(response => {
                    var data = response.data;
                    console.log(data);
                    data.payments.forEach((payment) => {
                        $('#pm_' + payment.payment_method_id).val(payment.amount);
                        $('.pm_' + payment.payment_method_id).val(payment.tax);
                    })
                    var modal = $(this);
                    $('#paymentsModal').attr('data-id', saleid);
                    $('#paymentsModal').attr('data-tax', data.tax);
                    $('#sale_subtotal').val(parseFloat(data.subtotal).toFixed(2));
                    calculateAmounts();
                    modal.find('.modal-title').text('Payment Methods Sale # ' + saleid);
                });

            });
        });
    });

    function calculateAmounts() {
        var x_subtotal = 0;
        var x_tax = 0;
        $('.payment_method').each(function() {
            x_subtotal += parseFloat(this.value);
        });
        $('.tax_input').each(function() {
            x_tax += parseFloat(this.value);
        });
        var x_total = parseFloat(x_subtotal) + parseFloat(x_tax);
        $('#subtotal').val(x_subtotal.toFixed(2));
        $('#taxes').val(x_tax.toFixed(2));
        $('#total').val(x_total.toFixed(2));

    }

    function validatePrice(pricelist, productid) {
        Imx.validaForm('/api/back-office/product-pricing/' + pricelist + '/' + productid, false);
    }

    function validateForm() {

        $('#salesdetail').html(`
        <div class="fa-3x">
  <i class="fas fa-spinner fa-spin"></i>
</div>
`);
        Imx.validaForm('/back-office/sales', false, ["payments_x"], "", "", "", false);

    }


    function callbackForm(result) {
        if (typeof result == "object") {
            $('#paymentsModal').modal('toggle');
            validateForm();

        } else {
            $('#salesdetail').html(result);
        }

    }
</script>


<!-- Payments Modal -->
<div class="modal fade" id="paymentsModal" tabindex="-1" role="dialog" aria-labelledby="paymentsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="paymentsModalLabel">Payment Methods Sale # </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <?php

                $params = [
                    'title' => "payments",
                    'name' => "payments_x",
                    'fields' => []

                ];
                $params['fields'][] = ['name' => 'spacer', 'type' => 'spacer', 'cols' => 6];
                $params['fields'][] = ['name' => 'sale_subtotal', 'label' => "Items Total", 'value' => 0, 'type' => 'rnumeric', 'cols' => 6, 'required' => true];
                $params['fields'][] = ['name' => 'separator', 'type' => 'separator', 'cols' => 12];
                $payment_methods = Imx\db::dataQueryMultiple("select * from payment_methods");
                foreach ($payment_methods as $payment_method) {
                    $params['fields'][] = [
                        'name' => 'pm_' . $payment_method['payment_method_id'],
                        'class' => 'payment_method',
                        'attrs' => "data-id='{$payment_method['payment_method_id']}' data-tax='{$payment_method['taxable']}'",
                        'label' => $payment_method['name'], 'value' => 0, 'type' => 'numeric', 'cols' => 7, 'required' => true
                    ];
                    $params['fields'][] = [
                        'name' => 'tax_' . $payment_method['payment_method_id'],
                        'class' => 'pm_' . $payment_method['payment_method_id'] . " tax_input",
                        'label' => "$ Tax", 'value' => 0, 'type' => 'rnumeric', 'cols' => 5, 'required' => true
                    ];
                }
                $params['fields'][] = ['name' => 'separator', 'type' => 'separator', 'cols' => 12];
                $params['fields'][] = ['name' => 'spacer', 'type' => 'spacer', 'cols' => 6];
                $params['fields'][] = ['name' => 'subtotal', 'label' => "Subtotal", 'value' => 0, 'type' => 'rnumeric', 'cols' => 6, 'required' => true];
                $params['fields'][] = ['name' => 'spacer', 'type' => 'spacer', 'cols' => 6];
                $params['fields'][] = ['name' => 'taxes', 'label' => "Taxes", 'value' => 0, 'type' => 'rnumeric', 'cols' => 6, 'required' => true];
                $params['fields'][] = ['name' => 'spacer', 'type' => 'spacer', 'cols' => 6];
                $params['fields'][] = ['name' => 'total', 'label' => "Total", 'value' => 0, 'type' => 'rnumeric', 'cols' => 6, 'required' => true];
                echo  $latte->renderToString('../app/templates/form.latte', $params);

                ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="savePayments()">Save changes</button>

            </div>
        </div>
    </div>
</div>
<!-- Receipt Modal -->
<div class="modal modal-message fade" style="height:90%;" id="receiptModal">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="receiptModalLabel">Receipt Preview </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <button class="btn btn-primary print">Print</button>
                <iframe id="receipts" style="border:0px;width:100%;height:70vh;;" src="" name="iframe_modal"></iframe>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Email Modal -->
<div class="modal fade" id="mailReceipt">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="mailReceiptLabel">Send receipt <i class="fa fa-mail"></i><span id="mailsaleid"></span></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <?php

                $params = [
                    'title' => "emailform",
                    'name' => "email_x",
                    'fields' => [
                        ['name' => 'email', 'type' => 'email', 'cols' => 12, 'label' => 'Customer Email']
                    ]

                ];
                echo  $latte->renderToString('../app/templates/form.latte', $params);

                ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="sendReceipt()">Send</button>
            </div>
        </div>
    </div>
</div>
<script>
    function savePayments() {
        var saleid = $('#paymentsModal').attr('data-id');
        Imx.validaForm('/api/sales/' + saleid + '/payments', true, ["salesform"], "", "", "", false);

    }
    async function sendReceipt() {
        $('#loader').fadeIn();
        var saleid = $('#mailReceipt').attr('data-id');
        var customeremail = $('#email').val();
        payload = {
            'email': customeremail
        };
        await axiosPost(payload, '/api/app/sales/' + saleid + '/mail', function(result) {
            $('#loader').fadeOut();

            let estatus = result.status ?? "";
            if (estatus == "ok") {
                alert('Receipt sent')
                $('#mailReceipt').modal('toggle');
                return true;
            }
            if (estatus == "error") {
                alert(result.text);
                return false;
            } else {

                alert("Server send an invalid response");
                // DESBLOQUEAR
                return false;
            }


        }).catch(function(err, result) {
            $('#loader').fadeOut();
            if (err.response === undefined) {
                alert('Error JS ' + err);
                return false;
            }
            if (err.response.status == 500) {
                alert('Error ' + error.response.status, 'Error de API');
                return false;

            } else {
                return false;
            }
        });
    }
</script>


<?php
html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
