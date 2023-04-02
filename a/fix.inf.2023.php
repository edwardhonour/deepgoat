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

    $sql="select * from nua_monthly_member_additions where month_id = '2023-01' and plan_type = '*MEDICAL*' and ";
    $sql.=" company_id in (select id from nua_company where org_id = 17) and ";
    $sql.=" employee_code not in (select employee_code from nua_monthly_member_census where month_id = '2023-01' and plan_type = '*MEDICAL*')";
    $c=$X->sql($sql);
    foreach($c as $d) {
	    $s="select * from nua_monthly_member_census where company_id = " . $d['company_id'] . " and employee_id = " . $d['employee_id'] . " and ";
	    $s.=" client_plan = '" . $d['client_plan'] . "'";
	    $q=$X->sql($s);
	    if (sizeof($q)==0) {
          $post=array();
          $post['table_name']="nua_monthly_member_census";
          $post['action']="insert";
          $post['client_id']=$d['client_id'];
  $post['month_id']="2023-01";
  $post['company_id']=$d['company_id'];
  $post['employee_code']=$d['employee_code'];
  $post['dependent_code']=$d['dependent_code'];
  $post['employee_id']=$d['employee_id'];
  $post['first_name']=$d['first_name'];
  $post['last_name']=$d['last_name'];
  $post['middle_initial']=$d['middle_initial'];
  $post['dob']=$d['dob'];
  $post['ssn']=$d['ssn'];
  $post['gender']=$d['gender'];
  $post['eff_dt']=$d['eff_dt'];
  $post['term_dt']=$d['term_dt'];
  $post['client_plan']=$d['client_plan'];
  $post['apa_plan']=$d['apa_plan'];
  $post['coverage_level']=$d['coverage_level'];
  $post['coverage_price']=$d['coverage_price'];
  $post['company_name']=$d['company_name'];
  $post['plan_type']=$d['plan_type'];
  print_r($post);
  $X->post($post);
	    }
    }

?>
