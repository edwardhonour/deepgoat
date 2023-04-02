<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
ini_set('memory_limit', '-1');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');
ini_set('max_execution_time', 900000);

require_once('class.XRDB.php');
$X=new XRDB();

$sql="select * from inf_client_employee where dependentId = '' and clientId in (select infinity_id from nua_company, nua_company_invoice where nua_company_invoice.company_id = nua_company.id and nua_company.org_id = 17) order by clientId, employeeId";
$employees=$X->sql($sql);
$count=sizeof($employees);
$i=0;
foreach ($employees as $employee) {
     $i++;
     $post=array();
	 $post['table_name']="nua_employee";
	 $post['action']="insert";
	 $sql="select id from nua_employee where company_code = '" . $employee['clientId'] . "' and employee_code = '" . $employee['employeeId'] . "'";
	 $z=$X->sql($sql);
	 if (sizeof($z)>0) {
	     $post['id']=$z[0]['id'];
		 $employee_id=$z[0]['id'];
	 }
	 
	 $sql="select * from nua_company where infinity_id = '" . $employee['clientId'] . "'";
	 $z=$X->sql($sql);
	 $company=$z[0];
	 
	 $post['user_id']=1;
	 $post['org_id']=17;
	 $post['company_id']=$company['id'];
	 $post['first_name']=$employee['first_name'];
	 $post['last_name']=$employee['last_name'];
	 $post['middle_name']=$employee['middle_initial'];
	 $post['employee_code']=$employee['employeeId'];
	 $post['company_code']=$employee['clientId'];
	 $post['email']=$employee['email_address'];
	 $post['date_hired']=$employee['peo_start_date'];
	 $post['marital_status']=$employee['marital_status'];
	 $post['gender']=$employee['gender'];
	 $post['date_of_birth']=$employee['dob'];
	 $post['address']=$employee['address'];
	 $post['state']=$employee['state'];
	 $post['city']=$employee['city'];
	 $post['suite']=$employee['address_2'];
	 $post['zip']=$employee['zipcode'];
	 $post['phone']=$employee['home_phone'];
	 $post['phone_mobile']=$employee['mobile_phone'];
	 $post['employee_status']=$employee['active'];
	 $post['how_record_was_added']="API";
	 $post['employee_name']=$employee['last_name'] . ", " . $employee['first_name'];
	 $post['work_status']=$employee['active'];
	 $post['social_security_number']=$employee['ssn'];
     $employee_id=$X->post($post);
     $post['i']=$i;
     $post['of']=$count;
     $post['pct']=($i/$count)*100;
     print_r($post);

	 $sql="select * from inf_client_employee where employeeId = '" . $employee['employeeId'] . "' and dependentId <> '' order by dependentId";
	 $dependents=$X->sql($sql);
	 foreach ($dependents as $d) {
				$p=array();
				$p['table_name']="nua_employee_dependent";
				$p['action']="insert";
				$p['user_id']=1;
				$p['employee_id']=$employee_id;
				$p['company_id']=$company['id'];
				$p['first_name']=$d['first_name'];
				$p['last_name']=$d['last_name'];
				$p['middle_name']=$d['middle_initial'];
				$p['gender']=$d['gender'];
				$p['date_of_birth']=$d['dob'];
				$p['social_security_number']=$d['ssn'];
				$p['relationship']=$d['relationship'];
				$p['company_code']=$d['clientId'];
				$p['employee_code']=$d['employeeId'];
				$p['dependent_id']=$d['dependentId'];
				$p['relation_type']=$d['relation_type'];
				$p['relation_to_insured']=$d['relation_to_insured'];
                print_r($p);
				$X->post($p);
	}
}
?>


