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

$sql="select * from nua_employee where employee_name = '' and medical_plan_level = '' and (first_name, last_name) in "; 
$sql.=" (select first_name, last_name from nua_employee where employee_name <> '' and medical_plan_level <> '')";
$a=$X->sql($sql);
print_r($a);
foreach ($a as $b) { 
    $sql="delete from nua_employee where id = " . $b['id'];
	$X->execute($sql);
}

?>
