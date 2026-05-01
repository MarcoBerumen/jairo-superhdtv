<?php
dispatch_post('/api/back-office/payroll/calculate', function () {
//    print_r($_POST);
    $start_date = Imx\utils::date2sql($_POST['start_date']);
    $end_date = Imx\utils::date2sql($_POST['end_date']);
    $store = $_POST['store'];
    $dates = Imx\utils::daysInRange($start_date,$end_date);
    $query = '';
    foreach($dates as $date){
        if($query != "")  $query.=" union ";
        $query.= "SELECT
`users`.`user_id` AS `user_id`,
users.wage,
`users`.`name` AS `name`,
coalesce((select sum(comission) from sales  where sales.user_id = users.user_id and date(sales.date) ='{$date}' and sales.status = 1),0) as comissions,
timediff( end_date , `shifts`.`start_date` ) AS `time`,
TIME_TO_SEC(timediff( end_date , `shifts`.`start_date` ))/3600 AS `hours`
FROM
users
	left join shifts on shifts.user_id =  users.user_id and date(shifts.start_date) ='{$date}'
	where
		users.store_id = {$store}
	";
    }
//    echo nl2br($query);\
    $query = "select 
    user_id,name,
    coalesce(sum(hours),0)  as hours,
    coalesce(wage,0) as wage,
    sum(comissions) as comissions  from ($query) as t group by user_id order by name";
//    echo $query;
    $payroll = Imx\db::dataQueryMultiple($query);
    array_walk($payroll ,function(&$pay){
        $pay['payroll'] = $pay['hours'] * $pay['wage'];
        $pay['amount'] = $pay['payroll'] + $pay['comissions'];
//        $pay['wage'] = $pay['wage']??0;
//        $pay['payroll'] = $pay['payroll']??0;
//
    });
     return json_encode($payroll);


});


// * payroll *
dispatch('/api/back-office/payroll', function () {
    $table = 'view_payroll';


    // Table's primary key
    $primaryKey = 'payroll_id';
    $columns = array(
        array('db' => 'payroll_id', 'dt' => 0),
        array(
            'db'        => 'payment_date',
            'dt'        => 1,
            'formatter' => function ($d, $row) {
                return date('m/d/Y', strtotime($d));
            }
        ),
        array('db' => 'store', 'dt' => 2),
        array(
            'db'        => 'start_date',
            'dt'        => 3,
            'formatter' => function ($d, $row) {
                return date('m/d/Y', strtotime($d));
            }
        ),
        array(
            'db'        => 'end_date',
            'dt'        => 4,
            'formatter' => function ($d, $row) {
                return date('m/d/Y', strtotime($d));
            }
        ),
        array('db' => 'total', 'dt' => 4),
        array('db' => 'status', 'dt' => 5),
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});


dispatch_post('/api/back-office/payroll/:id', function ($id) {
    // validate existing items
    $items = $_POST[1]['data'];
    $data = $_POST[0]['data'];
    $data['payment_date']['value'] = Imx\utils::date2sql($data['payment_date']['value']);
    $data['start_date']['value'] = Imx\utils::date2sql($data['start_date']['value']);
    $data['end_date']['value'] = Imx\utils::date2sql($data['end_date']['value']);
    if ($id == "new") {
        $id = Imx\db::i_post($data, "payroll");
    } else {
        // Edit invoice
        $orig_id = $id;
        $id = Imx\db::e_post($data, "payroll", $id, "", 'payroll_id');
        if (is_numeric($id)) {
            $id = $orig_id;
            // ! delete detail
            Imx\db::iquery("delete from payroll_detail where payroll_id ='$id'");
        }
    }
    $response = [];
    if (is_numeric($id)) {
        // CREAMOS LOS ITEMS 
        $items = $_POST[1]['data'];
        $user = $data['user_id']['value'];
        foreach ($items as $item) {
            // *  process each item group and store on transactions table 

            $date = $_POST[0]['data']['date']['value'];
            Imx\db::iquery("
            insert into payroll_detail
            (
            payroll_id,
            user_id,
            hours,
            wage,
            comissions,
             payroll,
             amount
            )
            values
            (
            '{$id}',
            '{$item['user_id']['value']}',
            '{$item['hours']['value']}',
            '{$item['wage']['value']}',
            '{$item['comissions']['value']}',
            '{$item['payroll']['value']}',
            '{$item['amount']['value']}'
            );
            ");
        }



        $response['status'] = "ok";
        return json_encode($response);
    } else {
        $response['status'] = "error";
        $response['text'] = $id;
    }
    return json_encode($response);
});
