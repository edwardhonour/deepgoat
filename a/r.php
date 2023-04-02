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

$sql="select * from nua_employee where employee_code <> '' and employee_code not in (select employeeId from inf_client_employee where has_plans = 'Y')";
$r=$X->sql($sql);

$count=0;
foreach($r as $j) { 
echo $j['id'] . ",";
//     $sql="select id from nua_employee where employee_code = '" . $j['employeeId'] . "'";
//     $f=$X->sql($sql);
//     if (sizeof($f)>0) {
          $sql="delete from nua_employee where id = " . $j['id'];
          echo $sql;
          $X->execute($sql);
//     }
}
?>
Finished

