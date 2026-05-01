<?php

use Imx\db;
use Imx\html;

html::head("Physical inventory ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from  inventories where inventory_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Physical inventory", "link" => "/back-office/inventories"],
    ['text' => $formTitle, "link" => "/back-office/inventories/$id"],
]);


$latte = new Latte\Engine;



$params = [
    'title' => "inventory",
    'name' => "verinventory",
    'cols' => "6",
    'fields' => [
    ['name' => 'date', 'value' => Imx\utils::sql2date($formdata['date'] ?? date('Y-m-d')), "locale" => "en", 'format' => 'm/d/Y', 'label' => 'Date', 'required' => 'true', 'type' => 'date'],
        [
            'name' => 'store_id', 'value' => $formdata['store_id'] ?? "",
            'label' => 'Store', 'required' => 'true', 'type' => "select", "ajax" => "/api/sel2/stores/"
        ],

        ['name' => 'total_cost', 'value' => $formdata['total'] ?? "0", 'label' => 'Total Cost',  'required' => 'true', 'type' => 'rnumeric'],
        ['name' => 'observations', 'value' => $formdata['observations'] ?? "", 'label' => 'Observations', 'type' => 'textarea'],

    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Physical inventory", "body" => $form]);



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
        ['name' => 'cost', 'value' => $formdata['cost'] ?? "0", 'label' => 'Cost', 'type' => 'rnumeric', 'required' => 'true', 'detail' => "no", 'readonly' => true],
        ['name' => 'item_id', 'required' => true, 'label' => 'Item', 'type' => 'select', 'helper' => "", 'detail' => "no"],
    ],

];

$form = $latte->renderToString('../app/templates/form-detail.latte', $params1);

