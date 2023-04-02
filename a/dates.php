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

?>
