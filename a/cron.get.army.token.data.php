<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('max_execution_time', '900');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json'); 
require_once('class.XRDB.php');
require_once('class.BSCScan.php');
$X=new XRDB();
$B=new BSCScan();
$l=$B->getArmyTokenPrices();
$list=json_decode($l,true);
foreach($list as $name=>$value) {
		$post=array();
		$post['TABLE_NAME']="BEP20_PRICE";
		$post['ACTION']="insert";
		$post['SYMBOL']=strtoupper($name);
		$post['TIMESTAMP']=time();
		$post['TOKEN_PRICE_USD']=$value;
		$X->post($post);
}
?>


