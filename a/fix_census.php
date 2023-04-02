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

$sql="select * from nua_census where employee_id not in (select id from nua_employee)";
$a=$X->sql($sql);
print_r($a);
foreach ($a as $b) { 
     $sql="select * from nua_employee where apa_member_id = '" . $b['apa_employee_id'] . "'";
	 $z=$X->sql($sql);
	 if (sizeof($z)>0) {
		 $sql="update nua_census set employee_id = " . $z[0]['id'] . " where id = " . $b['id'];
		 echo $sql;
		 $X->execute($sql);
	 } else {
		$sql="select * from nua_employee where first_name = '" . $b['first_name'] . "' and last_name = '" . $b['last_name'] . "' and company_id = " . $b['company_id'];
		$z=$X->sql($sql);		 
		if (sizeof($z)>0) {
			$sql="update nua_census set employee_id = " . $z[0]['id'] . " where id = " . $b['id'];
			echo $sql;
			$X->execute($sql);			
		} else {
			$sql="select * from nua_employee where first_name = '" . $b['first_name'] . "' and last_name = '" . $b['last_name'] . "'";
			$z=$X->sql($sql);			
			if (sizeof($z)>0) {
				$sql="update nua_census set employee_id = " . $z[0]['id'] . " where id = " . $b['id'];
				echo $sql;
				$X->execute($sql);			
			}			
		}
	 }
}

?>

