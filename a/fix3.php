<?php
//--
//-- Get ERC20 and BEP20 token assets for a wallet.
//--
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('max_execution_time',800);
ini_set('memory_limit', '2048M');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json'); 
require_once('class.XRDB.php');
require_once('class.ETHScan.php');
$X=new XRDB();
$B=new ETHScan();
 
$sql = "select distinct dependent_code from nua_monthly_member_additions where dob = '' or dob like '%*%'";
$z=$X->sql($sql);
foreach ($z as $a) {
    print_r($a);
    $sql="select date_of_birth from nua_inf_employee_dependent where dependent_id = '" . $a['dependent_code'] . "' and date_of_birth <> ''";
    $t=$X->sql($sql);
    if (sizeof($t)>0) {
         $sql="update nua_employee_dependent set date_of_birth = '" . $t[0]['date_of_birth'] . "' where dependent_id = '" . $a['dependent_code'] . "'";
         $X->execute($sql);
         $sql="update nua_monthly_member_census set dob = '" . $t[0]['date_of_birth'] . "' where dependent_code = '" . $a['dependent_code'] . "'";
echo $sql;
         $X->execute($sql);
         $sql="update nua_monthly_member_additions set dob = '" . $t[0]['date_of_birth'] . "' where dependent_code = '" . $a['dependent_code'] . "'";
echo $sql;
         $X->execute($sql);
         $sql="update nua_monthly_member_terminations set dob = '" . $t[0]['date_of_birth'] . "' where dependent_code = '" . $a['dependent_code'] . "'";
echo $sql;
         $X->execute($sql);

    } 
}
?>

