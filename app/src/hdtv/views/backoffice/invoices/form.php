<?php

use Imx\db;
use Imx\html;

html::head("invoices ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from invoices where invoice_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Invoices", "link" => "/back-office/invoices"],
    ['text' => $formTitle, "link" => "/back-office/invoices/$id"],
]);


$latte = new Latte\Engine;


$store = Imx\db::rquery("select store_id from users where user_id ='{$_SESSION['user']['user_id']}'");

$params = [
    'title' => "invoice",
    'name' => "verinvoice",
    'cols' => "6",
    'fields' => [
        ['name' => 'date', 'value' => Imx\utils::sql2date($formdata['date'] ?? date('Y-m-d')), "locale" => "en", 'format' => 'm/d/Y', 'label' => 'Date', 'required' => 'true', 'type' => 'date'],
        [
            'name' => 'store_id', 'value' => $formdata['store_id'] ?? $store,
            'label' => 'Store', 'required' => 'true', 'type' => "select", "ajax" => "/api/sel2/stores/"
        ],
        [
            'name' => 'provider_id', 'value' => $formdata['provider_id'] ?? "",
            'label' => 'Provider', 'required' => 'true', 'type' => "select", "ajax" => "/api/sel2/providers/"
        ],
        ['name' => 'document_number', 'value' => $formdata['document_number'] ?? "", 'label' => 'Document number', 'required' => 'true'],
        ['name' => 'taxes', 'value' => $formdata['taxes'] ?? "0", 'label' => 'Tax', 'type' => 'numeric', 'required' => 'true'],
        ['name' => 'shipping', 'value' => $formdata['shipping'] ?? "0", 'label' => 'Shipping Cost',  'required' => 'true'],
        ['name' => 'total_items', 'value' => $formdata['total_items'] ?? "0", 'label' => 'Total Items',  'required' => 'true'],
        ['name' => 'total_price', 'value' => $formdata['total_price'] ?? "0", 'label' => 'Total',  'required' => 'true'],
        ['name' => 'shipping_item', 'value' => $formdata['shipping_item'] ?? "0", 'label' => 'Shipping Cost', 'type' => 'readonly',  'required' => 'true', 'helper' => 'Per Item'],
        ['name' => 'observations', 'value' => $formdata['observations'] ?? "", 'label' => 'Observations', 'type' => 'textarea'],

    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Invoice", "body" => $form]);



$params1 = [
    'title' => "Items ",
    'addTitle' => "Add Item",
    'cols' => 12,
    'name' => 'x_items',
    'fields' => [
        ['name' => 'items', 'value' => $formdata['items'] ?? "", 'label' => 'Total Items', 'type' => 'numeric', 'required' => 'true'],
        ['name' => 'product_id', 'value' => $formdata['product_id'] ?? "", 'label' => 'Product', 'type' => 'select', 'ajax' => '/api/sel2/products-models', 'required' => 'true'],
        ['name' => 'stock_type', 'value' => "", 'label' => 'Stock Type', 'type' => 'readonly'],
        ['name' => 'status_id', 'value' => $formdata['status_id'] ?? "", 'label' => 'Status', 'type' => 'select', 'ajax' => '/api/sel2/status', 'required' => 'true'],
        ['name' => 'grade_id', 'value' => $formdata['grade_id'] ?? "", 'label' => 'Grade', 'type' => 'select', 'ajax' => '/api/sel2/grade', 'required' => 'true'],
        ['name' => 'shipping_price', 'value' => $formdata['shipping_price'] ?? "0", 'label' => 'Shipping cost', 'type' => 'numeric', 'required' => 'true', 'type' => 'readonly'],
        ['name' => 'price', 'value' => $formdata['price'] ?? "0", 'label' => 'Price per item', 'type' => 'numeric', 'required' => 'true'],
        ['name' => 'min_sale_price', 'value' => $formdata['min_sale_price'] ?? "0", 'label' => 'Minimum Sale Price', 'type' => 'numeric', 'required' => 'true', 'detail' => "no"],
        ['name' => 'sale_price', 'value' => $formdata['sale_price'] ?? "0", 'label' => 'Sale Price', 'type' => 'numeric', 'required' => 'true', 'detail' => "no"],
        ['name' => 'info', 'type' => 'info', 'label' => 'Info'],
        ['name' => 'subtotal', 'value' => $formdata['subtotal'] ?? "0", 'label' => 'Subtotal', 'type' => 'readonly', 'required' => 'true', 'type' => "numeric"],
        ['name' => 'serial_number', 'required' => true, 'label' => 'Serial numbers', 'type' => 'textarea', 'helper' => "One serial per row", 'detail' => "no"],
        ['name' => 'notes', 'label' => 'Notes', 'type' => 'textarea', 'detail' => "no"],
    ],

];

$form = $latte->renderToString('../app/src/hdtv/templates/invoice.latte', $params1);

if (Imx\db::rquery("select count(*) from items where invoice_id ='$id' and status > 1")) {

    $form = $form . '
    <h2>
    You cannot edit an invoice with sold items.
    </h2>
    ';
} else {
    $form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/back-office/invoices\'" value="Back">
    <input type="button" class="btn btn-primary" onclick="validaForm()" value="Save">';
}
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Items detail", "body" => $form]);
?>
<script>
    var stax = 0;
    var editing = false;

    function validaForm() {

        dt1 = {
            form: 'items',
            data: x_itemsobject._data.items
        }

        var serials = [];
        var error = false;
        x_itemsobject.items.forEach(function(item) {
            if(item.stock_type.value == "Unique Serial" ) {

                if (item.items.value != item.serial_number.value.split(",").length) {
                    alert('The serial numbers specified does not match total items on  item \n' + item.product_id.label);
                    error = true;

                }
            }
        });
        var serials = x_itemsobject.items.map(item => item.serial_number?.value.split(",")).flat().filter(Boolean).filter((value, index, array) => array.indexOf(value) != index);
        if (serials.length) {
            alert('The following serials are repeated : \n' + serials.join(", "));
            error = true;
        }
        if (error) {
            return false;
        }
        console.log(serials);
        // if (dt1.data.length != $('#items').val()) {
        //     alert('You must add the exact total items on the purchase order');
        //     return false;
        // }
        if (isNaN($('#total_price').val())) {
            alert('The value for total is incorrect');
            return false;

        }

        if (parseFloat($('#total_price').val()) <= 0) {
            alert('The value for total must be greater than 0 ');
            return false;

        }
        Imx.validaForm('/api/back-office/invoices/<?php echo $id; ?>', true, ['x_items'], dt1);

    }


    function callbackForm(result) {
        window.location.href = '/back-office/invoices';
    }
    document.addEventListener("DOMContentLoaded", function() {



        <?php
        if ($id != "new") {
            $query = "select 
            transactions.*,
            (select stock_type from products where products.product_id = transactions.product_id) as stock_type
            from transactions  where transaction_type ='Invoice' and reference_id =  '$id' ";
            $detalle = Imx\db::dataQueryMultiple($query);
            foreach ($detalle as $elemento) {
                $stock_type = ($elemento['stock_type'] == "1") ? "Bulk" : "Unique Serial";

        ?>
                setTimeout(function() {

                    x_itemsobject.items.push({

                        "items": {
                            "label": "<?php echo Imx\utils::clean($elemento['quantity']); ?>",
                            "type": "select",
                            "value": "<?php echo $elemento['quantity']; ?>",
                        },
                        "info": {
                            "label": "",
                            "type": "text",
                            "value": "",
                        },
                        "stock_type": {
                            "label": "<?php echo $stock_type; ?>",
                            "type": "text",
                            "value": "<?php echo $stock_type; ?>",
                        },

                        "product_id": {
                            "label": "<?php echo Imx\db::rquery("select model from products where product_id='{$elemento['product_id']}'"); ?>",
                            "type": "select",
                            "value": "<?php echo Imx\utils::clean($elemento['product_id']); ?>",
                        },
                        "status_id": {
                            "label": "<?php echo Imx\db::rquery("select name from status where status_id='{$elemento['status_id']}'"); ?>",
                            "type": "select",
                            "value": "<?php echo Imx\utils::clean($elemento['status_id']); ?>",
                        },
                        "grade_id": {
                            "label": "<?php echo Imx\db::rquery("select name from grades where grade_id='{$elemento['grade_id']}'"); ?>",
                            "type": "select",
                            "value": "<?php echo Imx\utils::clean($elemento['grade_id']); ?>",
                        },

                        "serial_number": {
                            "label": "<?php echo Imx\utils::clean($elemento['serials']); ?>",
                            "type": "text",
                            "value": "<?php echo $elemento['serials']; ?>",
                        },
                        "price": {
                            "label": "<?php echo Imx\utils::clean($elemento['price']); ?>",
                            "type": "text",
                            "value": "<?php echo $elemento['price']; ?>",
                        },
                        "shipping_price": {
                            "label": "<?php echo Imx\utils::clean($elemento['shipping_price']); ?>",
                            "type": "text",
                            "value": "<?php echo $elemento['total']; ?>",
                        },
                        "min_sale_price": {
                            "label": "<?php echo Imx\utils::clean($elemento['total']); ?>",
                            "type": "text",
                            "value": "<?php echo $elemento['min_sale_price']; ?>",
                        },
                        "sale_price": {
                            "label": "<?php echo Imx\utils::clean($elemento['total']); ?>",
                            "type": "text",
                            "value": "<?php echo $elemento['sale_price']; ?>",
                        },
                        "subtotal": {
                            "label": "<?php echo Imx\utils::clean($elemento['total']); ?>",
                            "type": "text",
                            "value": "<?php echo $elemento['total']; ?>",
                        },
                        "notes": {
                            "label": "<?php echo Imx\utils::clean($elemento['notes']); ?>",
                            "type": "text",
                            "value": "<?php echo $elemento['notes']; ?>",
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
            $('#store_id').change( function() {
                    window.stax = $(this).select2('data')[0].tax;
            });
            $('#product_id').on('select2:select', function(e) {
                if ($('#store_id').val() == null) {
                    alert('Please select the store before capturing items');
                    $('#product_id').val(null).trigger("change");
                }

            });


            $('#taxes').keyup(function() {
                x_itemsobject.totalinvoice();
            })


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

            function showInfo() {
                var totalcost = parseFloat($('#shipping_price').val()) + parseFloat($('#price').val());
                var min_utility = parseFloat(parseFloat($('#min_sale_price').val()) - totalcost);
                var max_utility = parseFloat(parseFloat($('#sale_price').val()) - totalcost);
                var min_utilitypct = (min_utility / totalcost) * 100;
                var max_utilitypct = (max_utility / totalcost) * 100;
                min_utilitypct = Math.round(min_utilitypct * 100) / 100;
                max_utilitypct = Math.round(max_utilitypct * 100) / 100;
                // min_utility = parseInt(min_utility * 100) / 100;
                // max_utility = parseInt(max_utility * 100) / 100;


                $('#info').val(
                    `Total Cost per Item : $` + totalcost +
                    `\nMinimum utility per item : $ ` + min_utility + ` ` + min_utilitypct + ` %` +
                    `\nMaximum utility per item : $ ` + max_utility + ` ` + max_utilitypct + ` %`);
            }
            $('#price,#min_sale_price,#sale_price').keyup(function() {
                showInfo()
            });

            $('#grade_id,#status_id,#product_id').change(async function() {
                if (window.editing == false) {


                    var gr = $('#grade_id').val();
                    var st = $('#status_id').val();
                    var pid = $('#product_id').val();

                    if (pid != null && gr != null && st != null) {
                        var url = "/api/products/" + pid + "/prices?grade=" + gr +
                            "&status=" + st + "&store=" + $('#store_id').val();

                        try {
                            const response = await axios.get(url);
                            $('#min_sale_price').val(response.data.min_price);
                            $('#sale_price').val(response.data.price);
                            $('#price').val(response.data.cost);
                            $('#stock_type').val(response.data.stock_type);
                            // Verification of stock_type 
                            if (response.data.stock_type == "Bulk") {
                                $('#serial_number').tagsinput('removeAll');
                                $('#serial_number').val('Bulk stock item');
                                $('#serial_number').attr('disabled', true);
                                $('.bootstrap-tagsinput').css('background-color', "#ccc");
                            } else {
                                $('#serial_number').removeAttr('disabled');
                                $('.bootstrap-tagsinput').css('background-color', "#fff");
                            }
                            calculate();
                            showInfo();
                        } catch (error) {
                            console.error(error);
                        }
                    }
                } else {
                    console.log('No se puede editar si se esta cargando la edicion');
                }
            });

            $("#serial_number").tagsinput({
                trimValue: true,
                allowDuplicates: false

            });

            $('#serial_number').on('beforeItemAdd', async function(event) {
                var serial = event.item;
                // Do some processing here
                await axios.get('/api/validation/serials/' + serial + '?id=<?php echo $id; ?>').then(resp => {
                        if (resp.status != "200") {
                            alert('Serial ' + serial + ' already exist on database');
                            $('#serial_number').tagsinput('remove', serial, {
                                preventPost: true
                            });
                        }
                    })
                    .catch(function(error) {
                        alert('Serial ' + serial + ' already exist on database');
                        $('#serial_number').tagsinput('remove', serial, {
                            preventPost: true
                        });

                        console.log(error.toJSON());
                    });
            });


            $('#items').keyup(function() {
                calculate();
            });
            $('#price').keyup(function() {
                calculate();
            });
            $('#sale_price').keyup(function() {
                calculate();
            });
            $('#min_sale_price').keyup(function() {
                calculate();
            });



        });
    });


    function calculate() {
        let total = parseFloat($('#price').val()) * parseFloat($('#items').val());
        $('#subtotal').val(total);

        total = total + parseFloat($('#vat').val());
        if (isNaN(total)) {
            total = 0;
        }
        $('#total_price').val(total);
    }

    function addTag(tag) {
        $('#serial_number').tagsinput('add', tag);
    }
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
