<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('memory_limit',-1);
ini_set('max_execution_time', 900000);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');

require_once('class.XRDB.php');
$X=new XRDB();

$sql="select id, company_name, org_id,  broker_email from nua_company where org_id <> 17 order by company_name"; 
$a=$X->sql($sql);
foreach ($a as $b) { 
    echo $b['id'] . "," . $b['org_id'] . "," . str_replace(",","",$b['company_name']) . "," . $b['broker_email'] . "<br>";
}

?>
