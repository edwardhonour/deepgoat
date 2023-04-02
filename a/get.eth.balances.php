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
//header('Content-Type: application/json'); 
require_once('class.ETHScan.php');
$X=new ETHScan();
$j=time();

while(1) {

        $sql="SELECT address, ID from ETH_ADDRESS WHERE ID NOT IN (select wallet_id FROM ETH_BALANCE) ORDER BY ID";
        $z=$X->sql($sql);
        $addresses=array();
        $cnt=0;
        foreach($z as $a) {
           $cnt++;
           if ($cnt<20) array_push($addresses,$a['address']);
        }

if (sizeof($addresses)==0) die();

        $d=$X->get100Balances($addresses);
        $a=json_decode($d,true);
       
        if (isset($a['result'])) {

            foreach($a['result'] as $r) {

                  $sql="SELECT ID FROM ETH_ADDRESS WHERE address = '" . $r['account'] . "'"; 
                  $y=$X->sql($sql);
                  if (sizeof($y)>0) {                 
                  echo $y[0]['ID'] . ": " . $r['balance'] . "\r\n";
                     $sql="insert into ETH_BALANCE (wallet_id, timestamp, contract, symbol, balance) VALUES (";
                     $sql.="" . $y[0]['ID'] . "," . $j . ",'','ETH','" . $r['balance'] . "')";

                     $X->execute($sql);
                  }
            }

        }
        sleep(1);
}
?>
[]
