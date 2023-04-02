<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');
ini_set('max_execution_time', 900000);
ini_set('memory_limit', -1);

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

//
// Add Employees that do not exist.
//
//
 
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

$sql="select * from nua_company where org_id = 17 and id > 1 and id not in (4719,4143,4288,5299,4352,4470,4523,4556,5546,";
$sql.="5908,6019,6164,6180,4118,4134,4276,4551,4280,4213,5767,6077,4521,4737,4216,4467,4405,5650,6277,4217,4218,4382,5062,4497,4264) order by id";
$sql="select * from nua_company where org_id = 17 and id > 1 and id = 5843";
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
        
	$sql="select * from inf_client_employee_plan where planId like 'OP%' and  clientId = '" . $clientId . "'";
	echo $sql;

	$z=$X->sql($sql);
        $count=0;
	foreach ($z as $plan) {

print_r($plan);
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
               $g['plan_type']='*MEDICAL*';
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
			   print_r($post);
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

$sql="select * from nua_company where org_id = 17 and id > 1 and id ";
$sql.="not in (4719,4143,4288,5299,4352,4470,4523,4556,5546,";
$sql.="5908,6019,6164,6180,4118,4134,4276,4551,4280,4213,5767,6077,4521,4737,4216,4467,4405,5650,6277,4217,4218,4382,5062,4497,4264) order by id";
$clients=$X->sql($sql);
foreach($clients as $client) {
        $company_id=$client['id'];
        $clientId=$client['infinity_id'];

	$sql="select * from inf_client_employee_plan where planId like 'OPEN%' and clientId = '" . $clientId . "'";
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
               $g['plan_type']='*MEDICAL*';
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
                   //        $X->execute($sql);
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

