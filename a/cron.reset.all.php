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
$sql="SELECT CONTRACT_ADDRESS AS A, CONTRACT_NAME, SYMBOL, CONTRACT_TYPE FROM BEP20_CONTRACT ";
$l=$X->sql($sql);
$h=sizeof($l);
$i=0;
echo time()-$j . " seconds, ";
$m=time();
foreach($l as $d) {
  $i++;
      $q=time()-$m;
      echo $i . '/' . $h . "(" . $q , "),";
      $m=time();
      $sql="UPDATE BEP20_TRANSACTIONS SET contractName= '" . $d['CONTRACT_NAME'] . "', ";
      $sql.=" contractSymbol = '" . $d['SYMBOL'] . "', ";
      $sql.=" contractType = '" . $d['CONTRACT_TYPE'] . "' WHERE contractAddress = '" . $d['A'] . "'";
      $X->execute($sql);
      $X->execute("COMMIT");
}

?>
[]
