<?php
dispatch_post('/api/reports/income-statement', function () {
    $store = $_POST[0]['data']['store']['value'];
    $end_date = Imx\utils::date2sql($_POST[0]['data']['end_date']['value']);
    $start_date = Imx\utils::date2sql($_POST[0]['data']['start_date']['value']);
    $query = "select 
view_sales.sale_id,
view_sales.store,
date_format(view_sales.date,'%m/%d/%Y') as date,
view_items.product,
view_items.status,
view_items.grade,
view_items.cost,
view_items.price,
view_items.warranty_price,
view_items.utility - view_invoices.shipping_item as utility,
view_items.customer,
view_sales.`user`,
view_invoices.shipping_item as shipping,
concat(view_invoices.provider,' ', view_invoices.document_number) as invoice,
brands.name as brand

 from view_sales        
  left join transactions on transactions.reference_id = view_sales.sale_id and transaction_type='Sale'
     left join view_items on view_items.item_id = transactions.item_id
     left join products on products.product_id = transactions.product_id
     left join brands on brands.brand_id = products.brand_id
 left join view_invoices on view_invoices.invoice_id = view_items.invoice_id
   where
        view_sales.store_id ='$store'
     and view_sales.status_code ='1'
         and date(view_sales.date) between '$start_date' and '$end_date'
 ";

    $sales = Imx\db::rquery("select sum(subtotal) from sales where 
        store_id ='$store'
     and sales.status ='1'
         and date(date) between '$start_date' and '$end_date'");
    $salespm = Imx\db::dataQueryMultiple("select sum(sales_payments.amount) as amount,pm.name as pm from sales 
    left join sales_payments on sales_payments.sale_id = sales.sale_id
    left join payment_methods pm on sales_payments.payment_method_id = pm.payment_method_id
where 
        store_id ='$store'
     and sales.status ='1'
         and date(date) between '$start_date' and '$end_date'
         group by sales_payments.payment_method_id
         ");
    $warranties = Imx\db::rquery("select sum(warranties) from sales where 
        store_id ='$store'
     and sales.status ='1'
         and date(date) between '$start_date' and '$end_date'");

    $expenses = Imx\db::rquery("select sum(subtotal) from expenses where 
        store_id ='$store'
         and date(date) between '$start_date' and '$end_date'");
    $payroll = Imx\db::rquery("select sum(wage) from payroll where 
        store_id ='$store'
     and payroll.status = 'Paid'
         and date(payment_date) between '$start_date' and '$end_date'");
    $comissions = Imx\db::rquery("select sum(comissions) from payroll where 
        store_id ='$store'
     and payroll.status = 'Paid'
         and date(payment_date) between '$start_date' and '$end_date'");

    $claims = Imx\db::rquery("select sum(credit) from claims where 
        store_id ='$store'
         and date(date) between '$start_date' and '$end_date'");
    $cost = Imx\db::rquery("select  sum(cost) from ($query) as q ");
    $shipping = Imx\db::rquery("select  sum(shipping) from ($query) as q ");
    $totalcosts = $expenses + $shipping + $payroll + $comissions + $cost;
    ?>
    <div class="row">
        <!-- BEGIN col-6 -->
        <div class="col-xl-6">
            <!-- BEGIN panel -->
            <div class="panel panel-inverse" data-sortable-id="table-basic-1">
                <!-- BEGIN panel-heading -->
                <div class="panel-heading">
                    <h4 class="panel-title">Income Statement report from <?php echo $start_date;?> to <?php echo $end_date;?> </h4>
                    <div class="panel-heading-btn">
                    </div>
                </div>
                <!-- END panel-heading -->
                <!-- BEGIN panel-body -->
                <div class="panel-body">
                    <!-- BEGIN table-responsive -->
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                            <tr>
                                <th>Revenues and other income</th>
                                <th></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td></td>
                                <td>Sales</td>
                                <td><?php echo Imx\utils::decimales2($sales-$warranties);?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Warranties</td>
                                <td><?php echo Imx\utils::decimales2($warranties);?></td>                            </tr>
                            <thead>
                            <tr>
                                <th>Total income :</th>
                                <th></th>
                                <th><?php echo Imx\utils::decimales2($sales);?></th>
                            </tr>

                            <thead>
                            <tr>
                                <th>Payment Methods</th>
                                <th></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php
                            $tpm = 0;
                            foreach($salespm as $s){
                                $tpm+=$s['amount'];
                                ?>
                                <tr>
                                    <td></td>
                                    <td><?php echo $s['pm'];?></td>
                                    <td><?php echo Imx\utils::decimales2($s['amount']);?></td>
                                </tr>
                                <?php
                            }
                            ?>

                            <thead>
                            <tr>
                                <th>Total payments :</th>
                                <th></th>
                                <th><?php echo Imx\utils::decimales2($tpm);?></th>
                            </tr>


                            <tr>
                                <th>Costs and other deductions</th>
                                <th></th>
                                <th></th>
                            </tr>

                            </thead>
                            <tr>
                                <td></td>
                                <td>Sale Cost</td>
                                <th><?php echo Imx\utils::decimales2($cost);?></th>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Shipping</td>
                                <th><?php echo Imx\utils::decimales2($shipping);?></th>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Claims</td>
                                <th><?php echo Imx\utils::decimales2($claims);?></th>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Payroll</td>
                                <td><?php echo Imx\utils::decimales2($payroll);?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Expenses</td>
                                <td><?php echo Imx\utils::decimales2($expenses);?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>Comissions</td>
                                <td><?php echo Imx\utils::decimales2($comissions);?></td>
                            </tr>

                            </tbody>
                            <thead>
                            <tr>
                                <th>Total costs :</th>
                                <th></th>
                                <th><?php echo Imx\utils::decimales2($totalcosts);?></th>
                            </tr>


                            </thead>
                            <tfoot>
                            <tr>
                                <th>Earning (loss) from selected period</th>
                                <th></th>
                                <th><?php echo Imx\utils::decimales2($sales - $totalcosts);?></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- END table-responsive -->
                </div>
                <!-- END panel-body -->
            </div>
            <!-- END panel -->
        </div>
    </div>
    <?php
//    return $query;
return "";

});
