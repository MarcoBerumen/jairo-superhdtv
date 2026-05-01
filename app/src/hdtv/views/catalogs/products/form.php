<?php

use Imx\db;
use Imx\html;

html::head("HDTV - products ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
    $fdata = [];
    $formdata['category_id'] = $_GET['category'] ?? "";
} else {
    $formdata = db::dataQuery("select * from products where product_id ='$id'");
    $formdata['category_id'] = $_GET['category'] ?? $formdata['category_id'];
    $formdata['password'] = Imx\utils::decrypt($formdata['password']);
    $formTitle = "Edit";
    $fdata = json_decode($formdata['features'], true);
}
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Products", "link" => "/catalogs/products"],
    ['text' => $formTitle, "link" => "/catalogs/products/$id"],
]);
//exit;



// print_r($fdata);
$latte = new Latte\Engine;


$img = (isset($formdata['images'])) ? explode(',', $formdata['images'] ?? "") : [];

$opts = [
    [
        'text' => 'Bulk',
        'value' => '1',
    ],
    [
        'text' => 'Unique Serial',
        'value' => '2',
    ],
];
$category = $_GET['category'] ?? $formdata['category_id'];
if($category == ""){

    $params = [
        'title' => "product",
        'name' => "viewproduct",
        'cols' => "6",
        'fields' => [
            ['name' => 'category_id', 'value' => $category, 'label' => 'Category', 'required' => 'true', 'type' => 'select', 'ajax' => '/api/sel2/categories'],
        ]

    ];
    $form =  $latte->renderToString('../app/templates/form.latte', $params);

    $form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/products\'" value="Go Back">';
    echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Please select one category ", "body" => $form]);

}
else
{


$params = [
    'title' => "product",
    'name' => "viewproduct",
    'cols' => "6",
    'fields' => [
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Name',  'required' => 'true'],
        ['name' => 'description', 'value' => $formdata['description'] ?? "", 'label' => 'Description', 'required' => 'true', 'type' => 'textarea'],
        ['name' => 'model', 'value' => $formdata['model'] ?? "", 'label' => 'Model', 'required' => 'true'],
        ['name' => 'stock_type', 'value' => $formdata['stock_type'] ?? "", 'label' => 'Stock Type', 'required' => 'true', 'type' => 'select', "data" => $opts],
        ['name' => 'sku', 'value' => $formdata['sku'] ?? "", 'label' => 'SKU', 'required' => 'true'],
        ['name' => 'tags', 'value' => $formdata['tags'] ?? "", 'label' => 'Webscrapper Tags'],
        ['name' => 'prices', 'value' => $formdata['prices'] ?? "", 'label' => 'Prices URL','cols'=>12],
        ['name' => 'brand_id', 'value' => $formdata['brand_id'] ?? "", 'label' => 'Brand', 'required' => 'true', 'type' => 'select', 'ajax' => '/api/sel2/brands'],
        ['name' => 'category_id', 'value' => $category, 'label' => 'Category', 'required' => 'true', 'type' => 'select', 'ajax' => '/api/sel2/categories'],
        // ['name' => 'screen_size', 'value' => $formdata['screen_size'] ?? "", 'label' => 'Screen size', 'helper' => 'Diagonal size in inches', 'required' => 'true', 'type' => "numeric"],
        // ['name' => 'year', 'value' => $formdata['year'] ?? "", 'label' => 'Year', 'required' => 'true', 'type' => "numeric"],
        // ['name' => 'price', 'value' => $formdata['price'] ?? "", 'label' => 'Price', 'required' => 'true', 'type' => "numeric"],
        // ['name' => 'min_price', 'value' => $formdata['min_price'] ?? "", 'label' => 'Min. price', 'required' => 'true', 'type' => "numeric"],
        ['name' => 'warranty_id', 'value' => $formdata['warranty_id'] ?? "", 'label' => 'Warranty', 'required' => 'true', 'type' => 'select', 'ajax' => '/api/sel2/warranties'],
        ['name' => 'images', 'files' => $img ?? "", 'label' => 'Images', 'type' => 'imgs3', 'delete_link' => "/api/catalogs/products/$id/images/delete/"],
    ]

];

if (count($img)  == 0) {
    $params['fields'][9]['required'] = "true";
}
$form =  $latte->renderToString('../app/templates/form.latte', $params);

echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle product", "body" => $form]);



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
    'title' => "features",
    'name' => "viewfeatures",
    'cols' => "6",
    'fields' => []

];

if ($category != "") {
    $features = Imx\db::dataQueryMultiple("select * from features where  FIND_IN_SET($category,categories) ");
    foreach ($features as $feature) {


        $f =  ['name' => $feature['slug'], 'value' => $fdata[$feature['slug']] ?? "", 'label' => $feature['name'],  'required' => 'true'];
        switch ($feature['type']) {
            case "Boolean";
                $f['type'] = "select";
                $f['data'] = $opts;
                break;
            case "List";
                $optsl = [];
                foreach (explode(',', $feature['options']) as $o) {
                    $optsl[] =
                        [
                            'text' => trim($o),
                            'value' => trim($o)
                        ];
                }
                $f['type'] = "select";
                $f['data'] = $optsl;
                break;
            case "Numeric";
                $f['type'] = "numeric";
                break;
        }
        $params['fields'][] = $f;
    }

    if (count($img)  == 0) {
        $params['fields'][9]['required'] = "true";
    }
    $form =  $latte->renderToString('../app/templates/form.latte', $params);
} else {
    $form = "<strong>Please select a category </strong>";

    echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Features $category", "body" => $form]);
?>
    <script>
        function callbackForm(result) {
            window.location.href = '/catalogs/products';
        }

        document.addEventListener("DOMContentLoaded", function() {


            $(document).ready(function() {




                $('#category_id').change(function() {
                    window.location.href = '<?php echo $id; ?>?category=' + $(this).val();
                });

                $("#tags").tagsinput({
                    trimValue: true,
                    allowDuplicates: false
                });

                console.log('prices');
                $("#prices").tagsinput({
                    trimValue: true,
                    allowDuplicates: false
                });


            });
        });
    </script>
<?php
    html::endContent();
    html::containerEnd();
    html::scripts(false);
    html::bodyEnd();

}





$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/products\'" value="Back">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/catalogs/products/' . $id . '\')" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Features ", "body" => $form]);
}
?>

<script>
    function callbackForm(result) {
        window.location.href = '/catalogs/products';
        // alert('ok');
    }

    document.addEventListener("DOMContentLoaded", function() {

        $(document).ready(function() {
            $('#category_id').change(function() {
                window.location.href = '<?php echo $id; ?>?category=' + $(this).val();
            });

            $("#tags").tagsinput({
                trimValue: true,
                allowDuplicates: false
            });
            console.log('prices');
            $("#prices").tagsinput({
                trimValue: true,
                allowDuplicates: false
            });
        });
    });
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();
