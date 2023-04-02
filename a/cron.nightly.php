<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');
ini_set('max_execution_time', 900000);
ini_set('memory_limit', -1);

die();
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

//--
//-- We have $session_id;
//
//-----------------------------------------------------
// CLIENTS
// ----------------------------------------------------
//
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
   $do_plans="Y";	
   $clients="Y";

$base_url="https://api.prismhr.com/api-1.27/services/rest/";

//
// Add New Plans for Client
//

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
	print_r($plans);
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
//
// Add Employees that do not exist.
//
//
 
$sql="select * from inf_client";
$list=$X->sql($sql);

foreach ($list as $c) {
        print_r($c);
	$clientId=$c['clientId'];
	$clientName=$c['clientName'];
	$legalName=$c['legalName'];
	

	$url="https://api.prismhr.com/api-1.27/services/rest/employee/getEmployeeList?clientId=";
	$url.=$clientId;
	$customHeaders = array(
		'sessionId: ' . $session_id,
		'Accept: application/json'
	);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($ch);
	if(curl_errno($ch)){ echo "There is an error";	}
	$employees=json_decode($response,true);	
        if (isset($employees['employeeList']['employeeId'])) {
		$employee_list=$employees['employeeList']['employeeId'];
                      	
			foreach($employee_list as $employee) {
				$employeeId=$employee;
				$sql="SELECT * FROM inf_client_employee where clientId = '" . $clientId . "' AND employeeId = '" . $employeeId . "' and dependentId =''";
				$p=$X->sql($sql);
				if (sizeof($p)==0) {
					$post=array();
					$post['table_name']="inf_client_employee";
					$post['action']="insert";
					$post['clientId']=$clientId;
					$post['employeeId']=$employeeId;
					$post['newDate']=0;		
                                        print_r($post);
					$X->post($post);
				}
			}
        }	
} 

$base_url="https://api.prismhr.com/api-1.27/services/rest/";

$session_id=login();

//--
//-- Get Employee Dependents
//--

$sql="select clientId, employeeId, id from inf_client_employee where dependentId = '' order by id";
$list=$X->sql($sql);
$count=sizeof($list);
$i=0;
foreach ($list as $c) {
      //print_r($c);
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
      //print_r($c);
	foreach($l as $k) {
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
              if (!isset($post['id'])) print_r($post);
	}  // EMPLOYEE DEPENDENTS 
   }  // CLIENT EMPLOYEES

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


$base_url="https://api.prismhr.com/api-1.27/services/rest/";

$session_id=login();
echo "SESSION ID: " . $session_id . "<br>";

$sql="select clientId, employeeId, id from inf_client_employee where last_name = ''";
$list=$X->sql($sql);
echo sizeof($list) . " Records to be processed";
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
                }		
}

