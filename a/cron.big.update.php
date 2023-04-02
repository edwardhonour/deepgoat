<?php

//----------------------------------------------------------------------
// INFINITI / PRISM
//
// Get employee detail for employees added by cron.add.employes.  The
// cron job to add employee only returns the employeeId from prism.
// 
// This job gets the employee person information and updates. 
// inf_client_employee.  It does not add records to nua_employee.
//
//----------------------------------------------------------------------

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

     $sql="select clientId, employeeId, id from inf_client_employee where last_name = ''";
     $list=$X->sql($sql);
     echo sizeof($list) . " Records to be processed";
     sleep(5);
foreach ($list as $c) {
	
        //--
	//-- Update Employee Data such as address and list of dependents each time the job runs.
	//--
		
		
	$clientId=$c['clientId'];
	$employeeId=$c['employeeId'];
	$id=$c['id'];

	//---
	//--- Get Employee Master for all employees with plans direct API
	//---
		

	$url="https://api.prismhr.com/api-1.27/services/rest/employee/getEmployee?&clientId=" . $clientId . "&employeeId=" . $employeeId . "&options=Compensation";
	$customHeaders = array(
		'sessionId: ' . $session_id,
		'Accept: application/json'
	);

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	if(curl_errno($ch)){ echo "There is an error"; }
	$employees2=json_decode($response,true);
print_r($employees);
	$ssn=$employees2['employee'][0]['compensation']['ssn'];

        $post=array();
        $post['table_name']="inf_client_employee";
        $post['action']="insert";


	$url="https://api.prismhr.com/api-1.27/services/rest/employee/getEmployee?&clientId=" . $clientId . "&employeeId=" . $employeeId;
	$customHeaders = array(
		'sessionId: ' . $session_id,
		'Accept: application/json'
	);

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	if(curl_errno($ch)){ echo "There is an error"; }
	$employees=json_decode($response,true);
	$birth_date=$employees['employee'][0]['birthDate'];
        $employee=$employees['employee'];
	//$sql="select * from nua_employee where employee_code = '" . $employeeId . "'";
        //echo $sql;
        //                $c=$X->sql($sql);
        //                if (sizeof($c)>0) {
        //                    $post['employee_id']=$c[0]['id'];
	//	            $employee_id=$c[0]['id'];
        //                } else {
        //                   $pp=array(); 
        //                    $pp['table_name']="nua_employee";
        //                    $pp['action']="insert";
        //                    $pp['employee_code']=$employeeId;
        //                    $pp['company_code']=$client_id;
        //                    $employee_id = $X->post($pp);
         //               }

        foreach($employee as $e) {
                 $post=array();
                 $post['table_name']="inf_client_employee";
 	         $post['action']="insert";
                 $post['id']=$id;
	         $post['table_name']="inf_client_employee";
                 $post['employee_id']=$employeeId;
	         $post['last_name']=strtoupper($e["lastName"]);
		 $post['first_name']=strtoupper($e["firstName"]);
		 $post['middle_initial']=strtoupper($e["middleInitial"]);
		 $post['geo_code']=$e["geoCode"];
		 $post['marital_status']=$e["maritalStatus"];
		 $post['smoker']=$e["smoker"];
		 $post['peo_start_date']=$e["peoStartDate"];
		 $post['address']=strtoupper($e["addressLine1"]);
		 $post['address_2']=strtoupper($e["addressLine2"]);
		 $post['city']=strtoupper($e["city"]);
		 $post['dob']=$e['birthDate'];
		 $post['ssn']=$ssn;
		 $post['state']=strtoupper($e["state"]);
		 $post['zipcode']=$e["zipcode"];
		 $post['county']=strtoupper($e["county"]);
		 $post['home_phone']=$e["homePhone"];
		 $post['mobile_phone']=$e["mobilePhone"];
		 $post['gender']=strtoupper($e["gender"]);
		 $post['newDate']=time();
		 $post['email_address']=strtolower($e["emailAddress"]);
                 print_r($post);
		 $X->post($post);
                //        $pe=array();
                //            $pe['table_name']="nua_employee";
                //            $pe['id']=$employee_id;
                //            $pe['action']="insert";
//                $sql="select id from nua_company where infinity_id = '" . $clientId . "'";
//                $zz=$X->sql($sql);
//                if (sizeof($zz)==0) {
//                $sql="select * from inf_client where clientId='" . $clientId . "'";
//                $hh=$X->sql($sql);

  // $rr=array();
  // $rr['table_name']="nua_company";
  // $rr['action']="insert";
  // $rr['org_id']=17;
  // $rr['infinity_id']=$clientId;
  // $rr['company_name']=$hh[0]['clientName'];
  // $rr['status']="enrolled";
  // print_r($rr);
  // $company_id=$X->post($rr);
  //} else {
  // $pe['company_id']=$zz[0]['id'];
 //  $company_id=$zz[0]['id'];
//}
		//	    $pe['last_name']=strtoupper($e["lastName"]);
	//		    $pe['first_name']=strtoupper($e["firstName"]);
	//		    $pe['middle_name']=strtoupper($e["middleInitial"]);
//			    $pe['email']=strtolower($e["emailAddress"]);
//                            $pe['social_security_number']=$ssn;
//                            $pe['date_hired']=$e['peoStartDate'];
//                            $pe['marital_status']=$e['maritalStatus'];
//			    $pe['gender']=strtoupper($e["gender"]);
//			    $pe['date_of_birth']=$e['birthDate'];
//			    $pe['address']=strtoupper($e["addressLine1"]);
//			    $pe['state']=strtoupper($e["state"]);
//			    $pe['city']=strtoupper($e["city"]);
//			    $pe['zip']=$e["zipcode"];
//			    $pe['county']=strtoupper($e["county"]);
//			    $pe['phone']=$e["homePhone"];
//                            print_r($pe);
//$X->post($pe);
                }		
}