$status = $formdata['status']??"Pending";
if($status == "Pending")
{


$form = $form . '
<div class="row">
<div class="input-group col-12">
                            <label class="col-form-label col-3">Multiple serials Add
                              </label>
                            <div class="col-8">
                                    <textarea id="serial_numbers"  class="form-control input-form" rows="3" style="height: 73px;"></textarea>
                            </div> 
                                                      <div class="col-1">
                                      <button class="btn btn-default" onclick="validateSerials();"><i class="fa fa-plus-circle"></i></button>

                            </div>
                        </div>
</div>
<input type="button" class="btn btn-danger" onclick="window.location.href=\'/back-office/inventories\'" value="Back">
    <input type="button" class="btn btn-primary" onclick="validaForm()" value="Save">';
}
else
{
    $form.='
<h2>This inventory is '.$status.'</h2>
<input type="button" class="btn btn-danger" onclick="window.location.href=\'/back-office/inventories\'" value="Back">';
}
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Items detail", "body" => $form]);
?>
    <div class="modal fade" id="validationmodal" tabindex="-1" role="dialog" aria-labelledby="{$name}modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="validationmodalmodalLabel"> Serials validation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h2>Validation result </h2>
                    <div id="validationresult" class="fs-6"> </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="addserials" onclick="appendserials()">Append Items</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        var serialsToValidate;

        function appendserials(){
            let error = false;
            document.serialsToValidate.forEach((item) =>
            {

                if(item.error.includes("Ok")  == false){
                    console.log(item);
                    error = true;
                }
            })
            if(error == true)
            {
                alert('Can not continue with errors');
                return false;
            }
            document.serialsToValidate.forEach((item) =>
            {

                var itemObject = {
                    "stock_type": {
                        "label": item.data.stock_type.label,
                        "type": item.data.stock_type.type,
                        "value": item.data.stock_type.value,
                    },

                    "product_id": {
                        "label": item.data.product_id.label,
                        "type": item.data.product_id.type,
                        "value": item.data.product_id.value,
                    },
                    "item_id": {
                        "label": item.data.item_id.label,
                        "type": item.data.item_id.type,
                        "value": item.data.item_id.value,
                    },
                    "status_id": {
                        "label": item.data.status_id.label,
                        "type": item.data.status_id.type,
                        "value": item.data.status_id.value,
                    },
                    "grade_id": {
                        "label": item.data.grade_id.label,
                        "type": item.data.grade_id.type,
                        "value": item.data.grade_id.value,
                    },

                    "cost": {
                        "label": item.data.cost.label,
                        "type": item.data.cost.type,
                        "value": item.data.cost.value,
                    },
                };
                x_itemsobject.items.push(
                    itemObject
                );
                });
            // clean textareas
            $('#serial_numbers').val('');
            $('#validationresult').html('');
            // colse modal
            $('#validationmodal').modal("hide");
            document.serialsToValidate=[];
            var total = 0;
            x_itemsobject.items.forEach(function(key) {
                total = total + parseFloat(key.cost.value);
            });
            $('#total_cost').val(total);

        }
        async function  validateSerials(){
            let xitems = x_itemsobject.items.map(function(item){return item.item_id.label;});
            if ($('#store_id').val() == null) {
                alert('Please select the store before capturing items');
                return false;
            }
            let serials = $('#serial_numbers').val().split("\n");
            if(serials.length == 0){
                alert('You have to type at least one serial per line');
                return false;
            }
            $('#validationresult').html('');
            $('#validationmodal').modal("show");
            $('#addserials').attr('disabled',true);
            document.serialsToValidate = [];
            serials.forEach(async (serial) => {


                if(serial.trim() == "") return;
                let response = await axios.get("/api/items/"+serial+"?store="+$('#store_id').val());
                // avoid duplicating
                console.log(response.data.data);
                if(xitems.includes(serial)){
                    response.data.error ='Duplicated item';
                }
                var prod = "N/A";
                if(response.data.error != "Not found") {
                    prod = response.data.data.product_id.label;

                }
                if(response.data.error == "Ok") {
                    response.data.error ='<span style="color:green;font-weight:bold;"> '+response.data.error+'</span>';

                }
                else{
                    response.data.error ='<span style="color:red;font-weight:bold;"> '+response.data.error+'</span>';
                }
                $('#validationresult').html($('#validationresult').html() +
                    response.data.error  + " " + serial + " => "+
                    prod+"<br>");

                var psconsole = $('#validationresult');
                if(psconsole.length)
                    psconsole.scrollTop(psconsole[0].scrollHeight - psconsole.height());


                document.serialsToValidate.push(response.data);
            });

            $('#addserials').removeAttr('disabled');

        }
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



            Imx.validaForm('/api/back-office/inventories/<?php echo $id; ?>', true, ['x_items'], dt1);

        }


        function callbackForm(result) {
            window.location.href = '/back-office/inventories';
        }
        document.addEventListener("DOMContentLoaded", function() {



            <?php
            if ($id != "new") {
            $query = "select 
            transactions.*,
            (select stock_type from products where products.product_id = transactions.product_id)
            from transactions  where transaction_type ='Inventory' and reference_id =  '$id' ";

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
                // $('#item_id').on('select2:select', function(e) {
                //     console.log(e);
                //     console.log('validamos cambios');
                //     var gradeOption = new Option(e.params.data.grade, e.params.data.grade_id, false, false);
                //     var productOption = new Option(e.params.data.product, e.params.data.product_id, false, false);
                //     var statusOption = new Option(e.params.data.status, e.params.data.status_id, false, false);
                //     $('#grade_id').append(gradeOption).val( e.params.data.grade_id);
                //     $('#status_id').append(statusOption).val( e.params.data.status_id);
                //     $('#product_id').append(productOption).val( e.params.data.product_id);
                //
                // });
                $('#x_itemsmodal').on('show.bs.modal', function() {
                    if ($('#store_id').val() == null) {
                        alert('Please select the store before capturing items');
                        setTimeout(function(){
                        $('#x_itemsmodal').modal("hide");
                        },500);
                    }
                    // $("#item_id").select2('destroy');
                    // $("#product_id").val(null).trigger("change");
                    // $("#status_id").val(null).trigger("change");
                    // $("#grade_id").val(null).trigger("change");
                    //
                    // $("#item_id").select2({
                    //     ajax: {
                    //         url:  function() {
                    //             return "/api/sel2/serials?store=" +  $('#store_id').val()
                    //                 +'&grade='+$('#grade_id').val()
                    //                 +'&product='+$('#product_id').val()
                    //                 +'&status='+$('#status_id').val();
                    //         } ,
                    //                 dataType: 'json'
                    //     }
                    // });


                });
                $('#x_itemsmodal').on('hidden.bs.modal', function() {
                    var total = 0;
                    x_itemsobject.items.forEach(function(key) {
                        total = total + parseFloat(key.cost.value);
                    });
                    $('#total_cost').val(total);
                });



                $('#product_id').on('select2:select', function(e) {
                    if ($('#store_id').val() == null) {
                        alert('Please select the store before capturing items');
                        $('#product_id').val(null).trigger("change");

                    }

                });





                $('#grade_id,#status_id,#product_id').change( async function() {
                    $("#item_id").val(null).trigger("change");

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
                            } catch (error) {
                                console.error(error);
                            }
                        }

                    } else {
                      console.log('Cant edit yet');

                    }
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





            });
        });
    </script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
