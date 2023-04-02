<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('max_execution_time',1200);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json'); 
require_once('class.BSCScan.php');
$X=new BSCScan();
$j=time();

$sql="INSERT INTO D VALUES ('" . $j . "')";
$X->execute($sql);
?>
[]
