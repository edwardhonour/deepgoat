<?php
//----------------------------------------------
// INFINITI - PRISM
//
// Update employee personal information from 
// prism for new employees that where missing  
// SSN and DOB.
//
//----------------------------------------------
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('memory_limit',-1);
ini_set('max_execution_time', 900000);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');

require_once('class.XRDB.php');
$X=new XRDB();

$flag='Y';
$id=65000;
while ($flag=='Y') {
$id++;
$sql="select id, employeeId, ssn, dob from inf_client_employee where id = " . $id . " and dependentId = ''";
$t=$X->sql($sql);
foreach($t as $a) {
     print_r($a);
     $sql="select * from nua_employee where employee_code = '" . $a['employeeId'] . "'";
     $z=$X->sql($sql);
     if (sizeof($z)>0) {
         $nua=$z[0];
         $post=array();
         $post['table_name']="nua_employee";
         $post['action']="insert";
         $post['id']=$nua['id'];
         $post['social_security_number']=$a['ssn'];
         $post['date_of_birth']=$a['dob'];
         print_r($post);
         $X->post($post);
         $sql="select * from nua_monthly_member_census where employee_code = '" . $a['employeeId'] . "' and dependent_code=''";
         $g=$X->sql($sql);
         foreach($g as $h) {
             $post=array();
             $post['table_name']="nua_monthly_member_census";
             $post['action']="insert";
             $post['id']=$h['id'];
             $post['dob']=$a['dob'];
             $post['ssn']=$a['ssn'];
         print_r($post);
             $X->post($post);
         }
         $sql="select * from nua_monthly_member_additions where employee_code = '" . $a['employeeId'] . "' and dependent_code=''";
         $g=$X->sql($sql);
         foreach($g as $h) {
             $post=array();
             $post['table_name']="nua_monthly_member_additions";
             $post['action']="insert";
             $post['id']=$h['id'];
             $post['dob']=$a['dob'];
             $post['ssn']=$a['ssn'];
         print_r($post);
             $X->post($post);
         }
         $sql="select * from nua_monthly_member_terminations where employee_code = '" . $a['employeeId'] . "' and dependent_code=''";
         $g=$X->sql($sql);
         foreach($g as $h) {
             $post=array();
             $post['table_name']="nua_monthly_member_terminations";
             $post['action']="insert";
             $post['id']=$h['id'];
             $post['dob']=$a['dob'];
             $post['ssn']=$a['ssn'];
         print_r($post);
             $X->post($post);
         }
      }
}
usleep(500000);
}
?>

