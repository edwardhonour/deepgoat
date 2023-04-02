<?php
//PRODUCTION
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json'); 
require_once('class.XRDB.php');

if (!isset($_GET['apikey'])) die();
if ($_GET['apikey']!="K14YJFYWC3NAWWU7J2T2G7KBXNDV993H85") die();

$X=new XRDB();
$post=array();
$post['TABLE_NAME']="GOATX_EXCHANGE_DATA";
$post['ACTION']="insert";
$post['BLOCK_TIMESTAMP']=$_GET['time'];
$post['BNB_PRICE_USD']=$_GET['bnb_usd'];
$post['BNB_POOL_GOATX_COUNT']=$_GET['bnb_goatx_count'];
$post['BNB_POOL_BNB_COUNT']=$_GET['bnb_bnb_count'];
$post['BNB_POOL_VALUE']=$_GET['bnb_total_value'];
$post['BNB_POOL_GOATX_PRICE']=$_GET['bnb_goatx_price'];
$post['BUSD_POOL_GOATX_COUNT']=$_GET['busd_goatx_count'];
$post['BUSD_POOL_BUSD_COUNT']=$_GET['busd_busd_count'];
$post['BUSD_POOL_GOATX_PRICE']=$_GET['busd_goatx_price'];
$post['BUSD_POOL_VALUE']=$_GET['busd_total_value'];
$post['TOTAL_POOL_GOATX_COUNT']=$_GET['goatx_total'];
$post['TOTAL_POOL_VALUE']=$_GET['pool_total'];
$post['GOATX_PRICE']=$_GET['goatx_price'];
$post['TOTAL_SUPPLY']=$_GET['total_supply'];
$post['MARKET_CAP']=$_GET['market_cap'];
$post['CIRCULATING_SUPPLY']=$_GET['circulating_supply'];
$post['WALLET_COUNT']=$_GET['wallet_count'];
$post['STAKED_COUNT']=$_GET['staked_count'];
$X->post($post);
?>
{"value":"done"}


