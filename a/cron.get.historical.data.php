<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
ini_set("max_execution_time", -1);
require_once('class.XRDB.php');
require_once('class.BSCScan.php');

$X=new XRDB();
$B=new BSCScan();

$sql="SELECT CONTRACT_ADDRESS, SYMBOL FROM BEP20_TOKEN ORDER BY ID";
$r=$X->sql($sql);

$count=0;
foreach($r as $j) {
	$contract=$j['CONTRACT_ADDRESS'];
	$prices=$B->getGeckoHistoricalPrices($contract);
	if (!isset($prices['error'])) {
		$p=json_decode($prices,true);
                if (isset($p['prices'])) {
                echo $j['SYMBOL'];
		foreach ($p['prices'] as $q) {
				$timestamp=$q[0] / 1000;
				$price=$q[1];
				$post=array();
				$post['TABLE_NAME']="GECKO_HISTORICAL_PRICES";
				$post['ACTION']="insert";
				$sql="SELECT ID FROM GECKO_HISTORICAL_PRICES WHERE symbol = '" . $j['SYMBOL'] . "' AND timestamp = " . $timestamp;
				$m=$X->sql($sql);
				if (sizeof($m)>0) {
					$post['ID']=$m[0]['ID'];
				} else {
                                        echo "ADDED " . $timestamp . "|";
					$post['contract_address']=$j['CONTRACT_ADDRESS'];
					$post['symbol']=$j['SYMBOL'];
					$post['timestamp']=$timestamp;
					$post['price']=$price;
					$X->post($post);
				}
		} 
                } else {
                      echo $j['SYMBOL'];
                      print_r($p);
                }
	}
	sleep(1);
}

?>
Finished
