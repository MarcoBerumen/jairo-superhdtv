<?php
//echo $id;

    $latte = new Latte\Engine;

    // $html2pdf = new Html2Pdf();
    // $html2pdf->setTestTdInOnePage(false);
    ob_clean();
    $claim = Imx\db::dataQuery("select * from view_claims where claim_id ='$id'");
//    print_r($claim);
    $transaction = Imx\db::dataQuery("select * from transactions where transaction_type='Claim'  and reference_id ='$id'");
//    print_r($transaction);
    $saletr =  Imx\db::dataQuery("select transactions.* from transactions 
left join sales on sales.sale_id = transactions.reference_id
where transaction_type ='sale'
and item_id = '{$claim['item_id']}' 
and sales.customer_id = '{$claim['customer_id']}' ");
//    print_r($saletr);
    $id = $saletr['reference_id'];
    $data = Imx\db::dataQuery("select * from view_sales where sale_id ='$id'");
    $signature = Imx\s3::getAsLink(Imx\db::rquery("select signature from sales where sale_id ='$id'"));
    $salestatus = Imx\db::rquery("select status from sales where sale_id ='$id'");

    $customer = Imx\db::dataQuery("select * from customers where customer_id ='{$data['customer_id']}'");
    $store = Imx\db::dataQuery("select * from stores where store_id ='{$data['store_id']}'");
    $store['policy'] = nl2br($store['policy']);

        $items = Imx\db::dataQueryMultiple("select 
    products.name,
    products.model,
    items.price,
    items.warranty_price,
    1 as quantity,
    items.price as amount,
    case when products.stock_type = 2 then
    items.serials
    else
    ''
    end
    as serial,
    status.name as status,
    grades.name as grade,
    warranties.name as warranty,
    warranties.warranty_days
     from transactions items
    left join products on products.product_id = items.product_id
    left join status on status.status_id = items.status_id
    left join grades on grades.grade_id = items.grade_id
    left join warranties on warranties.warranty_id  = items.warranty_id 
    where items.reference_id = '{$claim['claim_id']}'
    and items.transaction_type='Claim'
    ");
//        $data['observation'].=" <br><h1 style='color:red;'>Claim</h1>";


    // print_r($items);


    $params = [
        "customer" => $data['customer'],
        "customer_id" => $data['customer_id'],
        "store" => $store,
        "signature" => $signature,
        "total" => $claim['credit'],
        "subtotal" => $data['subtotal'],
        "tax" => $data['tax'] ?? 0,
        "observation" => $claim['observations'],
        "payment_method" => $data['payment_method'],
        "address" => $customer['address'],
        "phone" => $customer['phone_number'],
        "email" => $customer['email'],
        "date" => date('m/d/Y H:i:s', strtotime($data['date'])),
        "contact" => $data['contact'],
        "id" => $id,
        "items" => $items,
        "banner" => $store['banner']
    ];

    $receipt = $latte->renderToString("../app/src/hdtv/templates/claim.latte", $params);
    echo $receipt;
    // $html2pdf->writeHTML($receipt);
    // $html2pdf->Output();
