<?php

use Imx\db;
use Imx\html;

html::head("Physical inventory ");
html::bodyInit();
html::header("");
html::sidebar();
$formdata = db::dataQuery("select * from  out_inventory where out_inventory_id ='$id'");

html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Physical inventory", "link" => "/back-office/inventories"],
    ['text' => $id, "link" => "/back-office/inventories/$id"],
    ['text' => ucfirst($status), "link" => "/back-office/inventories/$id/$status"],
]);


$latte = new Latte\Engine;
$form = "";
# TODO check if the inventory is pending
switch ($status) {
    case "cancel":
        if (isset($_GET['process']))
            Imx\db::iquery("update inventories set status = 'Cancelled' where inventory_id='{$id}'");
        if (isset($_GET['process'])) {
            $form .= "Inventory {$id} cancelled successfully<br><br>
<input type='button' value='Go back' onclick=\"window.location.href='/back-office/inventories'\" class=\"btn btn-danger\">
";
        } else {
            $form .= "
<input type='button' value='Confirm' onclick=\"window.location.href='/back-office/inventories/{$id}/cancel?process=true'\" class=\"btn btn-success\">
<input type='button' value='Go back' onclick=\"window.location.href='/back-office/inventories'\" class=\"btn btn-danger\">
";
        }

        break;
    case "apply":
        // query the inventory
        $store = Imx\db::rquery("select store_id from inventories where inventory_id = '{$id}'");
        $query = "SELECT
	1 as stock,
	items.serial_number,
	items.item_id,
	categories.name as category,
	concat(items.product_id,'|',
	    items.grade_id,'|',
	    items.status_id) as uq,
	products.product_id,
	products.name AS product,
	items.cost,
	items.grade_id,
	items.status_id,
	products.model,
	brands.name AS brand,
	status.name AS status,
	grades.name AS grade,
	@inv:=(
	select coalesce(sum(quantity),0) from transactions  where reference_id = '{$id}' and transaction_type ='Inventory' and transactions.item_id  = items.item_id
 ) as inventory,
    coalesce(@inv,0) - 1   as difference,
    (coalesce(@inv,0) - 1) * items.cost  as difference_money,
    stores.name as store
FROM
	items
	    left join stores on stores.store_id = items.store_id
	LEFT JOIN products ON products.product_id = items.product_id
	LEFT JOIN categories ON categories.category_id = products.category_id
	LEFT JOIN brands ON brands.brand_id = products.brand_id
	LEFT JOIN status ON status.status_id = items.status_id
	LEFT JOIN grades ON grades.grade_id = items.grade_id 
WHERE
	items.store_id = {$store}
and items.status = 1
 ";
        $inventory = Imx\db::dataQueryMultiple($query);
        $data = [
            'columnsData' => [
                [

                    [
                        "key" => "store",
                        "label" => "Store",
                        "proportion" => 3,
                        "type" => "string"
                    ],
                    [
                        "key" => "stock",
                        "label" => "Stock",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],
                    [
                        "key" => "inventory",
                        "label" => "Inventory",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],
                    [
                        "key" => "difference",
                        "label" => "Difference",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,


                    ],
                    [
                        "key" => "difference_money",
                        "label" => "Difference $",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,
                    ],


                ],

                [

                    [
                        "key" => "brand",
                        "label" => "Brand",
                        "proportion" => 3,
                        "type" => "string"
                    ],
                    [
                        "key" => "stock",
                        "label" => "Stock",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],
                    [
                        "key" => "inventory",
                        "label" => "Inventory",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],
                    [
                        "key" => "difference",
                        "label" => "Difference",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,
                    ],
                    [
                        "key" => "difference_money",
                        "label" => "Difference $",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],


                ],
                [

                    [
                        "key" => "category",
                        "label" => "Category",
                        "proportion" => 3,
                        "type" => "string"
                    ],
                    [
                        "key" => "stock",
                        "label" => "Stock",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],
                    [
                        "key" => "inventory",
                        "label" => "Inventory",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],
                    [
                        "key" => "difference",
                        "label" => "Difference",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,


                    ],
                    [
                        "key" => "difference_money",
                        "label" => "Difference $",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],


                ],
                [

                    [
                        "key" => "product",
                        "label" => "Product",
                        "proportion" => 3,
                        "type" => "string"
                    ],
                    [
                        "key" => "model",
                        "label" => "Model",
                        "proportion" => 2,
                        "type" => "string"
                    ],

                    [
                        'key' => 'status',
                        'label' => 'Status',
                        "proportion" => 2,
                        "type" => "string"
                    ],

                    [
                        "key" => "grade",
                        "label" => "Grade",
                        "proportion" => 1,
                        "type" => "string"
                    ],
                    [
                        "key" => "stock",
                        "label" => "Stock",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],
                    [
                        "key" => "inventory",
                        "label" => "Inventory",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],
                    [
                        "key" => "difference",
                        "label" => "Difference",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,


                    ],
                    [
                        "key" => "difference_money",
                        "label" => "Difference $",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],

                ]
                ,
                [


                    [
                        "key" => "serial_number",
                        "label" => "Serial",
                        "proportion" => 1,
                        "type" => "string",

                    ], [
                    "key" => "cost",
                    "label" => "Cost",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",

                ],
                    [
                        "key" => "inventory",
                        "label" => "Inventory",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                    ],
                    [
                        "key" => "difference",
                        "label" => "Difference",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",


                    ],
                    [
                        "key" => "difference_money",
                        "label" => "Difference $",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,
                    ],

                ]
            ],
            'data' => $inventory,
            'groupByData' => ["store", "brand", "category", "uq"]

        ];
        $latte = new Latte\Engine;
        $data['latte'] = $latte;
        $form = $latte->renderToString('../app/templates/report.latte', $data);
        if (isset($_GET['process'])) {

            Imx\db::iquery("update inventories set status = 'Applied' where inventory_id='{$id}'");
            // Now we create the transactions
            foreach ($inventory as $item) {
                $status = ($item['difference'] == 0) ? "0" : "1";
                $query = "
            insert into transactions
            (
            store_id,
            product_id,
            transaction_type,
            status_id,
        grade_id,
            date,
            item_id,
            reference_id,
            status,
            quantity,
            quantity_inventory,
            price,
            total
            )
            values
            (
            '{$store}',
            '{$item['product_id']}',
            'Inventory adjustment',
            '{$item['status_id']}',
            '{$item['grade_id']}',
            now(),
            '{$item['item_id']}',
            '{$id}',
            '{$status}',
            '{$item['difference']}',
            '{$item['inventory']}',
            '{$item['difference_money']}',
            '{$item['difference_money']}'
            );
            ";
                Imx\db::iquery($query);
                if ($status)
                    Imx\db::iquery("update items set status = 4 ,inventory_id ='{$id}' where item_id = '{$item['item_id']}'");
                hdtv::inventory(
                    $item['product_id'],
                    $store,
                    $item['status_id'],
                    $item['grade_id']
                );

            }
                Imx\db::iquery("update inventories set status = 'Applied'  where inventory_id ='{$id}' ");


        }

        if (isset($_GET['process'])) {
            $form .= "Inventory {$id} applied successfully<br><br>
<input type='button' value='Go back' onclick=\"window.location.href='/back-office/inventories'\" class=\"btn btn-danger\">
";
        } else {
            $form .= "
<input type='button' value='Confirm' onclick=\"window.location.href='/back-office/inventories/{$id}/apply?process=true'\" class=\"btn btn-success\">
<input type='button' value='Go back' onclick=\"window.location.href='/back-office/inventories'\" class=\"btn btn-danger\">
";
        }


        break;
    case "report":
        // query the inventory
        $store = Imx\db::rquery("select store_id from inventories where inventory_id = '{$id}'");
        $query = "SELECT
	1 as stock,
	items.serial_number,
	items.item_id,
	categories.name as category,
	concat(items.product_id,'|',
	    items.grade_id,'|',
	    items.status_id) as uq,
	products.product_id,
	products.name AS product,
	items.cost,
	items.grade_id,
	items.status_id,
	products.model,
	brands.name AS brand,
	status.name AS status,
	grades.name AS grade,
	t.quantity_inventory as inventory,
    t.quantity  as difference,
    t.quantity  * items.cost  as difference_money,
    stores.name as store
FROM
    transactions t
        left join	items on  items.item_id = t.item_id
	    left join stores on stores.store_id = items.store_id
	LEFT JOIN products ON products.product_id = items.product_id
	LEFT JOIN categories ON categories.category_id = products.category_id
	LEFT JOIN brands ON brands.brand_id = products.brand_id
	LEFT JOIN status ON status.status_id = items.status_id
	LEFT JOIN grades ON grades.grade_id = items.grade_id 
	

WHERE
    t.reference_id = '{$id}'
    and t.transaction_type ='Inventory adjustment'
	and items.store_id = {$store}
 ";

        $inventory = Imx\db::dataQueryMultiple($query);
        $data = [
            'columnsData' => [
                [

                    [
                        "key" => "store",
                        "label" => "Store",
                        "proportion" => 3,
                        "type" => "string"
                    ],
                    [
                        "key" => "stock",
                        "label" => "Stock",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],
                    [
                        "key" => "inventory",
                        "label" => "Inventory",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],
                    [
                        "key" => "difference",
                        "label" => "Difference",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,


                    ],
                    [
                        "key" => "difference_money",
                        "label" => "Difference $",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,
                    ],


                ],

                [

                    [
                        "key" => "brand",
                        "label" => "Brand",
                        "proportion" => 3,
                        "type" => "string"
                    ],
                    [
                        "key" => "stock",
                        "label" => "Stock",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],
                    [
                        "key" => "inventory",
                        "label" => "Inventory",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],
                    [
                        "key" => "difference",
                        "label" => "Difference",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,
                    ],
                    [
                        "key" => "difference_money",
                        "label" => "Difference $",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],


                ],
                [

                    [
                        "key" => "category",
                        "label" => "Category",
                        "proportion" => 3,
                        "type" => "string"
                    ],
                    [
                        "key" => "stock",
                        "label" => "Stock",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],
                    [
                        "key" => "inventory",
                        "label" => "Inventory",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],
                    [
                        "key" => "difference",
                        "label" => "Difference",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,


                    ],
                    [
                        "key" => "difference_money",
                        "label" => "Difference $",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],


                ],
                [

                    [
                        "key" => "product",
                        "label" => "Product",
                        "proportion" => 3,
                        "type" => "string"
                    ],
                    [
                        "key" => "model",
                        "label" => "Model",
                        "proportion" => 2,
                        "type" => "string"
                    ],

                    [
                        'key' => 'status',
                        'label' => 'Status',
                        "proportion" => 2,
                        "type" => "string"
                    ],

                    [
                        "key" => "grade",
                        "label" => "Grade",
                        "proportion" => 1,
                        "type" => "string"
                    ],
                    [
                        "key" => "stock",
                        "label" => "Stock",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],
                    [
                        "key" => "inventory",
                        "label" => "Inventory",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],
                    [
                        "key" => "difference",
                        "label" => "Difference",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,


                    ],
                    [
                        "key" => "difference_money",
                        "label" => "Difference $",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,

                    ],

                ]
                ,
                [


                    [
                        "key" => "serial_number",
                        "label" => "Serial",
                        "proportion" => 1,
                        "type" => "string",

                    ], [
                    "key" => "cost",
                    "label" => "Cost",
                    "proportion" => 1,
                    "type" => "number",
                    "align" => "right",

                ],
                    [
                        "key" => "inventory",
                        "label" => "Inventory",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                    ],
                    [
                        "key" => "difference",
                        "label" => "Difference",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",


                    ],
                    [
                        "key" => "difference_money",
                        "label" => "Difference $",
                        "proportion" => 1,
                        "type" => "number",
                        "align" => "right",
                        "totalKey" => true,
                    ],

                ]
            ],
            'data' => $inventory,
            'groupByData' => ["store", "brand", "category", "uq"]

        ];
        $latte = new Latte\Engine;
        $data['latte'] = $latte;
        $form = $latte->renderToString('../app/templates/report.latte', $data);
        $form .= "
<input type='button' value='Go back' onclick=\"window.location.href='/back-office/inventories'\" class=\"btn btn-danger\">
";

        break;

//}
}
$status = ucfirst($status);
echo $latte->renderToString('../app/templates/panel.latte', ["title" => "$status Inventory", "body" => $form]);
html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
