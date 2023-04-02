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
//header('Content-Type: application/json'); 
require_once('class.XRDB.php');
require_once('class.ETHScan.php');
$X=new XRDB();
$B=new ETHScan();
 
$sql = "select * from nua_monthly_member_census where month_id = '2022-01' and client_plan in ('GUARDHIGH','GUARDLOW') order by client_plan, last_name,first_name";
$z=$X->sql($sql);
foreach ($z as $a) {
    echo $a['last_name'] . ",";
    echo $a['first_name'] . ",";
    echo $a['client_plan'] . ",";
    echo $a['coverage_level'] . "<br>";
}

?>

