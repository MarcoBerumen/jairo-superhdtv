<?php
// * expenses *
dispatch('/api/catalogs/expenses-types', function () {
    $table = 'expenses_types';
    // Table's primary key
    $primaryKey = 'expense_type_id';
    $columns = array(
        array('db' => 'expense_type_id', 'dt' => 0),
        array('db' => 'name', 'dt' => 1),
        array('db' => 'type', 'dt' => 2),
        // a
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});



dispatch_post('/api/catalogs/expenses-types/:id', function ($id) {

    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "expenses_types", "name");
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
        $id = Imx\db::e_post($data, "expenses_types", $id, "name", 'expense_type_id');
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
