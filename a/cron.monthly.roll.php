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
$from_month_id="2023-01";
$to_month_id="2023-02";

$sql="select * from nua_monthly_member_census where company_id in (select id from nua_company where infinity_id <> '') and ";
$sql.=" month_id = '" . $from_month_id . "' order by id";
$a=$X->sql($sql);
foreach ($a as $b) { 
    $post=array();
    $post['table_name']="nua_monthly_member_census";
    $post['action']="insert";
    foreach($b as $name=>$value) {
          if ($name!="id") $post[$name]=$value;
    }
    $post['month_id']=$to_month_id;
    $post['billed_month_id']="";
    print_r($post);
    $X->post($post);
}

?>
