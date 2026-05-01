<?php
dispatch_post('/api/reports/customers', function () {

    $latte = new Latte\Engine;

    $credit = $_POST[0]['data']['credit']['value'];
    $start_date = Imx\utils::date2sql($_POST[0]['data']['start_date']['value']);
    $end_date = Imx\utils::date2sql($_POST[0]['data']['end_date']['value']);

    $wuser = '';
    $wcredit = '';

    if ($credit)
    {
        if($wcredit == 0){

            $wcredit = " and credit is null or credit = 0";
        }
        else
        {
            $wcredit = " and credit is not  null and credit > 0";

        }
    }
    $skipdates = $_POST[0]['data']['skipdates']['value'];

    $wd = "";
    $limit = "";
    if($skipdates){
        $limit = " limit 100 ";
    }
    else{
        $wd = " and date(member_since) between '$start_date' and '$end_date'";

    }

    $query = "
    select 
    customers.*,
    1 as c,
           1 as record
    from customers
WHERE
customers.customer_id > 0 
{$wd}
  {$wcredit}
{$limit}
";
//echo $query;

    $attendance = Imx\db::dataQueryMultiple($query);
    $data = [
        'columnsData' => [
            [

                [
                    "key" => "record",
                    "label" => "Total Records",
                    "proportion" => 1,
                    'totalKey'=>true,
                    "align"=> "right",
                    "type" => "integer"
                ],
                [
                    "key" => "credit",
                    "label" => "Credit",
                    "proportion" => 1,
                    'totalKey'=>true,
                    "type" => "number",
                    "align"=>"right"

                ],
            ],
            [
                [
                    'key' => 'name',
                    'label' => 'name',
                    "proportion" => 2,
                ],
                [
                    'key' => 'email',
                    'label' => 'Email',
                    "proportion" => 2,
                ],
                [
                    'key' => 'phone_number',
                    'label' => 'name',
                    "proportion" => 2,

                ],

                [
                    'key' => 'member_since',
                    'label' => 'Member Since',
                    "proportion" => 2,
                    "type" => "date"
                ],

                [
                    "key" => "credit",
                    "label" => "Credit",
                    "proportion" => 1,
                    "type" => "number",
                    "align"=>"right"
                ],



            ]
        ],
        'data' => $attendance,
        'groupByData' => ["c"]

    ];

    $data['latte'] = $latte;
    echo $latte->renderToString('../app/templates/report.latte', $data);


});
