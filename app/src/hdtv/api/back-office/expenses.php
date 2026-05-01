<?php
// * expenses *
dispatch('/api/back-office/expenses', function () {
    $table = 'view_expenses';
    // Table's primary key
    $primaryKey = 'expense_id';
    $columns = array(
        array('db' => 'expense_id', 'dt' => 0),
        array('db' => 'date', 'dt' => 1),
        array('db' => 'store', 'dt' => 2),
        array('db' => 'bank_account', 'dt' => 3),
        array('db' => 'user', 'dt' => 4),
        array('db' => 'total', 'dt' => 5),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});



dispatch_post('/api/back-office/expenses/:id', function ($id) {
    $_POST[0]['data']['user_id']['value'] = $_SESSION['user']['user_id'];
    $_POST[0]['data']['date']['value']= Imx\utils::date2sql($_POST[0]['data']['date']['value']);

    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "expenses", "");
        $response = [];
        if (is_numeric($id)) {
            $response['status'] = "ok";
        } else {
            $response['status'] = "error";
            $response['text'] = $id;
        }
        return json_encode($response);
    } else {
        $data = $_POST[0]['data'];
        $id = Imx\db::e_post($data, "expenses", $id, "", 'expense_id');
        $response = [];
        if (is_numeric($id)) {
            $response['status'] = "ok";
        } else {
            $response['status'] = "error";
            $response['text'] = $id;
        }
        return json_encode($response);
    }
});