//
// Create nua_employee record from inf_employee
//
$sql="select * from inf_client_employee where dependentId = '' and clientId in (select infinity_id from nua_company ";
$sql.="  where nua_company.org_id = 17) order by clientId, employeeId";
$employees=$X->sql($sql);
$count=sizeof($employees);
$i=0;
foreach ($employees as $employee) {
         $i++;
         $post=array();
	 $post['table_name']="nua_employee";
	 $post['action']="insert";
	 $sql="select id from nua_employee where company_code = '" . $employee['clientId'] . "' and employee_code = '" . $employee['employeeId'] . "'";
	 $z=$X->sql($sql);
	 if (sizeof($z)>0) {
	     $post['id']=$z[0]['id'];
		 $employee_id=$z[0]['id'];
	 }
	 
	 $sql="select * from nua_company where infinity_id = '" . $employee['clientId'] . "'";
	 $z=$X->sql($sql);
	 $company=$z[0];
	 
	 $post['user_id']=1;
	 $post['org_id']=17;
	 $post['company_id']=$company['id'];
	 $post['first_name']=$employee['first_name'];
	 $post['last_name']=$employee['last_name'];
	 $post['middle_name']=$employee['middle_initial'];
	 $post['employee_code']=$employee['employeeId'];
	 $post['company_code']=$employee['clientId'];
	 $post['email']=$employee['email_address'];
	 $post['date_hired']=$employee['peo_start_date'];
	 $post['marital_status']=$employee['marital_status'];
	 $post['gender']=$employee['gender'];
	 $post['date_of_birth']=$employee['dob'];
	 $post['address']=$employee['address'];
	 $post['state']=$employee['state'];
	 $post['city']=$employee['city'];
	 $post['suite']=$employee['address_2'];
	 $post['zip']=$employee['zipcode'];
	 $post['phone']=$employee['home_phone'];
	 $post['phone_mobile']=$employee['mobile_phone'];
	 $post['employee_status']=$employee['active'];
	 $post['how_record_was_added']="API";
	 $post['employee_name']=$employee['last_name'] . ", " . $employee['first_name'];
	 $post['work_status']=$employee['active'];
	 $post['social_security_number']=$employee['ssn'];
         $employee_id=$X->post($post);
         $post['i']=$i;
         $post['of']=$count;
         $post['pct']=($i/$count)*100;

	 $sql="select * from inf_client_employee where employeeId = '" . $employee['employeeId'] . "' and dependentId <> '' order by dependentId";
	 $dependents=$X->sql($sql);
	 foreach ($dependents as $d) {
				$p=array();
				$p['table_name']="nua_employee_dependent";
				$p['action']="insert";
				$p['user_id']=1;
				$p['employee_id']=$employee_id;
				$p['company_id']=$company['id'];
				$p['first_name']=$d['first_name'];
				$p['last_name']=$d['last_name'];
				$p['middle_name']=$d['middle_initial'];
				$p['gender']=$d['gender'];
				$p['date_of_birth']=$d['dob'];
				$p['social_security_number']=$d['ssn'];
$relationship="";
if ($d['relation']=="02") $relationship="WIFE";
if ($d['relation']=="03") $relationship="HUSBAND";
if ($d['relation']=="04") $relationship="SON";
if ($d['relation']=="05") $relationship="DAUGHTER";
if ($d['relation']=="06") $relationship="OTHER";
if ($d['relation']=="") $relationship=$d['relation_to_insured'];
				$p['relationship']=$relationship;
				$p['company_code']=$d['clientId'];
				$p['employee_code']=$d['employeeId'];
				$p['dependent_id']=$d['dependentId'];
				$p['relation_type']=$d['relation_type'];
				$p['relation_to_insured']=$d['relation_to_insured'];
                                $sql="select * from nua_employee_dependent where employee_code = '" . $p['employee_code'] . "' and dependent_id = '" . $p['dependent_id'] . "'";
                                $tt=$X->sql($sql);
                                if (sizeof($tt)>0) {
                                     $p['id']=$tt[0]['id'];
                                }
                                print_r($p);
				$X->post($p);
	}
	 if (!isset($post['id'])) {
		 print_r($post);
		 $X->post($post);
	}
}


   $do_plans="Y";
   $clients="Y";


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

