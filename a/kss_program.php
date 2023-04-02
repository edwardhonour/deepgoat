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
$post['table_name']="kss_user_program";
$post['action']="insert";
$post['program_name']="First High Frequency Bulk";
$post['display_order']=1;
$post['user_id']=1;
$post['program_id']=558;
$post['week_count']=8;
$post['start_date']="2022-03-14";
$post['end_date']="2022-04-30";
$X->post($post);
$post=array();
$post['table_name']="kss_user_program";
$post['action']="insert";
$post['program_name']="Recomp HCG/High Frequency Bulk";
$post['display_order']=2;
$post['user_id']=1;
$post['program_id']=558;
$post['week_count']=8;
$post['start_date']="2022-05-01";
$post['end_date']="2022-06-29";
$X->post($post);
$post=array();
$post['table_name']="kss_user_program";
$post['action']="insert";
$post['program_name']="High Frequency Bulk";
$post['display_order']=3; 
$post['user_id']=1;
$post['program_id']=558;
$post['week_count']=8;
$post['start_date']="2022-06-30";
$post['end_date']="2022-08-24";
$X->post($post);
