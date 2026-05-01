<?php
dispatch_post('/api/reports/expenses', function () {

    $latte = new Latte\Engine;

    $store = $_POST[0]['data']['store']['value'];
    $start_date = Imx\utils::date2sql($_POST[0]['data']['start_date']['value']);
    $end_date = Imx\utils::date2sql($_POST[0]['data']['end_date']['value']);
    $type = $_POST[0]['data']['type']['value'];
    $wtype = '';

    if ($type)
        $wtype = " and expense_type_id = '{$type}'";


    $query = "
    select 
    *
    from view_expenses
WHERE
	store_id = {$store}
    and date(date) between '$start_date' and '$end_date'
    $wtype
";

    $expenses = Imx\db::dataQueryMultiple($query);
    array_walk($expenses, function(&$expense){
        $expenses['date'] = Imx\utils::sql2date($expense['date']);
    });
    $data = [
        'columnsData' => [
            [



                [
                    "key" => "store",
                    "label" => "Store",
                    "proportion" => 5,
                    "type" => "date"
                ],
                [
                    "key" => "subtotal",
                    "label" => "Subtotal",
                    "proportion" => 1,
                    'totalKey'=>true,
                    "align"=> "right",
                    "type" => "integer"
                ],
                [
                    "key" => "tax",
                    "label" => "TAX",
                    "proportion" => 1,
                    'totalKey'=>true,
                    "align"=> "right",
                    "type" => "integer"
                ],
                [
                    "key" => "total",
                    "label" => "Total",
                    "proportion" => 1,
                    'totalKey'=>true,
                    "align"=> "right",
                    "type" => "integer"
                ],
            ],

            [



                [
                    "key" => "expense_type",
                    "label" => "Type",
                    "proportion" => 5,
                    "type" => "date"
                ],
                [
                    "key" => "subtotal",
                    "label" => "Subtotal",
                    "proportion" => 1,
                    'totalKey'=>true,
                    "align"=> "right",
                    "type" => "integer"
                ],
                [
                    "key" => "tax",
                    "label" => "TAX",
                    "proportion" => 1,
                    'totalKey'=>true,
                    "align"=> "right",
                    "type" => "integer"
                ],
                [
                    "key" => "total",
                    "label" => "Total",
                    "proportion" => 1,
                    'totalKey'=>true,
                    "align"=> "right",
                    "type" => "integer"
                ],
            ],
                [
                    [
                        'key' => 'expense_id',
                        'label' => 'ID',
                        "proportion" => 2,
                        "type" => "date"
                    ],
                    [
                        'key' => 'date',
                        'label' => 'Date',
                        "proportion" => 2,
                        "type" => "date"
                    ],
                [
                    "key" => "bank_account",
                    "label" => "Bank Account",
                    "proportion" => 4,
                    "type" => "string"
                ],
                [
                    "key" => "observaciones",
                    "label" => "Observaciones",
                    "proportion" => 4,
                    "type" => "string"
                ],
                    [
                        "key" => "subtotal",
                        "label" => "Subtotal",
                        "proportion" => 2,
                        "align"=> "right",
                        "type" => "integer"
                    ],
                    [
                        "key" => "tax",
                        "label" => "TAX",
                        "proportion" => 2,
                        "align"=> "right",
                        "type" => "integer"
                    ],
                    [
                        "key" => "total",
                        "label" => "Total",
                        "proportion" => 2,
                        "align"=> "right",
                        "type" => "integer"
                    ],
            ]
        ],
        'data' => $expenses,
        'groupByData' => ["store","type_id"]

    ];

    $data['latte'] = $latte;
    echo $latte->renderToString('../app/templates/report.latte', $data);


});
