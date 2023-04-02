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

$sql="select clientId, employeeId, id from inf_client_employee where has_plans = 'Y' order by newDate, id";
$list=$X->sql($sql);

foreach ($list as $c) {
	
print_r($c);

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
			$ssn=$employees2['employee'][0]['compensation']['ssn'];

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
			$sql="select * from nua_employee where employee_code = '" . $employeeId . "'";
echo $sql;
                        $c=$X->sql($sql);
                        if (sizeof($c)>0) {
                            $post['employee_id']=$c[0]['id'];
		            $employee_id=$c[0]['id'];
                        } else {
                            $pp=array(); 
                            $pp['table_name']="nua_employee";
                            $pp['action']="insert";
                            $pp['employee_code']=$employeeId;
                            $pp['company_code']=$client_id;
                            $employee_id = $X->post($pp);
                        }

                        foreach($employee as $e) {
                             $post=array();
                             $post['table_name']="inf_client_employee";
			     $post['action']="insert";
                             $post['id']=$id;
			    $post['table_name']="inf_client_employee";
                            $post['employee_id']=$employee_id;
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
                            $pe=array();
                            $pe['table_name']="nua_employee";
                            $pe['id']=$employee_id;
                            $pe['action']="insert";
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
   $rr['status']="enrolled";
   print_r($rr);
   $company_id=$X->post($rr);
} else {
   $pe['company_id']=$zz[0]['id'];
   $company_id=$zz[0]['id'];
}
			    $pe['last_name']=strtoupper($e["lastName"]);
			    $pe['first_name']=strtoupper($e["firstName"]);
			    $pe['middle_name']=strtoupper($e["middleInitial"]);
			    $pe['email']=strtolower($e["emailAddress"]);
                            $pe['social_security_number']=$ssn;
                            $pe['date_hired']=$e['peoStartDate'];
                            $pe['marital_status']=$e['maritalStatus'];
			    $pe['gender']=strtoupper($e["gender"]);
			    $pe['date_of_birth']=$e['birthDate'];
			    $pe['address']=strtoupper($e["addressLine1"]);
			    $pe['state']=strtoupper($e["state"]);
			    $pe['city']=strtoupper($e["city"]);
			    $pe['zip']=$e["zipcode"];
			    $pe['county']=strtoupper($e["county"]);
			    $pe['phone']=$e["homePhone"];
                            print_r($pe);
$X->post($pe);
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
						$post['employee_id']=$employee_id;
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
		}  // CLIENT EMPLOYEES FROM API
}  // CLIENT WITH PLANS LOOP
//--
//-- we will run dependent getData by company.
//--

