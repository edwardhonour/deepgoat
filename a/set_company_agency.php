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
$from_month_id="2022-11";
$to_month_id="2022-11";

$sql="select distinct company_id from nua_monthly_member_census where month_id = '" . $from_month_id . "' ";
$sql.=" and company_id in (select id from nua_company where org_id = 17)";
$a=$X->sql($sql);
foreach ($a as $b) { 

$sql="select * from nua_company where id = " . $b['company_id'];
$j=$X->sql($sql);

    $post=array();
    $post['table_name']="nua_agency_company";
    $post['action']="insert";
    $post['agency_id']=53;
    $post['company_id']=$b['company_id'];
    $post['company_name']=$j[0]['company_name'];
    $post['plan_type']="*DENTAL*";
    $post['commission_rate']=0.12;
    $post['commission_type']="PCT";
    $sql="select * from nua_agency_company where company_id = " . $b['company_id'];
    $sql.=" and agency_id = " . $post['agency_id'];
    $sql.=" and plan_type = '*DENTAL*'";
    $k=$X->sql($sql);
    if (sizeof($k)==0) {
	    $X->post($post);
	    print_r($post);
    }
    $post['plan_type']="*VISION*";
    $post['commission_rate']=0.05;
    $sql="select * from nua_agency_company where company_id = " . $b['company_id'];
    $sql.=" and agency_id = " . $post['agency_id'];
    $sql.=" and plan_type = '*VISION*'";
    $k=$X->sql($sql);
    if (sizeof($k)==0) {
	    $X->post($post);
	    print_r($post);
    }
    $post['plan_type']="*ADD*";
    $post['commission_rate']=0.12;
    $sql="select * from nua_agency_company where company_id = " . $b['company_id'];
    $sql.=" and agency_id = " . $post['agency_id'];
    $sql.=" and plan_type = '*ADD*'";
    $k=$X->sql($sql);
    if (sizeof($k)==0) {
	    $X->post($post);
	    print_r($post);
    }
    $post['plan_type']="*LIFE*";
    $post['commission_rate']=0.12;
    $sql="select * from nua_agency_company where company_id = " . $b['company_id'];
    $sql.=" and agency_id = " . $post['agency_id'];
    $sql.=" and plan_type = '*LIFE*'";
    $k=$X->sql($sql);
    if (sizeof($k)==0) {
	    $X->post($post);
	    print_r($post);
    }
}

$sql="select distinct company_id from nua_monthly_member_census where month_id = '" . $from_month_id . "' ";
$sql.=" and plan_type = '*MEDICAL*' and company_id in (select id from nua_company where org_id = 17)";
$a=$X->sql($sql);
foreach ($a as $b) { 

$sql="select * from nua_company where id = " . $b['company_id'];
$j=$X->sql($sql);

    $post=array();
    $post['table_name']="nua_agency_company";
    $post['action']="insert";
    $post['agency_id']=52;
    $post['company_id']=$b['company_id'];
    $post['company_name']=$j[0]['company_name'];
    $post['plan_type']="*MEDICAL*";
    $post['commission_rate']=30.00;
    $post['commission_type']="FLAT";
    $sql="select * from nua_agency_company where company_id = " . $b['company_id'];
    $sql.=" and agency_id = " . $post['agency_id'];
    $sql.=" and plan_type = '*MEDICAL*'";
    $k=$X->sql($sql);
    if (sizeof($k)==0) {
	    $X->post($post);
	    print_r($post);
    }
}

?>
