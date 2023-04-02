<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');
ini_set('max_execution_time', 900000);

require_once('class.XRDB.php');
$X=new XRDB();

//--
//-- Query all client employee plans
//--

$month_id = "2022-01";

$sql="select * from nua_census where month_id = '" . $month_id . "'";
$cen=$X->sql($sql);
foreach ($cen as $census) {
	    $sql="select * from nua_employee where id = " . $census['employee_id'];
		$x=$X->sql($sql);
		$company_id=$x[0]['company_id']; 
		$sql="select * from nua_company where id = " . $company_id;
		$x=$X->sql($sql);
                if (sizeof($x)) {
		    $org_id=$x[0]['org_id'];
                } else {
                   $org_id = 0;
                }
		if ($org_id==17) {
			$sql="select * from nua_monthly_member_census where employee_id = " . $census['employee_id'] . " and dependent_code = ''";
		} else {
			$sql="select * from nua_monthly_member_census where apa_employee_id = '" . $census['apa_employee_id']  . "' and dob = '" . $census['dob'] . "'";
		}
		$p=$X->sql($sql);
		if (sizeof($p)>0) {
			$post=array();
			$post['table_name']="nua_monthly_member_census";
			$post['action']="insert";	
			$post['month_id']=$month_id;
                        $post['company_id']=$census['company_id'];
                        $post['id']=$p[0]['id'];
                        $post['apa_plan']=$census['plan'];
if ($census['dependent']=='Y') {
			$post['dependent_code']=$census['dob'] . rand(1,100000);
} else {
			$post['dependent_code']="";
}
            $post['apa_employee_id']=$census['apa_employee_id'];
            print_r($post);
            $X->post($post);
		} else {
            $post=array();
			$post['table_name']="nua_monthly_member_census";
			$post['action']="insert";	
			$post['month_id']=$month_id;
$post['company_id']=$census['company_id'];
            $post['apa_plan']=$census['plan'];
            $post['apa_employee_id']=$census['apa_employee_id'];
            $post['first_name']=$census['first_name'];
            $post['last_name']=$census['last_name'];
	        $post['dob']=$census['dob'];
	        $post['employee_code']=$census['apa_employee_id'];
if ($census['dependent']=='Y') {
			$post['dependent_code']=$census['dob'] . rand(1,100000);
} else {
			$post['dependent_code']=$census['dob'] . rand(1,100000);
}
			$post['employee_id']=$census['employee_id'];
			$post['client_plan']=$census['plan'];
			$post['coverage_level']=$census['coverage_level'];
			$post['eff_date']=$census['eff_dt'];
                        print_r($post);
          	$X->post($post);
		}
}
		

?>

