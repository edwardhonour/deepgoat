<?php

//------------------------------------------------------------------------------------
// Copyright 2021 - GEX Data Labs Inc.
// cron.check.wallets.php 
// Check for new wallets that have not been processed and wallets that have not 
//     been updated for over 30 days.
// Author: Edward Honour
// Date:  7/18/2021
//------------------------------------------------------------------------------------

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('max_execution_time',12000);
ini_set('memory_limit','-1');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json'); 
require_once('class.BSCScan.php');
$X=new BSCScan();
$j=time();

$i2=0;
//die();
$id=4652021;
while(1) {

$id=$id-1;
$i2++;
if ($id<3652021) { die('done'); } 

$sql="select * from RAW_ETH_BLOCK WHERE id = " . $id;
$l=$X->sql($sql);

echo $id . ' ' . ($i2 / 1000000) * 100 . '%' .  "\r\n";

if (sizeof($l)==0) {

echo "NO\r\n";
$k=$X->getEthBlock($id);

$j=json_decode($k,true);


if (!isset($j['status'])) {
if (isset($j['result'])) {

echo $id . "\r\n";
echo $j['result']['hash'] . "\r\n";

$sql="INSERT INTO RAW_ETH_BLOCK (id, id_hex, timestamp_hex, difficulty_hex, extraData, ";
$sql.=" gasLimit_hex, gasUsed_hex, hash, miner, minHash, nonce_hex, parentHash, ";
$sql.=" receiptsRoot, sha3Uncles, size_hex, stateRoot, totalDifficulty_hex, ";
$sql.=" transaction_count, transactionsRoot, uncles_count) values (";
$sql.=$id . ", ";
$sql.="'" . $j['result']['number'] . "',";
$sql.="'" . $j['result']['timestamp'] . "',";
$sql.="'" . $j['result']['difficulty'] . "',";
$sql.="'" . $j['result']['extraData'] . "',";
$sql.="'" . $j['result']['gasLimit'] . "',";
$sql.="'" . $j['result']['gasUsed'] . "',";
$sql.="'" . $j['result']['hash'] . "',";
$sql.="'" . $j['result']['miner'] . "',";
$sql.="'" . $j['result']['mixHash'] . "',";
$sql.="'" . $j['result']['nonce'] . "',";
$sql.="'" . $j['result']['parentHash'] . "',";
$sql.="'" . $j['result']['receiptsRoot'] . "',";
$sql.="'" . $j['result']['sha3Uncles'] . "',";
$sql.="'" . $j['result']['size'] . "',";
$sql.="'" . $j['result']['stateRoot'] . "',";
$sql.="'" . $j['result']['totalDifficulty'] . "',";
$sql.= sizeof($j['result']['transactions']) . ",";
$sql.="'" . $j['result']['transactionsRoot'] . "',";
$sql.= sizeof($j['result']['uncles']) . ")";

$s="select * from RAW_ETH_BLOCK WHERE id = " . $id;
$l2=$X->sql($s);


if (sizeof($l2)==0) {

    $X->execute($sql);

    $s="select count(*) as C FROM RAW_ETH_ADDRESS WHERE blockId_int = " . $id;
    $z=$X->sql($s);
    if ($z[0]['C']==0) {
    $sql="INSERT INTO RAW_ETH_ADDRESS (blockId_int, address_type, address, transId_int) values (";
    $sql.=$id . ", 3, '" . $j['result']['miner'] . "', 0)"; 
    $X->execute($sql);

foreach($j['result']['transactions'] as $b) {
    $sql="INSERT INTO RAW_ETH_TRANS (blockId_int, fromAddress, toAddress, gas_hex, gasPrice_hex, hash, ";
    $sql.=" input_hex, nonce_hex, transactionIndex_hex, value_hex, v, r, s) values (";
    $sql.= $id . ", ";
    $sql.= "'" . $b['from'] . "',";
    $sql.= "'" . $b['to'] . "',";
    $sql.= "'" . $b['gas'] . "',";
    $sql.= "'" . $b['gasPrice'] . "',";
    $sql.= "'" . $b['hash'] . "',";
    $sql.= "'" . $b['input'] . "',";
    $sql.= "'" . $b['nonce'] . "',";
    $sql.= "'" . $b['transactionIndex'] . "',";
    $sql.= "'" . $b['value'] . "',";
    $sql.= "'" . $b['v'] . "',";
    $sql.= "'" . $b['r'] . "',";
    $sql.= "'" . $b['s'] . "')";
    $i=$X->execute0($sql);

    $sql="INSERT INTO RAW_ETH_ADDRESS (blockId_int, address_type, address, transId_int) values (";
    $sql.=$id . ", 4, '" . $b['from'] . "', " . $i . ")"; 
    $X->execute($sql);

    $sql="INSERT INTO RAW_ETH_ADDRESS (blockId_int, address_type, address, transId_int) values (";
    $sql.=$id . ", 5, '" . $b['to'] . "', " . $i . ")"; 
    $X->execute($sql);
} // FOR EACH

    } else {
       echo "RPROCESSED\r\n";
    }
} else {
   
   echo "0-YES\r\n";
}
} else {
   echo "1-YES\r\n";
}
}
}
}
?>
[]
