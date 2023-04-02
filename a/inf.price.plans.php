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

    $sql="select distinct company_id, client_plan, coverage_level, coverage_price ";
    $sql.="  from nua_monthly_member_census where month_id = '2023-01' and ";
    $sql.= " company_id in (select id from nua_company where org_id = 17) order by company_id, client_plan, coverage_price";
    $c=$X->sql($sql);
    foreach($c as $d) {
	    $sql="select * from nua_company_plan where company_id = " . $d['company_id'] . " and plan_code = '" . $d['client_plan'] . "'";
            $q=$X->sql($sql);
            $post=array();
	    $post['action']="insert";
	    $post['table_name']="nua_company_plan";
	    $post['plan_code']=$d['client_plan'];
	    $post['company_id']=$d['company_id'];
	    $post['start_month_id']="2023-01";
	    $post['end_month_id']="2023-12";
	    if (sizeof($q)!=0) {
                 $post['id']=$q[0]['id'];
	    }
            if ($d['coverage_level']=='EE') $post['ee_price']=$d['coverage_price'];
            if ($d['coverage_level']=='ES') $post['ees_price']=$d['coverage_price'];
            if ($d['coverage_level']=='EC') $post['eec_price']=$d['coverage_price'];
            if ($d['coverage_level']=='FAM') $post['fam_price']=$d['coverage_price'];
            $X->post($post);

              print_r($post);
	      $X->post($post);
         }

?>
