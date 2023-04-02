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

$date=date_create();
$month_id=date_format($date,'Y-m');
$month_id="2022-03";

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
$target_file="02-15-2022.csv";
$target_file="2022-03-07.csv";
$file = fopen($target_file,"r");
$errors=0;

$company_id=0;
$alt_company_id = 0;
while(! feof($file)) {
        $data=fgetcsv($file);
        $record_count++;
echo $record_count;
        if ($data[0]!=''&&$data[1]==''&&$data[2]==''&&$data[3]=='') {
                $sql="select * from nua_company where company_name like '%" . str_replace("'","''",$data[0]) . "%'";
                $companys=$X->sql($sql);
                if (sizeof($companys)==0) {
                        $sql="select * from nua_company where apa_client_id = '" . $data[5] . "'";
                        $companys=$X->sql($sql);
                        if (sizeof($companys)==0) { 
                            $errors++;
                            if ($errors>1) {
                               echo $data[0]; die('invalid company'); }
                        }
               } else {
               echo "COMPANY FOUND";
               }
               $company=$companys[0];
               $current_apa_company=$data[5];
               $current_org=$company['org_id'];
        } else {
             if ($data[1]!="Type"&&$data[1]!="ID") {

                        if ($data[0]!="") {   //EMPLOYEE NOT DEPENDENT

                                $sql="select * from nua_census where apa_employee_id = '" . $data[0] . "' and dependent = 'N'";
                                $zz=$X->sql($sql);
								
                                if (sizeof($zz)==0) {    // ALREADY HAVE CENSUS RECORD
                                $last_employee_id = $data[0];
                                $post=array();
                                $post['action']="insert";
                                $post['table_name']="nua_census";
                                $post['month_id']=$month_id;
                                $post['apa_employee_id']=$data[0];
                                $last_apa_employee_id=$data[0];
                                $post['apa']=$data[1];
                                if ($data[2]=="SI")  $post['coverage_level']="EE";
                                if ($data[2]=="FA")  $post['coverage_level']="FAM";
                                if ($data[2]=="ES")  $post['coverage_level']="ES";
                                if ($data[2]=="EC")  $post['coverage_level']="EC";
                                $en=$data[3];
                                $post['employee_name']=$en;
                                $a=explode(",",$en);
                                $post['last_name']=$a[0];
                                $post['first_name']=str_replace(' ','',$a[1]);
                                $post['dob']=$data[4];
                                $post['gender']=$data[5];
                                $post['status']=$data[6];
                                $post['eff_dt']=$data[7];
                                $post['term_dt']=$data[8];
                                $post['plan']=$data[9];
                                $post['company_id']=$company['id'];
                                $post['company_name']=$company['company_name'];
                                $post['apa_company_id']=$current_apa_company;
                                $current_company_id=$company['id'];
                                $current_company_name=$company['company_name'];
                                $current_apa_employee=$data[0];
                                //--
                                //-- Find by APA_MEMBER_ID
                                //--
                                $sql="select id, company_id, apa_company_id from nua_employee where apa_member_id = '" . $data[0] . "'";
                                $g=$X->sql($sql);
                                    if (sizeof($g)==0) {
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
                                                $p['company_id']=$company['id'];
                                                $alt_company_id=$company['id'];
                                                $p['org_id']=$company['org_id'];
                                                if ($data[2]=="SI") $p['apa_medical_plan_level']="EE";
                                                if ($data[2]=="FA") $p['apa_medical_plan_level']="FAM";
                                                if ($data[2]=="ES") $p['apa_medical_plan_level']="ES";
                                                if ($data[2]=="EC") $p['apa_medical_plan_level']="EC";
                                                $p['apa_medical_eff_dt']=$data[7];
                                                $post['employee_id']=$X->post($p);
                                                $current_employee_id=$post['employee_id'];
                                                
                                        } else {
                                              $current_employee_id=$g[0]['id'];
                                        }// NEW EMPLOYEE
                  //                       if ($current_employee_id==0) {
                  //                           $en=$data[3];
                  //                           $post['employee_name']=$en;
                  //                           $a=explode(",",$en);
                  //                           $last_name=$a[0];
                  //                           $first_name=str_replace(' ','',$a[1]);
                  //                           $sql="update nua_employee set apa_member_id = '" . $data[0] . "' ";
                  //                           $sql.=" where first_name = '" . str_replace("'","''",$first_name) . "' and ";
                  //                           $sql.=" last_name = '" . str_replace("'","''",$last_name) . "' and company_id in (select id from nua_company where org_id = 17)";
                  //                           echo $sql;
                  //                           $X->execute($sql);
                  //                      }
print_r($post);
                               $X->post($post);
                               }  
			   else { // ALREADY HAVE CENSUS
                                  $current_employee_id=$zz[0]['employee_id'];
                                  $current_company_id=$zz[0]['company_id'];
                                  $current_company_name=$zz[0]['company_name'];
                                  $current_apa_employee=$zz[0]['apa_employee_id'];
                                  $alt_company_id=$zz[0]['alt_company_id'];
                                  $alt_company_name=$zz[0]['alt_company_name'];
                                  $sql="update nua_census set month_id = '2022-03' where id = " . $zz[0]['id'];
                                  $X->execute($sql);
                               }
                     //--
                     //-- INSERT INTO CENSUS
                     //--
                                $en=$data[3];
                                $post=array();
                                $post['employee_name']=$en;
                                $a=explode(",",$en);
                                $post['last_name']=$a[0];
                                $post['first_name']=str_replace(' ','',$a[1]);
                                $sql="select * from nua_monthly_member_census where month_id = '2022-03' and dependent_code = '' and ";
                                $sql.=" first_name = '" . str_replace("'","''",$post['first_name']) . "' and last_name = '" . str_replace("'","''",$post['last_name']) . "'";
                                if ($alt_company_id!=0) {
                                      $sql.=" and company_id = " . $alt_company_id;
                                } else {
                                      $sql.=" and company_id = " . $current_company_id;
                                }
echo $sql;
				$t=$X->sql($sql);
				if (sizeof($t)==0) {
print_r($data);                                         
				        $po=array();
					$po['table_name']="nua_monthly_member_census";
					$po['action']="insert";
					$po['clientId']="n" . $current_company_id;
					$po['month_id']="2022-03";
                                        if ($alt_company_id!=0) { $po['company_id']=$alt_company_id; } else { $po['company_id']=$current_company_id; }
                                        $sql="select id, company_id, apa_company_id from nua_employee where apa_member_id = '" . $data[0] . "'";
                                        $g=$X->sql($sql);
                                        if (sizeof($g)==0) {
                                             $en=$data[3];
                                             $employee_name=$en;
                                             $a=explode(",",$en);
                                             $last_name=$a[0];
                                             $first_name=str_replace(' ','',$a[1]);
                                             $sql="update nua_employee set apa_member_id = '" . $data[0] . "' ";
                                             $sql.=" where first_name = '" . str_replace("'","''",$first_name) . "' and ";
                                             $sql.=" last_name = '" . str_replace("'","''",$last_name) . "' and company_id = " . $current_company_id;
                                             echo $sql;
                                             $X->execute($sql);
                                             $sql="select id, company_id, apa_company_id from nua_employee where apa_member_id = '" . $data[0] . "'";
                                             $g=$X->sql($sql);
                                              if (sizeof($g)==0) {
                                                $sql="update nua_employee set apa_member_id = '" . $data[0] . "' ";
                                                $sql.=" where first_name = '" . str_replace("'","''",$first_name) . "' and ";
                                                $sql.=" last_name = '" . str_replace("'","''",$last_name) . "'";
                                                $X->execute($sql);
                                                $sql="select id, company_id, apa_company_id from nua_employee where apa_member_id = '" . $data[0] . "'";
                                                 $g=$X->sql($sql);
                                                if (sizeof($g)==0) {
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
                                                $p['company_id']=$company['id'];
                                                $alt_company_id=$company['id'];
                                                $p['org_id']=$company['org_id'];
                                                if ($data[2]=="SI") $p['apa_medical_plan_level']="EE";
                                                if ($data[2]=="FA") $p['apa_medical_plan_level']="FAM";
                                                if ($data[2]=="ES") $p['apa_medical_plan_level']="ES";
                                                if ($data[2]=="EC") $p['apa_medical_plan_level']="EC";
                                                $p['apa_medical_eff_dt']=$data[7];
                                                $current_employee_id=$X->post($p);
                                                $sql="select id, company_id, apa_company_id from nua_employee where apa_member_id = '" . $data[0] . "'";
                                                echo $sql;
                                                $g=$X->sql($sql);
                                                }
}
}
                                         $current_employee_id=$g[0]['id'];
					$po['employee_code']="n" . $g[0]['id'];
					$po['dependent_code']="";
					$po['employee_id']=$g[0]['id'];
					 $po['first_name']=$post['first_name'];
					 $po['last_name']=$post['last_name'];
					 $po['dob']=$data[4];
					 $po['ssn']="***";
					 $po['gender']=$data[5];
					 $po['eff_dt']=$data[7];
					 $po['client_plan']=$data[9];
					 $po['apa_plan']=$data[9];
                                if ($data[2]=="SI")  $post['coverage_level']="EE";
                                if ($data[2]=="FA")  $post['coverage_level']="FAM";
                                if ($data[2]=="ES")  $post['coverage_level']="ES";
                                if ($data[2]=="EC")  $post['coverage_level']="EC";
					 $po['coverage_level']=$post['coverage_level'];
					 $po['apa_employee_id']=$data[0];
					 $po['company_name']=$current_company_name;
					 $po['plan_type']="*MEDICAL*";
                                             print_r($po);
					     $X->post($po);

					}
					$dcount=0;					 
          } // EMPLOYEE NOT DEPENDENT
          else
          {   // DEPENDENT
                $sql="select * from nua_census where apa_employee_id = '" . $current_apa_employee . "' and dependent = 'Y'";
                $sql.=" and employee_name = '" . str_replace("'","''",$data[3]) . "'";
                $zz=$X->sql($sql);
								
                if (sizeof($zz)==0) {    // ALREADY HAVE CENSUS RECORD
                $post=array();
                $post['action']="insert";
                $post['table_name']="nua_census";
                $post['month_id']=$month_id;
                $post['apa_employee_id']=$current_apa_employee;
                $post['apa']=$data[1];
                $post['coverage_level']=$data[2];
                $en=$data[3];
                $post['employee_name']=$en;
                $post['apa_employee_id']=$current_apa_employee;
                $a=explode(",",$en);
                $post['last_name']=$a[0];
                if (isset($a[1])) $post['first_name']=str_replace(' ','',$a[1]);
                $post['dob']=$data[4];
                $post['gender']=$data[5];
                $post['status']=$data[6];
                $post['eff_dt']=$data[7];
                $post['term_dt']=$data[8];
                $post['plan']=$data[9];
                $post['company_id']=$current_company_id;
                $post['company_name']=$current_company_name;
                $post['apa_company_id']=$current_apa_company;
                $post['employee_id']=$current_employee_id;
                $post['dependent']="Y";
if ($current_org!=17) {
                print_r($post);
                $X->post($post);
}
if ($current_employee_id!=""&&$current_employee_id!="0") {
$sql="select * from nua_employee_dependent where employee_id = " . $current_employee_id . " and first_name = '" . str_replace("'","''",$post['first_name']) . "' and last_name = '" . str_replace("'","''",$post['last_name']) . "'";
echo $sql;
                $d=$X->sql($sql);
                if (sizeof($d)==0) {
                    $po=array();
                    $po['table_name']="nua_employee_dependent";
                    $po['action']="insert";
                    $po['employee_id']=$current_employee_id;
                    $po['company_id']=$current_company_id;
                    $po['first_name']=$post['first_name'];
                    $po['last_name']=$post['last_name'];
                    $po['gender']=$data[5];
                    $po['date_of_birth']=$data[4];
                    print_r($po);
                    $X->post($po);
}
                }
}
				$dcount++;

                                $sql="select * from nua_monthly_member_census where dependent_code <> '' and  month_id = '2022-03' and ";
                                $sql.=" first_name = '" . str_replace("'","''",$post['first_name']) . "' and last_name = '" . str_replace("'","''",$post['last_name']) . "'";
                                if ($alt_company_id!=0) {
                                      $sql.=" and company_id = " . $alt_company_id;
                                } else {
                                      $sql.=" and company_id = " . $current_company_id;
                                }
				$t=$X->sql($sql);
				if (sizeof($t)==0) {				
				$po=array();
				$po['table_name']="nua_monthly_member_census";
				$po['action']="insert";
				$po['clientId']="n" . $current_company_id;
				$po['month_id']="2022-03";
                                if ($alt_company_id!=0) {
                                    $po['company_id']=$alt_company_id;
                                } else { 
				    $po['company_id']=$current_company_id;
                                }
				$po['employee_code']="n" . $current_employee_id;
				$po['dependent_code']="n" . $current_employee_id . "." . $dcount;
				$po['employee_id']=$current_employee_id;
				$po['first_name']=$post['first_name'];
				$po['last_name']=$post['last_name'];
				$po['dob']=$data[4];
				$po['ssn']="***";
				$po['gender']=$data[5];
				$po['eff_dt']=$data[7];
				$po['client_plan']=$data[9];
				$po['apa_plan']=$data[9];
				$po['coverage_level']=$post['coverage_level'];
				$po['apa_employee_id']=$data[0];
				$po['company_name']=$current_company_name;
				$po['plan_type']="*MEDICAL*";
if ($current_org != 17) {
                                print_r($po);
				$X->post($po);
}
				}
					 
          }

        } // NOT BLANK
} // NOT COMPANY
} // WHILE
?>

~

~

