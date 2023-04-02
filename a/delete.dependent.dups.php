<?php

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

$sql="select employee_id, employee_code, dependent_id, count(*) from nua_employee_dependent ";
$sql.=" where dependent_id <> '' group by employee_id, employee_code, dependent_id having count(*) > 1";
$a=$X->sql($sql);
foreach ($a as $b) { 
print_r($b);
    $sql="select id from nua_employee_dependent where ";
    $sql.= " employee_code = '" . $b['employee_code'] . "' ";
    $sql.= " and dependent_id = '" . $b['dependent_id'] . "' ";
    $sql.= " and employee_id = " . $b['employee_id'];
    $c=$X->sql($sql);
    $id="0";
    foreach($c as $d) {
        $id=$d['id'];
    }
    $sql="delete from nua_employee_dependent where id = " . $id;
    echo $sql;
    $X->execute($sql);

}

?>
