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
require_once('class.XRDB.php');
require_once('class.ETHScan.php');
$X=new XRDB();
$B=new ETHScan();
 
$sql = "select * from nua_company where invoicing = 'Y' and id not in (select company_id from nua_census) order by company_name";
$z=$X->sql($sql);
foreach ($z as $a) {
echo $a['id'] . "," . $a['company_name'] . "," . $a['org_id'] .  "<br>";
}
?>

