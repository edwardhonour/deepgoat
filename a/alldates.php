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

function fix_dates($date) {
//   if (substr($date,4,1)=='-'&&substr($date,7,1)=='-') {
//       $y=substr($date,0,4);
//      $m=substr($date,5,2);
//       $d=substr($date,8,2);
//       $dd=$m . "/" . $d . "/" . $y;
//       return $dd;
//   } else {
//       return $date;
//   }
   if (substr($date,2,1)=='/'&&substr($date,5,1)=='/') {
       $y=substr($date,6,4);
       $m=substr($date,0,2);
       $d=substr($date,3,2);
       $dd=$y . "-" . $m . "-" . $d;
       return $dd;
   } else {
	   if (substr($date,7,1)=='1'||
	       substr($date,7,1)=='0'||
	       substr($date,7,1)=='2'||
	       substr($date,7,1)=='3'||
	       substr($date,7,1)=='4'||
	       substr($date,7,1)=='5'||
	       substr($date,7,1)=='6'||
	       substr($date,7,1)=='7'||
	       substr($date,7,1)=='8'||
	       substr($date,7,1)=='9') {
               $y=substr($date,4,4);
               $m=substr($date,0,2);
               $d=substr($date,2,2);
               $dd=$y . "-" . $m . "-" . $d;
               return $dd;
	   } else {
                return $date;
	   }
   }
}
/*
$sql="select * from nua_employee";
$a=$X->sql($sql);
$iii=0;
foreach ($a as $b) { 
   $post=array();
   $post['table_name']="nua_employee";
   $post['id']=$b['id'];
   $post['action']="insert";
   $post['date_hired']=fix_dates($b['date_hired']);
   $post['date_of_birth']=fix_dates($b['date_of_birth']);
   $post['effective_date']=fix_dates($b['effective_date']);
   print_r($post);
   $X->post($post);
}
$sql="select * from nua_employee_dependent";
$a=$X->sql($sql);
foreach ($a as $b) { 
   $post=array();
   $post['table_name']="nua_employee_dependent";
   $post['id']=$b['id'];
   $post['action']="insert";
   $post['date_of_birth']=fix_dates($b['date_of_birth']);
   print_r($post);
   $X->post($post);
}

*/
$sql="select * from nua_monthly_member_census";
$a=$X->sql($sql);
foreach ($a as $b) { 
   $post=array();
   $post['table_name']="nua_monthly_member_census";
   $post['id']=$b['id'];
   $post['action']="insert";
  // $post['eff_dt']=fix_dates($b['eff_dt']);
  // $post['term_dt']=fix_dates($b['term_dt']);
   $post['dob']=fix_dates($b['dob']);
   if (substr($post['dob'],4,1)=='-'&&substr($post['dob'],7,1)=='-') {
	   $datetime1=date_create_from_format('Y-m-d',$post['dob']);
	   $datetime2=date_create_from_format('Y-m-d','2022-06-10');
	   $interval=(array) date_diff($datetime1,$datetime2);
	   $post['dob_int']=strtotime($post['dob']);
	   $post['age_years']=$interval['y'];
	   $post['age_days']=$interval['days'];

   }
   print_r($post);
   $X->post($post);
   usleep(1000);
}
/*
$sql="select * from nua_monthly_member_additions";
$a=$X->sql($sql);
foreach ($a as $b) { 
   $post=array();
   $post['table_name']="nua_monthly_member_additions";
   $post['id']=$b['id'];
   $post['action']="insert";
   $post['eff_dt']=fix_dates($b['eff_dt']);
   $post['term_dt']=fix_dates($b['term_dt']);
   print_r($post);
   $X->post($post);
}
$sql="select * from nua_monthly_member_terminations";
$a=$X->sql($sql);
foreach ($a as $b) { 
   $post=array();
   $post['table_name']="nua_monthly_member_terminations";
   $post['id']=$b['id'];
   $post['action']="insert";
   $post['eff_dt']=fix_dates($b['eff_dt']);
   $post['term_dt']=fix_dates($b['term_dt']);
   print_r($post);
   $X->post($post);
}

$sql="select * from nua_employee_plan";
$a=$X->sql($sql);
foreach ($a as $b) { 
   $post=array();
   $post['table_name']="nua_employee_plan";
   $post['id']=$b['id'];
   $post['action']="insert";
   $post['coverage_start']=fix_dates($b['coverage_start']);
   $post['coverage_end']=fix_dates($b['coverage_end']);
   $post['deduction_start']=fix_dates($b['deduction_start']);
   $post['deduction_end']=fix_dates($b['deduction_end']);
   $post['effective_date']=fix_dates($b['effective_date']);
   print_r($post);
   $X->post($post);
}
 */
?>
