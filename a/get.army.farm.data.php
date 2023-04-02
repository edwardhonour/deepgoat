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
$l=$B->getArmyFarms();
$dateTime = time();
$list=json_decode($l,true);
foreach($list as $n) {
	if (isset($n['extra']['lpAddress'])) {
		$post=array();
		$post['TABLE_NAME']="BEP20_TOKEN_FARM";
		$post['ACTION']="insert";		
		$sql="SELECT ID FROM BEP20_TOKEN_FARM WHERE FARM_ID = '" . strtolower($n['id']) . "'";
		$x=$X->sql($sql);
		if (sizeof($x)>0) {
				$post['ID']=$x[0]['ID'];
		}
		$post['TIMESTAMP']=$dateTime;
		$post['FARM_ID']=$n['id'];
		$post['FARM_NAME']=$n['name'];
		$post['TOKEN']=strtoupper($n['token']);
		if (isset($n['platform'])) $post['PLATFORM']=$n['platform']; else $post['PLATFORM']="";
		$post['LP_ADDRESS']=strtolower($n['extra']['lpAddress']);
		if (isset($n['tvl']['amount'])) $post['TVL_AMOUNT']=$n['tvl']['amount'];
		if (isset($n['tvl']['usd'])) $post['TVL_USD']=$n['tvl']['usd'];
		if (isset($n['yield']['apy'])) $post['YIELD_APY']=$n['yield']['apy'];
		if (isset($n['yield']['daily'])) $post['YIELD_DAILY']=$n['yield']['daily'];
		try {
			$X->post($post);
		} catch(Exception $e) {
				echo 'Message: ' .$e->getMessage();			
				print_r($post);
				print_r($n);
		}
	}
}
?>


