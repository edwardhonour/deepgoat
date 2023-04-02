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

if (isset($_GET['plans'])) {
   $do_plans=$_GET['plans'];	
} else {
   $do_plans="Y";
}

if (isset($_GET['clients'])) {
   $clients=$_GET['clients'];	
} else {
   $clients="Y";
}

$base_url="https://api.prismhr.com/api-1.27/services/rest/";

//--
// Step 1 - Login
//--

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

//----------------------------------------------------
// get client list including only active clients.
//----------------------------------------------------
$url="https://api.prismhr.com/api-1.27/services/rest/clientMaster/getClientList?inActive=false";
    $customHeaders = array(
        'sessionId: ' . $session_id,
        'Accept: application/json'
);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
if(curl_errno($ch)){
        echo "There is an error";
}
$clients=json_decode($response,true);
$list=$clients["clientListResult"]["clientList"];

$sql="select * from inf_client";
$list=$X->sql($sql);
foreach ($list as $c) {
	print_r($c);
	$clientId=$c['clientId'];
	$clientName=$c['clientName'];
	$legalName=$c['legalName'];
	
	$url="https://api.prismhr.com/api-1.27/services/rest/benefits/getClientBenefitPlans?clientId=";
	$url.=$clientId;
        $customHeaders = array(
        'sessionId: ' . $session_id,
        'Accept: application/json'
        );
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
		if(curl_errno($ch)){
			echo "There is an error";
		}
	$plans=json_decode($response,true);	
	if (isset($plans['benefitPlanOverview'])) {
		$plan_list=$plans['benefitPlanOverview'];
		foreach($plan_list as $plan) {
			$planId=$plan['planId'];
			$planDescription=$plan['planDescription'];
			$sql="SELECT * FROM inf_client_plan where clientId = '" . $clientId . "' AND planId = '" . $planId . "'";
			$p=$X->sql($sql);
			if (sizeof($p)==0) {	
				$post=array();
				$post['table_name']="inf_client_plan";
                                $sql="select * from inf_client_plan where planId = '" . $planId . "'";
                                $zz=$X->sql($sql);
                                if (sizeof($zz)>0) {
                                    $post['active']=$zz[0]['active'];
                                    $post['plan_type']=$zz[0]['plan_type'];
                                } else {
                                    $post['active']="X";
                                    $post['plan_type']="X";
                                }
				$post['action']="insert";
				$post['clientId']=$clientId;
				$post['planId']=$planId;
				$post['planDescription']=$planDescription;
				$post['newDate']=time();	
                                $post['new_plan']='Y';
                                print_r($post);				
				$X->post($post);
			} 
		}
	}  
}		
