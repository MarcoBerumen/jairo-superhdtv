<?php

use Imx\db;
use Imx\html;

html::head("Stores ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from stores where store_id ='$id'");
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Stores", "link" => "/catalogs/stores"],
    ['text' => $formTitle, "link" => "/catalogs/stores/$id"],
]);


$latte = new Latte\Engine;



$params = [
    'title' => "store",
    'name' => "verstore",
    'cols' => "6",
    'fields' => [
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Name', 'required' => 'true'],
        ['name' => 'price_list_id', 'value' => $formdata['price_list_id'] ?? "", 'label' => 'Price List', 'type' => 'select', 'ajax' => '/api/sel2/price-lists', 'required' => 'true'],
        ['name' => 'address', 'value' => $formdata['address'] ?? "", 'label' => 'Address', 'required' => 'true'],
        ['name' => 'city', 'value' => $formdata['city'] ?? "", 'label' => 'City', 'required' => 'true'],
        ['name' => 'tax', 'value' => $formdata['tax'] ?? "", 'label' => 'Tax %', 'type' => "numeric", 'required' => 'true'],
        ['name' => 'telephone', 'value' => $formdata['telephone'] ?? "", 'label' => 'Telephone', 'required' => 'true'],
        ['name' => 'email', 'value' => $formdata['email'] ?? "", 'label' => 'Email', 'required' => 'true'],
        ['name' => 'lat', 'value' => $formdata['lat'] ?? "", 'label' => 'Latitude', 'type' => "numeric", 'required' => 'true'],
        ['name' => 'lng', 'value' => $formdata['lng'] ?? "", 'label' => 'Longitude', 'type' => "numeric", 'required' => 'true'],
        ['name' => 'maximum_valid_distance', 'value' => $formdata['maximum_valid_distance'] ?? "", 'label' => 'Max distance (mt)', 'type' => "numeric", 'required' => 'true', 'helper' => 'From the specified coordinates  in meters , example 50'],
        ['name' => 'emailadmin','cols'=>12, 'value' => $formdata['emailadmin'] ?? "", 'label' => 'Notification emails', 'required' => 'true'],
        ['name' => 'policy', 'value' => $formdata['policy'] ?? "", 'label' => 'Policy', 'required' => 'true', 'cols' => '12', 'type' => 'textarea'],
        ['name' => 'banner', 'value' => $formdata['banner'] ?? "", 'label' => 'Banner', 'required' => 'true', 'cols' => '12', 'type' => 'textarea'],
    ]
];

$form =  $latte->renderToString('../app/templates/form.latte', $params);

$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/stores\'" value="Back">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/catalogs/stores/' . $id . '\')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle store", "body" => $form]);
?>

<script>
    function callbackForm(result) {
        window.location.href = '/catalogs/stores';
    }
    document.addEventListener("DOMContentLoaded", function() {


        $(document).ready(function() {

            $("#emailadmin").tagsinput({
                trimValue: true,
                allowDuplicates: false,
                width: 'auto',

            });
        });
    });
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
