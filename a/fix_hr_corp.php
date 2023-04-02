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

//----------------------------------------------------
// get client list including only active clients.
//----------------------------------------------------

$monthlist=array();
array_push($monthlist,"2022-01");
array_push($monthlist,"2022-02");
array_push($monthlist,"2022-03");
array_push($monthlist,"2022-04");
array_push($monthlist,"2022-05");
array_push($monthlist,"2022-06");
array_push($monthlist,"2022-07");
array_push($monthlist,"2022-08");
array_push($monthlist,"2022-09");
array_push($monthlist,"2022-10");
array_push($monthlist,"2022-11");
array_push($monthlist,"2022-12");
array_push($monthlist,"2023-01");
array_push($monthlist,"2023-02");
array_push($monthlist,"2023-03");
array_push($monthlist,"2023-04");
array_push($monthlist,"2023-05");
array_push($monthlist,"2023-06");
array_push($monthlist,"2023-07");
array_push($monthlist,"2023-08");
array_push($monthlist,"2023-09");
array_push($monthlist,"2023-10");
array_push($monthlist,"2023-11");
array_push($monthlist,"2023-12");

$sql="select * from nua_company where id = 1 order by id";
$clients=$X->sql($sql);
foreach($clients as $client) {
        $company_id=$client['id'];
        $clientId=$client['infinity_id'];

	//--
	//-- CREATE THE OPTIONS LIST OF PLANS TO CHECK 
	//-- FOR THE CLIENT
	//--
$company_id = 2;	
        $sql="delete from nua_monthly_member_census where company_id = " . $company_id;
	$z=$X->execute($sql);
        $sql="delete from nua_monthly_member_additions where company_id = " . $company_id;
	$z=$X->execute($sql);
        $sql="delete from nua_monthly_member_terminations where company_id = " . $company_id;
	$z=$X->execute($sql);
        
$company_id = 1;

        $sql="delete from nua_monthly_member_census where company_id = " . $company_id;
	$z=$X->execute($sql);
        $sql="delete from nua_monthly_member_additions where company_id = " . $company_id;
	$z=$X->execute($sql);
        $sql="delete from nua_monthly_member_terminations where company_id = " . $company_id;
	$z=$X->execute($sql);
	$sql="select * from nua_census where company_id = 1 and apa = '01'";
	$z=$X->sql($sql);
        $count=0;
	foreach ($z as $plan) {

            $sql="select * from nua_employee where id  = " . $plan['employee_id'];
            $x=$X->sql($sql);
            if (sizeof($x)==0) {
               echo $sql;
               die();
            } else {
                $nua_employee=$x[0];
            }

            $sql="select * from nua_company where id = 1";
            $x=$X->sql($sql);
            if (sizeof($x)==0) {
               echo $sql;
               die();
            } else {
                $company=$x[0];
            }

            $sql="select * from nua_company_plan where company_id = 1 and APA_CODE = '" . $plan['plan'] . "'";
            $x=$X->sql($sql);
            if (sizeof($x)==0) {
               echo $sql;
               die();
            } else {
                $nua_plan=$x[0];
            }


            $plan['effectiveDate']=substr($plan['eff_dt'],6,4) . '-' . substr($plan['eff_dt'],0,2) . '-' . substr($plan['eff_dt'],3,2);
            $eff_month_id=substr($plan['effectiveDate'],0,7);
            $e_month_id=substr($plan['effectiveDate'],0,7);
            $term_month_id='';

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
                }
                
                if ($start=='Y') {
                     // write census

                           $post=array();
                           $post['table_name']="nua_monthly_member_census";
                           $post['action']="insert";
                           $post['client_id']='0000';
                           $post['month_id']=$m;
                           $post['company_id']=2;
                           $post['employee_code']='n' . $nua_employee['id'];
                           $post['dependent_code']='';
                           $post['employee_id']=$nua_employee['id'];
                           $post['first_name']=$nua_employee['first_name'];
                           $post['last_name']=$nua_employee['last_name'];
                           $post['middle_initial']=$nua_employee['middle_name'];
                           $post['dob']=$nua_employee['date_of_birth'];
                           $post['ssn']=$nua_employee['social_security_number'];
                           $post['gender']=$nua_employee['gender'];
                           $post['eff_dt']=$plan['effectiveDate'];
                           $post['term_dt']="";
                           $post['client_plan']="Platinum";
                           if ($plan['coverage_level']=="SI") {
                                $post['coverage_level']="EE";
                                $post['coverage_price']="386.46";
                           }
                           if ($plan['coverage_level']=="EC") {
                                $post['coverage_level']="EC";
                                $post['coverage_price']="796.91";
                           }
                           if ($plan['coverage_level']=="ES") {
                                $post['coverage_level']="ES";
                                $post['coverage_price']="854.88";
                           }
                           if ($plan['coverage_level']=="FA") {
                                $post['coverage_level']="FAM";
                                $post['coverage_price']="1252.13";
                           }
                           $post['company_name']=$company['company_name'];
                           $post['plan_type']="*MEDICAL*";
                           print_r($post);
                           $X->post($post);
                           if ($m=="2022-04") {
                                $count++;
                           }
                }
      
                if ($term_month_id==$m) {
                      $start='N';
                }
                if ($term_month_id==""&&$m=="2022-04") {
                      $start='N';
                }
            } 	

         } // PLAN LOOP
               $sql="update nua_company set invoicing = 'Y' where id = " . $company_id;
               $X->execute($sql);
} // CLIENT LOOP

?>

