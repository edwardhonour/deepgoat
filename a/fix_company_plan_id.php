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

$sql="select * from nua_company where id not in (select id from nua_company where org_id = 17) order by id";
$census=$X->sql($sql);
foreach($census as $c) {
print_r($c);
        $company_id=$c['id'];
        $sql="select count(*) as c from nua_company_plan where plan_type = '*MEDICAL*' and company_id = " . $company_id;
        $p=$X->sql($sql);
        if ($p[0]['c']!='0') { 
           $medical="Yes";
        } else {
           $medical="No";
        }
        $sql="select count(*) as c from nua_company_plan where plan_type = '*DENTAL*' and company_id = " . $company_id;
        $p=$X->sql($sql);
        if ($p[0]['c']!='0') { 
           $dental="Yes";
        } else {
           $dental="No";
        }
        $sql="select count(*) as c from nua_company_plan where plan_type = '*VISION*' and company_id = " . $company_id;
        $p=$X->sql($sql);
        if ($p[0]['c']!='0') { 
           $vision="Yes";
        } else {
           $vision="No";
        }
        $post=array();
        $post['table_name']='nua_company';
        $post['id']=$c['id'];
        $post['medical']=$medical;
        $post['dental']=$dental;
        $post['vision']=$vision;
        print_r($post);
        $X->post($post);
}

?>

