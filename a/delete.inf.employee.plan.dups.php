<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('memory_limit',-1);
ini_set('max_execution_time', 900000);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');

require_once('class.XRDB.php');
$X=new XRDB();

$sql="select clientId, employeeId, planId, count(*) from inf_client_employee_plan group by clientId, employeeId, planId having count(*) > 1";
$a=$X->sql($sql);
foreach ($a as $b) { 
    $sql="select id from inf_client_employee_plan where clientId = '" . $b['clientId'] . "' and ";
    $sql.= " employeeId = '" . $b['employeeId'] . "' and planId = '" . $b['planId'] . "'";
    $c=$X->sql($sql);
    $id="0";
    foreach($c as $d) {
        $id=$d['id'];
    }
    $sql="delete from inf_client_employee_plan where id = " . $id;
echo $sql;
//    $X->execute($sql);
}

?>
