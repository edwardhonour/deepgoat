<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('max_execution_time', '9000');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
//header('Content-Type: application/json'); 
require_once('class.XRDB.php');
require_once('class.BSCScan.php');
$X=new XRDB();
$B=new BSCScan();

$sql="SELECT * FROM ERC20_TOKEN ORDER BY SYMBOL";
echo $sql;

$list=$X->sql($sql);
foreach($list as $l) {
		$post=array();
		$post['TABLE_NAME']="ERC20_TOKEN";
		$post['ACTION']="insert";
		$post['ID']=$l['ID'];
		$result=$B->getERC20TokenInfo(strtolower($l['CONTRACT_ADDRESS']));		
		$r=json_decode($result,true);
		$post['DECIMALS']=$r['result'][0]['divisor'];
		$post['TOTAL_SUPPLY']=$r['result'][0]['totalSupply'];
		$post['BLUE_CHECKMARK']=$r['result'][0]['blueCheckmark'];
		//$post['DESCRIPTION']=$r['result'][0]['description'];
		$post['WEBSITE']=$r['result'][0]['website'];
		$post['EMAIL']=$r['result'][0]['email'];
		$post['BLOG']=$r['result'][0]['blog'];
		$post['REDDIT']=$r['result'][0]['reddit'];
		$post['SLACK']=$r['result'][0]['slack'];
		$post['FACEBOOK']=$r['result'][0]['facebook'];
		$post['TWITTER']=$r['result'][0]['twitter'];
		$post['BITCOINTALK']=$r['result'][0]['bitcointalk'];
		$post['GITHUB']=$r['result'][0]['github'];
		$post['TELEGRAM']=$r['result'][0]['telegram'];
		$post['WECHAT']=$r['result'][0]['wechat'];
		$post['LINKEDIN']=$r['result'][0]['linkedin'];
		$post['DISCORD']=$r['result'][0]['discord'];
		$post['WHITEPAPER']=$r['result'][0]['whitepaper'];
		$post['TOKEN_PRICE_USD']=$r['result'][0]['tokenPriceUSD'];
		$X->post($post);
		$post=array();
		$post['TABLE_NAME']="ERC20_TOKEN_PRICE";
		$post['ACTION']="insert";
		$post['CONTRACT_ADDRESS']=strtolower($l['CONTRACT_ADDRESS']);
		$post['SYMBOL']=$l['SYMBOL'];
		$post['TOTAL_SUPPLY']=$r['result'][0]['totalSupply'];
		$post['TOKEN_PRICE_USD']=$r['result'][0]['tokenPriceUSD'];
		$X->post($post);
		echo $post['SYMBOL'] . "<br>";
		usleep(600000);
}

?>

