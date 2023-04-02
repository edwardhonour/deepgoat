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

$sql="select id, company_name, flag_53, flag_eft, flag_current, flag_ghost from nua_company where id in (select distinct company_id from nua_monthly_member_census) and org_id <> 17 order by company_name"; 
$a=$X->sql($sql);
echo "<html>";
echo "<table>";
echo '<tr><th>Company Name</th><th style="width:120px;">ID</th><th style="width:120px;">EFT</th><th style="width:120px;">5/3</th><th style="width:120px;">Current</th><th style="width:120px;">Ghost</th><th style="width:120px;">None</th></tr>';

foreach ($a as $b) { 
    echo "<tr>";
    echo "<td>" . $b['company_name'] . "</td>";
    echo "<td>" . $b['id'] . "</td>";
    echo '<td><a href="post_fix.php?id=' . $b['id'] . '&a=eft">' . $b['flag_eft'] . "</td>";
    echo '<td><a href="post_fix.php?id=' . $b['id'] . '&a=53">' . $b['flag_53'] . "</td>";
    echo '<td><a href="post_fix.php?id=' . $b['id'] . '&a=current">' . $b['flag_current'] . "</td>";
    echo '<td><a href="post_fix.php?id=' . $b['id'] . '&a=ghost">' . $b['flag_ghost'] . "</td>";
    echo '<td><a href="post_fix.php?id=' . $b['id'] . '&a=reset">Reset</td>';
    echo "</tr>";
}
echo "</table>";
echo "<html>";

?>
