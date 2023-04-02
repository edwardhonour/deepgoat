<?php

//------------------------------------------------------------------------------------
// Copyright 2021 - GEX Data Labs Inc.
//     been updated for over 30 days.
// Author: Edward Honour
// Date:  9/21/2021
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

//-- Part 1: Check for new Wallets that have never been processed.

$sql="select distinct USER_ID from GOATX_USER_WALLET";
$list=$X->sql($sql);
foreach($list as $l) {
     echo "Calc Balance: " . $l['USER_ID'] . "\r\n";
     $X->calculate_token_balances($l['USER_ID']);
}

$sql="select distinct token_address from GOATX_USER_TOKEN_BALANCES";
$list=$X->sql($sql);
$i=0;
echo sizeof($list) . " TOKENS\r\n";
foreach ($list as $l) {
        $token_price='0';
        $sql="SELECT timestamp, price FROM GOATX_TOKEN_CONTRACT_PRICE where contract_address = '" . $l['token_address'] . "' order by timestamp desc";
	$w=$X->sql($sql);
        if (sizeof($w)>0) {
              $timestamp=$w[0]['timestamp'];
              $price=$w[0]['price'];
        } else {
              $timestamp=0;
              $price='0';
        }
        $sql="SELECT token_balance, wallet_address, uid FROM GOATX_USER_TOKEN_BALANCES WHERE token_address = '" . $l['token_address'] . "'";
        $h=$X->sql($sql);
        foreach($h as $i) {
            if (strpos($i['token_balance'],'E')>0) $i['token_balance']='0';
            $value=floatval($i['token_balance'])*floatval($price);
            $value=number_format($value,4);
            $sql="UPDATE GOATX_USER_TOKEN_BALANCES SET token_price = '" . $price . "', token_price_timestamp = '" . $timestamp . "', token_total_usd = '" . $value . "' WHERE ";
            $sql.=" wallet_address = '" . $i['wallet_address'] . "' and token_address = '" . $l['token_address'] . "'";
            $X->execute($sql);

        } 
   } 


?>
[]
