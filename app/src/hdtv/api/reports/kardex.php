<?php

dispatch_post('/api/reports/kardex', function () {
$item_id = $_POST[0]['data']['item']['value'];
$item = Imx\db::dataQuery("select 
datediff(items.provider_warranty_date,items.purchase_date) as warranty_days ,
datediff(items.provider_warranty_date,curdate()) as warranty_left_days ,
datediff(items.sale_warranty_date,items.sold_date) as cwarranty_days ,
datediff(items.sale_warranty_date,curdate()) as cwarranty_left_days ,
items.*
    from items
    where item_id ='$item_id'");
$product = Imx\db::dataQuery("select * from products where product_id = '{$item['product_id']}'");
    $invoice = Imx\db::dataQuery("select * from view_invoices where invoice_id = '{$item['invoice_id']}'");
$img = explode(",",$product['images'])[0];
    if($item['warramty_days']) {
    $pct = intval(($item['warranty_left_days']/$item['warranty_days'] )*100)??0;
    }else{
        $pct =0;
    }
    if($item['cwarranty_left_days']){

    $pct = intval(($item['cwarranty_left_days']/$item['cwarranty_days'] )*100);
    }
    else{
        $pctc =0;
    }

?>
    <div class="mb-10px mt-10px fs-10px">
        <b class="text-dark">Kardex Report <?php echo $item['serial_number'];?></b>
    </div>
    <div class="table-responsive">

        <table class="table table-bordered widget-table rounded" data-id="widget">
            <thead>
            <tr class="text-nowrap">
                <th width="1%">Transaction type</th>
                <th>Product</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <img src="/api/s3/<?php echo $img;?>" width="100">
                </td>
                <td>
                    <h5 class="mb-1"><?php echo $product['name'];?></h5>
                    <p class="fs-11px fw-bold text-gray-600 mb-3">
                        <?php echo $product['model'];?>
                        <?php echo $product['sku'];?>

                    </p>

                    <div class="clearfix fs-10px">
                        Status:
                        <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white">
                            <?php
                            switch($item['status']){
                                case "1":
                                    echo "Available";
                                    break;
                                case "2":
                                    echo "Sold";
                                    break;
                                case "3":
                                    echo "Claim";
                                    break;
                                case "4":
                                    echo "Out of inventory";
                                    break;

                            }
                            ?></b> <br>
                        Store:
                        <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white">
                            <?php

                            echo Imx\db::rquery("select name from stores where store_id='{$item['store_id']}'");
                            ?></b>
                    </div>
                </td>
                <td class="text-nowrap">
                </td>

            </tr>

            <tr>
                <td>

                    <span class="badge bg-info rounded-pill">Invoice <?php echo $invoice['document_number'];?></span>

                </td>
                <td>
                    <h5 class="mb-1">Provider : <?php echo $invoice['provider'];?></h5>
                    <p class="fs-11px fw-bold text-gray-600 mb-3">Store : <?php echo $invoice['store'];?>.</p>
                    <div class="progress h-10px rounded-pill mb-5px">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success fs-10px fw-bold" style="width: <?php echo $pct;?>%;">Provider Warranty until <?php echo Imx\utils::sql2date($item['provider_warranty_date']);?> ( <?php echo $item['warranty_left_days'];?> days ) </div>
                    </div>
                    <div class="clearfix fs-10px">
                        Purchase Date:
                        <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white"><?php echo Imx\utils::sql2date($invoice['date']);?></b>
                    </div>
                <td class="text-nowrap">
                    <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white">$<?php echo $item['cost'];?></b><br>
                </td>

            </tr>
            <?php
            $transactions = Imx\db::dataQueryMultiple("select * from transactions where item_id ='$item_id' order by date ");
//            print_r($transactions);
            foreach($transactions as $transaction){
                $type = $transaction['transaction_type'];
                switch($type){
                    case "Sale":
                        $sale = Imx\db::dataQuery("select * from view_sales where sale_id = '{$transaction['reference_id']}'");
                        ?>
                        <tr>
                            <td>
                                <span class="badge bg-success rounded-pill">Sale : <?php echo $transaction['reference_id'];?></span>
                            </td>
                            <td>
                                <h5 class="mb-1">Customer<?php echo $sale['provider'];?></h5>
                                <p class="fs-11px fw-bold text-gray-600 mb-3"><?php echo $sale['customer'];?>.</p>
                                <div class="progress h-10px rounded-pill mb-5px">

                                    <?php if ($item['sale_id'] == $transaction['reference_id']) {
                                        ?>
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-default fs-10px fw-bold" style="width: <?php echo $pct2;?>%;">Customer Warranty until <?php echo Imx\utils::sql2date($item['sale_warranty_date']);?> (<?php echo $item['cwarranty_left_days'];?> days) </div>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger fs-10px fw-bold" style="width:100%;">Cancelled </div>

                                        <?php
                                    }
                                    ?>


                                </div>
                                <div class="clearfix fs-10px">
                                    Sale date :
                                    <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white"><?php echo Imx\utils::sql2date(substr($sale['date'],0,10));?></b>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white">$<?php echo $item['price'];?></b><br>
                            </td>

                        </tr>
                        <?php
                        break;

                    case "Cancelation":
                        $sale = Imx\db::dataQuery("select * from view_sales where sale_id = '{$transaction['reference_id']}'");
                        ?>
                        <tr>
                            <td>
                                <span class="badge bg-danger rounded-pill">Cancellation : <?php echo $transaction['reference_id'];?></span>


                            </td>
                            <td>
                                <h5 class="mb-1">Customer<?php echo $sale['provider'];?></h5>
                                <p class="fs-11px fw-bold text-gray-600 mb-3"><?php echo $sale['customer'];?>.</p>
                                <div class="clearfix fs-10px">
                                    Cancellation date :
                                    <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white"><?php echo Imx\utils::sql2date(substr($transaction['date'],0,10));?></b>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white">$ -<?php echo $item['price'];?></b><br>
                            </td>

                        </tr>
                        <?php
                        break;
                    case "Claim":
                        $q= "select * from view_claims where claim_id = '{$transaction['reference_id']}'";
                        $claim = Imx\db::dataQuery($q);

                        ?>
                        <tr>
                            <td>
                                <span class="badge bg-warning rounded-pill">Claim : <?php echo $transaction['reference_id'];?></span>

                            </td>
                            <td>
                                <h5 class="mb-1">Customer<?php echo $claim['customer'];?></h5>
                                <p class="fs-11px fw-bold text-gray-600 mb-3"></p>
                                <div class="clearfix fs-10px">
                                    Observations :
                                    <?php echo $claim['observations']?>
                                </div>
                                <div class="clearfix fs-10px">
                                    Claim Date:
                                    <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white"><?php echo Imx\utils::sql2date($claim['date']);?></b>
                                </div>

                            </td>
                            <td class="text-nowrap">
                                <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white">$ -<?php echo $transaction['price'];?></b><br>

                            </td>


                        </tr>

                        <?php
                        break;
                    case "Out of Inventory":
                        $q= "select * from view_out_inventory where out_inventory_id =  '{$transaction['reference_id']}'";
                        $oi = Imx\db::dataQuery($q);
                        ?>
                        <tr>
                            <td>
                                <span class="badge bg-danger rounded-pill">Outgoing Inventory: <?php echo $transaction['reference_id'];?></span>

                            </td>
                            <td>
                                <h5 class="mb-1"></h5>
                                <p class="fs-11px fw-bold text-gray-600 mb-3"></p>
                                <div class="clearfix fs-10px">
                                    Motive :
                                    <?php echo $oi['motive']?>
                                </div>
                                <div class="clearfix fs-10px">
                                    Date:
                                    <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white"><?php echo Imx\utils::sql2date($oi['date']);?></b>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white">$ -<?php echo $transaction['price'];?></b><br>

                            </td>


                        </tr>

                        <?php
                        break;
                        case "Inventory adjustment":
                            if($transaction['quantity'] == 0) continue;
                        $q= "select * from view_inventories where inventory_id =  '{$transaction['reference_id']}'";
                        $oi = Imx\db::dataQuery($q);
//                        print_r($oi);
                        ?>
                        <tr>
                            <td>
                                <span class="badge bg-danger rounded-pill">Physical Inventory adjustment: <?php echo $transaction['reference_id'];?></span>

                            </td>
                            <td>
                                <h5 class="mb-1"></h5>
                                <p class="fs-11px fw-bold text-gray-600 mb-3"></p>
                                <div class="clearfix fs-10px">
                                    User :
                                    <?php echo $oi['user']?>
                                </div>
                                <div class="clearfix fs-10px">
                                    Adjustment date:
                                    <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white"><?php echo Imx\utils::sql2date($oi['date']);?></b>
                                </div>

                            </td>
                            <td class="text-nowrap">
                                <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white">$ -<?php echo $transaction['price'];?></b><br>

                            </td>


                        </tr>

                        <?php
                        break;
                        case "Transfer":
                        $q= "select view_transfers.*, date(`date`) as dates from view_transfers where transfer_id =  '{$transaction['reference_id']}'";
                        $oi = Imx\db::dataQuery($q);
                        ?>
                        <tr>
                            <td>
                                <span class="badge bg-primary-darker rounded-pill">Transfer: <?php echo $transaction['reference_id'];?></span>

                            </td>
                            <td>
                                <h5 class="mb-1"></h5>
                                <p class="fs-11px fw-bold text-gray-600 mb-3"></p>
                                <div class="clearfix fs-10px">
                                    Origin :
                                    <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white"><?php echo $oi['origin'];?></b>
                                </div>
                                <div class="clearfix fs-10px">
                                    Destination:
                                    <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white"><?php echo $oi['destination'];?></b>
                                </div>
                                <div class="clearfix fs-10px">
                                    Transfer Date:
                                    <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white"><?php echo Imx\utils::sql2date($oi['dates']);?></b>
                                </div>

                            </td>
                            <td class="text-nowrap">
                                <b class="text-dark" data-id="widget-elm" data-light-class="text-dark" data-dark-class="text-white">$ --.-- </b><br>

                            </td>


                        </tr>

                        <?php
                        break;


                }

            }


            ?>
            </tbody>
        </table>

    </div>
<?php
});
