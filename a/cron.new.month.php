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

$sql="select * from nua_monthly_member_census where month_id = '2022-02'"; 
$a=$X->sql($sql);
foreach ($a as $b) {
	if ($b['employee_id']==0) {
		$sql="select count(*) as c from nua_monthly_member_census where ";
		$sql.="month_id = '2022-03' and first_name = '" . $b['first_name'] . "' and last_name = '" . $b['last_name'] . "' and dob = '" . $b['dob'] . "'";
        $w=$X->sql($sql);
        if ($w[0]['c']==0) {
			$sql="select count(*) as c from nua_monthly_member_terminations where ";
			$sql.="month_id = '2022-02' and first_name = '" . $b['first_name'] . "' and last_name = '" . $b['last_name'] . "' and dob = '" . $b['dob'] . "'";
			$w=$X->sql($sql);
				$post=array();
				$post['table_name']="nua_monthly_member_census";
				$post['action']="insert";
				$post['month_id']="2022-03";
				$post['client_id']=$b['client_id'];
				$post['company_id']=$b['company_id'];
				$post['employee_code']=$b['employee_code'];
				$post['dependent_code']=$b['dependent_code'];
				$post['employee_id']=$b['employee_id'];
				$post['first_name']=$b['first_name'];
				$post['last_name']=$b['last_name'];
				$post['middle_initial']=$b['middle_initial'];
				$post['dob']=$b['dob'];
				$post['ssn']=$b['ssn'];
				$post['gender']=$b['gender'];
				$post['eff_dt']=$b['eff_dt'];
				$post['term_dt']=$b['term_dt'];
				$post['client_plan']=$b['client_plan'];
				$post['apa_plan']=$b['apa_plan'];
				$post['coverage_level']=$b['coverage_level'];
				$post['coverage_price']=$b['coverage_price'];
				$post['apa_employee_id']=$b['apa_employee_id'];
				$post['company_name']=$b['company_name'];
				$post['plan_type']=$b['plan_type'];
				print_r($post);
				$X->post($post);
        }
    } else {
        $sql="select count(*) as c from nua_monthly_member_terminations where ";
        $sql.=" month_id = '2022-03' and employee_id = " . $b['employee_id'];
        $w=$X->sql($sql);
        if ($w[0]['c']==0) {
            $sql="select count(*) as c from nua_monthly_member_terminations where ";
			$sql.=" month_id = '2022-02' and employee_id = " . $b['employee_id'];
			$w=$X->sql($sql);
			if ($w[0]['c']==0) {            
				$post=array();
				$post['table_name']="nua_monthly_member_census";
				$post['action']="insert";
				$post['month_id']="2022-03";
				$post['client_id']=$b['client_id'];
				$post['company_id']=$b['company_id'];
				$post['employee_code']=$b['employee_code'];
				$post['dependent_code']=$b['dependent_code'];
				$post['employee_id']=$b['employee_id'];
				$post['first_name']=$b['first_name'];
				$post['last_name']=$b['last_name'];
				$post['middle_initial']=$b['middle_initial'];
				$post['dob']=$b['dob'];
				$post['ssn']=$b['ssn'];
				$post['gender']=$b['gender'];
				$post['eff_dt']=$b['eff_dt'];
				$post['term_dt']=$b['term_dt'];
				$post['client_plan']=$b['client_plan'];
				$post['apa_plan']=$b['apa_plan'];
				$post['coverage_level']=$b['coverage_level'];
				$post['coverage_price']=$b['coverage_price'];
				$post['apa_employee_id']=$b['apa_employee_id'];
				$post['company_name']=$b['company_name'];
				$post['plan_type']=$b['plan_type'];
				print_r($post);
				$X->post($post);
			}
        }
	}
}

?>

