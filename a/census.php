<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('memory_limit',-1);
ini_set('max_execution_time', 900000);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');

require_once('class.XRDB.php');
$X=new XRDB();

//--
// Add to Upload log
//--

//$post=array();
//$post['table_name']="nua_upload_log";
//$post['action']="insert";
//$post['upload_type']="APA";
//$id=$X->post($post);
//$key="nuaxess".$id;
//$m=md5($key);
//$d=substr($m,0,7);
//$f=substr($m,7,7);
//$target_file="/var/www/uploads/apa" . $d . ".csv";

$date=date_create();
$month_id=date_format($date,'Y-m');
$month_id="2022-01";
//$month_id="2022-02";

//if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {

$current_client_id="";
$current_apa="";
$current_apa2="";
$current_company_name="";
$current_employee="";

$record_count=0;
$employee_count=0;
$company_count=0;
$new_employee_count=0;
$termed_employee_count=0;
$new_company_count=0;
$termed_company_count=0;

$last_employee_id="";
$target_file="01-17-2022.csv";
//$target_file="02-15-2022.csv";
$file = fopen($target_file,"r");

$sql="delete from nua_census_company where month_id = '" . $month_id . "'";
$X->execute($sql);

$sql="delete from nua_census where month_id = '" . $month_id . "'";
$X->execute($sql);
			
