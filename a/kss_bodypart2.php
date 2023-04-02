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

//Legs
//Glutes
$post=array();
$post['table_name']="kss_bodypart";
$post['action']="insert";
$post['parent']=563;
$post['part_level']=2;
$post['bodypart_name']="Trapezius";
$post['display_order']=1;
$X->post($post);
//Lats
$post=array();
$post['table_name']="kss_bodypart";
$post['action']="insert";
$post['parent']=572;
$post['part_level']=2;
$post['bodypart_name']="Upper Lats - Width Exercise";
$post['display_order']=3;
$X->post($post);
$post=array();
$post['table_name']="kss_bodypart";
$post['action']="insert";
$post['parent']=573;
$post['part_level']=2;
$post['bodypart_name']="Upper Lats - Depth Exercise";
$post['display_order']=3;
$X->post($post);
$post=array();
$post['table_name']="kss_bodypart";
$post['action']="insert";
$post['parent']=573;
$post['part_level']=2;
$post['bodypart_name']="Lower Lats - Width Exercise";
$post['display_order']=3;
$X->post($post);
$post=array();
$post['table_name']="kss_bodypart";
$post['action']="insert";
$post['parent']=572;
$post['part_level']=2;
$post['bodypart_name']="Lower Lats - Depth Exercise";
$post['display_order']=3;
$X->post($post);
