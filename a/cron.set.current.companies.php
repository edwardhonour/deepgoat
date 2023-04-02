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

function llog($msg) {
   $X=new XRDB();	
   $p=array();
   $p['table_name']="nua_log";
   $p['action']="insert";
   $p['msg']=$msg;
   $X->post($p);
	
}
llog("Monthly Census Started");

$months=array();
$date=date_create();
$m=date_format($date,'Y-m');
array_push($months,$m);

$last="X";

foreach ($months as $month) {
	
		$sql="select id from nua_company where status = 'enrolled' order by 1";
		$clients=$X->sql($sql);
		
		foreach ($clients as $client) {

		   $company_id=$client['id'];
		   $sql="select count(*) as c from nua_monthly_member_terminations where company_id = " . $company_id . " and month_id = '" . $month . "' and dependent_code =''";
		   $t=$X->sql($sql);
		   $terminations=$t[0]['c'];
		   $sql="select count(*) as c from nua_monthly_member_additions where company_id = " . $company_id . " and month_id = '" . $month . "' and dependent_code =''";
		   $t=$X->sql($sql);
		   $additions=$t[0]['c'];
		   
		   $sql="select count(*) as c from nua_monthly_member_census where company_id = " . $company_id . " and month_id = '" . $month . "' and dependent_code =''";
		   $sql.=" and client_plan in (select planId from inf_client_plan where inf_client_plan.clientId  =  nua_monthly_member_census.client_id and plan_type = 'MEDICAL') ";
		   $t=$X->sql($sql);
		   $medical=$t[0]['c'];		   
		   $sql="select count(*) as c from nua_monthly_member_census where company_id = " . $company_id . " and month_id = '" . $month . "' and dependent_code =''";
		   $sql.=" and client_plan in (select planId from inf_client_plan where inf_client_plan.clientId  =  nua_monthly_member_census.client_id and plan_type = 'DENTAL') ";
		   $t=$X->sql($sql);
		   $dental=$t[0]['c'];

		   $sql="select count(*) as c from nua_monthly_member_census where company_id = " . $company_id . " and month_id = '" . $month . "' and dependent_code =''";
		   $sql.=" and client_plan in (select planId from inf_client_plan where inf_client_plan.clientId  =  nua_monthly_member_census.client_id and plan_type = 'VISION') ";
		   $t=$X->sql($sql);
		   $vision=$t[0]['c'];				   

		   $sql="select count(*) as c from nua_monthly_member_census where company_id = " . $company_id . " and month_id = '" . $month . "' and dependent_code =''";
		   $sql.=" and client_plan in (select planId from inf_client_plan where inf_client_plan.clientId  =  nua_monthly_member_census.client_id and plan_type = 'ADD') ";
		   $t=$X->sql($sql);
		   $add=$t[0]['c'];				   
		   $sql="select distinct employee_id from nua_monthly_member_census where company_id = " . $company_id . " and month_id = '" . $month . "' and dependent_code =''";
		   $t=$X->sql($sql);
		   $j=0;
		   foreach($t as $u) {
		       $j++;	   
		   }
		   $insured_employees=$j;	

		   $sql="select distinct employee_id, dependent_code from nua_monthly_member_census where company_id = " . $company_id . " and month_id = '" . $month . "'";
		   $t=$X->sql($sql);
		   $j=0;
		   foreach($t as $u) {
		       $j++;	   
		   }
		   $insured_lives=$j;	

		   $sql="select * from nua_monthly_member_census where company_id = " . $company_id . " and month_id = '" . $month . "' and dependent_code =''";
		   $t=$X->sql($sql);
		   $j=0;
		   foreach($t as $u) {
		       $j+=floatval($u['coverage_price']);
		   }
		   $monthly_total=number_format($j,2);
		   
		   $post=array();
		   $post['table_name']="nua_company";
		   $post['action']="insert";
		   $post['id']=$company_id;
		   $post['medical_count']=$medical;
		   $post['dental_count']=$dental;		   
		   $post['vision_count']=$vision;
		   $post['add_count']=$add;
//		   $post['insured_employees']=$insured_employees;
//		   $post['insured_lives']=$insured_lives;		   
		   $post['monthly_total']=$monthly_total;
		   $X->post($post);
		   print_r($post);
                   $sql="update nua_company set insured_employees = '" . $insured_employees . "'  where id = " . $company_id;
                   echo $sql;
                   $X->execute($sql);
                   $sql="update nua_company set insured_lives = '" . $insured_lives . "' where id = " . $company_id;
                   echo $sql;
                   $X->execute($sql);


	} // CLIENT
} //MONTH
?>

