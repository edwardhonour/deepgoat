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
   if (substr($date,4,1)=='-'&&substr($date,7,1)=='-') {
       $y=substr($date,0,4);
       $m=substr($date,5,2);
       $d=substr($date,8,2);
       $dd=$m . "/" . $d . "/" . $y;
       return $dd;
   } else {
       return $date;
   }
}
$sql="select distinct employee_id, client_plan, coverage_level, coverage_price, plan_type from nua_monthly_member_census where employee_id <> 0 order by employee_id, plan_type";
$a=$X->sql($sql);
$iii=0;
foreach ($a as $b) { 
	$sql="select min(month_id) as min_month from nua_monthly_member_census where ";
	$sql.=" employee_id = " . $b['employee_id'] . " and client_plan = '" . $b['client_plan'] . "' ";
	$sql.=" and coverage_level = '" . $b['coverage_level'] . "' ";
	$sql.=" and coverage_price = '" . $b['coverage_price'] . "' ";
	$z=$X->sql($sql);
        $min_month=$z[0]['min_month'];

	$sql="select max(month_id) as max_month from nua_monthly_member_census where ";
	$sql.=" employee_id = " . $b['employee_id'] . " and client_plan = '" . $b['client_plan'] . "' ";
	$sql.=" and coverage_level = '" . $b['coverage_level'] . "' ";
	$sql.=" and coverage_price = '" . $b['coverage_price'] . "' ";
	$z=$X->sql($sql);
        $max_month=$z[0]['max_month'];

	if ($min_month=='2021-01') $start_date="01/01/2021";
	if ($min_month=='2021-02') $start_date="02/01/2021";
	if ($min_month=='2021-03') $start_date="03/01/2021";
	if ($min_month=='2021-04') $start_date="04/01/2021";
	if ($min_month=='2021-05') $start_date="05/01/2021";
	if ($min_month=='2021-06') $start_date="06/01/2021";
	if ($min_month=='2021-07') $start_date="07/01/2021";
	if ($min_month=='2021-08') $start_date="08/01/2021";
	if ($min_month=='2021-09') $start_date="09/01/2021";
	if ($min_month=='2021-10') $start_date="10/01/2021";
	if ($min_month=='2021-11') $start_date="11/01/2021";
	if ($min_month=='2021-12') $start_date="12/01/2021";
	if ($min_month=='2022-01') $start_date="01/01/2022";
	if ($min_month=='2022-02') $start_date="02/01/2022";
	if ($min_month=='2022-03') $start_date="03/01/2022";
	if ($min_month=='2022-04') $start_date="04/01/2022";
	if ($min_month=='2022-05') $start_date="05/01/2022";
	if ($min_month=='2022-06') $start_date="06/01/2022";
	if ($min_month=='2022-07') $start_date="07/01/2022";
	if ($min_month=='2022-08') $start_date="08/01/2022";
	if ($min_month=='2022-09') $start_date="09/01/2022";
	if ($min_month=='2022-10') $start_date="10/01/2022";
	if ($min_month=='2022-11') $start_date="11/01/2022";
	if ($min_month=='2022-12') $start_date="12/01/2022";

	if ($max_month=='2021-01') $end_date="01/31/2021";
	if ($max_month=='2021-02') $end_date="02/28/2021";
	if ($max_month=='2021-03') $end_date="03/31/2021";
	if ($max_month=='2021-04') $end_date="04/30/2021";
	if ($max_month=='2021-05') $end_date="05/31/2021";
	if ($max_month=='2021-06') $end_date="06/30/2021";
	if ($max_month=='2021-07') $end_date="07/31/2021";
	if ($max_month=='2021-08') $end_date="08/31/2021";
	if ($max_month=='2021-09') $end_date="09/30/2021";
	if ($max_month=='2021-10') $end_date="10/31/2021";
	if ($max_month=='2021-11') $end_date="11/30/2021";
	if ($max_month=='2021-12') $end_date="12/31/2021";
	if ($max_month=='2022-01') $end_date="01/31/2022";
	if ($max_month=='2022-02') $end_date="02/28/2022";
	if ($max_month=='2022-03') $end_date="03/31/2022";
	if ($max_month=='2022-04') $end_date="04/30/2022";
	if ($max_month=='2022-05') $end_date="05/31/2022";
	if ($max_month=='2022-06') $end_date="";
	if ($max_month=='2022-07') $end_date="";
	if ($max_month=='2022-08') $end_date="";
	if ($max_month=='2022-09') $end_date="";
	if ($max_month=='2022-10') $end_date="";
	if ($max_month=='2022-11') $end_date="";
	if ($max_month=='2022-12') $end_date="";

	$sql="select * from nua_employee_plan where employee_id = " . $b['employee_id'] . " and plan_id = '" . $b['client_plan'] . "' ";
        $sql.=" and plan_type = '" . $b['coverage_level'] . "'";
        $y=$X->sql($sql);
        $post=array();
        $post['table_name']="nua_employee_plan";
        $post['plan_code_type']=$b['plan_type'];
        $post['action']="insert";
        $post['plan_id']=$b['client_plan'];
        $post['plan_type']=$b['coverage_level'];
        $post['peo_premium']=$b['coverage_price'];
        $post['employee_id']=$b['employee_id'];
        $post['coverage_start']=$start_date;
        $post['coverage_end']=$end_date;
        if (sizeof($y)>0) $post['id']=$y[0]['id'];
	print_r($post);
   $X->post($post);
}

?>
