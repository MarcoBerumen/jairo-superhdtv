<?php
// * ENDPOINDS PARA SELECT2


dispatch('/api/sel2/users', function () {

    $table = "users";
    if(isset($_GET['store']))
    {
        $table = "(select * from users    where store_id='{$_GET['store']}' and row_status = 1) as u";
    }
    else{
        $table = "(select * from users    where row_status = 1) as u";
    }

    Imx\select2::get($table, $_GET['page'] ?? 1, "user_id", "name", $_GET['term'] ?? "");
});


dispatch('/api/sel2/profiles', function () {
    Imx\select2::get("profiles", $_GET['page'] ?? 1, "profile_id", "name", $_GET['term'] ?? "");
});

dispatch('/api/sel2/brands', function () {
    $table = "(select * from brands where row_status = 1) as brand ";
    Imx\select2::get($table, $_GET['page'] ?? 1, "brand_id", "name", $_GET['term'] ?? "");
});
dispatch('/api/sel2/warranties', function () {

    Imx\select2::get("warranties", $_GET['page'] ?? 1, "warranty_id", "name", $_GET['term'] ?? "");
});
dispatch('/api/sel2/categories', function () {

    Imx\select2::get("categories", $_GET['page'] ?? 1, "category_id", "name", $_GET['term'] ?? "");
});
dispatch('/api/sel2/bank-accounts', function () {
        $table = "(select bank_account_id,concat(name,' ',number) as account from bank_accounts where row_status = 1 ) as b";
    Imx\select2::get($table, $_GET['page'] ?? 1, "bank_account_id", "account", $_GET['term'] ?? "");
});

dispatch('/api/sel2/providers', function () {
    $table = "(select * from providers where row_status =1) as provider ";
    Imx\select2::get($table, $_GET['page'] ?? 1, "provider_id", "name", $_GET['term'] ?? "");
});
dispatch('/api/sel2/stores', function () {
    $filter = hdtv::storeFilter();
    $table = "( select * from stores where row_status = 1 and store_id in ($filter)) as f ";

    Imx\select2::get($table, $_GET['page'] ?? 1, "store_id", "name", $_GET['term'] ?? "",["tax"]);
});
dispatch('/api/sel2/products-sku', function () {
    Imx\select2::get("products", $_GET['page'] ?? 1, "product_id", "concat(sku,model) as sku", $_GET['term'] ?? "");
});

