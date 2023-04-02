<?php

//------------------------------------------------------------------------------------
// Copyright 2021 - GEX Data Labs Inc.
// process.eth.blocks.php 
//
//
// Author: Edward Honour
// Date:  12/2/2021
//
// Arg 1 - Process ID (start with 0)
//     2 - Number processes run.
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

//-- Part 1: Get first Million Blocks.

$uu=50000;
$vv=100000;

$version = $argv[1];
$mod = $argv[2];

while (1) {
    $uu+=50000;
    $vv+=50000;
    $sql="SELECT * FROM ETH_ADDRESS WHERE ID BETWEEN " . $uu . " and " . $vv . " ORDER BY ID";
    $end = microtime(true);
    $list=$X->sql($sql);
    if (sizeof($list)==0) die('done');
    foreach ($list as $l) {
    $i++;
        if (($i % $mod)==$version) {

            echo $l['ID'] . "\r\n";

            $sql="SELECT * FROM ETH_RAW_TRANSACTIONS WHERE walletAddress = '" . $l['address'] . "' LIMIT 2";
            $z=$X->sql($sql);
            if (sizeof($z)==0) {
                $start = microtime(true);
                $X->populate_raw_transactions($l['address'],$l['ID']); 
                $end = microtime(true);
                $exec_time = ($end - $start);
                echo "Time: ".$exec_time." sec\r\n";
            }
        }
    }

}
?>
[]
