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
//header('Content-Type: application/json'); 
require_once('class.BSCScan.php');
$X=new BSCScan();
$j=time();

//-- Part 1: Check for new Wallets that have never been processed.

$i=0;
$sql="SELECT ID, wallet_address from GOATX_WALLET WHERE last_erc20_timestamp = 0 AND exclude = 'N' ORDER BY ID LIMIT 10000";
$list=$X->sql($sql);
echo sizeof($list) . " WALLETS\r\n";
foreach ($list as $l) {
	$address=$l['wallet_address'];
$i++;
echo $i . ",";

	$id=$l['ID'];
	$time=time();
        $t0=time();
	$sql="UPDATE GOATX_WALLET SET last_erc20_timestamp = " . $time . " where ID = " . $id;
	$X->execute($sql);
        $t=time()-$t0;
        $t=time();
	$X->populate_raw_transactions($address,"ETH","N");
        $t2=time()-$t;
        $t2=time();
	$X->process_transactions($address,"ETH","N");
        $t3=time()-$t2;
        $kk=time()-$j;
        if ($kk>3550) die(); 
        usleep(20000);
}
$k=time();
die();
//-- Part 2: If Part 1 took less than 30 seconds, check for wallets that have not been processed in over 30 days.

if (($k-$j) < 30) {
	$m=$k-2592000;
	$sql="SELECT ID, wallet_address from GOATX_WALLET WHERE last_bep20_timestamp < " . $m . " ORDER BY last_bep20_timestamp";
	$list=$X->sql($sql);
	foreach ($list as $l) {
		$address=$l['wallet_address'];
		$id=$l['ID'];
		$time=time();
		$sql="UPDATE GOATX_WALLET SET last_bep20_timestamp = " . $time . " where ID = " . $id;
		$X->execute($sql);
		$X->populate_raw_transactions($address);
		$X->process_transactions($address);
	}
}

?>
[]
