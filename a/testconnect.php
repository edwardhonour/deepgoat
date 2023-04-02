<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json'); 		
	$cs=file_get_contents("/var/www/vault/cs.json");
	$c=json_decode($cs,true);
	$cstring=$c['cs'];
	$un=$c['un'];
	$pwd=$c['pwd'];
		
	$db = new PDO($cstring,$un,$pwd);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
	$sql="SELECT * FROM D";
        $stmt = $db->prepare($sql);
	$stmt->execute();
	$output = array();
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);	
        print_r($results);
?>
