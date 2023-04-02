<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('memory_limit',-1);
ini_set('max_execution_time', 900000);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
//header('Content-type: application/json');

require_once('class.XRDB.php');
$X=new XRDB();

$post=array();
$post['table_name']="kss_exercise";
$post['action']="insert";
$post['exercise_name']="Tricep Preacher";
$post['category']="Arms";
$post['primary_bodypart_id']=562;
$post['is_compound']=0;
$post['is_primary']=0;
$post['display_order']=90;
$X->post($post);
