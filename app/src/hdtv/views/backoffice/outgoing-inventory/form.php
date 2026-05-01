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

    $formdata = db::dataQuery("select * from  out_inventory where out_inventory_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Outgoing Inventory", "link" => "/back-office/outgoing-inventory"],
    ['text' => $formTitle, "link" => "/back-office/outgoing-inventory/$id"],
]);


$latte = new Latte\Engine;

$store = Imx\db::rquery("select store_id from users where user_id ='{$_SESSION['user']['user_id']}'");


$params = [
    'title' => "physicalinventory",
    'name' => "verphysicalinventory",
    'cols' => "6",
    'fields' => [
        ['name' => 'date', 'value' => $formdata['date'] ?? "", "locale" => "en", 'format' => 'Y-m-d', 'label' => 'Date', 'required' => 'true', 'type' => 'date'],
        [
            'name' => 'store_id', 'value' => $formdata['store_id'] ?? $store,
            'label' => 'Store', 'required' => 'true', 'type' => "select", "ajax" => "/api/sel2/stores/"
        ],
        [
            'name' => 'motive_id', 'value' => $formdata['motive_id'] ?? "",
            'label' => 'Outgoing Motive', 'required' => 'true', 'type' => "select", "ajax" => "/api/sel2/motives/"
        ],
        ['name' => 'total_cost', 'value' => $formdata['total_cost'] ?? "0", 'label' => 'Total Cost',  'required' => 'true', 'type' => 'rnumeric'],
        ['name' => 'observations', 'value' => $formdata['observations'] ?? "", 'label' => 'Observations', 'type' => 'textarea'],

    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Outgoing Inventory", "body" => $form]);



$params1 = [
    'title' => "Items ",
    'addTitle' => "Add Item",
    'cols' => 12,
    'name' => 'x_items',
    'fields' => [
        ['name' => 'product_id', 'value' => $formdata['product_id'] ?? "", 'label' => 'Product', 'type' => 'select', 'ajax' => '/api/sel2/products-models', 'required' => 'true'],
        ['name' => 'status_id', 'value' => $formdata['status_id'] ?? "", 'label' => 'Status', 'type' => 'select', 'ajax' => '/api/sel2/status', 'required' => 'true'],
        ['name' => 'grade_id', 'value' => $formdata['grade_id'] ?? "", 'label' => 'Grade', 'type' => 'select', 'ajax' => '/api/sel2/grade', 'required' => 'true'],
        ['name' => 'stock_type', 'value' => "", 'label' => 'Stock Type', 'type' => 'readonly'],
        ['name' => 'stock', 'value' => "", 'label' => 'Stock', 'type' => 'readonly'],
        ['name' => 'cost', 'value' => $formdata['cost'] ?? "0", 'label' => 'Cost', 'type' => 'rnumeric', 'required' => 'true', 'detail' => "no", 'readonly' => true],
        ['name' => 'item_id', 'required' => true, 'label' => 'Item', 'type' => 'select', 'helper' => "", 'detail' => "no"],
    ],

];

$form = $latte->renderToString('../app/templates/form-detail.latte', $params1);


$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/back-office/outgoing-inventory\'" value="Back">
    <input type="button" class="btn btn-primary" onclick="validaForm()" value="Save">';

echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Items detail", "body" => $form]);
?>
<script>
    var editing = false;

    function validaForm() {

        dt1 = {
            form: 'items',
            data: x_itemsobject._data.items
        }

        var serials = [];
        var error = false;
        if (x_itemsobject.items.length == 0) {
            alert('You must add at least one item');
            return false;

        }

        if (error) {
            return false;
        }
        console.log(serials);
        // if (dt1.data.length != $('#items').val()) {
        //     alert('You must add the exact total items on the purchase order');
        //     return false;
        // }
        if (isNaN($('#total_cost').val())) {
            alert('The value for total is incorrect');
            return false;

        }



        Imx.validaForm('/api/back-office/outgoing-inventory/<?php echo $id; ?>', true, ['x_items'], dt1);

    }


    function callbackForm(result) {
        window.location.href = '/back-office/outgoing-inventory';
    }
    document.addEventListener("DOMContentLoaded", function() {



        <?php
        if ($id != "new") {
            $query = "select 
            transactions.*,
            (select stock_type from products where products.product_id = transactions.product_id)
            from transactions  where transaction_type ='Out of Inventory' and reference_id =  '$id' ";

            $detalle = Imx\db::dataQueryMultiple($query);
            foreach ($detalle as $elemento) {
                $stock_type = ($elemento['stock_type'] == "1") ? "Bulk" : "Unique Serial";


                /*

                */
                $stock = 1;
        ?>
                setTimeout(function() {

                    x_itemsobject.items.push({


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
                        "item_id": {
                            "label": "<?php echo Imx\db::rquery("select serial_number from items where item_id='{$elemento['item_id']}'"); ?>",
                            "type": "select",
                            "value": "<?php echo Imx\utils::clean($elemento['item_id']); ?>",
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

                        "stock": {
                            "label": "<?php echo Imx\utils::clean($stock); ?>",
                            "type": "text",
                            "value": "<?php echo $stock; ?>",
                        },
                        "cost": {
                            "label": "<?php echo Imx\utils::clean($elemento['price']); ?>",
                            "type": "text",
                            "value": "<?php echo $elemento['price']; ?>",
                        },

                        "subtotal": {
                            "label": "<?php echo Imx\utils::clean($elemento['total']); ?>",
                            "type": "text",
                            "value": "<?php echo $elemento['total']; ?>",
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

            $('#x_itemsmodal').on('hidden.bs.modal', function() {
                var total = 0;
                x_itemsobject.items.forEach(function(key) {
                    total = total + parseFloat(key.cost.value);
                });
                console.log('Total Cost');
                console.log(total);
                $('#total_cost').val(total);
            });
            $('#product_id').on('select2:select', function(e) {
                if ($('#store_id').val() == null) {
                    alert('Please select the store before capturing items');
                    $('#product_id').val(null).trigger("change");
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
                            $('#cost').val(response.data.cost);
                            $('#stock_type').val(response.data.stock_type);
                            $('#stock').val(response.data.stock);
                            $('#item_id').val(null).trigger('change');
                            $("#item_id").empty();
                            var itemnumber = 0;
                            response.data.items.forEach((item) => {
                                itemnumber = itemnumber + 1;
                                if (response.data.stock_type == "Bulk") {
                                    var newOption = new Option("Bulk Item #" + itemnumber, item.item_id, false, false);
                                } else {
                                    var newOption = new Option(item.serial_number, item.item_id, false, false);

                                }
                                $('#item_id').append(newOption);
                                console.log(item);
                            });
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
                maxTags: 1,
                allowDuplicates: false

            });

            $('#serial_number').on('beforeItemAdd', async function(event) {
                var serial = event.item;
                // Do some processing here
                var storeid = $('#store_id').val();
                await axios.get('/api/validation/serials/' + serial + '?id=<?php echo $id; ?>&exists=1&store=' + storeid).then(resp => {
                        if (resp.status != "200") {
                            alert('Serial ' + serial + ' does not exists on store stock or is not available');
                            $('#serial_number').tagsinput('remove', serial, {
                                preventPost: true
                            });
                        }
                    })
                    .catch(function(error) {
                        alert('Serial ' + serial + ' does not exists on store stock or is not available');
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
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
