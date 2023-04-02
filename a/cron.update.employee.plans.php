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
print_r($ch);
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

//$sql="select clientId, count(*) as c from inf_client_employee_plan group by clientId order by 2 desc";
$sql="select clientId from inf_client where clientId not in (select infinity_id from nua_company where org_id = 17 and ";
$sql.=" id in (select company_id from nua_company_invoice)) order by 1";
$clients=$X->sql($sql);
foreach($clients as $client) {
        $clientId=$client['clientId'];
        //print_r($client);

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
                                                //if ($post['changes']!='N') {
print_r($benefitPlan);
						     print_r($post);
						     $X->post($post);
                                                //}					
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

?>

