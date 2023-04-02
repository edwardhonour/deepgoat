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

$sql="select month_id, employee_code, client_plan, coverage_level, coverage_price, count(*) from ";
$sql.=" nua_monthly_member_census where month_id = '2022-12' and dependent_code = '' group by month_id, employee_code, client_plan, coverage_level, coverage_price having count(*) > 1";
$a=$X->sql($sql);
foreach ($a as $b) { 
print_r($b);
    $sql="select id from nua_monthly_member_census where ";
    $sql.= " dependent_code = '' and employee_code = '" . $b['employee_code'] . "' ";
    $sql.= " and client_plan = '" . $b['client_plan'] . "' ";
    $sql.= " and month_id = '" . $b['month_id'] . "' ";
    $sql.= " and coverage_level = '" . $b['coverage_level'] . "' ";
    $sql.= " and coverage_price = '" . $b['coverage_price'] . "' ";
    $c=$X->sql($sql);
    $id="0";
    foreach($c as $d) {
        $id=$d['id'];
    }
    $sql="delete from nua_monthly_member_census where id = " . $id;
echo $sql;
    $X->execute($sql);

}

?>
