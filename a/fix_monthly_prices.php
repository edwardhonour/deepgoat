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

$sql="select distinct company_id from nua_company_plan where company_id not in (select id from nua_company where org_id = 17) and start_month_id = '2023-01' order by 1";
$a=$X->sql($sql);
foreach($a as $b) {
     $sql="select distinct client_plan, coverage_level from nua_monthly_member_census where month_id = '2023-01' and company_id = " . $b['company_id'];
     $t=$X->sql($sql);
     foreach ($t as $r) {
	     $s="select * from nua_company_plan where plan_code = '" . $r['client_plan'] . "' and '2023-01' between ";
	     $s.=" start_month_id and end_month_id";
	     $z=$X->sql($s);
	     if (sizeof($z)==0) {
		    echo "Not found";
		    print_r($b);
		    print_r($r);
		    echo $sql;
		    die();
	     } 
	     if ($r['coverage_level']=='EE') {
		     $sql="update nua_monthly_member_census set coverage_price = '" . $z[0]['ee_price'] . "' where ";
		     $sql.="company_id = " . $b['company_id'] . " and client_plan = '" . $r['client_plan'] . "' and ";
		     $sql.="coverage_level = 'EE'";
		     echo $sql;
		     $X->execute($sql);
             }
	     if ($r['coverage_level']=='EC') {
		     $sql="update nua_monthly_member_census set coverage_price = '" . $z[0]['eec_price'] . "' where ";
		     $sql.="company_id = " . $b['company_id'] . " and client_plan = '" . $r['client_plan'] . "' and ";
		     $sql.="coverage_level = 'EC'";
		     echo $sql;
		     $X->execute($sql);
             }
	     if ($r['coverage_level']=='ES') {
		     $sql="update nua_monthly_member_census set coverage_price = '" . $z[0]['ees_price'] . "' where ";
		     $sql.="company_id = " . $b['company_id'] . " and client_plan = '" . $r['client_plan'] . "' and ";
		     $sql.="coverage_level = 'ES'";
		     echo $sql;
		     $X->execute($sql);
             }
	     if ($r['coverage_level']=='FAM') {
		     $sql="update nua_monthly_member_census set coverage_price = '" . $z[0]['fam_price'] . "' where ";
		     $sql.="company_id = " . $b['company_id'] . " and client_plan = '" . $r['client_plan'] . "' and ";
		     $sql.="coverage_level = 'FAM'";
		     echo $sql;
		     $X->execute($sql);
             }
     }
}

?>
