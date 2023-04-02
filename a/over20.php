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
require_once('class.ETHScan.php');
$X=new ETHScan();
$j=time();

$last=$X->getTopETHBlock();
echo $last;
$i2=0;
$id=$last;
$id-=19;
while(1) {
	
	$start = microtime(true);
	$id=$id-20;
	$i2+=20;
	if ($id>$last) { die('done'); }

	$sql="select * from RAW_ETH_BLOCK WHERE id = " . $id;
	$l=$X->sql($sql);

	echo $id . ' ' . (($i2 / 1000000) * 100) . '%' . "\r\n";

	if (sizeof($l)==0) {

			$k=$X->getEthBlock($id);
			$j=json_decode($k,true);
			if (!isset($j['status'])) {
			if (isset($j['result'])) {


			$s="select * from RAW_ETH_BLOCK WHERE id = " . $id;
			$l2=$X->sql($s);
			if (sizeof($l2)==0) {
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
          try { $X->execute($sql); } catch(Exception $e) { echo 'Message: ' .$e->getMessage(); }

          //--- Insert MINER Address

          $s="select address FROM RAW_ETH_ADDRESS2 WHERE address = '" . $j['result']['miner'] . "' LIMIT 1";
          $z=$X->sql($s);
          if (sizeof($z)==0) {
              $sql="INSERT INTO RAW_ETH_ADDRESS2 (blockId_int, address_type, address, transId_int) values (";
              $sql.=$id . ", 3, '" . $j['result']['miner'] . "', 0)";
              try { $X->execute($sql); } catch(Exception $e) { echo 'Message: ' .$e->getMessage(); }
          }


            $addresses=array();
            //-- Make a list of all the addresses in the transaction.
            $start1 = microtime(true);
            foreach($j['result']['transactions'] as $b) {
                      $flag=0;
                      foreach($addresses as $a) {
                           if ($a['address']==$b['from']) $flag=4;
                      }
                      if ($flag==0) {
                          $g=array();
                          $g['address']=$b['from'];
                          $g['atype']=4;
                          array_push($addresses,$g);
                      }
             }

             foreach($j['result']['transactions'] as $b) {
                      $flag=0;
                      foreach($addresses as $a) {
                         if ($a['address']==$b['to']) $flag=4;
                      }
                      if ($flag==0) {
                         $g=array();
                         $g['address']=$b['to'];
                         $g['atype']=5;
                         array_push($addresses,$g);
					  }
              }

              echo sizeof($j['result']['transactions']) . "\r\n";

              //-- Make a query to find existing addresses.

              $sql="SELECT DISTINCT address FROM RAW_ETH_ADDRESS2 WHERE address IN (";
              $list="";
              foreach($addresses as $b) {
                 if ($list=="") {
                     $list.="'" . $b['address'] . "'";
                 } else {
                     $list.=",'" . $b['address'] . "'";
                 }
              }
              $sql.=$list . ") AND blockId_int < " . $id;


              if ($list!="") {
				  
                   $uuu=$X->sql($sql);
				   
                   $addresses2=array();
                   foreach($addresses as $aaa) {
                   $flag=0;
                   foreach($uuu as $u) {
                      if ($u['address']==$aaa['address']) $flag=1;
                    }
                   if ($flag==0) {
                       array_push($addresses2,$aaa);
                    }
				   }
                    foreach($addresses2 as $b) {
                       $i=0;
                       if ($b['atype']==4) {
                          $sql="INSERT INTO RAW_ETH_ADDRESS2 (blockId_int, address_type, address, transId_int) values (";
                          $sql.=$id . ", 4, '" . $b['address'] . "', " . $i . ")";
                          try { $X->execute($sql); } catch(Exception $e) { echo 'Message: ' .$e->getMessage(); }
                       }

                       if ($b['atype']==5) {
                          $sql="INSERT INTO RAW_ETH_ADDRESS2 (blockId_int, address_type, address, transId_int) values (";
                          $sql.=$id . ", 5, '" . $b['address'] . "', " . $i . ")";
                          try { $X->execute($sql); } catch(Exception $e) { echo 'Message: ' .$e->getMessage(); }
                       }
                   }

				   
			  } 
$end = microtime(true);
$exec_time = ($end - $start);
echo "Time: ".$exec_time." sec\r\n";
			 }
}  //isseet
}  // isset
		}  // sizeof($l)
}  // while(1)
?>


