<?php
// ? Trigger Jobs file 
// ! Close opened shifts
$shifts = Imx\db::dataQueryMultiple("select * from shifts  where status = 1 and end_date is null 
and date(start_date) < date(now())");
foreach ($shifts as $shift) {
    $date = date('Y-m-d H:i:s');
    Imx\db::iquery("update shifts set end_date = '{$date}',status = 0  where shift_id =  '{$shift['shift_id']}'");
    Imx\db::iquery("update users set shift_id = null where shift_id =  '{$shift['shift_id']}'");
}
