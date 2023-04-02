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
 
$sql = "select * from inf_client where clientId not in (select infinity_id from nua_company where org_id = 17)";
$z=$X->sql($sql);
foreach ($z as $a) {
    print_r($a);
    $post=array();
    $post['table_name']="nua_company";
    $post['action']="insert";
    $post['org_id']=17;
    $post['user_id']=1;
    $post['company_name']=$a['clientName'];
    $post['infinity_id']=$a['clientId'];
    $X->post($post);
 
}
?>

