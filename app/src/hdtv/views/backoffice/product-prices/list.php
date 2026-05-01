<?php
error_reporting(0);

use Imx\db;
use Imx\html2 as html;

html::head(" Product Pricing ");
html::bodyInit();
html::header("");
html::sidebar();
html::beginContent();

$latte = new Latte\Engine;

$options = [
    ["text" => "Fecha", "value" => "fecha"],
    ["text" => "Fecha Pago", "value" => "pago_fecha"],
    ["text" => "Proveedor", "value" => "proveedor_id"],
    ["text" => "Id", "value" => "id"],
];

$fecha = [
    ["text" => "Solicitud", "value" => 1],
    ["text" => "Pago", "value" => 2],
];

$params = [
    'title' => "Product Prices",
    'name' => "pprices",
    'fields' => [
        ['name' => 'price-list', 'label' => 'Price List', 'type' => 'select', 'ajax' => "/api/sel2/price-lists", 'required' => true],
        ['name' => 'product', 'label' => 'Product', 'type' => 'select', 'ajax' => "/api/sel2/products", 'required' => true],
    ]

];
$html = '
<input type="button" class="btn btn-primary" onclick="validateForm();" value="Get Prices">
<br>
<br>
<div id="pricedetail" class="col-12">

</div>
';
$form =  $latte->renderToString('../app/templates/form.latte', $params);
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Price List Setup", "body" => $form . $html]);
?>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        $(document).ready(function() {
            // validateForm();
        });
    });

    function validatePrice(pricelist, productid) {
        Imx.validaForm('/api/back-office/product-pricing/' + pricelist + '/' + productid, false);
    }

    function validateForm() {

        $('#pricedetail').html(`
        <div class="fa-3x">
  <i class="fas fa-spinner fa-spin"></i>
</div>
`);
        Imx.validaForm('/back-office/product-prices', false, [], "", "", "", false);

    }


    function callbackForm(result) {
        if (result == "recarga") {
            validateForm();
        } else {

            $('#pricedetail').html(result);
        }
    }
</script>
<?php
html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
