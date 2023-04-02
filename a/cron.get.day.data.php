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

$sql="SELECT chain, address, token_name, token_symbol FROM GOATX_TOKEN_CONTRACT WHERE EXCLUDE = 'N'";
$r=$X->sql($sql);

$count=0;
foreach($r as $j) {
	$contract=$j['address'];
	$prices=$B->getGeckoDayPrices($contract);
	if (!isset($prices['error'])) {
		$array=json_decode($prices,true);
			if (isset($array['prices'])) {
                foreach($array['prices'] as $a) {
                    $timestamp=$a[0];
                    $price=$a[1];                            
                    $sql="select * from GOATX_TOKEN_CONTRACT_PRICE WHERE contract_address = '" . $contract . "' and timestamp = '" . substr($timestamp,0,10) . "'";
                    $z=$this->sql($sql);
                    if (sizeof($z)==0) {     
                        $sql="INSERT INTO GOATX_TOKEN_CONTRACT_PRICE (contract_address, timestamp, token_symbol, token_name, price,last_timestamp) VALUES (";
                        $sql.="'" . $contract . "'," . substr($timestamp,0,10) . ",'" . $symbol . "','" . $name . "','" . $price . "'," . time() . ")";
                        echo $sql;
                        $this->execute($sql);
                    }
                }
			} else {
				$sql="UPDATE GOATX_USER_TOKEN_BALANCES SET EXCLUDE = 'Y' WHERE token_address = '" . $contract . "'";
				$this->execute($sql);
			}
	}
	sleep(1);
}

?>
Finished
