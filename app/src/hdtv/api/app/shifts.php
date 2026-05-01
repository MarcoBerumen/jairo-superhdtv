<?php

dispatch('/api/app/shift', function () {
    $token = hdtv::checkToken();
    Imx\headers::json();
    $user = Imx\db::dataQuery("select 
    user_id,
    stores.store_id,
    shift_id,
    users.name,
    stores.name as store
    from users 
    left join stores on stores.store_id = users.store_id
    where token ='$token'");

    $shift_id = $user['shift_id'];

    if ($shift_id  ==  "") {
        return json_encode([
            "shift_id" => null
        ]);
    }
    $date  = Imx\db::rquery("select start_date from shifts where shift_id ='{$shift_id}'");

    $sales = Imx\db::dataQueryMultiple("select 
    category,
    sum(price) as amount,
    count(*) as quantities 
     from view_shift_detail
    where shift_id = '{$shift_id}' ");

    $payments =  Imx\db::dataQueryMultiple("select 
    payment_method as type,
    sum(price) as amount
         from view_shift_detail
    where shift_id = '{$shift_id}' ");

    return json_encode([
        "shift_id" => $shift_id,
        "user_name" => $user['name'],
        "store" => $user['store'],
        "comissions" => Imx\db::rquery("select sum(comission) from items where sale_id in (select sales.sale_id from sales where sales.status = 1 and  sales.shift_id ='{$shift_id}')"),
        "start_date" => $date, "sales" => $sales, "payment_methods" => $payments
    ], JSON_PRETTY_PRINT);
});

dispatch('/api/app/shift/open', function () {
    $token = hdtv::checkToken();
    Imx\headers::json();
    $user = Imx\db::dataQuery("select 
    user_id,
    stores.store_id,
    shift_id,
    users.name,
    stores.name as store
    from users 
    left join stores on stores.store_id = users.store_id
    where token ='$token'");
    //  * Store attendance record



    $shift_id = $user['shift_id'];

    if ($shift_id  ==  "") {
        // creat the shift
        $location = getallheaders()['app-location'] ?? '';
        hdtv::attendance($user['user_id'], $user['store_id'], 'Open Shift', $location, 2);
        $date = date('Y-m-d H:i:s');
        $shift_id = Imx\db::iquery("insert into shifts 
        (store_id,user_id,start_date,status)
        values 
        ('{$user['store_id']}','{$user['user_id']}','{$date}','1') ");

        Imx\db::iquery("update users set shift_id ='$shift_id' where user_id = '{$user['user_id']}'");
    }

    return json_encode([
        'shift_id' => $shift_id
    ], JSON_PRETTY_PRINT);
});

dispatch('/api/app/shift/close', function () {
    $token = hdtv::checkToken();
    error_log("Closing shift token {$token}");
    Imx\headers::json();
    $date = date('Y-m-d H:i:s');
    $user = Imx\db::dataQuery("select 
    shift_id,
    store_id,
    user_id
    from users where token ='$token'");
    $location = getallheaders()['app-location'] ?? '';
    hdtv::attendance($user['user_id'], $user['store_id'], 'Close Shift', $location, 2);
    Imx\db::iquery("update users set shift_id =null where user_id = '{$user['user_id']}'");
    Imx\db::iquery("update shifts set status =0, end_date = '{$date}' where shift_id = '{$user['shift_id']}'");

    return json_encode(['shift_id' => $user['shift_id']], JSON_PRETTY_PRINT);
});
