<?php

use Imx\db;
use Imx\html;

html::head("Payment Methods ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from payment_methods where payment_method_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Payment methods", "link" => "/catalogs/payment-methods"],
    ['text' => $formTitle, "link" => "/catalogs/payment-methods/$id"],
]);


$latte = new Latte\Engine;


$opts = [
    [
        'text' => 'Yes',
        'value' => '1'
    ],
    [
        'text' => 'No',
        'value' => '0'
    ],
];
$params = [
    'title' => "payment_method",
    'name' => "verpayment_method",
    'cols' => "6",
    'fields' => [
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Name', 'required' => 'true'],
        [
            'name' => 'bank_account_id', 'value' => $formdata['bank_account_id'] ?? "",
            'label' => 'Bank Account', 'type' => "select", "ajax" => "/api/sel2/bank-accounts/"
        ],
        ['name' => 'taxable', 'value' => $formdata['taxable'] ?? "",'type'=>'select','data'=>$opts ,'label' => 'Taxable'],
        ['name' => 'credit', 'value' => $formdata['credit'] ?? "",'type'=>'select','data'=>$opts ,'label' => 'Credit'],
        ['name' => 'contact', 'value' => $formdata['contact'] ?? "", 'label' => 'Contact Info'],
    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);

$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/payment-methods\'" value="Back">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/catalogs/payment-methods/' . $id . '\')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Payment method", "body" => $form]);
?>

<script>
    function callbackForm(result) {
        window.location.href = '/catalogs/payment-methods';
    }
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