$sql="select clientId from inf_client order by clientId";
$clients=$X->sql($sql);
foreach($clients as $client) {
        $clientId=$client['clientId'];

	//--
	//-- CREATE THE OPTIONS LIST OF PLANS TO CHECK 
	//-- FOR THE CLIENT
	//--
	
	$sql="select planId from inf_client_plan where clientId = '" . $clientId . "' and active ='Y'";
	$z=$X->sql($sql);
	$options="";
	foreach($z as $z0) {
             $options.="&planId=".$z0['planId'];	   
	}
	//--
	//-- ONLY PROCESS THEM CLIENT IF IT HAS AT LEAST 1 NUAXESS PLAN.
	//--
	if ($options!="") {	

		//--
		//-- UPDATE THE LIST OF EMPLOYEES AT THE CLIENT
		//-- WE ADD EMPLOYEES BOTH WEEKLY AND DAILY
		//--
		$sql="select * from inf_client_employee where clientId = '" . $clientId . "'";
		$employees=$X->sql($sql);
		foreach ($employees as $employee) {
		
			$employeeId=$employee['employeeId'];
	
			//--
			//-- THIS FUNCTION RETURN AVAILABLE EMPLOYEE PLANS NOT ONLY 
			//-- ENROLLED PLAN.  IF THE planDetail ARRAY is EMPTY the 
			//-- CUSTOMER IS NOT ENROLLED IN THE PLAN.
			//--
				
			echo $employeeId . ",";
			
			$url="https://api.prismhr.com/api-1.27/services/rest/benefits/getBenefitPlans?clientId=";
			$url.=$clientId;
			$url.="&employeeId=" . $employeeId;
			$url.=$options;
			$customHeaders = array(
				'sessionId: ' . $session_id,
				'Accept: application/json'
			);
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $customHeaders);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			if(curl_errno($ch)){ echo "There is an error"; }
			$ep=json_decode($response,true);	
			
			if (isset($ep['benefitPlan'])) {
				$benefitPlans=$ep['benefitPlan'];
				foreach($benefitPlans as $benefitPlan) {
					//print_r($benefitPlan);
					$effectiveDate="";
					$planType="";
					$peoPremium="";
					foreach($benefitPlan['planDetail'] as $detail) {
						$effectiveDate=$detail['effectiveDate'];
						$planType=$detail['planType'];
						$peoPremium=$detail['peoPremium'];
                                                if (isset($detail['coverage'])) $coverage=$detail['coverage']; else $coverage="";
                                                if (isset($detail['spouseCoverage'])) $spouseCoverage=$detail['spouseCoverage']; else $spouseCoverage="";
                                                if (isset($detail['dependentCoverage'])) $dependentCoverage=$detail['dependentCoverage']; else $dependentCoverage="";

					}
					$planId=$benefitPlan['planId'];
					//--
					//-- ONLY ADD ENROLLED PLANS
					//--
					if ($effectiveDate!=""||$planType!=""||$peoPremium!="") {
                                                
						$sql="select * from inf_client_employee_plan where ";
                                                $sql.=" clientId = '" . $clientId . "' and employeeId = '" . $employeeId . "'";
                                                $sql.="  and planId = '" . $planId . "'";
						$g=$X->sql($sql);
						$post=array();
                                                $post['changes']='N';
						if (sizeof($g)>0) {
							$post['id']=$g[0]['id'];
						} else {
                                                        $post['changes']='A';
                                                }
						$post['table_name']="inf_client_employee_plan";
						$post['action']="insert";
						$post['clientId']=$clientId;
						$post['employeeId']=$employeeId;
						$post['planId']=$planId; 
						$post['effectiveDate']=$effectiveDate;
						$post['planType']=$planType;
						$post['peoPremium']=$peoPremium;
                                                $post['coverage']=$coverage;
                                                $post['spouseCoverage']=$spouseCoverage;
                                                $post['dependentCoverage']=$dependentCoverage;
						$post['coverageStart']=$benefitPlan['coverageStart'];
						if (isset($benefitPlan['coverageEnd'])) {
							$post['coverageEnd']=$benefitPlan['coverageEnd'];
						} else {
							$post['coverageEnd']="";
						}
                                                if (sizeof($g)>0) {
                                                       if ($g[0]['effectiveDate']!=$effectiveDate) $post['changes']='Y';
                                                       if ($g[0]['coverageStart']!=$benefitPlan['coverageStart']) $post['changes']='Y';
                                                       if (isset($benefitPlan['coverageEnd'])) {
                                                            if ($g[0]['coverageEnd']!=$benefitPlan['coverageEnd']) {
                                                                $post['changes']='Y';
                                                            }
                                                       }
                                                       if ($g[0]['peoPremium']!=$peoPremium) $post['changes']='Y';
                                                }
						$post['newDate']=time();
						print_r($post);
						$X->post($post);
						//--- Make sure the employee is processed by the getData CRON Job.
						//
						
						$sql="update inf_client_employee set has_plans = 'Y' where ";
                                                $sql.=" employeeId = '" . $employeeId . "' and clientId = '" . $clientId . "'";
						$X->execute($sql);
						
						}  // EMPLOYEE IS ENROLLED IN PLAN
					} // LOOP THOUGH PLANS	
				}  // THERE IS AT LEAST 1 PLAN			
			}  // EMPLOYEE LOOP
        $sql="select count(*) as c from inf_client_employee where clientId = '" . $clientId . "'";
        $r=$X->sql($sql);
        $sql="update inf_client set employee_count = " . $r[0]['c'] . " where clientId = '" . $clientId . "'";
        $X->execute($sql);

	} // HAS NUAXESS PLANS 
} // CLIENT LOOP
//
   $do_plans="Y";
   $clients="Y";

$base_url="https://api.prismhr.com/api-1.27/services/rest/";

//----------------------------------------------------
// get client list including only active clients.
//----------------------------------------------------

$monthlist=array();
array_push($monthlist,"2023-01");
array_push($monthlist,"2023-02");
//array_push($monthlist,"2023-03");
//array_push($monthlist,"2023-04");
//array_push($monthlist,"2023-05");
//array_push($monthlist,"2023-06");
//array_push($monthlist,"2023-07");
//array_push($monthlist,"2023-08");
//array_push($monthlist,"2023-09");
//array_push($monthlist,"2023-10");
//array_push($monthlist,"2023-11");
//array_push($monthlist,"2023-12");

