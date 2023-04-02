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

$sql="select distinct primary_member_id, '2022' as month_id, plan_code, coverage_level from apa_claims where primary_member_id <> '' order by 2";
$a=$X->sql($sql);

$iii=0;
foreach ($a as $b) { 
   $sql="select * from apa_claims where primary_member_id = '" . $b['primary_member_id'] . "'";
   $ff=$X->sql($sql);
   $billed=0;
   $approved=0;
   $paid=0;
   $out_of_pocket=0;
   $count=0;
   foreach($ff as $f) {
       $count++;
       $billed+=floatval($f['charged_amount']);
       $approved+=floatval($f['allowed_amount']);
       $paid+=floatval($f['paid_amount']);
       $out_of_pocket+=(floatval($f['allowed_amount']) - floatval($f['paid_amount']));
   } 
   $post=array();
   $post['table_name']="apa_yearly_member_claims";
   $post['action']="insert";
   $post['member_id']=$b['primary_member_id'];
   $post['year_id']=$b['month_id'];
   $post['claim_count']=$count;
   $post['billed']=$billed;
   if (floatval($approved)!=0) {
       $post['approved']=floatval($approved);
   } else {
        $post['approved']="0";
   }
   if (floatval($paid)!=0) {
       $post['paid']=floatval($paid);
   } else {
        $post['paid']="0";
   }
   if (floatval($out_of_pocket)!=0) {
       $post['out_of_pocket']=floatval($out_of_pocket);
   } else {
        $post['out_of_pocket']="0";
   }
   $post['plan_code']=$b['plan_code'];
   $post['coverage_level']=$b['coverage_level'];
   $sql="select * from apa_yearly_member_claims where member_id = '" . $post['member_id'] . "'";
   $h=$X->sql($sql);
   if (sizeof($h)>0) $post['id']=$h[0]['id'];
   print_r($post);
   $iii++;
   $X->post($post);
}

?>
