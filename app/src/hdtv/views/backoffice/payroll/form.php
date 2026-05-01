<?php

use Imx\db;
use Imx\html;

html::head("Outgoing Inventory ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from  payroll where payroll_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Payroll", "link" => "/back-office/payroll"],
    ['text' => $formTitle, "link" => "/back-office/payroll/$id"],
]);


$latte = new Latte\Engine;
$store = Imx\db::rquery("select store_id from users where user_id ='{$_SESSION['user']['user_id']}'");

$formdata['payment_date'] = Imx\utils::sql2date($formdata['payment_date']??date('Y-m-d'));
$formdata['start_date'] = Imx\utils::sql2date($formdata['start_date']??date('Y-m-d'));
$formdata['end_date'] = Imx\utils::sql2date($formdata['end_date']??date('Y-m-d'));
$params = [
    'title' => "payroll",
    'name' => "verpayroll",
    'cols' => "6",
    'fields' => [
        ['name' => 'payment_date', 'value' => $formdata['payment_date'] ?? date('m/d/Y'), "locale" => "en", 'format' => 'm/d/Y', 'label' => 'Date', 'required' => 'true', 'type' => 'date'],
        [
            'name' => 'store_id', 'value' => $formdata['store_id'] ?? $store,
            'label' => 'Store', 'required' => 'true', 'type' => "select", "ajax" => "/api/sel2/stores/"
        ],
        ['name' => 'start_date', 'value' => $formdata['start_date'] ?? date('m/d/Y'), "locale" => "en", 'format' => 'm/d/Y', 'label' => 'Start Date', 'required' => 'true', 'type' => 'date'],
        ['name' => 'end_date', 'value' => $formdata['end_date'] ?? date('m/d/Y'), "locale" => "en", 'format' => 'm/d/Y', 'label' => 'End Date', 'required' => 'true', 'type' => 'date'],
        ['name' => 'total', 'value' => $formdata['total'] ?? "0", 'label' => 'Total amount',  'required' => 'true', 'type' => 'rnumeric'],
        ['name' => 'observations', 'value' => $formdata['observations'] ?? "", 'label' => 'Observations', 'type' => 'textarea'],

    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Payroll", "body" => $form]);



$params1 = [
    'title' => "Items ",
    'addTitle' => "Add Item",
    'cols' => 12,
    'name' => 'x_payroll',
    'fields' => [
        ['name' => 'user_id', 'value' => "", 'label' => 'Employee', 'type' => 'select', 'required' => 'true'],
        ['name' => 'hours', 'value' => "0", 'label' => 'Hours', 'type' => 'numeric', 'required' => 'true', 'detail' => "no", 'readonly' => true],
        ['name' => 'wage', 'value' => "0", 'label' => 'Wage per Hour', 'type' => 'numeric', 'required' => 'true', 'detail' => "no", 'readonly' => true],
        ['name' => 'comissions', 'value' => "0", 'label' => 'Comissions', 'type' => 'numeric', 'required' => 'true', 'detail' => "no", 'readonly' => true],
        ['name' => 'payroll', 'value'=> "0", 'label' => 'Payroll', 'type' => 'rnumeric', 'required' => 'true', 'detail' => "no", 'readonly' => true],
        ['name' => 'amount', 'value' => "0", 'label' => 'Amount to pay', 'type' => 'rnumeric', 'required' => 'true', 'detail' => "no", 'readonly' => true],
    ],

];

$form = $latte->renderToString('../app/templates/form-detail.latte', $params1);


$form = $form . '
<input type="button" class="btn btn-success cpayroll " value="Calculate Payrolls">
<input type="button" class="btn btn-danger" onclick="window.location.href=\'/back-office/payroll\'" value="Back">
    <input type="button" class="btn btn-primary" onclick="validaForm()" value="Save">';

echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Payroll detail", "body" => $form]);
?>
<script>
    var editing = false;

    function validaForm() {

        dt1 = {
            form: 'items',
            data: x_payrollobject._data.items
        }
        var error = false;
        if (x_payrollobject.items.length == 0) {
            alert('You must add at least one item');
            return false;

        }

        if (error) {
            return false;
        }

        if (isNaN($('#total').val())) {
            alert('The value for total is incorrect');
            return false;

        }
        Imx.validaForm('/api/back-office/payroll/<?php echo $id; ?>', true, ['x_payroll'], dt1);

    }


    function callbackForm(result) {
        window.location.href = '/back-office/payroll';
    }
    document.addEventListener("DOMContentLoaded", function() {



        <?php
        if ($id != "new") {
            $query = "select 
            payroll_detail.*,
            (select name from users where users.user_id = payroll_detail.user_id) as user
            from payroll_detail  where payroll_id =  '$id' ";

            $detalle = Imx\db::dataQueryMultiple($query);
            foreach ($detalle as $elemento) {
                $stock_type = ($elemento['stock_type'] == "1") ? "Bulk" : "Unique Serial";


                /*

                */
                $stock = 1;
        ?>
                setTimeout(function() {

                    x_payrollobject.items.push({


                        "user_id": {
                            "label": "<?php echo Imx\utils::clean($elemento['user']); ?>",
                            "type": "select",
                            "value": "<?php echo Imx\utils::clean($elemento['user_id']); ?>",
                        },

                        "hours": {
                            "label": hoursToHHMM(<?php echo Imx\utils::clean($elemento['hours']); ?>),
                            "type": "text",
                            "value": "<?php echo $elemento['hours']; ?>",
                        },

                        "comissions": {
                            "label": "<?php echo Imx\utils::clean($elemento['comissions']); ?>",
                            "type": "text",
                            "value": "<?php echo $elemento['comissions']; ?>",
                        },

                        "wage": {
                            "label": "<?php echo Imx\utils::clean($elemento['wage']); ?>",
                            "type": "text",
                            "value": "<?php echo $elemento['wage']; ?>",
                        },

                        "payroll": {
                            "label": "<?php echo Imx\utils::clean($elemento['payroll']); ?>",
                            "type": "text",
                            "value": "<?php echo $elemento['payroll']; ?>",
                        },

                        "amount": {
                            "label": "<?php echo Imx\utils::clean($elemento['amount']); ?>",
                            "type": "text",
                            "value": "<?php echo $elemento['amount']; ?>",
                        },





                    });
                }, 1000);

                // console.log(parti);
        <?php
            }
        }
        ?>




        var stock_type = "";
        $(document).ready(function() {



                    $("#user_id").select2({
                        ajax: {
                            url: function(){
                                return "/api/sel2/users/?store=" + $('#store_id').val()
                            },
                            dataType: 'json'
                        }
                    });
                    $('#store_id').change(function() {
                        $('#user_id').val(null).trigger("change");
                    });



            $('#x_payrollmodal').on('hidden.bs.modal', function() {
                var total = 0;
                x_payrollobject.items.forEach(function(key) {
                    total = total + parseFloat(key.amount.value);
                });
                console.log('Total Cost');
                console.log(total);
                $('#total').val(total);
            });
            $('#user_id').on('select2:select', function(e) {
                if ($('#store_id').val() == null) {
                    alert('Please select the store before capturing items');
                    $('#user_id').val(null).trigger("change");
                }

            });



            $('#shipping,#total_items').keyup(function() {
                $('#shipping_item').val(

                    Math.round(
                        (parseFloat($('#shipping').val() /
                                parseFloat($('#total_items').val()) * 100
                            )

                        )
                    ) / 100
                );
            });





            $('#hours').keyup(function() {
                payrollcalc();
            });
            $('#wage').keyup(function() {
                payrollcalc();
            });
            $('#comissions').keyup(function() {
                payrollcalc();
            });



            // ! calculate payroll
            $('.cpayroll').on('click', async function(event) {
                // Do some processing here
                let payload = {
                    'start_date' : $('#start_date').val(),
                    'end_date' : $('#end_date').val(),
                    'store' : $('#store_id').val(),
                }
                let uri = `/api/back-office/payroll/calculate`;
                await axios.post(uri,payload).then(resp => {
                    if (resp.status == "200") {

                        let payrolldetail = resp.data;
                        if(payrolldetail.length > 0){
                            x_payrollobject.items = [];
                            var total = 0;
                        payrolldetail.forEach(function(p){
                                total = total + parseFloat(p.amount.toFixed(2));


                            console.log(p);
                            x_payrollobject.items.push({


                                "user_id": {
                                    "label": p.name,
                                    "type": "select",
                                    "value": p.user_id,
                                },

                                "hours": {
                                    "label": hoursToHHMM(p.hours),
                                    "type": "text",
                                    "value": p.hours,
                                },

                                "comissions": {
                                    "label": p.comissions,
                                    "type": "text",
                                    "value": p.comissions,
                                },

                                "wage": {
                                    "label": p.wage,
                                    "type": "text",
                                    "value": p.wage,
                                },

                                "payroll": {
                                    "label": p.payroll.toFixed(2),
                                    "type": "text",
                                    "value": p.payroll.toFixed(2),
                                },

                                "amount": {
                                    "label": p.amount.toFixed(2),
                                    "type": "text",
                                    "value": p.amount.toFixed(2),
                                },
                            });

                        });
                            $('#total').val(total);

                        }

                    }
                })
                    .catch(function(error) {
                        alert('Your request cannot be processed,please try again');
                        console.log(error.toJSON());
                    });
            });



        });
    });

    function payrollcalc() {
        let total = parseFloat($('#hours').val()) * parseFloat($('#wage').val());
        $('#payroll').val(total);

        total = total + parseFloat($('#comissions').val());
        if (isNaN(total)) {
            total = 0;
        }
        $('#amount').val(total);
    }

    function hoursToHHMM(hours) {
        var h = String(Math.trunc(hours)).padStart(2, '0');
        var m = String(Math.abs(Math.round((hours - h) * 60))).padStart(2, '0');
        return h + ':' + m;
    }

</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
