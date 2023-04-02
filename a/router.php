<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set("max_execution_time", -1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json');
$data = file_get_contents("php://input");
$data = json_decode($data, TRUE);
if (!isset($data['q'])) die('[]');
switch ($data['q']) {
	case 'add-wallet':
            print_r($data);
	break;				
        case 'facility':
            echo file_get_contents('/var/www/html/api/data.facilitylist.php');
            break;
}
?>

