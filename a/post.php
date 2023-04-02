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

$output=array();
$output['request']=$_REQUEST;
$output['post']=$_POST;
$output['files']=$_FILES;
echo json_encode($output);
?>
