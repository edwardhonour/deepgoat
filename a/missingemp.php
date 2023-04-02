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

$sql="select * from nua_census where dependent = 'N' and employee_id not in (select id from nua_employee)";
$a=$X->sql($sql);
print_r($a);
foreach ($a as $b) { 
    $p=array();
    $p['table_name']="nua_employee";
    $p['action']="insert";
    $p['apa_member_id']=$b['apa_employee_id'];
    $p['first_name']=$b['first_name'];
    $p['last_name']=$b['last_name'];
    $p['date_of_birth']=$b['dob'];
    $p['gender']=$b['gender'];
    $p['apa_medical_plan']=$b['plan'];
    $p['apa_company_id']=$b['company_id'];
	$p['company_id']=$b['company_id'];
	$sql="select * from nua_company where id = " . $b['company_id'];
	$g=$X->sql($sql);
	$p['org_id']=$g[0]['org_id'];
 	if ($b['coverage_level']=='SI'||$b['coverage_level']=='SI') $p['apa_medical_plan_level']="EE";
	if ($b['coverage_level']=='FA'||$b['coverage_level']=='FAM') $p['apa_medical_plan_level']="FAM";
	if ($b['coverage_level']=='ES'||$b['coverage_level']=='ES') $p['apa_medical_plan_level']="ES";
	if ($b['coverage_level']=='EC'||$b['coverage_level']=='EC') $p['apa_medical_plan_level']="EC";
    $p['apa_medical_eff_dt']=$b['eff_dt'];
	print_r($p);
    $id=$X->post($p);
	$sql="update nua_employee set id = " . $b['employee_id'] . " where id = " . $id;
	$X->execute($sql);
}

?>

