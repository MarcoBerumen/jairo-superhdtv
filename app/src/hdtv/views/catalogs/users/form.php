<?php

use Imx\db;
use Imx\html;

html::head("User");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from users where user_id ='$id'");
    $formdata['password'] = Imx\utils::decrypt($formdata['password']);
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Users", "link" => "/catalogs/users"],
    ['text' => $formTitle, "link" => "/catalogs/users/$id"],
]);


$latte = new Latte\Engine;
if($formdata['bo_stores']){
    $bo_stores = Imx\db::dataQueryMultiple("select name as text, store_id as value, 1 as selected from stores where row_status =1 and store_id in({$formdata['bo_stores']})");
} else{
    $bo_stores= [];
}

$img = (isset($formdata['face'])) ? explode(',', $formdata['face'] ?? "") : [];

$workdays = [
    ['text'=>'Monday','value'=>'Monday'],
    ['text'=>'Tuesday','value'=>'Tuesday'],
    ['text'=>'Wednesday','value'=>'Wednesday'],
    ['text'=>'Thursday','value'=>'Thursday'],
    ['text'=>'Friday','value'=>'Friday'],
    ['text'=>'Saturday','value'=>'Saturday'],
    ['text'=>'Sunday','value'=>'Sunday']
];
if($formdata['workdays']){

$wdays = json_decode($formdata['workdays'],false);

    array_walk($workdays,function(&$wd) use($wdays){
        if(in_array($wd['text'],$wdays)){
      $wd['selected'] = true;
        }
    });
}

$formdata['fechaIngreso'] = Imx\utils::fechamex($formdata['fechaIngreso']);
$params = [
    'title' => "user",
    'name' => "veruser",
    'cols' => "6",
    'fields' => [
        ['name' => 'email', 'value' => $formdata['email'] ?? "", 'label' => 'Email', 'helper' => 'Login ID', 'required' => 'true', 'type' => 'email'],
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Name', 'helper' => 'User Full name', 'required' => 'true'],
        ['name' => 'password', 'value' => $formdata['password'] ?? "", 'label' => 'Login Password', 'required' => 'true', 'type' => 'password'],
        ['name' => 'profile_id', 'value' => $formdata['profile_id'] ?? "", 'label' => 'Profile', 'required' => 'true', 'type' => 'select', 'ajax' => '/api/sel2/profiles'],
        ['name' => 'store_id', 'value' => $formdata['store_id'] ?? "",'label' => 'POS Store', 'required' => 'true', 'type' => 'select', 'ajax' => '/api/sel2/stores'],
        ['name' => 'bo_stores', 'value' => $formdata['bo_stores'] ?? "", 'data'=>$bo_stores, 'label' => 'Stores Management', 'type' => 'multiple', 'ajax' => '/api/sel2/stores'],
        ['name' => 'face', 'files' => $img ?? "", 'label' => 'Face Photo', 'type' => 'imgs3', 'delete_link' => "/api/catalogs/users/$id/face/delete/"],
        ['name' => 'workdays', 'value' => "",'label' => 'Working days (Week)', 'required' => 'true', 'type' => 'multiple', 'data' => $workdays],
        ['name' => 'wage', 'value' => $formdata['wage'] ?? "0", 'label' => 'Hourly Wage', 'type' => 'numeric', 'required' => 'true'],

    ]

];

$form =  $latte->renderToString('../app/templates/form.latte', $params);
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle user", "body" => $form]);

