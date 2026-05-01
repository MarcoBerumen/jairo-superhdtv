<?php
$status = $_POST[0]['data']['status']['value'];
if($status == '2') $status = "";
$store = $_POST[0]['data']['store']['value'];
$user = $_POST[0]['data']['user']['value'];
$skip = $_POST[0]['data']['skipdates']['value'];
$customer = $_POST[0]['data']['customer']['value'];
$date = $_POST[0]['data']['date']['value'];
$datestring = explode(" ", $date);
//$status = $_POST[0]['data']['status']['value'];
$sdate = explode("/", $datestring[0]);
$date =  $sdate[2] . '-' . $sdate[0] . '-' . $sdate[1];
$sdate = explode("/", $datestring[2] ?? $datestring[0]);
$dateend =  $sdate[2] . '-' . $sdate[0] . '-' . $sdate[1];

?>
<table class="table table-sm">
    <thead>
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Customer</th>
        <th>Date</th>
        <th>Payment Method</th>
        <th>Subtotal</th>
        <th>Tax</th>
        <th>Total</th>
        <th>Comission</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $wu = "";
    $wc = "";
    $wst = "";
    $wd = "";
    if(!$skip){
        $wd = "and  date(date) between '$date' and '$dateend'";
    }
    if ($user != "")
        $wu = "and user_id = '$user'";
    if ($status != "")
        $wst = "and status_code = '$status'";
    if ($customer != "")
        $wc = "and customer_id = '$customer'";
    $q = "select 
        view_sales.*,
        (select email from customers where customers.customer_id = view_sales.customer_id) as email
        from view_sales where 
        store_id = '$store'
        {$wd}
        {$wu}
        {$wc}
        {$wst}
        ";
    // echo nl2br($q);
    $sales = Imx\db::dataQueryMultiple($q);
    $total = 0;
    foreach ($sales as $sale) {
        $sale['date'] = date('m/d/Y H:i:s', strtotime($sale['date']));
        if($sale['status_code'] == 0) {

            $total += $sale['total'];
        }
        ?>
        <tr <?php
        if($sale['status_code'] == 0){
            echo "style='color:#be4d25;'";
        }
        ?>>
            <td><a href='/api/app/sales/<?php echo $sale['sale_id']; ?>/receipt' target="_blank"><?php echo $sale['sale_id']; ?><a /></td>
            <td><?php echo $sale['user']; ?></td>
            <td><?php echo $sale['customer']; ?></td>
            <td><?php echo $sale['date']; ?></td>
            <td>
                <a href='#' data-id="<?php echo $sale['sale_id']; ?>" data-toggle="modal" data-target="#paymentsModal">
                    <?php echo $sale['payment_method'] ?? "Define"; ?>
                </a>
            </td>
            <td><?php echo $sale['subtotal']; ?></td>
            <td><?php echo $sale['tax']; ?></td>
            <td><?php echo $sale['total']; ?></td>
            <td><?php echo $sale['comission']; ?></td>
            <td>
                <!-- dropdown -->
                <div class="btn-group m-r-5 m-b-5">
                    <a href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle"><b class="caret"></b></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href='#' data-toggle="modal" data-target="#receiptModal" data-id='<?php echo $sale['sale_id']; ?>' target="_blank" class="dropdown-item">Print Receipt</a>
                        <a href='#' data-toggle="modal" data-target="#mailReceipt" data-id='<?php echo $sale['sale_id']; ?>' data-email='<?php echo $sale['email']; ?>' target="_blank" class="dropdown-item">E-mail</a>
                        <?php
                        if($sale['status_code'] == 1){

                            ?>
                            <a href='/back-office/sales/<?php echo $sale['sale_id']; ?>/cancel' class="dropdown-item">Cancel</a>
                            <?php
                        }
                        ?>
                    </div>
                </div>

            </td>
        </tr>

        <?php
    }
    ?>
    </tbody>
    <thead>
    <tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th>Total Sales</th>
        <th><?php echo Imx\utils::decimales($total); ?></th>

    </tr>
    </thead>
</table>