$sql="select * from nua_company where org_id = 17 and id > 1 order by id";
$clients=$X->sql($sql);
foreach($clients as $client) {
        $company_id=$client['id'];
        $clientId=$client['infinity_id'];

	//--
	//-- CREATE THE OPTIONS LIST OF PLANS TO CHECK 
	//-- FOR THE CLIENT
	//--
	
//      $sql="delete from nua_monthly_member_census where company_id = " . $company_id;
//	$z=$X->execute($sql);
//      $sql="delete from nua_monthly_member_additions where company_id = " . $company_id;
//	$z=$X->execute($sql);
//      $sql="delete from nua_monthly_member_terminations where company_id = " . $company_id;
//	$z=$X->execute($sql);
        
	$sql="select * from inf_client_employee_plan where clientId = '" . $clientId . "'";
	$z=$X->sql($sql);
        $count=0;
	foreach ($z as $plan) {

            $sql="select * from inf_client_employee where clientId = '" . $clientId . "' and employeeId = '" . $plan['employeeId'] . "'";
            $x=$X->sql($sql);
            $inf_employee=$x[0];

            $sql="select * from nua_employee where company_code = '" . $clientId . "' and employee_code = '" . $plan['employeeId'] . "'";
            $x=$X->sql($sql);
            if (sizeof($x)==0) {
               echo $sql;
            } else {
                $nua_employee=$x[0];
            }

            $sql="select * from nua_company where infinity_id = '" . $clientId . "'";
            $x=$X->sql($sql);
            if (sizeof($x)==0) {
               echo $sql;
            } else {
                $company=$x[0];
            }

            $sql="select * from nua_company_plan where company_id = " . $company['id']  . " and plan_code = '" . $plan['planId'] . "'";
            $x=$X->sql($sql);
            if (sizeof($x)==0) {
               $g=array();
               $g['table_name']="nua_company_plan";
               $g['action']="insert";
               $g['company_id']=$company['id'];
               $g['plan_code']=$plan['planId'];
               $g['ee_price']=$plan['peoPremium'];
               $g['eec_price']=$plan['peoPremium'];
               $g['ees_price']=$plan['peoPremium'];
               $g['fam__price']=$plan['peoPremium'];
               $g['plan_type']='*LIFE*';
               print_r($g);
               $X->post($g);
               $sql="select * from nua_company_plan where company_id = " . $company['id']  . " and plan_code = '" . $plan['planId'] . "'";
               $x=$X->sql($sql);
               $nua_plan=$x[0];
            } else {
                $nua_plan=$x[0];
            }


            $eff_month_id=substr($plan['effectiveDate'],0,7);
            $e_month_id=substr($plan['effectiveDate'],0,7);
            if ($plan['coverageEnd']!="") {
                 $term_month_id=substr($plan['coverageEnd'],0,7);
            } else {
                 $term_month_id='';
            }

            if (intval(substr($eff_month_id,0,4))<2022&&$term_month_id=='') {
                 $eff_month_id = "2022-01";
            } else {
                 if (intval(substr($eff_month_id,0,4))<2022) {
                      if ($term_month_id!='') {
                      if (intval(substr($term_month_id,0,4))>=2022) {
                            $eff_month_id="2022-01";
                      } else {
                             $eff_month_id = "0000-00";
                      }
                    }
                 }
            }
           
            $start="N";
            foreach($monthlist as $m) {
                if ($eff_month_id==$m) {
                      $start="Y";
                      if ($eff_month_id==$e_month_id) {
                           // write addition record.
                           $post=array();
                           $post['table_name']="nua_monthly_member_additions";
                           $post['action']="insert";
                           $post['client_id']=$plan['clientId'];
                           $post['month_id']=$m;
                           $post['company_id']=$company['id'];
                           $post['employee_code']=$inf_employee['employeeId'];
                           $post['dependent_code']='';
                           $post['employee_id']=$nua_employee['id'];
                           $post['first_name']=$inf_employee['first_name'];
                           $post['last_name']=$inf_employee['last_name'];
                           $post['middle_initial']=$inf_employee['middle_initial'];
                           $post['dob']=$inf_employee['dob'];
                           $post['ssn']=$inf_employee['ssn'];
                           $post['gender']=$inf_employee['gender'];
                           $post['eff_dt']=$plan['effectiveDate'];
                           $post['term_dt']=$plan['coverageEnd'];
                           $post['client_plan']=$plan['planId'];
                           $post['coverage_level']=$plan['planType'];
                           $post['coverage_price']=$plan['peoPremium'];
                           $post['company_name']=$company['company_name'];
                           $post['plan_type']=$nua_plan['plan_type'];
                           $sql="select id from nua_monthly_member_additions where month_id = '" . $m . "' ";
                           $sql.=" and employee_code='" . $post['employee_code'] . "' and ";
                           $sql.=" client_plan = '" . $post['client_plan'] . "'";
                           $s=$X->sql($sql);
                           if (sizeof($s)==0) {
                               if ($post['eff_dt']!=$post['term_dt']) {
                                   if ($post['coverage_price']!='') print_r($post);
                                   if ($post['coverage_price']!='') $X->post($post);
                               }
                           }
                      } 
                }
                
                if ($start=='Y') {
                     // write census

                           $post=array();
                           $post['table_name']="nua_monthly_member_census";
                           $post['action']="insert";
                           $post['client_id']=$plan['clientId'];
                           $post['month_id']=$m;
                           $post['company_id']=$company['id'];
                           $post['employee_code']=$inf_employee['employeeId'];
                           $post['dependent_code']='';
                           $post['employee_id']=$nua_employee['id'];
                           $post['first_name']=$inf_employee['first_name'];
                           $post['last_name']=$inf_employee['last_name'];
                           $post['middle_initial']=$inf_employee['middle_initial'];
                           $post['dob']=$inf_employee['dob'];
                           $post['ssn']=$inf_employee['ssn'];
                           $post['gender']=$inf_employee['gender'];
                           $post['eff_dt']=$plan['effectiveDate'];
                           $post['term_dt']=$plan['coverageEnd'];
                           $post['client_plan']=$plan['planId'];
                           $post['coverage_level']=$plan['planType'];
                           $post['coverage_price']=$plan['peoPremium'];
                           $post['company_name']=$company['company_name'];
                           $post['plan_type']=$nua_plan['plan_type'];
                           $sql="select id from nua_monthly_member_census where month_id = '" . $m . "' ";
                           $sql.=" and employee_code='" . $post['employee_code'] . "' and ";
                           $sql.=" client_plan = '" . $post['client_plan'] . "'";
                           $s=$X->sql($sql);
                           if (sizeof($s)>0) {
                               $post['id']=$s[0]['id'];
                           }
                           if ($post['coverage_price']!='') print_r($post);
                           if ($post['coverage_price']!='') $X->post($post);
                } else {
                           $sql="delete from nua_monthly_member_census where month_id = '" . $m . "' ";
                           $sql.=" and employee_code='" . $post['employee_code'] . "' and ";
                           $sql.=" client_plan = '" . $post['client_plan'] . "'";
               //            $X->execute($sql);
                }
      
                if ($term_month_id==$m) {
                      $start='N';
                           $post=array();
                           $post['table_name']="nua_monthly_member_terminations";
                           $post['action']="insert";
                           $post['client_id']=$plan['clientId'];
                           $post['month_id']=$m;
                           $post['company_id']=$company['id'];
                           $post['employee_code']=$inf_employee['employeeId'];
                           $post['dependent_code']='';
                           $post['employee_id']=$nua_employee['id'];
                           $post['first_name']=$inf_employee['first_name'];
                           $post['last_name']=$inf_employee['last_name'];
                           $post['middle_initial']=$inf_employee['middle_initial'];
                           $post['dob']=$inf_employee['dob'];
                           $post['ssn']=$inf_employee['ssn'];
                           $post['gender']=$inf_employee['gender'];
                           $post['eff_dt']=$plan['effectiveDate'];
                           $post['term_dt']=$plan['coverageEnd'];
                           $post['client_plan']=$plan['planId'];
                           $post['coverage_level']=$plan['planType'];
                           $post['coverage_price']=$plan['peoPremium'];
                           $post['company_name']=$company['company_name'];
                           $post['plan_type']=$nua_plan['plan_type'];
                           $sql="select id from nua_monthly_member_terminations where month_id = '" . $m . "' ";
                           $sql.=" and employee_code='" . $post['employee_code'] . "' and ";
                           $sql.=" client_plan = '" . $post['client_plan'] . "'";
                           $s=$X->sql($sql);
                           if (sizeof($s)==0) {
                               print_r($post);
                               if ($post['coverage_price']!='') $X->post($post);
                           }
                }
            } 	

         } // PLAN LOOP
} 

