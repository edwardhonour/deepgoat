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

$sql="select distinct clientId from inf_client_employee_plan"; 
$a=$X->sql($sql);
foreach ($a as $b) { 

    $sql="select * from nua_company where infinity_id = '" . $b['clientId'] . "'";
    $z=$X->sql($sql);
if (sizeof($z)==0) {
//echo $sql;
//die();
}
    $company=$z[0];

    $sql="select distinct planId from inf_client_employee_plan where clientId = '" . $b['clientId'] . "'";
    $c=$X->sql($sql);
    foreach($c as $d) {
         $sql="select * from inf_client_plan where clientId = '" . $b['clientId'] . "' and planId = '" . $d['planId'] . "'";
         $e=$X->sql($sql);
         if ($e[0]['active']=="Y") {
              $post=array();
              $post['table_name']="nua_company_plan";
              $post['action']="insert";
              $post['plan_code']=$d['planId'];
              if ($post['plan_code']=="GUARDHIGH") $post['APA_CODE']="GUARDHIGH";
              if ($post['plan_code']=="GUARDLOW") $post['APA_CODE']="GUARDLOW";
              if ($post['plan_code']=="VSP") $post['APA_CODE']="VSP";
              if ($post['plan_code']=="VISION") $post['APA_CODE']="VSP";
              
              $sql="select * from nua_company_plan where plan_code = '" . $d['planId'] . "' and company_id = " . $company['id'];
              $f=$X->sql($sql);
              if (sizeof($f)>0) {
                  $post['id']=$f[0]['id'];
              }
              $post['plan_type']=$e[0]['plan_type'];
              $sql="select peoPremium from inf_client_employee_plan where clientId = '" . $b['clientId'] . "' and planId = '" . $d['planId'] . "' and planType = 'EE' order by coverageStart";
              $h=$X->sql($sql);
              $ee="0.00";
              foreach($h as $i) {
                  $ee=$i['peoPremium'];
              }
              $sql="select peoPremium from inf_client_employee_plan where clientId = '" . $b['clientId'] . "' and planId = '" . $d['planId'] . "' and planType = 'ES' order by coverageStart";
              $h=$X->sql($sql);
              $es="0.00";
              foreach($h as $i) {
                  $es=$i['peoPremium'];
              }
              $sql="select peoPremium from inf_client_employee_plan where clientId = '" . $b['clientId'] . "' and planId = '" . $d['planId'] . "' and planType in ('EC','EC2','ECN') order by coverageStart";
              $h=$X->sql($sql);
              $ec="0.00";
              foreach($h as $i) {
                  $ec=$i['peoPremium'];
              }
              $sql="select peoPremium from inf_client_employee_plan where clientId = '" . $b['clientId'] . "' and planId = '" . $d['planId'] . "' and planType = 'FAM' order by coverageStart";
              $h=$X->sql($sql);
              $fam="0.00";
              foreach($h as $i) {
                  $fam=$i['peoPremium'];
              }
              $sql="select peoPremium from inf_client_employee_plan where clientId = '" . $b['clientId'] . "' and planId = '" . $d['planId'] . "' and planType = '' order by coverageStart";
              $h=$X->sql($sql);
              $blank="0.00";
              foreach($h as $i) {
                  $blank=$i['peoPremium'];
              }
              if (($ee!="0.00"||$es!="0.00"||$ec!="0.00"||$fam!="0.00")) {
              $post['ee_price']=$ee;
              $post['ees_price']=$es;
              $post['eec_price']=$ec;
              $post['fam_price']=$fam;
              } else {
              $post['ee_price']=$blank;
              $post['ees_price']=$blank;
              $post['eec_price']=$blank;
              $post['fam_price']=$blank;
              }
              $post['company_id']=$company['id'];
              $post['clientId']=$b['clientId'];
	      $sql="select * from nua_company_plan where company_id = " . $post['company_id'] . " and plan_code = '" . $post['plan_code'] . "'";
	      $zzz=$X->sql($sql);
              print_r($post);
	      $X->post($post);
         }
    }
}

?>
