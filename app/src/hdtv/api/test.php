<?php
dispatch('/public/test',function(){
   echo "test";
   hdtv::updateCredit(344);
   exit;
   exit;
//   echo hdtv::getComission(6,2,600,6,1);
    $comission = hdtv::getComission(9,50, 800,2,3) ?? 0;
    echo $comission;

});
