<?php

//--
// Weekly Cron Job for InfinityHR.
//
// Gets updates data for employees and dependents using getData.
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

//--
// Step 1 - Login
//--

function strip_special($string) {
	$res = preg_replace("/[^a-zA-Z0-9\s]/", "", $string);	
	return $res;
}
function login() {
	$login_url="https://api.prismhr.com/api-1.27/services/rest/login/createPeoSession";

	$customHeaders = array(
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json'
	);

	$postRequest="username=Nuaxess2&password=NewProject1&peoId=350*HSG";
	echo "LOGGING IN<br>";

	$ch = curl_init($login_url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postRequest);
	$response = curl_exec($ch);
	if(curl_errno($ch)){ echo "There is an error"; }
	$array=json_decode($response,true);
	$session_id=$array['sessionId'];
	return $session_id;
}

$session_id=login();
echo "SESSION ID: " . $session_id . "<br>";

//--
//-- We have $session_id;
//
$doit="Y";

if ($doit=="Y") {

$sql="select *  from inf_client_employee where employee_id <> 0 and  has_plans = 'Y' and employee_id not in (select id from nua_employee) order by newDate, id";
$list=$X->sql($sql);

foreach ($list as $c) {
            print_r($c);
	
	    //--
		//-- Update Employee Data such as address and list of dependents each time the job runs.
		//--
		
		
		$clientId=$c['clientId'];
		$employeeId=$c['employeeId'];
		$id=$c['id'];

                                $p=array();
                                $p['table_name']="nua_employee";
                                $p['action']="insert";
                                $p['company_id']=$c['company_id'];
                                $p['company_code']=$clientId;
                                $p['employee_code']=$c['employeeId'];
				$p['last_name']=$c['last_name'];
				$p['first_name']=$c['first_name'];
				$p['middle_name']=$c['middle_initial'];
				$p['marital_status']=$c["marital_status"];
				$p['peo_start_date']=$c["peo_start_date"];
				$p['address']=$c["address"];
				$p['city']=$c["city"];
				$p['state']=$c["state"];
				$p['zip']=$c["zipcode"];
$p['social_security_number']=$c['ssn'];
$p['date_of_birth']=$c['dob'];
				$p['county']=$c["county"];
				$p['home_phone']=$c["home_phone"];
				$p['phone_mobile']=$c["mobile_phone"];
				$p['gender']=$c["gender"];
				$p['email_address']=$c['email_address'];
                                print_r($p);
                                $y=$X->post($p);
$sql="select count(*) from nua_employee where id = " . $c['employee_id'];
$tt=$X->sql($sql);
if ($tt[0]['c']=='0') {
                                $sql="update nua_employee set id = " . $c['employee_id'] . " where id = " . $y;
                                echo $sql;
                                $X->execute($sql);
}
} 
//--
//-- we will run dependent getData by company.
//--
}
die();
?>