$ct = [
    [
        "text" => "Percentage",
        "value" => "Percentage",
    ],
    [
        "text" => "Fixed",
        "value" => "Fixed",
    ]
];
$params1 = [
    'title' => "Comission Setup ",
    'addTitle' => "Config. Comission",
    'cols' => 12,
    'name' => 'x_comissions',
    'fields' => [
        ['name' => 'category_id', 'value' => $formdata['category_id'] ?? "", 'label' => 'Category', 'required' => 'true', 'type' => 'select', 'ajax' => '/api/sel2/categories'],
        ['name' => 'product_id', 'value' => $formdata['product_id'] ?? "", 'label' => 'Product', 'type' => 'select', 'ajax' => '/api/sel2/products'],
        ['name' => 'grade_id', 'value' => $formdata['grade_id'] ?? "", 'label' => 'Grade', 'type' => 'select', 'ajax' => '/api/sel2/grade'],
        ['name' => 'status_id', 'value' => $formdata['status_id'] ?? "", 'label' => 'Status', 'type' => 'select', 'ajax' => '/api/sel2/status'],
        ['name' => 'comission_type', 'value' =>  "", 'label' => 'Comission Type', 'type' => 'select', 'data' => $ct, 'required' => 'true'],
        ['name' => 'range', 'value' => $formdata['range'] ?? "0", 'label' => 'Start Range $', 'Amount per sale in USD', 'type' => 'numeric', 'required' => 'true'],
        ['name' => 'comission', 'value' => $formdata['comission'] ?? "0", 'label' => 'Comission', 'type' => 'numeric', 'required' => 'true',],
    ],

];

$form = $latte->renderToString('../app/templates/form-detail.latte', $params1);


$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/users\'" value="Back">
<input type="button" class="btn btn-primary" onclick="validaForm()" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Comission Setup", "body" => $form]);



?>

<script>
    function validaForm() {
        comm = {
            form: 'comissions',
            data: x_comissionsobject._data.items
        }

        Imx.validaForm('/api/catalogs/users/<?php echo $id; ?>', true, ['x_comissions'], comm);

    }



    function callbackForm(result) {
        window.location.href = '/catalogs/users';
    }

    document.addEventListener("DOMContentLoaded", function() {



        <?php
        if ($id != "new") {
            $query = "select users_comissions.*,
            (select categories.name from categories where category_id = users_comissions.category_id)  as category,
            (select products.name from products where product_id = users_comissions.product_id)  as product,
            (select grades.name from grades where grade_id = users_comissions.grade_id)  as grade,
            (select status.name from status where status_id = users_comissions.status_id)  as status
            
            from users_comissions  where user_id =  '$id' ";
            $detalle = Imx\db::dataQueryMultiple($query);

            foreach ($detalle as $elemento) {
        ?>
                setTimeout(function() {

                    x_comissionsobject.items.push({

                        "category_id": {
                            "label": `<?php echo Imx\utils::clean($elemento['category']); ?>`,
                            "type": "select",
                            "value": "<?php echo $elemento['category_id']; ?>",
                        },
                        "product_id": {
                            "label": `<?php echo Imx\utils::clean($elemento['product']); ?>`,
                            "type": "select",
                            "value": "<?php echo $elemento['product_id']; ?>",
                        },
                        "grade_id": {
                            "label": `<?php echo Imx\utils::clean($elemento['grade']); ?>`,
                            "type": "select",
                            "value": "<?php echo $elemento['grade_id']; ?>",
                        },
                        "status_id": {
                            "label": `<?php echo Imx\utils::clean($elemento['status']); ?>`,
                            "type": "select",
                            "value": "<?php echo $elemento['status_id']; ?>",
                        },
                        "comission_type": {
                            "label": `<?php echo Imx\utils::clean($elemento['comission_type']); ?>`,
                            "type": "select",
                            "value": "<?php echo $elemento['comission_type']; ?>",
                        },

                        "range": {
                            "label": `<?php echo Imx\utils::clean($elemento['range']); ?>`,
                            "type": "text",
                            "value": "<?php echo $elemento['range']; ?>",
                        },
                        "comission": {
                            "label": `<?php echo Imx\utils::clean($elemento['comission']); ?>`,
                            "type": "text",
                            "value": "<?php echo $elemento['comission']; ?>",
                        },

                    });
                }, 1000);

        <?php
            }
        }
        ?>


    });
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
