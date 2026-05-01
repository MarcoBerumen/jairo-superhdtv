<?php
dispatch_post('/api/reports/attendance', function () {

    $latte = new Latte\Engine;
    $store = $_POST[0]['data']['store']['value'];
    $start_date = Imx\utils::date2sql($_POST[0]['data']['start_date']['value']);
    $end_date = Imx\utils::date2sql($_POST[0]['data']['end_date']['value']);
    $user = $_POST[0]['data']['user']['value'];
    $type = $_POST[0]['data']['type']['value'];
    $wuser = '';
    $wtype = '';
    if ($user)
        $wuser = " and users.user_id = '{$user}'";
    if ($type)
        $wtype = " and type = '{$type}'";

    if ($type != "Worked Hours") {


        $query = "
    select 
    view_attendance.*,
           1 as record
    from view_attendance
WHERE
	store_id = {$store}
    and date(date) between '$start_date' and '$end_date'
    $wtype
    $wuser
";

        $attendance = Imx\db::dataQueryMultiple($query);

        $data = [
            'columnsData' => [
                [

                    [
                        "key" => "user",
                        "label" => "user",
                        "proportion" => 3,
                        "type" => "string"
                    ], [
                    "key" => "record",
                    "label" => "Total Records",
                    "proportion" => 1,
                    'totalKey' => true,
                    "align" => "right",
                    "type" => "integer"
                ],
                ], [

                    [
                        "key" => "type",
                        "label" => "Type",
                        "proportion" => 1,
                        "type" => "date"
                    ],
                    [
                        "key" => "record",
                        "label" => "Total Records",
                        "proportion" => 1,
                        'totalKey' => true,
                        "align" => "right",
                        "type" => "integer"
                    ],
                ],
                [
                    [
                        'key' => 'date',
                        'label' => 'Date',
                        "proportion" => 2,
                        "type" => "date"
                    ],

                    [
                        "key" => "coordinates",
                        "label" => "Coords",
                        "proportion" => 1,
                        "type" => "string"
                    ],
                    [
                        "key" => "ipaddress",
                        "label" => "IP",
                        "proportion" => 1,
                        "type" => "string"
                    ],

                ]
            ],
            'data' => $attendance,
            'groupByData' => ["user", "type"]

        ];

        $data['latte'] = $latte;
        echo $latte->renderToString('../app/templates/report.latte', $data);
    } else {
        // Calculate working hours


        $dates = Imx\utils::daysInRange($start_date,$end_date);
        $query = '';
        foreach($dates as $date){
            if($query != "")  $query.=" union ";
            $query.= "SELECT
`users`.`user_id` AS `user_id`,
`users`.`name` AS `name`,
case when shifts.shift_id is null then
    case when exceptions.exception is not null then
        'Exception'
            else
    'Absence'
    end 
        else
            'Attendance'
            end as att_status,
`users`.`store_id` AS `store_id`,
users.workdays,
date('{$date}') AS `date`,
dayname('{$date}') AS `weekday`,
time(`shifts`.`start_date`) AS `start_date`,
time(`shifts`.`end_date`) AS `end_date`,
`stores`.`name` AS `store`,
`shifts`.`status` AS `status`,
timediff( end_date , `shifts`.`start_date` ) AS `time`,
TIME_TO_SEC(timediff( end_date , `shifts`.`start_date` ))/3600 AS `hours`,
exceptions.exception
FROM
users
	left join shifts on shifts.user_id =  users.user_id and date(shifts.start_date) ='{$date}'
	left join exceptions on exceptions.user_id =  users.user_id and date(date) ='{$date}'
	left join stores on stores.store_id = users.store_id
	where
		users.store_id = {$store}
    $wuser
	";
        }
//    echo nl2br($query);
        $attendance = Imx\db::dataQueryMultiple($query);
        array_walk($attendance,function(&$at){
            $at['date'] = Imx\utils::sql2date($at['date']);
            if($at['att_status']=='Absence' && $at['workdays'] !='')
            {
                if(!in_array($at['weekday'],json_decode($at['workdays'],false)))
                $at['att_status'] ='Rest day';
            }
//            $at['att_status']
        });
        $data = [
            'columnsData' => [
                [

                    [
                        "key" => "store",
                        "label" => "Store",
                        "proportion" => 3,
                        "type" => "string"
                    ], [
                    "key" => "hours",
                    "label" => "Hours",
                    "proportion" => 1,
                    'totalKey' => true,
                    "align" => "right",
                    "type" => "integer"
                ],
                ],
                [

                    [
                        "key" => "name",
                        "label" => "Employee",
                        "proportion" => 3,
                        "type" => "string"
                    ], [
                    "key" => "hours",
                    "label" => "Hours",
                    "proportion" => 1,
                    'totalKey' => true,
                    "align" => "right",
                    "type" => "integer"
                ],
                ],

                [
                    [
                        'key' => 'date',
                        'label' => 'Date',
                        "proportion" => 1,
                        "type" => "date"
                    ],
                    [
                        'key' => 'weekday',
                        'label' => 'Week day',
                        "proportion" => 1,
                        "type" => "string"
                    ],
                    [
                        'key' => 'att_status',
                        'label' => 'Status',
                        "proportion" => 1,
                    ],
                    [
                        'key' => 'exception',
                        'label' => 'Exception',
                        "proportion" => 1,
                    ],


                    [
                        "key" => "start_date",
                        "label" => "Open Shift",
                        "proportion" => 1,
                        "type" => "string"
                    ],

                    [
                        "key" => "end_date",
                        "label" => "Close Shift",
                        "proportion" => 1,
                        "type" => "string"
                    ],

                    [
                        "key" => "time",
                        "label" => "Time",
                        "proportion" => 1,
                        "type" => "string",
                        "align" =>"right"
                    ],

                ]
            ],
            'data' => $attendance,
            'groupByData' => ["store", "name"]

        ];

        $data['latte'] = $latte;
        return $latte->renderToString('../app/templates/report.latte', $data);
    }

});
