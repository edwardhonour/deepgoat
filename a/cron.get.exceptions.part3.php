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
ini_set('memory_limit','256M');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json'); 
require_once('class.BSCScan.php');
$X=new BSCScan();
$j=time();

$sql="SELECT LOWER(contractAddress) AS A, CHAIN AS B, COUNT(*) AS C FROM BEP20_TRANSACTIONS WHERE ";
$sql.=" LOWER(contractAddress) <> '' AND contractType = '' ";
$sql.=" AND NOT EXISTS (SELECT 'X' FROM BEP20_CONTRACT WHERE CONTRACT_ADDRESS = LOWER(contractAddress)) ";
$sql.=" AND NOT EXISTS (SELECT 'X'  ";
$sql.=" FROM GOATX_CONTRACT_EXCEPTION WHERE ADDRESS = LOWER(contractAddress)) " ;
$sql.=" GROUP BY LOWER(contractAddress), CHAIN ORDER BY 3 DESC LIMIT 33500";
//echo $sql;
$l=$X->sql($sql);
echo sizeof($l) . "/r/n";
$j=0;
$k=0;
foreach($l as $d) {

$sql="SELECT  COUNT(*) AS C FROM GOATX_CONTRACT_EXCEPTION WHERE ADDRESS = '" . strtolower($d['A']) . "'";
$r=$X->sql($sql);
if ($r[0]['C']==0) {
   $sql="INSERT INTO GOATX_CONTRACT_EXCEPTION (ADDRESS,CHAIN,C) VALUES ('" . strtolower($d['A']) . "','" . $d['B'] . "','" . $d['C'] . "')";
   $X->execute($sql);
   $j++;
} else {
   $sql="UPDATE GOATX_CONTRACT_EXCEPTION SET C = " . $d['C'] . " WHERE ADDRESS = '" . strtolower($d['A']) . "'";
   $X->execute($sql);
   $k++;
}
}
echo $j . " ADDED\r\n";
echo $k . " UPDATED\r\n";
?>
[]
