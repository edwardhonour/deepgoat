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

$sql="select * from inf_client_employee_plan";
$plans=$X->sql($sql);
foreach ($plans as $plan) {
		$month_code = substr($plan['effectiveDate'],0,7);
		$sql="select * from nua_monthly_member_additions where client_id = '";
        $sql.= $plan['clientId'] . "' and employee_code = '" . $plan['employeeId'] . "' ";
        $sql.="	and month_id = '" . $month_code . "' ";
		$sql.=" and client_plan = '" . $plan['planId'] . "'";
		$p=$X->sql($sql);
		$post=array();
		$post['table_name']="nua_monthly_member_additions";
		$post['action']="insert";	
		$post['month_id']=$month_code;
		$post['client_id']=$plan['clientId'];
		$sql="select id from nua_company where infinity_id = '" . $post['client_id'] . "'";
		$c=$X->sql($sql);
		if (sizeof($c)>0) {
			$post['company_id']=$c[0]['id'];			
		}
		$post['employee_code']=$plan['employeeId'];
		$sql="select id from nua_employee where employee_code = '" . $post['employee_code'] . "'";
		$c=$X->sql($sql);
		if (sizeof($c)>0) {
			$post['employee_id']=$c[0]['id'];			
		} else {
				$p0=array();
				$p0['table_name']="nua_employee";
				$p0['action']="insert";
				$p0['company_code']=$post['client_id'];
				$p0['employee_code']=$post['employee_code'];
				$sql="select id from nua_company where infinity_id = '" . $post['client_id'] . "'";
				$c=$X->sql($sql);
				if (sizeof($c)>0) {
					$post['company_id']=$c[0]['id'];			
				}
				$sql="select * from inf_client_employee where clientId = '" . $plan['clientId'] . "' and employeeId = '" . $plan['employeeId'] . "'";
				$e=$X->sql($sql);
				if (sizeof($e)>0) {
					$p0['first_name']=$e[0]['first_name'];
					$p0['last_name']=$e[0]['last_name'];
					$p0['middle_initial']=$e[0]['middle_initial'];
					$p0['social_security_number']=$e[0]['ssn'];
					$p0['date_of_birth']=$e[0]['dob'];
					$p0['gender']=$e[0]['gender'];
				}
			$post['employee_id']=$X->post($p0);
		}
		
		$post['coverage_level']=$plan['planType'];
		$post['coverage_price']=$plan['peoPremium'];
		$post['client_plan']=$plan['planId'];
		$post['eff_dt']=$plan['effectiveDate'];
		$sql="select * from inf_client_employee where clientId = '" . $plan['clientId'] . "' and employeeId = '" . $plan['employeeId'] . "'";
		$e=$X->sql($sql);
		
		if (sizeof($e)>0) {
				$post['first_name']=$e[0]['first_name'];
				$post['last_name']=$e[0]['last_name'];
				$post['middle_initial']=$e[0]['middle_initial'];
				$post['ssn']=$e[0]['ssn'];
				$post['dob']=$e[0]['dob'];
				$post['gender']=$e[0]['gender'];
		}
		
		if (sizeof($p)>0) {
			$post['id']=$p[0]['id'];	
		}
		print_r($post);
		$X->post($post);
		if ($plan['planType']!='EE'&&$plan['planType']!='') {
			$sql="select * from inf_client_employee where employeeId = '" .  $plan['employeeId'] . "' and dependentId <> ''";
			echo $sql;
			$deps=$X->sql($sql);
			foreach ($deps as $dep) {
				$sql="select * from nua_monthly_member_additions where client_id = '";
				$sql.= $plan['clientId'] . "' and employee_code = '" . $plan['employeeId'] . "' ";
				$sql.=" and dependent_code = '" . $dep['dependentId'] . "' ";
				$sql.="	and month_id = '" . $month_code . "' ";
				$sql.=" and client_plan = '" . $plan['planId'] . "'";
				$p=$X->sql($sql);
				$post=array();
				$post['table_name']="nua_monthly_member_additions";
				$post['action']="insert";
				$post['month_id']=$month_code;
				$post['client_id']=$plan['clientId'];
				$sql="select id from nua_company where infinity_id = '" . $post['client_id'] . "'";
				$c=$X->sql($sql);
				if (sizeof($c)>0) {
					$post['company_id']=$c[0]['id'];			
				}
				$post['employee_code']=$plan['employeeId'];
				$sql="select id from nua_employee where employee_code = '" . $post['employee_code'] . "'";
				$c=$X->sql($sql);
				if (sizeof($c)>0) {
					$post['employee_id']=$c[0]['id'];			
				} 
				$post['dependent_code']=$dep['dependentId'];
				$post['coverage_level']=$plan['planType'];
				$post['coverage_price']="0.00";
				$post['client_plan']=$plan['planId'];
				$post['eff_dt']=$plan['effectiveDate'];
				$sql="select * from inf_client_employee where clientId = '" . $plan['clientId'] . "' and dependentId = '" . $dep['dependentId'] . "'";
				$e=$X->sql($sql);
		
				if (sizeof($e)>0) {
						$post['first_name']=$e[0]['first_name'];
						$post['last_name']=$e[0]['last_name'];
						$post['middle_initial']=$e[0]['middle_initial'];
						$post['ssn']=$e[0]['ssn'];
						$post['dob']=$e[0]['dob'];
						$post['gender']=$e[0]['gender'];
				}
		
				if (sizeof($p)>0) {
					$post['id']=$p[0]['id'];	
				}				
				print_r($post);
				$X->post($post);
			}
		}
}

