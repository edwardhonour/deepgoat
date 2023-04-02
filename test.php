<?php

$i=file_get_contents('input.json');
$array=json_decode($i,true);
foreach($array as $a) {
   echo '"' . $a['address'] . '",';
   echo '"' . $a['tokens'][0]['address'] . '",';
   echo '"' . $a['tokens'][0]['symbol'] . '",';
   echo $a['tokens'][0]['amount'] . ',';
   echo '"' . $a['tokens'][1]['address'] . '",';
   echo '"' . $a['tokens'][1]['symbol'] . '",';
   echo $a['tokens'][1]['amount'] . "\r\n";
}
?>
