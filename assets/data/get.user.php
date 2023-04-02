<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');

require_once('class.XRDB.php');
$X=new XRDB();

if (isset($_COOKIE['uid'])) {
   $uid=$_COOKIE['uid'];
} else {
   $uid=55009;
}

$sql="select * from FPS_USER where USER_ID = " . $uid;
$user=$X->sql($sql);
$output=array();
$output['id']="cfaad35d-07a3-4447-a6c3-d8c3d54fd5df";
$output['name']=$user[0]['FIRST_NAME'] . " " . $user[0]['LAST_NAME'];
$output['email']=$user[0]['EMAIL'];
$output['avatar']="assets/images/avatars/brian-hughes.jpg";
$output['status']="online";
echo json_encode($output);

?>
