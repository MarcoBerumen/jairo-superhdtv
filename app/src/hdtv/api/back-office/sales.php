<?php
dispatch('/api/sales/:id/payments', function ($id) {

    $tax = Imx\db::rquery("select tax from stores where 
    store_id = (select store_id from sales where sales.sale_id ='$id')");
    $subtotal = Imx\db::rquery("select subtotal from sales where sales.sale_id ='$id'");
    $payments = Imx\db::dataQueryMultiple("select * from sales_payments where sale_id ='$id'");
    $response = [
        "subtotal" => $subtotal,
        "tax" => $tax,
        "payments" => $payments
    ];
    Imx\headers::json();
    return json_encode($response);
});
dispatch_post('/api/sales/:id/payments', function ($id) {
    $payments = $_POST[1]['data'];

    Imx\db::iquery("delete from sales_payments where sale_id = '$id'");
    foreach ($payments as $payment => $value) {
        if (substr($payment, 0, 3) == "pm_") {
            $pid = preg_replace('~\D~', '', $payment);
            $tax = $payments['tax_' . $pid]['value'];
            $amount = $value['value'] ?? 0;
            if ($amount == 0) continue;
            $q = "insert into sales_payments 
            (sale_id, payment_method_id,amount,tax)
            values 
            ('{$id}', '{$pid}','{$amount}','{$tax}')";
            Imx\db::iquery($q);
        }
    }
    Imx\db::iquery("update sales set subtotal ='{$payments['subtotal']['value']}',tax='{$payments['taxes']['value']}',total='{$payments['total']['value']}' where sale_id = '$id'");
    return json_encode(['status' => "ok"]);
});