die();
$sql="select clientId, employeeId, id from inf_client_employee where has_plans = 'Y' and first_name = '' order by newDate, id";
$list=$X->sql($sql);

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
			$l=$employees['employee'];
			foreach($l as $e) {
				$sql="select * from inf_client_employee where id = " . $id;
				$j=$X->sql($sql);
				$post=$j[0];
				if ($post['company_id']==0) {
				    $sql="select id from nua_company where infinity_id = '" . $clientId . "'";
                                    $c=$X->sql($sql);
                                    if (sizeof($c)>0) {
                                    $post['company_id']=$c[0]['id'];
		                    $company_id=$c[0]['id'];
                                } 
			} else {
			       $company_id=$post['company_id'];	
			}
			if ($post['employee_id']==0) {
			$sql="select id from nua_employee where employee_code = '" . $employeeId . "'";
                        $c=$X->sql($sql);
                        if (sizeof($c)>0) {
                            $post['employee_id']=$c[0]['id'];
		            $employee_id=$c[0]['id'];
                        }
					} else {
						    $employee_id=$post['employee_id'];	
					}
					$post['table_name']="inf_client_employee";
					$post['action']="insert";
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
						$post['dob']=$k["birthdate"];
						$post['ssn']=$k["ssn"];
					$post['state']=strtoupper($e["state"]);
					$post['zipcode']=$e["zipcode"];
					$post['county']=strtoupper($e["county"]);
					$post['home_phone']=$e["homePhone"];
					$post['mobile_phone']=$e["mobilePhone"];
					$post['gender']=strtoupper($e["gender"]);
					$post['newDate']=time();
					$post['email_address']=strtolower($e["emailAddress"]);
					$X->post($post);
	         			if (sizeof($c)==0) {
                                             print_r($post);
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
					foreach($l as $k) {
						$sql="select * from inf_client_employee where clientId = '" . $clientId . "' and dependentId = '" . $k['dependentId'] . "'";
						$x=$X->sql($sql);
						$post=array();
						if (sizeof($x)==0) {
						$post['table_name']="inf_client_employee";
						$post['action']="insert";
						$post['clientId']=$clientId;
						$post['employeeId']=$employeeId;
						$post['company_id']=$company_id;
						$post['employee_id']=$employee_id;
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

				  } else {
					  //--
					  //-- UPDATE WITHOUT CHANGING DOB OR SSN
					  //--
					$post=$x[0];
					$post['table_name']="inf_client_employee";
					$post['action']="insert";
					if ($post['company_id']==0) $post['company_id']=$company_id;
					if ($post['employee_id']==0) $post['employee_id']=$employee_id;
					$post['clientId']=$clientId;
					$post['first_name']=strtoupper($k["firstName"]);
					$post['last_name']=strtoupper($k["lastName"]);
					$post['email_address']=strtolower($k["email"]);
					$post['middle_initial']=strtoupper($k["middleInitial"]);
					$post['gender']=strtoupper($k["gender"]);
					$post['relation']=strtoupper($k["relation"]);
						$post['dob']=$k["birthdate"];
						$post['ssn']=$k["ssn"];
					$post['address']=strtoupper($k["address"]);
					$post['address_2']=strtoupper($k["addressLine2"]);
					$post['city']=strtoupper($k["city"]);
					$post['state']=strtoupper($k["state"]);
					$post['zipcode']=$k["zip"];
					$post['home_phone']=$k["homePhone"];
					$post['work_phone']=$k["workPhone"];
					$post['relation_type']=strtoupper($k["relationType"]);
					$post['newDate']=time();
					$post['status']=strtoupper($k["status"]);
					$post['relation_to_insured']=strip_special(strtoupper($k["relationToInsured"]));
	                $X->post($post);					  
				  }  // UPDATE
			}  // EMPLOYEE DEPENDENTS 
		}  // CLIENT EMPLOYEES FROM API
}  // CLIENT WITH PLANS LOOP
//--
//-- we will run dependent getData by company.
//--

die();
$sql="select distinct clientId from inf_client_employee where dependentId <> '' and (dob='****-**-**' or ssn='***-**-****' or dob='' or ssn='')";
$clients=$X->sql($sql);
foreach ($clients as $client) {
	$session_id=login();
	$clientId=$client['clientId'];
	
	echo "SESSION ID: " . $session_id . "<br>";

			$url="https://api.prismhr.com/api-1.27/services/rest/system/getData?schemaName=Benefit&className=Dependent&clientId=" . $clientId;
			$customHeaders = array(
				'sessionId: ' . $session_id,
				'Accept: application/json'
			);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			if(curl_errno($ch)){ echo "There is an error"; }
			$job=json_decode($response,true);
			print_r($job);
			$downloadId=$job['downloadId'];
			$buildStatus="INIT";
			
			while($buildStatus!="DONE") {
				sleep(10);
				$url="https://api.prismhr.com/api-1.27/services/rest/system/getData?schemaName=Benefit&className=Dependent&clientId=" . $clientId . "&downloadId=" . $downloadId;
				echo $url;
				$customHeaders = array(
					'sessionId: ' . $session_id,
					'Accept: application/json'
				);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			if(curl_errno($ch)){ echo "There is an error"; }
			$job=json_decode($response,true);		
		    $buildStatus=$job['buildStatus'];
				if ($buildStatus=="DONE") {
					$dataObject=$job['dataObject'];
					$ch = curl_init($dataObject);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$response = curl_exec($ch);
                    $deps=json_decode($response,true);
                    $deplist=$deps['data'];
                    foreach($deplist as $dep) {
							$sql="select * from inf_client_employee where clientId = '" . $clientId . "' and dependentId = '" . $dep['id']. "' and (dob like '%*%' or ssn like '%*%')";
							echo $sql;
							$h=$X->sql($sql);
							if (sizeof($h)>0) {
							     $post=array();
								 $post['table_name']="inf_client_employee";
								 $post['action']="insert";
								 $post['id']=$h[0]['id'];
                                 $post['dob']=$dep['birthdate'];	
                                 $post['ssn']=$dep['ssn'];									
                                 $X->post($post);								 
							}
                    }
				}
			}
}

