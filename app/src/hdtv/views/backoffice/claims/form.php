<?php

use Imx\db;
use Imx\html;

html::head("Claim ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from  claims where claim_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Claim", "link" => "/back-office/claims"],
    ['text' => $formTitle, "link" => "/back-office/claims/$id"],
]);


$latte = new Latte\Engine;


$options = [
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
$store = Imx\db::rquery("select store_id from users where user_id ='{$_SESSION['user']['user_id']}'");

$params = [
    'title' => "claim",
    'name' => "verclaim",
    'cols' => "6",
    'fields' => [
        ['name' => 'date', 'value' => Imx\utils::sql2date($formdata['date'] ?? date('Y-m-d')), "locale" => "en", 'format' => 'm/d/Y', 'label' => 'Date', 'required' => 'true', 'type' => 'date'],
        ['name' => 'store_id', 'value' => $formdata['store_id'] ?? $store, 'label' => 'Store', 'required' => 'true', 'type' => "select", "ajax" => "/api/sel2/stores/"],
        ['name' => 'customer_id', 'value' => $formdata['customer_id'] ?? "", 'label' => 'Customer', 'required' => 'true', 'type' => "select", "ajax" => "/api/sel2/customers/"],
        ['name' => 'item_id', 'value' => $formdata['item_id'] ?? "", 'label' => 'Item', 'required' => 'true', 'type' => "select"],
        [
            'name' => 'status', 'value' => $formdata['status'] ?? "",
            'label' => 'Claim Status', 'required' => 'true', 'type' => "select", "data" => $options
        ],
        ['name' => 'credit', 'value' => $formdata['credit'] ?? "", 'label' => 'Credit', 'type' => 'num`eric'],
        ['name' => 'observations', 'value' => $formdata['observations'] ?? "", 'label' => 'Observations', 'type' => 'textarea'],

    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);


$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/back-office/claims\'" value="Back">
<input type="button" class="btn btn-primary" onclick="validaForm()" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Claim", "body" => $form]);

?>
<script>
    var editing = false;

    function validaForm() {




        Imx.validaForm('/api/back-office/claims/<?php echo $id; ?>', true, ['x_items']);

    }


    function callbackForm(result) {
        window.location.href = '/back-office/claims';
    }
    document.addEventListener("DOMContentLoaded", function() {




        var stock_type = "";
        $(document).ready(function() {
            $('#customer_id,#store_id').change(function() {
                console.log("ok");
                var user = $('#customer_id').val();
                var store = $('#store_id').val()
                if (user != null && store != null) {
                    $('#item_id').val(null).trigger("change");
                    $("#item_id").select2({
                        ajax: {
                            url: "/api/sel2/items/claims?customer=" + user + "&store=" + store,
                            dataType: 'json'
                        }
                    });
                }
            });
            $('#item_id').change(function(){
                console.log('Selected item ID :' + $(this).val() );
                if($(this).val() != null){
                 let rcredit = $('#item_id').select2('data')[0].total;
                    <?php
                    if ($id == "new") {
                    ?>
                    $('#credit').val(rcredit);
                    <?php
                    }
                    else{
                        ?>
                    if($('#item_id').val() != '<?php echo $formdata['item_id'];?>'){
                        $('#credit').val(rcredit);
                    }
                    // alert('not new');
                        <?php
                    }
                    ?>
                }

            });

            <?php
            if ($id != "new") {
                $claim = Imx\db::dataQuery("select * from view_claims where claim_id='$id'");

            ?>
                setTimeout(function() {

                    $("#item_id").append(new Option(`<?php echo $claim['item'];?> <?php echo $claim['product'];?>`, "<?php echo $claim['item_id'];?>"));
                    $('#item_id').trigger('change');


                }, 1500);
            <?php
            }
            ?>

        });
    });
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
