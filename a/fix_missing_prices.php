<?php

//--
// Weekly Cron Job for InfinityHR.
//
// Gets new clients, available plans, and employees.
//
//
//--
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');
ini_set('max_execution_time', 900000);

require_once('class.XRDB.php');
$X=new XRDB();

$sql="select * from nua_monthly_member_census where month_id = '2022-11' and company_id not in (select id from nua_company where org_id = 17) order by id";
$census=$X->sql($sql);
foreach($census as $c) {
        $company_id=$c['company_id'];
        $client_plan=$c['client_plan'];
	$sql="select * from nua_company_plan where plan_code = '" . $client_plan . "' and '2022-11' between start_month_id and ";
	$sql.=" end_month_id and company_id = " . $company_id;
        $p=$X->sql($sql);
if (sizeof($p)>0) {
        $plan=$p[0];
        print_r($plan);
        $post=array();
        $post['table_name']='nua_monthly_member_census';
        $post['id']=$c['id'];
        $post['client_plan']=$c['client_plan'];
        $post['coverage_level']=$c['coverage_level'];
        if ($c['coverage_level']=="EE") $post['coverage_price']=$plan['ee_price'];
        if ($c['coverage_level']=="SI") $post['coverage_price']=$plan['ee_price'];
        if ($c['coverage_level']=="EC") $post['coverage_price']=$plan['eec_price'];
        if ($c['coverage_level']=="EC2") $post['coverage_price']=$plan['eec_price'];
        if ($c['coverage_level']=="ES") $post['coverage_price']=$plan['ees_price'];
        if ($c['coverage_level']=="FAM") $post['coverage_price']=$plan['fam_price'];
        if ($c['coverage_level']=="FA") $post['coverage_price']=$plan['fam_price'];
        print_r($post);
        $X->post($post);
}
}

?>

