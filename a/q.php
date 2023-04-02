<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
ini_set("max_execution_time", -1);
ini_set('memory_limit','-1');
require_once('class.XRDB.php');
require_once('class.BSCScan.php');

$X=new XRDB();
$B=new BSCScan();

$sql="select * from inf_client_employee where has_plans = 'Y' and employee_id = 0";
$r=$X->sql($sql);

$count=0;
foreach($r as $j) { 
     $sql="select id from nua_employee where employee_code = '" . $j['employeeId'] . "'";
     $f=$X->sql($sql);
     if (sizeof($f)>0) {
          $sql="update inf_client_employee set employee_id = " . $f[0]['id'] . " where ";
          $sql.="id = " . $j['id'];
          echo $sql;
          $X->execute($sql);
     }
}
?>
Finished

