<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('memory_limit', '-1');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
require_once('class.CryptoBooks.php');
require_once('class.BSCScan.php');

$Y=new BSCScan();
$Y->process_transactions('0x4056087801950f51e814b772b3e11f99147df1d4','BSC','Y');

$X=new CryptoBooks();
$X->process_transaction_list();

?>
