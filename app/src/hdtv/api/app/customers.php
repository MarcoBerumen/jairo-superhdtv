<?php

/**
 * Customers Listing pagination
 */
dispatch('/api/app/customers', function () {
    // * Check for active Token
    // error_log(implode("", $_GET ?? []), 0);
    $token = hdtv::checkToken();

    // * Filtering customers with status 1 (active) 
    $search = $_GET['search'];
    error_log("Search parameter : " . print_r($_GET, 1), 0);
    $table = "
    (select * from customers where row_status = 1
    limit 500
    ) as 
    customers";
    if ($search) {
        $table = "
        (select * from customers where row_status = 1
        and name like '%$search%'
        limit 500
        ) as 
        customers";
    }
    error_log("\n Table customer ** ".$table,0);
    // Table's primary key
    $primaryKey = 'customer_id';
    $columns = array(
        array('db' => 'customer_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        array('db' => 'email', 'dt' => 2),
        array('db' => 'phone_number', 'dt' => 3),
        array('db' => 'address', 'dt' => 4),
        array('db' => 'member_since', 'dt' => 5),
        array('db' => 'credit', 'dt' => 6),
    );


    Imx\headers::json();

    return Imx\utils::safe_json_encode(Imx\jsonapi::simple($_GET, $table, $primaryKey, $columns));
});
/**
 * Endpoint for customer data
 */
dispatch('/api/app/customers/:id', function ($id) {
    // * Check for active Token
    $token = hdtv::checkToken();
    // update credit before preview
    hdtv::updateCredit($id);

    $customer = Imx\db::dataQuery("select * from customers where customer_id ='$id'");
    $customer['credit_payment_method_id'] = 1;
//    $customer['credit'] = 500;

    Imx\headers::json();

    return Imx\utils::safe_json_encode($customer);
});
dispatch_post('/api/app/customers', function () {
    // * Check for active Token
    hdtv::checkToken();
    $data =  file_get_contents('php://input');
    $data =  json_decode($data, true);
    $data['member_since'] = date('Y-m-d');
    $data['status'] = '1';
    unset($data['credit_payment_method_id']);

    $table = "customers";
    $id = Imx\db::jsonPost($data, $table, "name");
    if (is_numeric($id)) {
        return json_encode(['customer_id' => $id]);
    }
    Imx\headers::conflict();
    return json_encode(['error' => $id]);
});
dispatch_put('/api/app/customers/:id', function ($id) {
    // * Check for active Token
    // Update customer credit on edit
    hdtv::updateCredit($id);
    hdtv::checkToken();
    $data =  file_get_contents('php://input');
    $data =  json_decode($data, true);
    unset($data['credit_payment_method_id']);
    Imx\headers::json();
    $response = Imx\db::jsonPut($data, "customers", $id, "name", "customer_id");
    if (is_numeric($response)) {
        return json_encode(['customer_id' => $id]);
    }
    Imx\headers::conflict();
    return json_encode(['error' => $response]);
});
