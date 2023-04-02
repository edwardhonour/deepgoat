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

$sql="select distinct company_id, client_plan from nua_monthly_member_census where ";
$sql.=" month_id = '2023-01' and client_plan not in (select distinct plan_code from nua_company_plan where company_id = ";
$sql.=" nua_monthly_member_census.company_id and '2023-01' between ";
$sql.=" start_month_id and end_month_id) and company_id in (select id from nua_company where invoicing = 'Y')";
    $c=$X->sql($sql);
    foreach($c as $d) {
	    $sql="update nua_company_plan set end_month_id = '2023-12' where end_month_id = '2022-12' and ";
	    $sql.=" plan_code = '" . $d['client_plan'] . "' and company_id = " . $d['company_id'];
	    echo $sql;
	    $X->execute($sql);
	    }

?>
