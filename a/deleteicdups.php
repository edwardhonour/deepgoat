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

$sql="select first_name, last_name, count(*) as c from nua_monthly_member_census where company_id = 5052 ";
$sql.=" and month_id = '2022-03' group by first_name, last_name having count(*) > 1"; 
$a=$X->sql($sql);
foreach ($a as $b) { 
print_r($a);
    $sql="select id from nua_monthly_member_census where company_id = 5052 and ";
    $sql.=" last_name = '" . $b['last_name'] . "' and first_name = '" . $b['first_name'] . "' order by id desc";
echo $sql;
    
    $c=$X->sql($sql);
    $id="0";
    foreach($c as $d) {
        print_r($d);
        $id=$d['id'];
    }
    $sql="delete from nua_monthly_member_census where id = " . $id;
    $X->execute($sql);
}

?>