$company_id=0;
while(! feof($file)) {
	$data=fgetcsv($file);
        print_r($data);
	$record_count++;
	if ($data[7]=='APA') {
		
			//--
			//-- this is a company
			//--
						
			$post=array();
			$post['action']="insert";
			$post['table_name']="nua_census_company";
			$post['month_id']=$month_id;
			$post['client_id']=$data[5];
			$post['apa']=$data[8];
			$post['apa2']=$data[9];		
			$post['company_name']=$data[0];
$current_company_name=$post['company_name'];
			
			//--
			//-- Get Company ID if APA_CLIENT_ID is set.
			//--
			
			$sql="select id from nua_company where apa_client_id = '" . $data[5] . "'";
			$c=$X->sql($sql);
			if (sizeof($c)>0) {
				
				 //--
				 //-- APA CLIENT ID EXISTS
				 //--
			     $post['company_id']=$c[0]['id'];
                             $company_id=$post['company_id'];	
			} else {
				
				//--
				//-- DOESNT EXIST FIND BY COMPANY NAME
				//--
				
				$sql="select id from nua_company where upper(company_name) = '" . strtoupper(str_replace("'","",$data[0])) . "'";
				$cc=$X->sql($sql);
					if (sizeof($cc)>0) {
						$post['company_id']=$cc[0]['id'];
						$p=array();
						$p['table_name']="nua_company";
						$p['id']=$cc[0]['id'];
						$p['action']="insert";
						$p['apa_client_id']=$data[5];					 
						$company_id=$X->post($p);
					} else {
						
						//--
						//-- DIDNT FIND BY COMPANY NAME, ADD COMPANY
						//--
						
						$p['table_name']="nua_company";
						$p['action']="insert";
						$p['company_name']=$data[0];
                                                $curent_company_name=$data[0];
						$p['org_id']=0;
						$p['status']="enrolled";
						$p['status_date']=$month_id;
						$p['apa_client_id']=$data[5];					 
						$company_id = $X->post($p);		
                        $post['company_id']=$company_id;						
					}
			}
                        print_r($post);
print_r($post);
			$X->post($post);
			$company_count++;
			$current_apa_company = $data[5];
			
	} else {
		
		if ($data[1]!="Type"&&$data[1]!="ID") {
			
			if ($data[0]!="") {
				
				$last_employee_id = $data[0];
				
				$post=array();
				$post['action']="insert";
				$post['table_name']="nua_census";
				$post['month_id']=$month_id;
                $post['apa_employee_id']=$data[0];
                $last_apa_employee_id=$data[0];
                $post['apa']=$data[1];
                $post['coverage_level']=$data[2];
				$en=$data[3];
				$post['employee_name']=$en;
				$a=explode(",",$en);					
				$post['last_name']=$a[0];
				$post['first_name']=$a[1];
				$post['dob']=$data[4];
				$post['gender']=$data[5];
				$post['status']=$data[6];
				$post['eff_dt']=$data[7];
				$post['term_dt']=$data[8];
				$post['plan']=$data[9];
				$post['company_id']=$company_id;
				$post['company_name']=$current_company_name;
				$post['apa_company_id']=$current_apa_company;
				//--
				//-- Find by APA_MEMBER_ID
				//--
				$sql="select id, company_id, apa_company_id from nua_employee where apa_member_id = '" . $data[0] . "'";
				$g=$X->sql($sql);
				    if (sizeof($g)==0) {
						//--
						//-- No Employee found
						//-- search by name
						//--
							if ($current_apa_company=='1591'||$current_apa_company=='1408') {
	$sql="select id, company_id from nua_employee where upper(first_name) = '" . strtoupper(str_replace("'","''",$post['first_name'])) . "' and upper(last_name) = '" . strtoupper(str_replace("'","''",$post['last_name'])) . "' ";						
							} else {
	$sql="select id, company_id from nua_employee where upper(first_name) = '" . strtoupper(str_replace("'","''",$post['first_name'])) . "' and upper(last_name) = '" . strtoupper(str_replace("'","''",$post['last_name'])) . "' ";
									$sql.=" and company_id = " . $company_id;
							}
							$gg=$X->sql($sql);
							if (sizeof($gg)>0) {
								$post['employee_id']=$gg[0]['id'];
								$current_employee_id=$post['employee_id'];
								$p=array();
								$p['table_name']="nua_employee";
								$p['action']="insert";
								$p['id']=$post['employee_id'];
                                $p['apa_company_id']=$company_id;
                                $p['company_id']=$gg[0]['company_id'];								
								//-- Update Missing Data.
								
								$p['apa_member_id']=$data[0];
								$p['apa_medical_plan']=$data[9];
								if ($data[3]=="SI") $p['apa_medical_plan_level']="EE";
								if ($data[3]=="FA") $p['apa_medical_plan_level']="FAM";			
								if ($data[3]=="ES") $p['apa_medical_plan_level']="ES";
								if ($data[3]=="EC") $p['apa_medical_plan_level']="EC";
                                $p['apa_medical_eff_dt']=$data[7];
                                $X->post($p);
							} else {
								//
								//-- New Employee 
								//
								$p=array();
								$p['table_name']="nua_employee";
								$p['action']="insert";
								$p['apa_member_id']=$data[0];
								$p['first_name']=$post['first_name'];
								$p['last_name']=$post['last_name'];
								$p['date_of_birth']=$data[4];
								$p['gender']=$data[5];
								$p['apa_medical_plan']=$data[9];
								$p['apa_company_id']=$company_id;
                                $p['company_id']=$company_id;	
								if ($data[3]=="SI") $p['apa_medical_plan_level']="EE";
								if ($data[3]=="FA") $p['apa_medical_plan_level']="FAM";			
								if ($data[3]=="ES") $p['apa_medical_plan_level']="ES";
								if ($data[3]=="EC") $p['apa_medical_plan_level']="EC";
                                $p['apa_medical_eff_dt']=$data[7];
echo "New Employee";
print_r($p);

                                $post['employee_id']=$X->post($p);								
								$current_employee_id=$post['employee_id'];
							}
					} else  {
						
						//--
						//-- Found Employee by APA_MEMBER_ID.
						//--

						$p=array();
						$p['table_name']="nua_employee";
						$p['action']="insert";
						$p['id']=$g[0]['id'];
						$current_employee=$p['id'];
						$p['apa_medical_plan']=$data[9];
						if ($data[3]=="SI") $p['apa_medical_plan_level']="EE";
						if ($data[3]=="FA") $p['apa_medical_plan_level']="FAM";			
						if ($data[3]=="ES") $p['apa_medical_plan_level']="ES";
						if ($data[3]=="EC") $p['apa_medical_plan_level']="EC";
                        $p['apa_medical_eff_dt']=$data[7];
                        $post['employee_id']=$X->post($p);			
						$current_employee_id=$post['employee_id'];
					}
                               
				$X->post($post);
                                echo "employee";
                                print_r($post);
   	  } // EMPLOYEE NOT DEPENDENT
	  else {
		  //-- Process Dependent
				$post=array();
				$post['action']="insert";
				$post['table_name']="nua_census";
				$post['month_id']=$month_id;
                $post['apa_employee_id']=$data[0];
                $post['apa']=$data[1];
                $post['coverage_level']=$data[2];
				$en=$data[3];
				$post['employee_name']=$en;
$post['apa_employee_id']=$last_apa_employee_id;
				$a=explode(",",$en);					
				$post['last_name']=$a[0];
				if (isset($a[1])) $post['first_name']=$a[1];
				$post['dob']=$data[4];
				$post['gender']=$data[5];
				$post['status']=$data[6];
				$post['eff_dt']=$data[7];
				$post['term_dt']=$data[8];
				$post['plan']=$data[9];
				$post['company_id']=$company_id;
				$post['apa_company_id']=$current_apa_company;		
				$post['employee_id']=$current_employee_id;
echo "dependent";
print_r($post);
				$X->post($post);
	  }
	} // NOT BLANK
} // NOT COMPANY
} // WHILE
	//-- RETURN SUCCESS
	$output=array();
    $output['error_code']=0;
    $output['error_message']="";
    echo json_encode($output);
?>
