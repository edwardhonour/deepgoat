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

$id=$_GET['id'];
$a=$_GET['a'];

if ($a=='eft') {
    $sql="update nua_company set flag_eft = 'Yes', flag_53 = 'Yes', flag_current = 'Yes' where id = " . $id; 
    $X->execute($sql);
}
if ($a=='53') {
    $sql="update nua_company set flag_eft = 'No', flag_53 = 'Yes', flag_current = 'Yes' where id = " . $id; 
    $X->execute($sql);
}
if ($a=='current') {
    $sql="update nua_company set flag_eft = 'No', flag_53 = 'No', flag_current = 'Yes' where id = " . $id; 
    $X->execute($sql);
}
if ($a=='reset') {
    $sql="update nua_company set flag_eft = 'No', flag_53 = 'No', flag_current = 'No', flag_ghost = 'Yes' where id = " . $id; 
    $X->execute($sql);
}
if ($a=='ghost') {
    $sql="update nua_company set flag_ghost = 'No' where id = " . $id; 
    $X->execute($sql);
}

header('location: https://deepgoat.com/api/fix.php');
?>
