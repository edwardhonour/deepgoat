<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
ini_set("max_execution_time", -1);
ini_set('memory_limit','-1');
require_once('class.XRDB.php');
require_once('class.BSCScan.php');

$X=new XRDB();
$B=new BSCScan();

$sql="SELECT WALLET_ADDRESS FROM GOATX_WALLET";
$r=$X->sql($sql);

$count=0;
foreach($r as $j) {
	$contract=$j['WALLET_ADDRESS'];
        $sql="insert into GNODE (address, node_type) values ('";
        $sql .= $contract . "',1)";
        $B->execute($sql);
}

?>
Finished