$sql="select * from inf_client_employee_plan where coverageEnd <> ''";
$plans=$X->sql($sql);
foreach ($plans as $plan) {
		$month_code = substr($plan['coverageEnd'],0,7);
		$sql="select * from nua_monthly_member_terminations where client_id = '";
        $sql.= $plan['clientId'] . "' and employee_code = '" . $plan['employeeId'] . "' ";
        $sql.="	and month_id = '" . $month_code . "' ";
		$sql.=" and client_plan = '" . $plan['planId'] . "'";
		$p=$X->sql($sql);
		$post=array();
		$post['table_name']="nua_monthly_member_terminations";
		$post['action']="insert";
		$post['month_id']=$month_code;
		$post['client_id']=$plan['clientId'];
				$sql="select id from nua_company where infinity_id = '" . $post['client_id'] . "'";
				$c=$X->sql($sql);
				if (sizeof($c)>0) {
					$post['company_id']=$c[0]['id'];			
				}
		$post['employee_code']=$plan['employeeId'];
		$post['coverage_level']=$plan['planType'];
		$post['coverage_price']=$plan['peoPremium'];
		$post['client_plan']=$plan['planId'];
		$post['eff_dt']=$plan['effectiveDate'];
		$sql="select * from inf_client_employee where clientId = '" . $plan['clientId'] . "' and employeeId = '" . $plan['employeeId'] . "'";
		$e=$X->sql($sql);
		
		if (sizeof($e)>0) {
				$post['first_name']=$e[0]['first_name'];
				$post['last_name']=$e[0]['last_name'];
				$post['middle_initial']=$e[0]['middle_initial'];
				$post['ssn']=$e[0]['ssn'];
				$post['dob']=$e[0]['dob'];
				$post['gender']=$e[0]['gender'];
		}
		
		$post['term_dt']=$plan['coverageEnd'];		
		if (sizeof($p)>0) {
			$post['id']=$p[0]['id'];	
		}
		print_r($post);
		$X->post($post);
		if ($plan['planType']!='EE'&&$plan['planType']!='') {
			$sql="select * from inf_client_employee where employeeId = '" .  $plan['employeeId'] . "' and dependentId <> ''";
			$deps=$X->sql($sql);
			foreach ($deps as $dep) {
				$sql="select * from nua_monthly_member_terminations where client_id = '";
				$sql.= $plan['clientId'] . "' and employee_code = '" . $plan['employeeId'] . "' ";
				$sql.=" and dependent_code = '" . $dep['dependentId'] . "' ";
				$sql.="	and month_id = '" . $month_code . "' ";
				$sql.=" and client_plan = '" . $plan['planId'] . "'";
				$p=$X->sql($sql);
				$post=array();
				$post['table_name']="nua_monthly_member_terminations";
				$post['action']="insert";
				$post['month_id']=$month_code;
				$post['client_id']=$plan['clientId'];
				$sql="select id from nua_company where infinity_id = '" . $post['client_id'] . "'";
				$c=$X->sql($sql);
				if (sizeof($c)>0) {
					$post['company_id']=$c[0]['id'];			
				}
				$post['employee_code']=$plan['employeeId'];
					$sql="select id from nua_employee where employee_code = '" . $post['employee_code'] . "'";
				$c=$X->sql($sql);
				if (sizeof($c)>0) {
					$post['employee_id']=$c[0]['id'];			
				} 
				$post['dependent_code']=$dep['dependentId'];
				$post['coverage_level']=$plan['planType'];
				$post['coverage_price']="0.00";
				$post['client_plan']=$plan['planId'];
				$post['eff_dt']=$plan['effectiveDate'];

				$sql="select * from inf_client_employee where clientId = '" . $plan['clientId'] . "' and dependentId = '" . $dep['dependentId'] . "'";
				$e=$X->sql($sql);
		if (sizeof($e)>0) {
				$post['first_name']=$e[0]['first_name'];
				$post['last_name']=$e[0]['last_name'];
				$post['middle_initial']=$e[0]['middle_initial'];
				$post['ssn']=$e[0]['ssn'];
				$post['dob']=$e[0]['dob'];
				$post['gender']=$e[0]['gender'];
		}
		
				if (sizeof($p)>0) {
					$post['id']=$p[0]['id'];	
				}				
				print_r($post);
				$X->post($post);
			}
		}
}

?>