$monthlist=array();
array_push($monthlist,"2023-01");
array_push($monthlist,"2023-02");

$sql="select * from nua_company where org_id = 17 and id > 1 order by id";
$clients=$X->sql($sql);
foreach($clients as $client) {
        $company_id=$client['id'];
        $clientId=$client['infinity_id'];

	$sql="select * from inf_client_employee_plan where clientId = '" . $clientId . "'";
	$z=$X->sql($sql);
        $count=0;
	foreach ($z as $plan) {

            $sql="select * from inf_client_employee where clientId = '" . $clientId . "' and employeeId = '" . $plan['employeeId'] . "'";
            $x=$X->sql($sql);
            $inf_employee=$x[0];

            $sql="select * from nua_employee where company_code = '" . $clientId . "' and employee_code = '" . $plan['employeeId'] . "'";
            $x=$X->sql($sql);
            if (sizeof($x)==0) {
               echo $sql;
            } else {
                $nua_employee=$x[0];
            }

            $sql="select * from nua_company where infinity_id = '" . $clientId . "'";
            $x=$X->sql($sql);
            if (sizeof($x)==0) {
               echo $sql;
            } else {
                $company=$x[0];
            }

            $sql="select * from nua_company_plan where company_id = " . $company['id']  . " and plan_code = '" . $plan['planId'] . "'";
            $x=$X->sql($sql);
            if (sizeof($x)==0) {
               $g=array();
               $g['table_name']="nua_company_plan";
               $g['action']="insert";
               $g['company_id']=$company['id'];
               $g['plan_code']=$plan['planId'];
               $g['ee_price']=$plan['peoPremium'];
               $g['eec_price']=$plan['peoPremium'];
               $g['ees_price']=$plan['peoPremium'];
               $g['fam__price']=$plan['peoPremium'];
               $g['start_month_id']="2021-01";
               $g['end_month_id']="2022-12";
               $g['plan_type']='*LIFE*';
               print_r($g);
               $X->post($g);
               $sql="select * from nua_company_plan where company_id = " . $company['id']  . " and plan_code = '" . $plan['planId'] . "'";
               $x=$X->sql($sql);
               $nua_plan=$x[0];
            } else {
                $nua_plan=$x[0];
            }

            $eff_month_id=substr($plan['effectiveDate'],0,7);
            $e_month_id=substr($plan['effectiveDate'],0,7);
            if ($plan['coverageEnd']!="") {
                 $term_month_id=substr($plan['coverageEnd'],0,7);
            } else {
                 $term_month_id='';
            }

            if (intval(substr($eff_month_id,0,4))<2022&&$term_month_id=='') {
                 $eff_month_id = "2022-01";
            } else {
                 if (intval(substr($eff_month_id,0,4))<2022) {
                      if ($term_month_id!='') {
                      if (intval(substr($term_month_id,0,4))>=2022) {
                            $eff_month_id="2022-01";
                      } else {
                             $eff_month_id = "0000-00";
                      }
                    }
                 }
            }
           
            $start="N";
            foreach($monthlist as $m) {
                if ($eff_month_id==$m) {
                      $start="Y";
                      if ($eff_month_id==$e_month_id) {
                           // write addition record.
                           $post=array();
                           $post['table_name']="nua_monthly_member_additions";
                           $post['action']="insert";
                           $post['client_id']=$plan['clientId'];
                           $post['month_id']=$m;
                           $post['company_id']=$company['id'];
                           $post['employee_code']=$inf_employee['employeeId'];
                           $post['dependent_code']='';
                           $post['employee_id']=$nua_employee['id'];
                           $post['first_name']=$inf_employee['first_name'];
                           $post['last_name']=$inf_employee['last_name'];
                           $post['middle_initial']=$inf_employee['middle_initial'];
                           $post['dob']=$inf_employee['dob'];
                           $post['ssn']=$inf_employee['ssn'];
                           $post['gender']=$inf_employee['gender'];
                           $post['eff_dt']=$plan['effectiveDate'];
                           $post['term_dt']=$plan['coverageEnd'];
                           $post['client_plan']=$plan['planId'];
                           $post['coverage_level']=$plan['planType'];
                           $post['coverage_price']=$plan['peoPremium'];
                           $post['company_name']=$company['company_name'];
                           $post['plan_type']=$nua_plan['plan_type'];
                           $sql="select id from nua_monthly_member_additions where month_id = '" . $m . "' ";
                           $sql.=" and employee_code='" . $post['employee_code'] . "' and ";
                           $sql.=" client_plan = '" . $post['client_plan'] . "'";
                           $s=$X->sql($sql);
                           if (sizeof($s)==0) {
                               if ($post['eff_dt']!=$post['term_dt']) {
                                   if ($post['coverage_price']!='') print_r($post);
                                   if ($post['coverage_price']!='') $X->post($post);
                               }
                           }
                      } 
                }
                
                if ($start=='Y') {
                     // write census

                           $post=array();
                           $post['table_name']="nua_monthly_member_census";
                           $post['action']="insert";
                           $post['client_id']=$plan['clientId'];
                           $post['month_id']=$m;
                           $post['company_id']=$company['id'];
                           $post['employee_code']=$inf_employee['employeeId'];
                           $post['dependent_code']='';
                           $post['employee_id']=$nua_employee['id'];
                           $post['first_name']=$inf_employee['first_name'];
                           $post['last_name']=$inf_employee['last_name'];
                           $post['middle_initial']=$inf_employee['middle_initial'];
                           $post['dob']=$inf_employee['dob'];
                           $post['ssn']=$inf_employee['ssn'];
                           $post['gender']=$inf_employee['gender'];
                           $post['eff_dt']=$plan['effectiveDate'];
                           $post['term_dt']=$plan['coverageEnd'];
                           $post['client_plan']=$plan['planId'];
                           $post['coverage_level']=$plan['planType'];
                           $post['coverage_price']=$plan['peoPremium'];
                           $post['company_name']=$company['company_name'];
                           $post['plan_type']=$nua_plan['plan_type'];
                           $sql="select id from nua_monthly_member_census where month_id = '" . $m . "' ";
                           $sql.=" and employee_code='" . $post['employee_code'] . "' and ";
                           $sql.=" client_plan = '" . $post['client_plan'] . "'";
                           $s=$X->sql($sql);
                           if (sizeof($s)>0) {
                               $post['id']=$s[0]['id'];
                               if ($post['coverage_price']!='') print_r($post);
                           }
                           if ($post['coverage_price']!='') $X->post($post);
                } else {
                           $sql="delete from nua_monthly_member_census where month_id = '" . $m . "' ";
                           $sql.=" and employee_code='" . $post['employee_code'] . "' and ";
                           $sql.=" client_plan = '" . $post['client_plan'] . "'";
                           $X->execute($sql);
                }
      
                if ($term_month_id==$m) {
                      $start='N';
                           $post=array();
                           $post['table_name']="nua_monthly_member_terminations";
                           $post['action']="insert";
                           $post['client_id']=$plan['clientId'];
                           $post['month_id']=$m;
                           $post['company_id']=$company['id'];
                           $post['employee_code']=$inf_employee['employeeId'];
                           $post['dependent_code']='';
                           $post['employee_id']=$nua_employee['id'];
                           $post['first_name']=$inf_employee['first_name'];
                           $post['last_name']=$inf_employee['last_name'];
                           $post['middle_initial']=$inf_employee['middle_initial'];
                           $post['dob']=$inf_employee['dob'];
                           $post['ssn']=$inf_employee['ssn'];
                           $post['gender']=$inf_employee['gender'];
                           $post['eff_dt']=$plan['effectiveDate'];
                           $post['term_dt']=$plan['coverageEnd'];
                           $post['client_plan']=$plan['planId'];
                           $post['coverage_level']=$plan['planType'];
                           $post['coverage_price']=$plan['peoPremium'];
                           $post['company_name']=$company['company_name'];
                           $post['plan_type']=$nua_plan['plan_type'];
                           $sql="select id from nua_monthly_member_terminations where month_id = '" . $m . "' ";
                           $sql.=" and employee_code='" . $post['employee_code'] . "' and ";
                           $sql.=" client_plan = '" . $post['client_plan'] . "'";
                           $s=$X->sql($sql);
                           if (sizeof($s)==0) {
                               print_r($post);
                               if ($post['coverage_price']!='') $X->post($post);
                           }
                }
            } 	
         } 
} 

?>

