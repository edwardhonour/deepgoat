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
$sql="SELECT * FROM GOATX_CONTRACT_EXCEPTION WHERE FLAG = 'N' AND ADDRESS NOT IN (SELECT CONTRACT_ADDRESS FROM BEP20_CONTRACT) ORDER BY C DESC";
$r=$X->sql($sql);
$i=0;
echo sizeof($r) . " CONTRACTS\r\n";
foreach($r as $s) {
    $i++;
    $zz=time()-$j;
    if ($zz<3590) {
    if ($s['CHAIN']=="BSC") $w=$X->getBEP20TokenInfo($s['ADDRESS']);
    if ($s['CHAIN']=="ETH") $w=$X->getERC20TokenInfo($s['ADDRESS']);
    $w=json_decode($w,true);
    if (isset($w['result'][0]['tokenName'])) {
          $sql="SELECT ID FROM BEP20_CONTRACT WHERE CONTRACT_ADDRESS = '" . $s['ADDRESS'] . "'";
          $z=$X->sql($sql);
          $post=array();
          $post['ACTION']="insert";
          $post['TABLE_NAME']="BEP20_CONTRACT";

          if (sizeof($z)>0) {
             $post['ID']=$z[0]['ID'];
          }
          $post['CONTRACT_ADDRESS']=$s['ADDRESS'];
          $post['CONTRACT_NAME']=$w['result'][0]['tokenName'];
          $post['CONTRACT_TYPE']="TOKEN";
          $post['SYMBOL']=strtoupper($w['result'][0]['symbol']);
          $post['TIMESTAMP']=time();
          $post['PLATFORM']=$s['CHAIN'];
          echo $post['SYMBOL'] . ",\r\n";
          $id=$X->post($post);
          if ($id>0) {
           $sql="DELETE FROM GOATX_CONTRACT_EXCEPTION WHERE ADDRESS = '" . $s['ADDRESS'] . "'";
           $X->execute($sql);
         }
    } else {
          $sql="UPDATE GOATX_CONTRACT_EXCEPTION SET FLAG = 'Y' WHERE ADDRESS = '" . $s['ADDRESS'] . "'";
          $X->execute($sql);
    }
    echo $i . ",";
    sleep(1);
    }
}

?>
[]
