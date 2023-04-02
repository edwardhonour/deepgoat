<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('memory_limit',-1);
ini_set('max_execution_time', 900000);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
//header('Content-type: application/json');

require_once('class.XRDB.php');
$X=new XRDB();

        $date=date_create();
        $month_id=date_format($date,'Y-m');
	if ($month_id=="2022-05") $m="2022-06";
	if ($month_id=="2022-06") $m="2022-07";
	if ($month_id=="2022-07") $m="2022-08";
	if ($month_id=="2022-08") $m="2022-09";
	if ($month_id=="2022-09") $m="2022-10";
	if ($month_id=="2022-10") $m="2022-11";

$sql="select id from nua_company"; 
$a=$X->sql($sql);
foreach ($a as $b) { 
	$sql="select count(distinct employee_id) as c from nua_monthly_member_census where month_id = '" . $m . "' and company_id = " . $b['id'];
        $t=$X->sql($sql);
	$member_count=$t[0]['c'];
	$sql="select count(distinct employee_id) as c from nua_monthly_member_census where plan_type = '*MEDICAL*' and month_id = '" . $m . "' and company_id = " . $b['id'];
        $t=$X->sql($sql);
	$medical_count=$t[0]['c'];
	$sql="select count(distinct employee_id) as c from nua_monthly_member_census where plan_type = '*DENTAL*' and month_id = '" . $m . "' and company_id = " . $b['id'];
        $t=$X->sql($sql);
	$dental_count=$t[0]['c'];
	$sql="select count(distinct employee_id) as c from nua_monthly_member_census where plan_type = '*VISION*' and month_id = '" . $m . "' and company_id = " . $b['id'];
        $t=$X->sql($sql);
	$vision_count=$t[0]['c'];
	$sql="select count(distinct employee_id) as c from nua_monthly_member_census where plan_type = '*ADD*' and month_id = '" . $m . "' and company_id = " . $b['id'];
        $t=$X->sql($sql);
	$add_count=$t[0]['c'];

	$post=array();
	$post['table_name']="nua_company";
	$post['id']=$b['id'];
	$post['member_count']=$member_count;
	$post['medical_count']=$medical_count;
	$post['dental_count']=$dental_count;
	$post['vision_count']=$vision_count;
	$post['add_count']=$add_count;
	print_r($post);
	$X->post($post);
}
echo "</table>";
echo "<html>";

?>
