<?php

use Imx\html2 as html;

html::head("Invoices");
html::bodyInit();
html::header("");
html::sidebar();
html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Attendance", "link" => "/back-office/attendance"]
]);
$date = $_GET['date']??date("d/m/Y");
$st = $_GET['store'] ?? "";

if(isset($_GET['exception'])){
    $dateq = Imx\utils::date2sql($date);

    Imx\db::iquery("insert into exceptions 
    (user_id,date,exception)
values   
    ('{$_GET['user']}','{$dateq}','{$_GET['exception']}')
");
}
?>
<div class="card border-0">
    <ul class="nav nav-tabs nav-tabs-v2 px-3">
        <li class="nav-item me-2"><a href="/back-office/attendance" class="nav-link px-2 <?php if ($st == "") echo "active font-weight-bolder"; ?>" data-bs-toggle="tab">All</a></li>
        <?php
        $filter = hdtv::storeFilter();

        foreach (Imx\db::dataQueryMultiple("select * from stores where row_status = 1 and store_id in({$filter})") as $store) {
        ?>
            <li class="nav-item me-2 "><a href="/back-office/attendance?store=<?php echo $store['store_id']; ?>" class="nav-link px-2 <?php if ($st == $store['store_id']) echo "active font-weight-bolder "; ?>" data-bs-toggle=" tab"><?php echo $store['name'] ?></a></li>
        <?php
        }
        ?>
        <li class="nav-item me-2 ">    <input type="text" class="form-control" id="date" placeholder="Select Date" value="<?php echo $date;?>">
        </li>
    </ul>
    <div class="tab-content p-3">
        <div class="tab-pane fade active show" id="allTab">
 
            <!-- BEGIN table -->
            <div class="table-responsive mb-3">
                <table class="table table-hover table-panel text-nowrap align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Store</th>
                            <th>Employee</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Hours</th>
                            <th>Status</th>
                            <th>Sales</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                            $total = 0;
                            $store = $_GET['store'] ?? "";
                            $date = Imx\utils::date2sql($date);
                            $query= "SELECT
`users`.`user_id` AS `user_id`,
`users`.`name` AS `name`,
case when shifts.shift_id is null then
    
    case when exceptions.exception is not null then
        'Exception'
            else
    'Absence'
    end 
        else
            'Attendance'
            end as att_status,
`users`.`store_id` AS `store_id`,
date('{$date}') AS `date`,
time(`shifts`.`start_date`) AS `start_date`,

shifts.shift_id,
time(`shifts`.`end_date`) AS `end_date`,
`stores`.`name` AS `store`,
(
	SELECT
		sum( `sales`.`total` ) 
	FROM
		`sales` 
	WHERE
		`sales`.`shift_id` = `shifts`.`shift_id` 
	) AS `sales`,
`shifts`.`status` AS `status`,
timediff( end_date , `shifts`.`start_date` ) AS `time`,
TIME_TO_SEC(timediff( end_date , `shifts`.`start_date` ))/3600 AS `hours`,
exceptions.exception
FROM
users
	left join shifts on shifts.user_id =  users.user_id and date(shifts.start_date) ='{$date}'
	left join exceptions on exceptions.user_id =  users.user_id and date(exceptions.date) ='{$date}'
	left join stores on stores.store_id = users.store_id


	";

                            $filter = hdtv::storeFilter();
                            if ($store){
                                $query .= " where users.store_id ='$store' and users.store_id in ($filter)";
                            }
                            else{
                                $query.=" where users.store_id in ($filter)";
                            }
                            $query .= "
                             and users.row_status = 1
                             and stores.row_status = 1
                             order by store,name";
//                            echo $query;

                            foreach (Imx\db::dataQueryMultiple($query) as $row) {
                                $total += $row['sales'] ?? 0;

                            ?>
                                <td>
                                    <?php if($row['shift_id'] == "" && $row['exception']==""){
                                       echo '<span class="badge bg-primary rounded-pill" style="cursor:pointer;" onclick="exceptions('.$row['user_id'].')"><i class="fa fa-plus"></i> Exception</span>';
                                    }?>
                                    <a href="#" class="fw-bold"><?php
                                                                echo $row['shift_id'];
                                                                ?></a></td>

                                <td><?php echo $row['store']; ?></td>
                                <td><?php
                                    echo $row['name'];
                                    ?></td>
                                <td>
                                    <?php
                                    echo $row['start_date'];
                                    ?>

                                </td>
                                <td> <?php
                                        echo $row['end_date'];
                                        ?></td>
                                <td> <?php
                                        echo $row['hours'];
                                        ?>
                                </td>
                                <td>
                                    <?php
                                    switch($row['att_status']){
                                        case "Attendance":
                                            echo '<span class="badge bg-success rounded-0">Attendance</span>';
                                            break;
                                        case "Absence":
                                            echo '<span class="badge bg-danger rounded-0">Absense</span>';
                                            break;
                                        case "Exception":
                                            echo '<span class="badge bg-warning rounded-0" title="'.$row['exception'].'">Exception</span>';
                                            break;

                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo Imx\utils::decimales($row['sales']);
                                    ?>
                                </td>
                                <td></td>
                        </tr>
                    <?php
                            }
                    ?>

                    </tbody>
                    <tfood>
                        <tr>
                            <th></th>
                            <th></th>
                            <th> </th>
                            <th> </th>
                            <th></th>
                            <th>Total Sales</th>
                            <th><?php
                                echo Imx\utils::decimales($total);
                                ?></th>
                            <th></th>
                        </tr>
                    </tfood>
                </table>
            </div>
            <!-- END table -->

        </div>
    </div>
</div>
<?php
$date = $_GET['date']??date('d/m/Y');
$url = "";
if(isset($_GET['exception'])){
    $url = "window.history.replaceState(null, document.title, \"?store=$st&date=$date\")";
}
$scripts = "
<script src=\"/assets2/plugins/datatables.net/js/jquery.dataTables.min.js\"></script>
<script src=\"/assets2/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js\"></script>
<script src=\"/assets2/plugins/datatables.net-responsive/js/dataTables.responsive.min.js\"></script>
<script src=\"/assets2/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js\"></script>

<script>
function exceptions(user){
    msg =prompt('Please type the exception motive');
    if(msg){
        window.location.href='?store={$st}&date={$date}&user='+user+'&exception='+msg;
    }
    else{
        alert('Please type a valid exception motive')
    }
}
$(document).ready(function() {
                {$url}
                flatpickr(\"#date\",{
                dateFormat: \"m/d/Y\",
                locale: \"en\",
            });

                   $('#date').change(function(){
                       window.location.href='?store={$st}&date='+$(this).val();
                   })
});
</script>";
html::endContent();
html::containerEnd();
html::scripts(false, $scripts);
html::bodyEnd();
