<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('max_execution_time',1200);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
$db = new PDO("mysql:host=goatx-db-1-instance-1.cdywrvje0okb.us-east-1.rds.amazonaws.com:3306;dbname=GOATX;charset=utf8", 'GOATX', 'Meelup578!!!');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "Hello";
$sql="SELECT X FROM D";
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$output = array();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);	
print_r($results);
?>
