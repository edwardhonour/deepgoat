<?php
//
//--
// MIST - Technical Service Desk
//--
// Left Menu
//--
//
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');

require_once('class.XRDB.php');
$X=new XRDB();

$data = file_get_contents("php://input");
$data = json_decode($data, TRUE);

$sql="select * from FPS_USER where USER_ID = " . $data['uid'];
$user=$X->sql($sql);
$role=$user[0]['ROLE'];

$menu=array();
$item=array();
$item['id']="home";
$item['title']="Trouble Tickets";

$item['subtitle']="";
$item['type']="group";
$item['icon']="heroicons_outline:home";
$item['children']=array();

$children=array();


//-- Home
$child=array();
$child['id']="db";
$child['title']="Home";
$child['type']="basic";
$child['icon']="heroicons_outline:clipboard-check";
$child['link']="/tsd-home";
array_push($children,$child);

//-- Facility List
$child=array();
$child['id']="db";
$child['title']="Facility Search";
$child['type']="basic";
$child['icon']="heroicons_outline:clipboard-check";
$child['link']="/facility-list";
array_push($children,$child);

//-- Open Tickets
$child=array();
$child['id']="db";
$child['title']="Open Tickets";
$child['type']="basic";
$child['icon']="heroicons_outline:clipboard-check";
$child['link']="/ticket-list";
array_push($children,$child);

//-- Closed Tickets
$child=array();
$child['id']="db";
$child['title']="Closed Tickets";
$child['type']="basic";
$child['icon']="heroicons_outline:clipboard-check";
$child['link']="/ticket-list/1";
array_push($children,$child);

//-- Aging Report
$child=array();
$child['id']="db";
$child['title']="Aging Report";
$child['type']="basic";
$child['icon']="heroicons_outline:clipboard-check";
$child['link']="/aging-report";
array_push($children,$child);

//-- Account Profile
$child=array();
$child['id']="db";
$child['title']="Profile";
$child['type']="basic";
$child['icon']="heroicons_outline:clipboard-check";
$child['link']="/account-profile";
array_push($children,$child);

//-- Logout
//
$child=array();
$child['id']="db";
$child['title']="Sign Out";
$child['type']="basic";
$child['icon']="heroicons_outline:clipboard-check";
$child['link']="/sign-out";
array_push($children,$child);
$item['children']=$children;

array_push($menu,$item);

$output=array();
$output['default']=$menu;
$output['compact']=array();
$output['futuristic']=array();
$output['horizontal']=array();
echo json_encode($output);


?>

