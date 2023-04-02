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
$clients='Y';
if ($clients=='Y') {
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

//--
// LOOP THROUGH CLIENTS ALL CLIENTS INFINITI CONSIDERS ACTIVE
//--
if (0==0) {
foreach ($list as $c) {
	print_r($c);
	$clientId=$c['clientId'];
	$clientName=$c['clientName'];
	$legalName=$c['legalName'];
	
	$sql="select * from inf_client where clientId='" . $clientId . "'";
	$c=$X->sql($sql);
	if (sizeof($c)==0) {
		//-----------------------------------------------------
		// Adding the minimal data provided by this function.
		//-----------------------------------------------------
	    $post=array();
        $post['table_name']="inf_client";
        $post['action']="insert";
        $post['clientId']=$clientId;
        $post['clientName']=$clientName;
        $post['legalName']=$legalName;
        $post['newDate']=time();		
		$X->post($post);
	}
	
	//--
	//-- GET THE PLANS THIS CLIENT OFFERS
	//--


    if ($do_plans=="Y") {	
echo "Doing Plans";
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
				//-------------------------------------------------------------------------
				// New plans are assumed to be ours until they are manually deactivated
				//-------------------------------------------------------------------------			
				$post=array();
				$post['table_name']="inf_client_plan";
				$post['action']="insert";
				$post['clientId']=$clientId;
				$post['planId']=$planId;
				$post['planDescription']=$planDescription;
				$post['newDate']=time();	
                print_r($post);				
				$X->post($post);
			}  //  Add if does not exist.
		}	// planList Loop
	}  // isset(benefitPlanOverview)
	}
}		
}
}
//--
//-- QUERY ALL CLIENTS THAT HAVE AT LEAST ONE NUAXESS PLAN
//--

//---------------------------------------------------------------------------
// Daily Processing
//---------------------------------------------------------------------------

$sql="select clientId from inf_client where active = 'Y' and exists (select 'x' from inf_client_plan where inf_client_plan.clientId = inf_client.clientId and active = 'Y')";
$clients=$X->sql($sql);
foreach($clients as $client) {
	$clientId=$client['clientId'];
    print_r($client);

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
			//--
			//-- ADD ANY EMPLOYEES THAT DO NOT EXIST
			//--
                      	
			foreach($employee_list as $employee) {
				$employeeId=$employee;
				$sql="SELECT * FROM inf_client_employee where clientId = '" . $clientId . "' AND employeeId = '" . $employeeId . "'";
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
		  } else {
                      print_r($employees);
                  }
	     //--
		 //-- GET LIST OF EMPLOYEES AT THE CLIENT
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
					//	print_r($benefitPlan);
						$effectiveDate="";
						$planType="";
						$peoPremium="";
						foreach($benefitPlan['planDetail'] as $detail) {
							$effectiveDate=$detail['effectiveDate'];
							$planType=$detail['planType'];
							$peoPremium=$detail['peoPremium'];
						}
						$planId=$benefitPlan['planId'];
						//--
						//-- ONLY ADD ENROLLED PLANS
						//--
						if ($effectiveDate!=""||$planType!=""||$peoPremium!="") {
							$sql="select * from inf_client_employee_plan where clientId = '" . $clientId . "' and employeeId = '" . $employeeId . "' and planId = '" . $planId . "'";
							$g=$X->sql($sql);
							$post=array();
							$post['table_name']="inf_client_employee_plan";
							$post['action']="insert";
							$post['clientId']=$clientId;
							$post['employeeId']=$employeeId;
							$post['planId']=$planId;
							$post['effectiveDate']=$effectiveDate;
							$post['planType']=$planType;
							$post['peoPremium']=$peoPremium;
							if (sizeof($g)>0) {
								$post['id']=$g[0]['id'];
							}
							$post['coverageStart']=$benefitPlan['coverageStart'];
							if (isset($benefitPlan['coverageEnd'])) {
								$post['coverageEnd']=$benefitPlan['coverageEnd'];
							} else {
								$post['coverageEnd']="";
							}
							// Only Insert for now
							$post['newDate']=time();
							if (sizeof($g)==0) {
								print_r($post);
								$X->post($post);
							}
					
							//--- Make sure the employee is processed by the getData CRON Job.
							//
						
							$sql="update inf_client_employee set has_plans = 'Y' where employeeId = '" . $employeeId . "' and clientId = '" . $clientId . "'";
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

?>

