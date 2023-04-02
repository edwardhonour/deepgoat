<?php
//--
//-- Get ERC20 and BEP20 token assets for a wallet.
//--
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('max_execution_time',800);
ini_set('memory_limit', '2048M');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json'); 
require_once('class.XRDB.php');
require_once('class.ETHScan.php');
$X=new XRDB();
$B=new ETHScan();
 
$sql = "select distinct planId, plan_type from inf_client_plan where plan_type <> ''";
$z=$X->sql($sql);
foreach ($z as $a) {
    print_r($a);
         $sql="update nua_monthly_member_census set plan_type = '*" . $a['plan_type'] . "*' where client_plan = '" . $a['planId'] . "'";
         $X->execute($sql);
         $sql="update nua_monthly_member_additions set plan_type = '*" . $a['plan_type'] . "*' where client_plan = '" . $a['planId'] . "'";
         $X->execute($sql);
         $sql="update nua_monthly_member_terminations set plan_type = '*" . $a['plan_type'] . "*' where client_plan = '" . $a['planId'] . "'";
         $X->execute($sql);
    } 
?>

