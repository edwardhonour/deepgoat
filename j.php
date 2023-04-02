<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json'); 

for($i=0;$i<250;$i++) {
   $u=rand(100000,999999) . rand(100000,999999);
   echo $u . "\r\n";
}
?>
