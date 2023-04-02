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

$sql="select clientId, employeeId, id from inf_client_employee where dependentId = '' order by id";
$list=$X->sql($sql);
$count=sizeof($list);
$i=601;
foreach ($list as $c) {
      $i++;
      $clientId=$c['clientId'];
      $employeeId=$c['employeeId'];

      $sql="select id from nua_company where infinity_id = '" . $clientId . "'";
      $zz=$X->sql($sql);
      if (sizeof($zz)==0) {
             $sql="select * from inf_client where clientId='" . $clientId . "'";
             $hh=$X->sql($sql);

             $rr=array();
             $rr['table_name']="nua_company";
             $rr['action']="insert";
             $rr['org_id']=17;
             $rr['infinity_id']=$clientId;
             $rr['company_name']=$hh[0]['clientName'];
             $rr['status']="infiniti";
             print_r($rr);
             $company_id=$X->post($rr);
      } else {
            $company_id=$zz[0]['id'];
      }
	//--
	//-- Get Employee Dependents using API
	//--
			
	$url="https://api.prismhr.com/api-1.27/services/rest/benefits/getDependents?employeeId=" . $employeeId . "&clientId=" . $clientId;
	$customHeaders = array(
		'sessionId: ' . $session_id,
		'Accept: application/json'
	);

        $ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	if(curl_errno($ch)){ echo "There is an error"; }
	$deps=json_decode($response,true);
	$l=$deps['dependent'];
      $c['deps']=sizeof($l);
      $c['i']=$i;
      $c['of']=$count;	
      $c['pct']=($i/$count)*100;
      print_r($c);
	foreach($l as $k) {
              print_r($k);
	      $sql="select * from inf_client_employee where clientId = '" . $clientId . "' and dependentId = '" . $k['dependentId'] . "'";
	      $x=$X->sql($sql);
	      $post=array();
	      $post['table_name']="inf_client_employee";
	      $post['action']="insert";
	      if (sizeof($x)!=0) {
                   $post['id']=$x[0]['id'];
              }
	      $post['clientId']=$clientId;
	      $post['employeeId']=$employeeId;
	      $post['company_id']=$company_id;
	      $post['dependentId']=$k['dependentId'];
	      $post['dependent']="Y";
	      $post['spouse']=$k["spouse"];
	      $post['first_name']=strtoupper($k["firstName"]);
	      $post['last_name']=strtoupper($k["lastName"]);
	      $post['email_address']=strtolower($k["email"]);
	      $post['middle_initial']=strtoupper($k["middleInitial"]);
	      $post['gender']=$k["gender"];
	      $post['dob']=$k["birthdate"];
	      $post['ssn']=$k["ssn"];
	      $post['relation']=strtoupper($k["relation"]);
	      $post['address']=strtoupper($k["address"]);
	      $post['address_2']=strtoupper($k["addressLine2"]);
	      $post['city']=strtoupper($k["city"]);
	      $post['state']=strtoupper($k["state"]);
	      $post['zipcode']=$k["zip"];
	      $post['home_phone']=$k["homePhone"];
	      $post['work_phone']=$k["workPhone"];
	      $post['relation_type']=strtoupper($k["relationType"]);
	      $post['status']=strtoupper($k["status"]);
	      $post['newDate']=time();
	      $post['relation_to_insured']=strip_special(strtoupper($k["relationToInsured"]));
	      $X->post($post);				    
              print_r($post);
	}  // EMPLOYEE DEPENDENTS 
   }  // CLIENT EMPLOYEES
?>
