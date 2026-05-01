<?php
// * invoices *
dispatch('/api/back-office/invoices', function () {
    $filter = hdtv::storeFilter();


    $table = "(select * from view_invoices where store_id in ($filter)) as vi";


    // Table's primary key
    $primaryKey = 'invoice_id';
    $columns = array(
        array('db' => 'invoice_id', 'dt' => 0),
        array(
            'db'        => 'date',
            'dt'        => 1,
            'formatter' => function ($d, $row) {
                return date('m/d/Y', strtotime($d));
            }
        ),
        array('db' => 'store', 'dt' => 2),
        array('db' => 'provider', 'dt' => 3),
        array('db' => 'document_number', 'dt' => 4),
        array('db' => 'total_price', 'dt' => 5),
    );
    return Imx\utils::safe_json_encode(Imx\datatable::simple($_GET, $table, $primaryKey, $columns));
});


dispatch_post('/api/back-office/invoices/:id', function ($id) {
    $_POST[0]['data']['date']['value']= Imx\utils::date2sql($_POST[0]['data']['date']['value']);

    // sold items serverside validation 
    if (Imx\db::rquery("select count(*) from items where invoice_id ='$id' and status > 1")) {
        $response['status'] = "error";
        $response['text'] = 'You cannot edit an invoice with sold items.';
        return json_encode($response);
    }
    // validate existing items
    $items = $_POST[1]['data'];
    foreach ($items as $item) {
        if ($item['stock_type']['value'] == "Unique Serial") {
            $serials = explode(",", $item['serial_number']['value']);
            foreach ($serials as $serial) {
                $db = Imx\db::mycon();
                $serial = $db->real_escape_string($serial);
                // * validate the serial is unique on the database
                $q = "select count(*) from items where serial_number ='$serial' and invoice_id != '$id'";
                if (Imx\db::rquery($q) > 0) {
                    $response = [];
                    $response['status'] = "error";
                    $response['text'] = "The serial $serial already exist on items database";
                    return json_encode($response);
                }
            }
        }
    }

    if ($id == "new") {
        $data = $_POST[0]['data'];
        $id = Imx\db::i_post($data, "invoices", "document_number");
    } else {
        // Edit invoice
        $data = $_POST[0]['data'];
        $orig_id = $id;
        $id = Imx\db::e_post($data, "invoices", $id, "document_number", 'invoice_id');
        if (is_numeric($id)) {
            $id = $orig_id;
            // ! backup items 
            $items = Imx\db::dataQueryMultiple("select 
            product_id,
            store_id,
            status_id,
            grade_id
            from transactions  where transaction_type ='Invoice' and reference_id ='$id'");

            // ! Delete Invoices detail
            Imx\db::iquery("delete from items where invoice_id ='$id'");
            // ! delete transactions
            Imx\db::iquery("delete from transactions where transaction_type ='Invoice' and reference_id ='$id'");
            // * Update inventory of deleted items
            foreach ($items as $item) {
                hdtv::inventory(
                    $item['product_id'],
                    $item['store_id'],
                    $item['status_id'],
                    $item['grade_id']
                );
            }
        }
    }
    $response = [];
    if (is_numeric($id)) {
        // CREAMOS LOS ITEMS 
        $items = $_POST[1]['data'];
        $product = $data['product_id']['value'];
        foreach ($items as $item) {
            // *  process each item group and store on transactions table 

            $date = $_POST[0]['data']['date']['value'];
            Imx\db::iquery("
            insert into transactions
            (
            store_id,
            product_id,
            transaction_type,
            status_id,
            grade_id,
            date,
            notes,
            serials,
            reference_id,
            status,
            quantity,
            price,
            sale_price,
            min_sale_price,
            total
            )
            values
            (
            '{$_POST[0]['data']['store_id']['value']}',
            '{$item['product_id']['value']}',
            'Invoice',
            '{$item['status_id']['value']}',
            '{$item['grade_id']['value']}',
            '$date',
            '{$item['grade_id']['value']}',
            '{$item['serial_number']['value']}',
            '{$id}',
            '1',
            '{$item['items']['value']}',
            '{$item['price']['value']}',
            '{$item['sale_price']['value']}',
            '{$item['min_sale_price']['value']}',
            '{$item['subtotal']['value']}'
            );
            ");

            // * updating latest price and cost 
            hdtv::product_pricing(
                $item['product_id']['value'],
                $item['sale_price']['value'],
                $item['min_sale_price']['value'],
                $item['price']['value'],
                Imx\db::rquery("select price_list_id from stores where store_id ='{$_POST[0]['data']['store_id']['value']}'"),
                $item['status_id']['value'],
                $item['grade_id']['value']
            );
            // * NOW we create each item
            // * provider warranty date 
            $warrantydate = Imx\db::rquery("select 
            DATE_ADD('{$date}',interval warranty_days day ) as d
             from providers  where provider_id = '{$_POST[0]['data']['provider_id']['value']}'");
            // print_r($item);
            if ($item['stock_type']['value'] == "Bulk") {
                $serials = [];
                // * If de product users bulk inventory mode the system create a unique serial number per item
                for ($i = 0; $i < $item['items']['value']; $i++) {
                    $serials[] = Imx\utils::get_guid();
                }
            } else {
                $serials = explode(",", $item['serial_number']['value']);
            }

            foreach ($serials as $serial) {
                Imx\db::iquery("
                insert into items
                (
                store_id,
                product_id,
                serial_number,
                invoice_id,
                status_id,
                grade_id,
                provider_warranty_date,
                purchase_date,
                cost
                )
                values
                (
                '{$_POST[0]['data']['store_id']['value']}',
                '{$item['product_id']['value']}',
                '{$serial}',
                '{$id}',
                '{$item['status_id']['value']}',
                '{$item['grade_id']['value']}',
                '{$warrantydate}',
                '{$date}',
                '{$item['price']['value']}'
                );
                ");
            }
            // * create and update stock 
            hdtv::inventory(
                $item['product_id']['value'],
                $_POST[0]['data']['store_id']['value'],
                $item['status_id']['value'],
                $item['grade_id']['value']
            );
        }



        $response['status'] = "ok";
        return json_encode($response);
    } else {
        $response['status'] = "error";
        $response['text'] = $id;
    }
    return json_encode($response);
});
