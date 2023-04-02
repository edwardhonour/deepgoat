<?php

//---------------------------------------------------------------------
// Copyright 2021 - GEX Data Labs Inc.
// Main API Router for deepgoat.com on Amazon Aurora
// Author:  Edward Honour
// Date: 07/18/2021
//---------------------------------------------------------------------

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json'); 
require_once('class.GOATX.php');

$X=new GOATX();
$data = file_get_contents("php://input");
$data = json_decode($data, TRUE);
if (!isset($data['q'])) die('[]');

$output=$X->router($data);
$o=json_encode($output);
$o=stripcslashes($o);
echo $o;

?>
