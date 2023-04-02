<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('memory_limit', '-1');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
require_once('class.BSCScan.php');

$X=new BSCScan();
$sql="ALTER TABLE BEP20_RAW_TRANSACTIONS ADD currentCost VARCHAR(80) NOT NULL DEFAULT ''";
echo $sql;
$X->execute($sql);
?>

