<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('max_execution_time',800);
ini_set('memory_limit', '2048M');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
//header('Content-Type: application/text'); 
require_once('class.XRDB.php');
require_once('class.ETHScan.php');
$X=new XRDB();
$B=new ETHScan();
 
$sql = "select planId, planDescription, count(*) as c from inf_client_plan where active = 'Y' group by planId, planDescription order by 1";
$z=$X->sql($sql);
foreach ($z as $a) {
 echo $a['planId'] . "," . $a['planDescription'] . "," . $a['c'] . "<br>";
}
?>

