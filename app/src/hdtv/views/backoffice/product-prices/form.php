<?php

$product_id = $_POST[0]['data']['product']['value'];
$price_list = $_POST[0]['data']['price-list']['value'];
$inventory = Imx\db::rquery("select count(*) from items where items.product_id = $product_id and status = 1");
if($inventory <= 0){
    $avc = Imx\db::rquery("select cost from items where items.product_id = $product_id order by item_id desc limit 1");
}
else
{
    $avc = Imx\db::rquery("select avg(cost) from items where items.product_id = $product_id and status = 1");
}
$avc = Imx\utils::decimales2($avc);
$q = "select * from product_pricing where product_id ='$product_id' and price_list_id ='$price_list'";
$prices = Imx\db::dataQueryMultiple($q);
$status = Imx\db::dataQueryMultiple("select * from status where row_status = 1");
$grades = Imx\db::dataQueryMultiple("select * from grades where row_status = 1");
$latte = new Latte\Engine;


$params = [
    'title' => "prices",
    'name' => "viewprices",
    'cols' => "6",
    'fields' => []

];

foreach ($status as $state) {

    foreach ($grades as $grade) {
        if ($state['name'] == "New" && $grade['name'] != "A") continue;
        if ($state['name'] != "New" && $grade['name'] == "A") continue;
        $value = 0;
        $minvalue = 0;
        foreach ($prices as $price) {
            if ($price['status_id'] == $state['status_id'] && $price['grade_id'] == $grade['grade_id']) {
                $value = $price['price'];
                $minvalue = $price['min_price'];
            }
        }
//        if($value != 0 && $minvalue != 0){
        $f =  ['name' => "price_{$state['status_id']}_{$grade['grade_id']}", 'value' => $value, 'label' => "{$state['name']} {$grade['name']} ", 'type' => 'numeric', 'required' => 'true'];
        $f2 =  ['name' => "min_price_{$state['status_id']}_{$grade['grade_id']}", 'value' => $minvalue, 'label' => "Min. {$state['name']} {$grade['name']} ", 'type' => 'numeric', 'required' => 'true'];
        $params['fields'][] = $f;
        $params['fields'][] = $f2;
//        }
    }
}

$form =  $latte->renderToString('../app/templates/form.latte', $params);
$form = $form . '
<input type="button" class="btn btn-success" onclick="validatePrice(' . $price_list . ',' . $product_id . ')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Price per Status and Grade  (Average Cost : $ $avc)", "body" => $form]);
