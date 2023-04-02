<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json');
ini_set("max_execution_time", -1);
require_once('class.XRDB.php');
require_once('class.BSCScan.php');

$X=new XRDB();
$B=new BSCScan();

$count=0;
	$contract=$j['address'];
	$chain=$j['chain'];
	$prices=$B->getETHPriceHistory('2015-01-01','2021-09-30');
        $contract="ETH Coin";
        $token_symbol="ETH";
        $token_name="Ethereum Coin";
	$array=json_decode($prices,true);
        echo $prices;
               foreach($array['result'] as $a) {
                    $sql="select * from GOATX_TOKEN_CONTRACT_PRICE WHERE contract_address = 'ETH Coin' and timestamp = '" . substr($a['unixTimeStamp'],0,10) . "'";
                    $z=$X->sql($sql);
                    if (sizeof($z)==0) { 
                        $sql="INSERT INTO GOATX_TOKEN_CONTRACT_PRICE (contract_address, timestamp, token_symbol, token_name, price,last_timestamp) VALUES (";
                        $sql.="'" . $contract . "'," . substr($a['unixTimeStamp'],0,10) . ",'" . $token_symbol . "','" . $token_name . "','" . $a['value'] . "'," . time() . ")";
                        $X->execute($sql);
                        $count++;
                    }
                }
?>
Finished
