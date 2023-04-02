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

?>
[]
