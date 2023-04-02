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
 
$sql = "select distinct employee_id from nua_monthly_member_additions where ssn = '' or ssn like '%*%'";
$z=$X->sql($sql);
foreach ($z as $a) {
    print_r($a);
    $sql="select social_security_number from nua_inf_employee where id = " . $a['employee_id'] . " and social_security_number <> ''";
    $t=$X->sql($sql);
    if (sizeof($t)>0) {
         $sql="update nua_employee set social_security_number = '" . $t[0]['social_security_number'] . "' where id = " . $a['employee_id'];
         $X->execute($sql);
         $sql="update nua_monthly_member_census set ssn = '" . $t[0]['social_security_number'] . "' where employee_id = " . $a['employee_id'];
echo $sql;
         $X->execute($sql);
         $sql="update nua_monthly_member_additions set ssn = '" . $t[0]['social_security_number'] . "' where employee_id = " . $a['employee_id'];
echo $sql;
         $X->execute($sql);
         $sql="update nua_monthly_member_terminations set ssn = '" . $t[0]['social_security_number'] . "' where employee_id = " . $a['employee_id'];
echo $sql;
         $X->execute($sql);

    } 
}
?>

