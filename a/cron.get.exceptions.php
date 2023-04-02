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
$sql="SELECT * FROM GOATX_CONTRACT_EXCEPTION WHERE FLAG = 'N' AND ADDRESS NOT IN (SELECT CONTRACT_ADDRESS FROM BEP20_CONTRACT)ORDER BY C DESC";
$r=$X->sql($sql);
$i=0;
echo sizeof($r) . " CONTRACTS\r\n";
foreach($r as $s) {
    $i++;
    if ($i<101) {
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
echo "Starting Query,";
$sql="SELECT DISTINCT LOWER(contractAddress) AS A FROM BEP20_TRANSACTIONS WHERE ";
$sql.=" LOWER(contractAddress) <> '' AND contractType = '' AND EXISTS (SELECT 'X' FROM BEP20_CONTRACT WHERE CONTRACT_ADDRESS = LOWER(contractAddress))";
$l=$X->sql($sql);
$h=sizeof($l);
$i=0;
echo time()-$j . " seconds, ";

foreach($l as $d) {
  $i++;
  echo $i . '/' . $h . ",";
  $sql="SELECT SYMBOL, CONTRACT_NAME, CONTRACT_TYPE FROM BEP20_CONTRACT WHERE CONTRACT_ADDRESS = '" . $d['A'] . "'";
  $x=$X->sql($sql);
  if (sizeof($x)>0) {
      $sql="UPDATE BEP20_TRANSACTIONS SET contractName= '" . str_replace("'","",$x[0]['CONTRACT_NAME']) . "', ";
      $sql.=" contractSymbol = '" . str_replace("'","",$x[0]['SYMBOL']) . "', ";
      $sql.=" contractType = '" . $x[0]['CONTRACT_TYPE'] . "' WHERE contractAddress = '" . $d['A'] . "'";
      //echo $sql;
      echo $d['A'] . "\r\n";
      $X->execute($sql);
      $X->execute("COMMIT");
  } 
}

$sql="SELECT LOWER(contractAddress) AS A, CHAIN AS B, COUNT(*) AS C FROM BEP20_TRANSACTIONS WHERE ";
$sql.=" LOWER(contractAddress) <> '' AND contractType = '' ";
$sql.=" AND NOT EXISTS (SELECT 'X' FROM BEP20_CONTRACT WHERE CONTRACT_ADDRESS = LOWER(contractAddress)) ";
$sql.=" GROUP BY LOWER(contractAddress), CHAIN ORDER BY 3 DESC LIMIT 13500";
$l=$X->sql($sql);
foreach($l as $d) {

$sql="SELECT  COUNT(*) AS C FROM GOATX_CONTRACT_EXCEPTION WHERE ADDRESS = '" . strtolower($d['A']) . "'";
$r=$X->sql($sql);
if ($r[0]['C']==0) {
   $sql="INSERT INTO GOATX_CONTRACT_EXCEPTION (ADDRESS,CHAIN,C) VALUES ('" . strtolower($d['A']) . "','" . $d['B'] . "','" . $d['C'] . "')";
   $X->execute($sql);
} else {
   $sql="UPDATE GOATX_CONTRACT_EXCEPTION SET C = " . $d['C'] . " WHERE ADDRESS = '" . strtolower($d['A']) . "'";
   $X->execute($sql);
}
}

?>
[]
