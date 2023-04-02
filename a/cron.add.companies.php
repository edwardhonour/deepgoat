<?php

//--
// Weekly Cron Job for InfinityHR.
//
// Gets new clients, available plans, and employees.
//
//
//--
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');
ini_set('max_execution_time', 900000);

require_once('class.XRDB.php');
$X=new XRDB();

$base_url="https://api.prismhr.com/api-1.27/services/rest/";


$login_url="https://api.prismhr.com/api-1.27/services/rest/login/createPeoSession";

$customHeaders = array(
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json'
);

$postRequest="username=Nuaxess2&password=NewProject1&peoId=350*HSG";

$ch = curl_init($login_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postRequest);
$response = curl_exec($ch);
if(curl_errno($ch)){
        echo "There is an error";
}
$array=json_decode($response,true);
$session_id=$array['sessionId'];
print_r($array);
//--
//-- We have $session_id;
//

$url="https://api.prismhr.com/api-1.27/services/rest/clientMaster/getClientList?inActive=false";
    $customHeaders = array(
        'sessionId: ' . $session_id,
        'Accept: application/json'
        );

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
if(curl_errno($ch)){ echo "There is an error"; }
$clients=json_decode($response,true);
$list=$clients["clientListResult"]["clientList"];

//--
// LOOP THROUGH CLIENTS ALL CLIENTS INFINITI CONSIDERS ACTIVE
//--
foreach ($list as $c) {
	print_r($c);
	$clientId=$c['clientId'];
	$clientName=$c['clientName'];
	$legalName=$c['legalName'];
	
	$sql="select * from inf_client where clientId='" . $clientId . "'";
	$c=$X->sql($sql);
	if (sizeof($c)==0) {
           $post=array();
           $post['table_name']="inf_client";
           $post['action']="insert";
           $post['clientId']=$clientId;
           $post['clientName']=strtoupper($clientName);
           $post['legalName']=strtoupper($legalName);
           $post['newDate']=time();		
           print_r($post);
	   $X->post($post);
	}

}
die();
?>
