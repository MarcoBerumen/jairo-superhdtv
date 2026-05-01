<?php

dispatch_put("/api/app/sales/:id/signature", function ($id) {

    $data =  json_decode(file_get_contents('php://input'), true);

        $signature = $data['signature'] ?? "";
        if ($signature == "") {
            Imx\headers::conflict();
            error_log("Invalid signature", 0);
            return json_encode(['error' => 'Invalid Signature']);
        }
    if (strpos($signature, "data:image/png;base64,") === false) {
        $signature =  Imx\s3::storeString("Signature-$id.webp", "sales/$id/signature", base64_decode(str_replace("data:image/webp;base64,", "", $signature)))['key'];
    } else {
        $signature =  Imx\s3::storeString("Signature-$id.png", "sales/$id/signature", base64_decode(str_replace("data:image/png;base64,", "", $signature)))['key'];
    }
        Imx\db::iquery("update sales set signature ='$signature' where sale_id = '{$id}'");
    return $id;
});
dispatch_post("/api/app/sales", function () {
    error_log("Begin sale", 0);
    $token = hdtv::checkToken(); // ? User validation
    $user = Imx\db::dataQuery("select 
    user_id,
    stores.store_id,
    users.name,
    stores.name as store,
    shift_id
    from users 
    left join stores on stores.store_id = users.store_id
    where token ='$token'");
    $shift_id = $user['shift_id'] ?? "";
    $user_id = $user['user_id'];
    //! validation of open shift
    if ($shift_id == "") {
        Imx\headers::forbidden();
        return json_encode([
            "response" => "Shift not open"
        ]);
    }
//     error_log(file_get_contents('php://input'), 0);
    // exit;
    $data =  json_decode(file_get_contents('php://input'), true);
    if (json_last_error_msg() == "No error") {
        // manually validation of json 
        $store = $data['store'] ?? "";
        $payment_method = $data['payment_method'] ?? "";
        $payment_reference = $data['payment_reference'] ?? "";
        $observation = $data['observation'] ?? "";
        $customer = $data['customer'] ?? "";
        $signature = "";
//        $signature = $data['signature'] ?? "";
//        if ($signature == "") {
//            Imx\headers::conflict();
//            error_log("Invalid signature", 0);
//            return json_encode(['error' => 'Invalid Signature']);
//        }
        Hdtv::recordLog("sales", "New sale");
        // error_log($signature);


        $tax = Imx\db::rquery("select tax from stores where store_id ='$store'") ?? 0;
        $price_list = Imx\db::rquery("select price_list_id from stores where store_id ='$store'") ?? 0;
        $total =  0;
        $disccount =  0;
        $taxes =  0;
        $subtotal =  0;
        $items = $data['items'] ?? [];

        if ($store == "") {
            Imx\headers::conflict();
            return json_encode(['error' => 'Invalid Store']);
        }
        if ($payment_method == []) {
            Imx\headers::conflict();
            return json_encode(['error' => 'Invalid payment method']);
        }
        if ($customer == "") {
            Imx\headers::conflict();
            return json_encode(['error' => 'Invalid customer']);
        }

        if (!is_array($items)) {
            $items = [];
        }
        if (count($items) <= 0) {
            Imx\headers::conflict();
            return json_encode(['error' => 'There is no items']);
        }
        // verify that the product isnt sold
        foreach ($items as $item) {
            if (isset($item['product_id'])) {
                $subtotal += $item['price'] * $item['quantity'];
                $total += ($item['price'] * $item['quantity']) * (1 + ($tax / 100));
                $taxes += ($item['price'] * $item['quantity']) *  ($tax / 100);
                // $disccount += $it[''] $item['price'] * $item['quantity']) *  ($tax / 100);
            } else {
                $it = Imx\db::dataQuery("select * from items where item_id ='{$item['item_id']}'");
                $subtotal += $item['price'] * $item['quantity'];
                $total += $item['price']  * (1 + ($tax / 100));
                $taxes += $item['price']  *  ($tax / 100);
                // $disccount += $it[''] $item['price'] * $item['quantity']) *  ($tax / 100);

                if ($it['status'] != 1) {
                    Imx\headers::conflict();
                    return json_encode(['error' => "The item {$it['serial_number']} its already sold, please pick another item "]);
                }
            }
        }
        if ($total <= 0) {
            Imx\headers::conflict();
            return json_encode(['error' => 'Invalid total']);
        }
        $date = date('Y-m-d H:i:s');
        // begin query insert 
        $query = "insert into 
        sales 
        (store_id,
        signature,
        shift_id,
        customer_id,
        date,
        subtotal,
        disccounts,
        tax,
        total,
        payment_reference,
        observation,
        user_id,
        comission,
        status)

        values 
        (
        '$store',
        '',
        '$shift_id',
        '$customer',
        '$date',
        '$subtotal',
        '0',
        '$taxes',
        '$total',
        '$payment_reference',
        '$observation',
        '$user_id',
        '0',
        '1')
        ";
        error_log($query, 0);

        $sale = Imx\db::iquery($query);
        if (strpos($signature, "data:image/png;base64,") === false) {
            $signature =  Imx\s3::storeString("Signature-$sale.webp", "sales/$sale/signature", base64_decode(str_replace("data:image/webp;base64,", "", $signature)))['key'];
        } else {
            $signature =  Imx\s3::storeString("Signature-$sale.png", "sales/$sale/signature", base64_decode(str_replace("data:image/png;base64,", "", $signature)))['key'];
        }
        // error_log($signature, 0);
        Imx\db::iquery("update sales set signature ='$signature' where sale_id ='$sale'");
        // obtain payment methods
        $payments = $data['payment_method'];
        foreach ($payments as $payment) {
            $taxespm = Imx\db::rquery("select taxable from payment_methods where payment_method_id ='{$payment['payment_method_id']}'") *    ($tax / 100) * $payment['amount'];
            $q = "insert ignore into sales_payments 
            (sale_id, payment_method_id,amount,tax)
            values 
            ('{$sale}', '{$payment['payment_method_id']}','{$payment['amount']}','{$taxespm}')";
            Imx\db::iquery($q);
        }
        //! CREDITS
        hdtv::updateCredit($customer);

        // now we update products  sale_id
        foreach ($items as $item) {
            // check of warranty expiry      
            $warranty = Imx\db::dataQuery("select warranty_days,price,date_add(date(now()), interval warranty_days day ) as date  from warranties where  warranty_id = '{$item['warranty_id']}' ");
            if (isset($item['product_id'])) {
                // get the current price from pricelist
                $price = hdtv::getMaxPrice($price_list, $item['
                product_id'], $item['status_id'], $item['grade_id']);
                $comission = hdtv::getComission($user_id, $item['product_id'], $item['price'] * $item['quantity'],$item['grade_id'],$item['status_id']) ?? 0;
                $tax = hdtv::tax($store, ($item['price'] + $item['warranty_price']) * $item['quantity']);
                $disccount = ($price - $item['price'])  * $item['quantity'];;
                $q = "update items set status=2,
                 sale_id = '{$sale}',
                price='{$item['price']}',
                warranty_id ='{$item['warranty_id']}',
                warranty_price ='{$item['warranty_price']}',
                sale_warranty_date ='{$warranty['date']}',
                sold_date =now(),
                disccount='{$disccount}',
                comission='{$comission}',
                tax='{$tax}'
                where 
                product_id ='{$item['product_id']}'
                and grade_id ='{$item['grade_id']}'
                and status ='{$item['status_id']}'
                and store_id ='{$store}'
                order by item_id limit {$item['quantity']}
                ";
                error_log($q, 0);

                // bbuild transaction
                $tr = "
        insert into transactions 
                (store_id,
                product_id,
                 date,
                transaction_type,
                status_id,
                grade_id,
                notes,
                reference_id,
                quantity,
                price,
                item_id,
                warranty_id,
                warranty_price,
                serials,
                sale_price,
                `status`,
                total,user_id,comission)
                            values 
                ('{$item['store_id']}',
                '{$item['product_id']}',
                 now(),
                'Sale',
                '{$item['status_id']}',
                '{$item['grade_id']}',
                '{$item['notes']}',
                '$sale',
                '1',
                '{$item['price']}',
                '{$item['item_id']}',
                '{$item['warranty_id']}',
                '{$item['warranty_price']}',
                '{$item['serial_number']}',
                '{$item['price']}',
                '1',
                '{$item['total']}','{$user_id}','{$comission}')
                        ";


                error_log($tr,0);
                Imx\db::iquery($q);
                Imx\db::iquery($tr);
            } else {

                $item['product_id'] = Imx\db::rquery("select product_id from items where item_id ='{$item['item_id']}'");
                $item['status_id'] = Imx\db::rquery("select status_id from items where item_id ='{$item['item_id']}'");
                $item['grade_id'] = Imx\db::rquery("select grade_id from items where item_id ='{$item['item_id']}'");
                // get the current price from pricelist
                $price = hdtv::getMaxPrice($price_list, $item['product_id'], $item['status_id'], $item['grade_id']);
                $disccount = $price - $item['price'];
                $comission = hdtv::getComission($user_id, $item['product_id'], $item['price']) ?? 0;
                $tax = hdtv::tax($store, $item['price'] + $item['warranty_price']);
                $disccount = ($price - $item['price'])  * $item['quantity'];



                Imx\db::iquery("update items set status=2,
                 sale_id = '{$sale}',price='{$item['price']}',
                 warranty_id ='{$item['warranty_id']}',
                 warranty_price ='{$item['warranty_price']}',
                 sale_warranty_date ='{$warranty['date']}',
                 sold_date =now(),
                 disccount='{$disccount}',
                 comission='{$comission}',
                 tax='{$tax}'
  
                where item_id ='{$item['item_id']}'");

                // bbuild transaction
                $tr = "
        insert into transactions 
                (store_id,
                product_id,
                 date,
                transaction_type,
                status_id,
                grade_id,
                notes,
                reference_id,
                quantity,
                price,
                item_id,
                warranty_id,
                warranty_price,
                serials,
                sale_price,
                `status`,
                total,user_id)
                            values 
                ('{$store}',
                '{$item['product_id']}',
                 now(),
                'Sale',
                '{$item['status_id']}',
                '{$item['grade_id']}',
                '{$item['notes']}',
                '$sale',
                '1',
                '{$item['price']}',
                '{$item['item_id']}',
                '{$item['warranty_id']}',
                '{$item['warranty_price']}',
                '{$item['serial_number']}',
                '{$item['price']}',
                '1',
                '{$item['total']}','{$_SESSION['user']['user_id']}')
                        ";


                error_log($tr,0);
                Imx\db::iquery($q);
                Imx\db::iquery($tr);

            }
            hdtv::inventory(
                $item['product_id'],
                $store,
                $item['status_id'],
                $item['grade_id']
            );
        }
        Imx\headers::json();
        // udate sale totals :
        $qs = "
        update sales set 
        subtotal = (select sum(price+warranty_price) from items  where items.sale_id = sales.sale_id),
        warranties = (select sum(warranty_price) from items  where items.sale_id = sales.sale_id),
        tax = (select sum(tax) from sales_payments  where sales_payments.sale_id = sales.sale_id),
        total = (select sum(tax+amount) from sales_payments  where sales_payments.sale_id = sales.sale_id),
        disccounts = (select sum(disccount) from items  where items.sale_id = sales.sale_id and items.disccount > 0),
        comission = (select sum(items.comission) from items  where items.sale_id = sales.sale_id )
        where sale_id ='{$sale}'
        ";
        error_log($qs);
        Imx\db::iquery($qs);
        return json_encode(['sale_id' => $sale]);
    } else {
        Imx\headers::conflict();
        return json_encode(['error' => 'invalid json data']);
    }
});
/**
 * Get Sales endpoint
 * retreives sales of the open shift id
 */
dispatch("/api/app/sales", function () {
    $token = hdtv::checkToken(); // ? User validation
    $user = Imx\db::dataQuery("select 
    user_id,
    stores.store_id,
    shift_id
    from users 
    left join stores on stores.store_id = users.store_id
    where token ='$token'");

    $sales = Imx\db::dataQueryMultiple("select 
    sale_id,
    store,
    date,
    customer,
    payment_method,
    total,
    (select count(*) from items where items.sale_id = view_sales.sale_id) as items 
    from view_sales where shift_id ='{$user['shift_id']}'");

    Imx\headers::json();
    return json_encode($sales);
});
dispatch("/api/app/sales/:id", function ($id) {
    $token = hdtv::checkToken(); // ? User validation
    $user = Imx\db::dataQuery("select 
    user_id,
    stores.store_id,
    shift_id
    from users 
    left join stores on stores.store_id = users.store_id
    where token ='$token'");

    $sales = Imx\db::dataQuery("select 
    sale_id,
    store,
    date,
    customer,
    email,
    payment_method,
    total
    from view_sales where sale_id='$id'");

    Imx\headers::json();
    return json_encode($sales);
});
/*
//* request
{
    "store" : 1,
    "payment_method": 0,
    "payment_reference": 0,
    "observations": "",
    "customer" :1 ,
    "total" : 1,
    "items" :
    [
        {
            "product" : 1,
            "quantity" : "xxx",
            "price" : "xxx",
            "store" : "xxx",
            "status" : "xxx",
            "grade" : "xxx",
            "item_id": "xxx"        
        },
        {
            "product": 1,
            "quantity" : "xxx",
            "price" : "xxx",
            "store" : "xxx",
            "status" : "xxx",
            "grade" : "xxx",
            "item_id": "xxx"        
        }
    ]
}
//*responses
http 403 conflict
{
    response : "Shift not open"
    
}
http 409 forbidden
{
    response : "Serials   already sold",
    serials : [xx,yy,zz]
    
}
http 200 
{
    response : "ok"
}

*/
