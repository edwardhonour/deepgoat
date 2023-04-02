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

//-- Part 1: Check for new Wallets that have never been processed.

$sql="SELECT ID, wallet_address from GOATX_WALLET WHERE last_bep20_timestamp > 0 ORDER BY last_bep20_timestamp LIMIT 10000";
$list=$X->sql($sql);
$i=0;
foreach ($list as $l) {
	$address=$l['wallet_address'];
        $i++;
	$id=$l['ID'];
	$time=time();
        $t0=time();
	$sql="UPDATE GOATX_WALLET SET last_bep20_timestamp = " . $time . " where ID = " . $id;
	$X->execute($sql);
        $t=time()-$t0;
        $t=time();
	$X->populate_raw_transactions($address,"BSC","N");
        $t2=time()-$t;
        $t2=time();
	$X->process_transactions($address,"BSC","N");
        $t3=time()-$t0;
        echo $i . " (" . $t3 . "), ";
}

?>
[]