$sql="select distinct clientId from inf_client_employee where has_plans = 'Y' and dependentId = '' and ssn='***-**-****'";
$clients=$X->sql($sql);
foreach ($clients as $client) {
			$session_id=login();
			$clientId=$c['clientId'];
			echo "SESSION ID: " . $session_id . "<br>";

			$url="https://api.prismhr.com/api-1.27/services/rest/system/getData?schemaName=Employee&className=Compensation&clientId=" . $clientId;
			$customHeaders = array(
				'sessionId: ' . $session_id,
				'Accept: application/json'
			);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			if(curl_errno($ch)){ echo "There is an error"; }
			$job=json_decode($response,true);
			print_r($job);
			$downloadId=$job['downloadId'];
			$buildStatus="INIT";
			
			while($buildStatus!="DONE") {
				sleep(10);
				$url="https://api.prismhr.com/api-1.27/services/rest/system/getData?schemaName=Employee&className=Compensation&clientId=" . $clientId . "&downloadId=" . $downloadId;
				$customHeaders = array(
					'sessionId: ' . $session_id,
					'Accept: application/json'
				);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			if(curl_errno($ch)){ echo "There is an error"; }
			$job=json_decode($response,true);
			print_r($job);
		    $buildStatus=$job['buildStatus'];
				if ($buildStatus=="DONE") {
					$dataObject=$job['dataObject'];
					$ch = curl_init($dataObject);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$response = curl_exec($ch);
                    $deps=json_decode($response,true);
                    $deplist=$deps['data'];
                    foreach($deplist as $dep) {
							$sql="select * from inf_client_employee where clientId = '" . $clientId . "' and dependentId = '' and employeeId = '" . $dep[id] . "' and (ssn like '%*%')";
							echo $sql;
							$h=$X->sql($sql);
							if (sizeof($h)>0) {
							     $post=array();
								 $post['table_name']="inf_client_employee";
								 $post['action']="insert";
								 $post['id']=$h[0]['id'];
                                 $post['ssn']=$dep['ssn'];									
								 print_r($post);
                                 $X->post($post);								 
							}
                    }
				}
			}
}
die();
$sql="select distinct clientId from inf_client_employee where has_plans = 'Y' and dependentId = '' and (ssn='***-**-****' or ssn='')";
$clients=$X->sql($sql);
foreach ($clients as $client) {
	$session_id=login();
	echo "SESSION ID: " . $session_id . "<br>";

	$url="https://api.prismhr.com/api-1.27/services/rest/system/getData?schemaName=Employee&className=Compensation&clientId=" . $clientId;
	$customHeaders = array(
		'sessionId: ' . $session_id,
		'Accept: application/json'
	);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			if(curl_errno($ch)){ echo "There is an error"; }
			$job=json_decode($response,true);
						print_r($job);
			$downloadId=$job['downloadId'];
			$buildStatus="INIT";
			
			while($buildStatus!="DONE") {
				sleep(10);
				$url="https://api.prismhr.com/api-1.27/services/rest/system/getData?schemaName=Employee&className=Compensation&clientId=" . $clientId . "&downloadId=" . $downloadId;
				$customHeaders = array(
					'sessionId: ' . $session_id,
					'Accept: application/json'
				);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			if(curl_errno($ch)){ echo "There is an error"; }
			$job=json_decode($response,true);
			print_r($job);			
		    $buildStatus=$job['buildStatus'];
				if ($buildStatus=="DONE") {
					$dataObject=$job['dataObject'];
					$ch = curl_init($dataObject);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$response = curl_exec($ch);
                    $deps=json_decode($response,true);
                    $deplist=$deps['data'];
                    foreach($deplist as $dep) {
							$sql="select * from inf_client_employee where clientId = '" . $clientId . "' and dependentId = '' and employeeId = '" . $dep[id] . "' and (dob like '%*%')";
							$h=$X->sql($sql);
							if (sizeof($h)>0) {
							     $post=array();
								 $post['table_name']="inf_client_employee";
								 $post['action']="insert";
								 $post['id']=$h[0]['id'];
                                 $post['dob']=$dep['birthdate'];										
                                 $X->post($post);								 
							}
                    }
				}
			}
}


?>

