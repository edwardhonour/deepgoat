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

//-- Part 1: Get first Million Blocks.

$i=0;
$uu=5032771;
$vv=5082771;

while ($vv<14800000) {

$uu+=50000;
$vv+=50000;

$start = microtime(true);
$sql="SELECT * FROM RAW_ETH_BLOCK WHERE id BETWEEN " . $uu . " and " . $vv . " ORDER BY id";
$end = microtime(true);
$exec_time = ($end - $start);
//echo "The query of RAW_ETH_BLOCK: ".$exec_time." sec\r\n";
$list=$X->sql($sql);
foreach ($list as $l) {

        //--
        //-- Convert HEX to DEC.
        //--
        $timestamp=hexdec(str_replace("0x","",$l['timestamp_hex']));
        $difficulty=hexdec(str_replace("0x","",$l['difficulty_hex']));
        $totalDifficulty=hexdec(str_replace("0x","",$l['totalDifficulty_hex']));
        $gasLimit=hexdec(str_replace("0x","",$l['gasLimit_hex']));
        $gasUsed=hexdec(str_replace("0x","",$l['gasUsed_hex']));

        //--
        //-- Insert the MINER address if it does not exist.
        //--

        $start = microtime(true);
        $sql="SELECT * FROM ETH_ADDRESS WHERE address = '" . $l['miner'] . "'";
        $z=$X->sql($sql);
        $end = microtime(true);
        $exec_time = ($end - $start);
        //echo "The query of ETH_ADDRESS: ".$exec_time." sec\r\n";
        if (sizeof($z)==0) {


            $start = microtime(true);
            $sql="insert into ETH_ADDRESS (address, first_block_id, latest_block_id, first_timestamp, latest_timestamp, sender, receiver, contract, miner) VALUES (";
            $sql.="'" . $l['miner'] . "'," . $l['id'] . "," . $l['id'] . "," . $timestamp . "," . $timestamp . ",0,0,0,1)";
            $miner_id=$X->execute0($sql); 
            $end = microtime(true);
            $exec_time = ($end - $start);
            //echo "The INSERT of ETH_ADDRESS: ".$exec_time." sec\r\n";
            //echo "New Miner: " . $miner_id . "\r\n";
//            $start = microtime(true);
//            $X->populate_raw_transactions($l['miner'],$miner_id); 
//            $exec_time = ($end - $start);
//            echo "Populate Raw Transactions: ".$exec_time." sec\r\n";
            echo "New Miner: " . $miner_id . "\r\n";

        } else {
            $miner_id=$z[0]['ID'];
        }

        //--
        //-- Insert the BLOCK if it does not exist
        //--

        $start = microtime(true);
        $sql="SELECT * FROM ETH_BLOCKS WHERE id = " . $l['id'];
        $z=$X->sql($sql);
            $end = microtime(true);
            $exec_time = ($end - $start);
            //echo "The QUERY of ETH_BLOCKS: ".$exec_time." sec\r\n";
        if (sizeof($z)==0) {
           $sql= "INSERT INTO ETH_BLOCKS (id, timestamp, hash, gasLimit, gasUsed, difficulty, totalDifficulty, transactions, uncles, miner_id) ";
           $sql.=" VALUES (" . $l['id'] . "," . $timestamp . ",'" . $l['hash'] . "','" . $gasLimit . "','" . $gasUsed . "','";
           $sql.= $difficulty . "','" . $totalDifficulty . "'," . $l['transaction_count'] . "," . $l['uncles_count'] . "," . $miner_id . ")";

           echo "Block: " . $l['id'] . " at " . date("Y-m-d h:i", $timestamp) .  "\r\n";

           $X->execute($sql);
        }
        //--
        //-- Loop Through all addresses in the block and insert them if they do not exists.
        //--

        $start = microtime(true);
        $sql="SELECT * FROM RAW_ETH_ADDRESS2 WHERE blockId_int = " . $l['id'];
        $t=$X->sql($sql);
            $end = microtime(true);
            $exec_time = ($end - $start);
            //echo "The QUERY of RAW_ETH_ADDRESS2 (blockId_int): ".$exec_time." sec\r\n";
        foreach($t as $z0) {

            $start = microtime(true);
            $sql="SELECT * FROM ETH_ADDRESS WHERE address = '" . $z0['address'] . "'";
            $z=$X->sql($sql);
            $end = microtime(true);
            $exec_time = ($end - $start);
            //echo "The QUERY of ETH_ADDRESS (address): ".$exec_time." sec\r\n";
            if (sizeof($z)==0) {
                $sql="insert into ETH_ADDRESS (address, first_block_id, latest_block_id, first_timestamp, latest_timestamp, sender, receiver, contract, miner) VALUES (";
                $sql.="'" . $z0['address'] . "'," . $l['id'] . "," . $l['id'] . "," . $timestamp . "," . $timestamp;
                if ($z0['address_type']==3) $sql .= ",0,0,0,1)";
                if ($z0['address_type']==4) $sql .= ",1,0,0,0)";
                if ($z0['address_type']==5) $sql .= ",0,1,0,0)";
                $start = microtime(true);
                $xid=$X->execute0($sql); 
            $end = microtime(true);
            $exec_time = ($end - $start);
            //echo "The INSERT of ETH_ADDRESS: ".$exec_time." sec\r\n";
                echo "Address: " . $xid . "\r\n";
//                $start = microtime(true);
//                $X->populate_raw_transactions($z0['address'],$xid); 
//            $end = microtime(true);
//            $exec_time = ($end - $start);
//            echo "The Populate Raw Transactions: ".$exec_time." sec\r\n";
             }
        }
     }
}

?>
[]