dispatch('/api/sel2/products-models', function () {
    Imx\select2::get("products", $_GET['page'] ?? 1, "product_id", "name", $_GET['term'] ?? "");
});
dispatch('/api/sel2/products', function () {
    Imx\select2::get("products", $_GET['page'] ?? 1, "product_id", "name", $_GET['term'] ?? "");
});
dispatch('/api/sel2/status', function () {
    Imx\select2::get("status", $_GET['page'] ?? 1, "status_id", "name", $_GET['term'] ?? "");
});
dispatch('/api/sel2/grade', function () {
    Imx\select2::get("grades", $_GET['page'] ?? 1, "grade_id", "name", $_GET['term'] ?? "");
});
dispatch('/api/sel2/price-lists', function () {
    Imx\select2::get("price_lists", $_GET['page'] ?? 1, "price_list_id", "name", $_GET['term'] ?? "");
});
dispatch('/api/sel2/motives', function () {
    Imx\select2::get("motives", $_GET['page'] ?? 1, "motive_id", "name", $_GET['term'] ?? "");
});dispatch('/api/sel2/expenses-types', function () {
    Imx\select2::get("expenses_types", $_GET['page'] ?? 1, "expense_type_id", "name", $_GET['term'] ?? "");
});
dispatch('/api/sel2/customers', function () {
    $table = "
    (select customer_id, concat(name,' ',phone_number) as name from customers where row_status =1 ) as t
    ";
    Imx\select2::get($table, $_GET['page'] ?? 1, "customer_id", "name", $_GET['term'] ?? "");
});
dispatch('/api/sel2/features', function () {
    $table = "features";
    if(isset($_GET['category']))
    {
        $table = "(select * from features    where FIND_IN_SET('{$_GET['category']}',categories)) as ca";
    }
    Imx\select2::get($table, $_GET['page'] ?? 1, "feature_id", "name", $_GET['term'] ?? "");
});
dispatch('/api/sel2/feature/:id', function ($id) {
    $feature = Imx\db::dataQuery("select * from features where feature_id ='$id'");
//    print_r($feature);
//    switch($feature['type']){
//        case "Numeric":
    if($feature['type']!='Boolean'){


                $q = "(select
distinct(
replace(JSON_EXTRACT(features, '$.{$feature['slug']}'),'\"','')
)  as options 
 from products 
 where JSON_EXTRACT(features, '$.{$feature['slug']}')  is not null ) as t ";
            Imx\select2::get($q, $_GET['page'] ?? 1, "options", "options", $_GET['term'] ?? "");
    }
    else
    {
        return '{"results":[{"id":"1","text":"True"},{"id":"0","text":"False"}],"pagination":{"more":false}}';
    }
//            break;
//
//    }
//    if(isset($_GET['category']))
//    {
//        $table = "(select * from features    where FIND_IN_SET('{$_GET['category']}',categories)) as ca";
//    }
//    Imx\select2::get($table, $_GET['page'] ?? 1, "feature_id", "name", $_GET['term'] ?? "");
});
dispatch('/api/sel2/items/claims', function () {
    $customer = $_GET['customer'] ?? 0;
    $store = $_GET['store'] ?? 0;
    $table = "
    (SELECT
	
	item_id,
	concat(
	serial_number,' ',
	product
	) as item ,
	price as total
	
FROM
	view_items 
WHERE
	 status_code = 2
     and sale_id is not null
     and customer_id = '{$customer}'
     and store_id = '{$store}'
     and item_id not in(select item_id from claims where claims.customer_id = view_items.customer_id  and item_id  = view_items.item_id) 
    ) as claims
    ";
//    echo $table;
    Imx\select2::get($table, $_GET['page'] ?? 1, "item_id", "item", $_GET['term'] ?? "",['total']);
});
dispatch('/api/sel2/items', function () {

    $store = $_GET['store'] ?? 0;
    $customer = $_GET['customer'] ?? 0;
    $wcustomer = "";
    if($customer  != "null" && $customer){
        $wcustomer = " and customer_id ='$customer'";
    }
        $table = "
    (SELECT
	
	item_id,
	concat(
	serial_number,' ',
	product
	) as item 
FROM
	view_items 
WHERE
	  store_id = '{$store}'
	$wcustomer
    ) as claims
    ";

    Imx\select2::get($table, $_GET['page'] ?? 1, "item_id", "item", $_GET['term'] ?? "");
});


dispatch('/api/sel2/serials', function () {
    $store = $_GET['store'] ?? 0;
    $status = $_GET['status'];
    $grade = $_GET['grade'];
    $product = $_GET['product'];
    $wstatus = '';
    $wproduct = '';
    $wgrade = '';
    if($status != 'null')  $wstatus = " and status_id ='$status'";
    if($grade != 'null')  $wgrade = " and grade_id ='$grade'";
    if($product != 'null')  $wproduct = " and product_id ='$product'";
        $table = "
    (SELECT
	item_id,
product,
product_id,
status,
status_id,
grade,
grade_id,
store_id,
serial_number
FROM
	view_items 
WHERE
	 status_code = 1
     and sale_id is null
     and store_id = '{$store}'
	$wproduct
	$wstatus
	$wgrade
    ) as serials
    ";


    Imx\select2::get($table, $_GET['page'] ?? 1, "item_id", "serial_number", $_GET['term'] ?? "",["status","status_id","grade","grade_id","product","product_id"]);
});
