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

$sql="select employee_id, plan_id, plan_type, coverage_start, coverage_end, count(*) from nua_employee_plan group by employee_id, plan_id, plan_type, coverage_start, coverage_end having count(*) > 1";
$a=$X->sql($sql);
foreach ($a as $b) { 
    $sql="select id from nua_employee_plan where employee_id = '" . $b['employee_id'] . "' and ";
    $sql.= " plan_id = '" . $b['plan_id'] . "' and plan_type = '" . $b['plan_type'] . "' and coverage_start = '" . $b['coverage_start'] . "' and coverage_end = '" . $b['coverage_end'] . "'";
    $c=$X->sql($sql);
    $id="0";
    foreach($c as $d) {
        $id=$d['id'];
    }
    $sql="delete from nua_employee_plan where id = " . $id;
echo $sql;
    $X->execute($sql);
}

?>
