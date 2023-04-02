<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('memory_limit',-1);
ini_set('max_execution_time', 900000);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');

//header('Content-type: application/json');

require_once('class.XRDB.php');
$X=new XRDB();

//--
// Add to Upload log
//--

$post=array();
$post['table_name']="nua_data_upload";
$post['action']="insert";
$post['record_count']=0;
$id=$X->post($post);
//$target_file="/var/www/uploads/apa" . $id . ".csv";
$target_file="/var/www/html/data/01-17-2022.csv";
if (1) {
//if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {

$sql="delete from nua_census_upload";
$X->execute($sql);
$sql="delete from nua_census_company_upload";
$X->execute($sql);
$sql="delete from nua_census_company_additions";
$X->execute($sql);

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
$file = fopen($target_file,"r");
$current_company_id=0;
$post=array();
$post['table_name']="nua_data_upload";
$post['action']="insert";
$post['employee_count']=0;
$upload_id=$X->post($post);
$company_id=0;
        $data=fgetcsv($file);
        while(! feof($file)) {
        $data=fgetcsv($file);
        $record_count++;
        if ($data[7]=='APA') {
            // this is a new company_
            //--
            //-- Put in Upload file
            //--
            $post=array();
            $post['action']="insert";
            $post['table_name']="nua_census_company_upload";
            $post['client_id']=$data[5];
            $post['apa']=$data[8];
            $post['apa2']=$data[9];
            $post['company_name']=$data[0];
            $sql="select * from nua_company where apa_client_id = '" . $data[5] . "'";
            $z=$X->sql($sql);

            if (sizeof($z)==0) {
                 $p2=array();
                 $p2['table_name']="nua_company";
                 $p2['action']="insert";
                 $p2['company_name']=$data[0];
                 $p2['apa_client_id']=$data[5];
                 $company_id=$X->post($p2);
            } else {
                 $post['company_id']=$z[0]['id'];
                 $company_id=$post['company_id'];
            }

            $X->post($post);
            $company_count++;

            //--
            //-- See if it's new
            //--

            $sql="select * from nua_company where apa_client_id = '" . $data[5] . "'";
            $c=$X->sql($sql);
            $current_client_id=$data[5];
            if (sizeof($c)==0) {
                        //--
                        //-- NEW COMPANY
                        //--
                        $post=array();
                        $post['action']="insert";
                        $post['table_name']="nua_census_company_upload";
                        $post['client_id']=$data[5];
                        $post['apa']=$data[8];
                        $post['apa2']=$data[9];
                        $post['company_name']=$data[0];
                        $current_company_name=$data[0];
                        $post['first_seen']=$upload_id;
                        $new_company_count++;
                        $new_company_id=$X->post($post);
            } else {
                        //--
                        //-- EXISTING COMPANY IS ACTIVE
                        //--
                        $company_id=$c[0]['id'];
                        $current_company_name=$data[0];
                        $sql="update nua_company set status = 'enrolled', apa_client_id = '" . $data[5] . "' where id = " . $company_id;
                        $X->execute($sql);
            }
        } else {
			//--
			//-- NOT A COMPANY LINE
			//
                if (($data[1]!="Type"&&$data[0]!="ID")&&($data[0]!=''&&$data[1]!=''&&$data[2]!='')) {
                        if ($data[0]!="") {
							//--
							//-- EMPLOYEE
							//--
                                $sql="select count(*) as c from nua_census_upload where apa_employee_id = '" . $data[0] . "' and apa = '" . $data[1] . "'";
                                $c=$X->sql($sql);

                                if ($c[0]['c']==0) {   //-- NOT IN CENSUS
                                        $post=array();
                                        $post['action']="insert";
                                        $post['table_name']="nua_census_upload";
                                        $post['apa_employee_id']=$data[0];
                                        $last_employee_id=$data[0];
                                        $post['apa']=$data[1];
                                        $post['coverage_level']=$data[2];
                                        $en=$data[3];
                                        $post['employee_name']=$en;
                                        $a=explode(",",$en);
                                        if (isset($a[0])) $post['last_name']=$a[0];
                                        if (isset($a[1])) $post['first_name']=$a[1];
                                        $post['dob']=$data[4];
                                        $post['gender']=$data[5];
                                        $post['status']=$data[6];
                                        $post['client_id']=$current_client_id;
                                        $post['eff_dt']=$data[7];
                                        $post['term_dt']=$data[8];
                                        $post['plan']=$data[9];
                                        $post['company_id']=$company_id;
                                        $post['company_name']=$current_company_name;
                                        $post['dependent']="N";
                                        $sql="select id from nua_employee where apa_member_id = '" . $data[0] . "'";
                                        $g=$X->sql($sql);
                                        if (sizeof($g)==0) {
                                           $sql="select id from nua_employee where company_id = " . $company_id;
                                           $sql .= " and upper(last_name) = '" . str_replace("'","",strtoupper($post['last_name']));
                                           $sql .= "' AND upper(first_name) = '" . str_replace("'","",strtoupper($post['first_name'])) . "'";
                                           $g=$X->sql($sql);
                                        }
                                        //--
                                        //-- IF EMPLOYEE EXISTS / UPDATE
                                        //--
                                        if (sizeof($g)>0) {
                                                $post['employee_id']=$g[0]['id'];
                                                $sql="update nua_employee set apa_member_id = '" . $data[0] . "' where id = " . $g[0]['id'];
                                                $X->execute($sql);
                                                $sql="update nua_employee set employee_status = 'enrolled' where id = " . $g[0]['id'];
                                                $X->execute($sql);
                                                $sql="update nua_employee set medical_plan = '" . $data[9] . "' where id = " . $g[0]['id'];
                                                $X->execute($sql);
                                                $sql="update nua_employee set medical_plan_level = '" . $data[2] . "' where id = " . $g[0]['id'];
                                                $X->execute($sql);
                                                $post['employee_id']=$g[0]['id'];
                                                $current_employee_id=$g[0]['id'];
                                        } else {
                                                //--
                                                //-- EMPLOYEE DOES NOT EXIST
                                                //--
                                                $post2=array();
                                                $post2['action']="insert";
                                                $post2['table_name']="nua_employee";
                                                $post2['company_id']=$company_id;
                                                $post2['apa_client_id']=$current_client_id;
                                                $post2['apa_client_id2']=$current_apa;
                                                $post2['apa_client_id3']=$current_apa2;

                                                $post2['employee_status']="enrolled";
                                                $post2['last_name']=$a[0];
                                                $post2['first_name']=$a[1];
                                                $post2['date_of_birth']=$data[4];
                                                $post2['gender']=$data[5];
                                                $post2['apa_status']=$data[6];
                                                $post2['medical_plan']=$data[9];
                                                $post2['medical_plan_level']=$data[10];
                                                $post2['apa_member_id']=$data[0];
                                                $current_employee=$data[0];
                                                $current_employee_id=$X->post($post2);
                                                $post['employee_id']=$current_employee_id;
                                                // employee plan
                                                $post2=array();
                                                $post2['action']="insert";
                                                $post2['table_name']="nua_employee_plan";
                                                $post2['employee_id']=$current_employee_id;
                                                $post2['plan_id']=$data[9];
                                                $post2['plan_type']=$data[2];
                                                $post2['effective_date']=$data[7];
                                                $X->post($post2);
                                                
                                       } //-- EMPLOYEE DOES NOT EXISTS
				}  //-- ALREADY IN FILE
                                print_r($post);
                                $X->post($post);
                            }   else {
                                $sql="select count(*) as c from nua_census_upload where apa_employee_id = '" . $data[0] . "' and apa = '" . $data[1] . "'";
                                $c=$X->sql($sql);
                                if ($c[0]['c']==0) {
                                        $post=array();
                                        $post['action']="insert";
                                        $post['table_name']="nua_census_upload";
                                        $post['apa_employee_id']=$last_employee_id;
                                        $post['apa']=$data[1];
                                        $post['coverage_level']=$data[2];
                                        $en=$data[3];
                                        $post['employee_name']=$en;
                                        $a=explode(",",$en);
                                        if (isset($a[0])) $post['last_name']=$a[0];
                                        if (isset($a[1])) $post['first_name']=$a[1];
                                        $post['dob']=$data[4];
                                        $post['gender']=$data[5];
                                        $post['status']=$data[6];
                                        $post['eff_dt']=$data[7];
                                        $post['term_dt']=$data[8];
                                        $post['plan']=$data[9];
                                        $post['company_id']=$company_id;
                                        $post['company_name']=$current_company_name;
                                        $post['dependent']="Y";
                                        $post['employee_id']=$current_employee_id;
                                print_r($post);
                                $X->post($post);
                                }
                            }   // DEPENDENT NOT EMPLOYEE
        } // NOT BLANK
} // NOT COMPANY
} // WHILE
//-- RETURN SUCCESS
$sql="update nua_census set confirmed = 'N'"; 
$X->execute($sql);
$time=time();
$sql="create table nua_census_" . $time . " as select * from nua_census";
$X->execute($sql);

$sql="select * from nua_census_upload";
$y=$X->sql($sql);
foreach($y as $z) {
    $sql="select count(*) as c from nua_census where apa_employee_id = '" . $z['apa_employee_id'] . "' and apa= '" . $z['apa'] . "'";
    $w=$X->sql($sql);
    if ($w[0]['c']==0) {
          $post=array();
          $post['action']="insert";
          $post['table_name']="nua_census";
          $post['apa_employee_id']=$z['apa_employee_id'];
          $post['apa']=$z['apa'];
          $post['coverage_level']=$z['coverage_level'];
          $post['employee_name']=$z['employee_name'];
          $post['last_name']=$z['last_name'];
          $post['first_name']=$z['first_name'];
          $post['dob']=$z['dob'];
          $post['gender']=$z['gender'];
          $post['status']=$z['status'];
          $post['eff_dt']=$z['eff_dt'];
          $post['term_dt']=$z['term_dt'];
          $post['plan']=$z['plan'];
          $post['company_id']=$z['company_id'];
          $post['client_id']=$z['client_id'];
          $post['company_name']=$z['company_name'];
          $post['dependent']=$z['dependent'];
          $post['employee_id']=$z['employee_id'];
          print_r($post);
          $X->post($post);
    }
}

$sql="select * from nua_census";
$y=$X->sql($sql);
foreach($y as $z) {
    $sql="select count(*) as c from nua_census_upload where apa_employee_id = '" . $z['apa_employee_id'] . "' and apa= '" . $z['apa'] . "'";
    $w=$X->sql($sql);
    if ($w[0]['c']>0) {
          $sql="update nua_census set confirmed='Y' where id = " . $z['id'];
          $X->execute($sql);
    }
}
$sql="select * from nua_census where confirmed = 'N'";
$y=$X->sql($sql);
foreach($y as $z) {
          $post=array();
          $post['action']="insert";
          $post['table_name']="nua_census_terminations";
          $post['apa_employee_id']=$z['apa_employee_id'];
          $post['apa']=$z['apa'];
          $post['coverage_level']=$z['coverage_level'];
          $post['employee_name']=$z['employee_name'];
          $post['last_name']=$z['last_name'];
          $post['first_name']=$z['first_name'];
          $post['dob']=$z['dob'];
          $post['gender']=$z['gender'];
          $post['status']=$z['status'];
          $post['eff_dt']=$z['eff_dt'];
          $post['term_dt']=$z['term_dt'];
          $post['plan']=$z['plan'];
          $post['company_id']=$z['company_id'];
          $post['client_id']=$z['client_id'];
          $post['company_name']=$z['company_name'];
          $post['dependent']=$z['dependent'];
          $post['employee_id']=$z['employee_id'];
          print_r($post);
          $X->post($post);
          $sql="delete from nua_census where id = " . $z['id'];
          $X->execute($sql);
}
$sql="select * from nua_census_company_upload";
$y=$X->sql($sql);
foreach($y as $z) {
          $sql="select count(*) as c from nua_census_company where client_id = '" . $z['client_id'] . "'";
          $g=$X->sql($sql);
          if ($g[0]['c']==0) {
              $post=array();
              $post['action']="insert";
               $post['table_name']="nua_census_company_upload";
               $post['client_id']=$z['client_id'];
               $post['apa']=$z['apa'];
               $post['apa2']=$z['apa2'];
               $post['company_name']=$z['company_name'];
               //$post['first_seen']=$z['first_seen'];
               $X->post($post);
          }
}

$sql="select * from nua_census_company";
$y=$X->sql($sql);
foreach($y as $z) {
    $sql="select count(*) as c from nua_census_company_upload where client_id = '" . $z['client_id'] . "'";
    $w=$X->sql($sql);
    if ($w[0]['c']>0) {
          $sql="update nua_census_company set confirmed='Y' where id = " . $z['id'];
          $X->execute($sql);
    }
}

$sql="select * from nua_census_company where confirmed = 'N'";
$y=$X->sql($sql);
foreach($y as $z) {
          $post=array();
          $post['action']="insert";
          $post['table_name']="nua_census_company_terminations";
               $post['client_id']=$z['client_id'];
               $post['apa']=$z['apa'];
               $post['apa2']=$z['apa2'];
               $post['company_name']=$z['company_name'];
               $post['first_seen']=$z['first_seen'];
               $X->post($post);
}

    $output=array();
    $output['error_code']=0;
    $output['error_message']="";
    echo json_encode($output);
} else {
         //-- UPLOAD ERROR
         $output=array();
         $output['error_code']=1;
         $output['error_message']="An Error has Occured";
         echo json_encode($output);
}
?>
