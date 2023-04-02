<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');
ini_set('max_execution_time', 900000);

require_once('class.XRDB.php');
$X=new XRDB();

function llog($msg) {
   $X=new XRDB();	
   $p=array();
   $p['table_name']="nua_log";
   $p['action']="insert";
   $p['msg']=$msg;
   $X->post($p);
	
}
llog("Monthly Census Started");

$months=array();
array_push($months,"2009-01");

array_push($months,"2009-02");
array_push($months,"2009-03");
array_push($months,"2009-04");
array_push($months,"2009-05");
array_push($months,"2009-06");
array_push($months,"2009-07");
array_push($months,"2009-08");
array_push($months,"2009-09");
array_push($months,"2009-10");
array_push($months,"2009-11");
array_push($months,"2009-12");
array_push($months,"2010-01");
array_push($months,"2010-02");
array_push($months,"2010-03");
array_push($months,"2010-04");
array_push($months,"2010-05");
array_push($months,"2010-06");
array_push($months,"2010-07");
array_push($months,"2010-08");
array_push($months,"2010-09");
array_push($months,"2010-10");
array_push($months,"2010-11");
array_push($months,"2010-12");
array_push($months,"2011-01");
array_push($months,"2011-02");
array_push($months,"2011-03");
array_push($months,"2011-04");
array_push($months,"2011-05");
array_push($months,"2011-06");
array_push($months,"2011-07");
array_push($months,"2011-08");
array_push($months,"2011-09");
array_push($months,"2011-10");
array_push($months,"2011-11");
array_push($months,"2011-12");
array_push($months,"2012-01");
array_push($months,"2012-02");
array_push($months,"2012-03");
array_push($months,"2012-04");
array_push($months,"2012-05");
array_push($months,"2012-06");
array_push($months,"2012-07");
array_push($months,"2012-08");
array_push($months,"2012-09");
array_push($months,"2012-10");
array_push($months,"2012-11");
array_push($months,"2012-12");
array_push($months,"2013-01");
array_push($months,"2013-02");
array_push($months,"2013-03");
array_push($months,"2013-04");
array_push($months,"2013-05");
array_push($months,"2013-06");
array_push($months,"2013-07");
array_push($months,"2013-08");
array_push($months,"2013-09");
array_push($months,"2013-10");
array_push($months,"2013-11");
array_push($months,"2013-12");
array_push($months,"2014-01");
array_push($months,"2014-02");
array_push($months,"2014-03");
array_push($months,"2014-04");
array_push($months,"2014-05");
array_push($months,"2014-06");
array_push($months,"2014-07");
array_push($months,"2014-08");
array_push($months,"2014-09");
array_push($months,"2014-10");
array_push($months,"2014-11");
array_push($months,"2014-12");
array_push($months,"2015-01");
array_push($months,"2015-02");
array_push($months,"2015-03");
array_push($months,"2015-04");
array_push($months,"2015-05");
array_push($months,"2015-06");
array_push($months,"2015-07");
array_push($months,"2015-08");
array_push($months,"2015-09");
array_push($months,"2015-10");
array_push($months,"2015-11");
array_push($months,"2015-12");
array_push($months,"2016-01");
array_push($months,"2016-02");
array_push($months,"2016-03");
array_push($months,"2016-04");
array_push($months,"2016-05");
array_push($months,"2016-06");
array_push($months,"2016-07");
array_push($months,"2016-08");
array_push($months,"2016-09");
array_push($months,"2016-10");
array_push($months,"2016-11");
array_push($months,"2016-12");
array_push($months,"2017-01");
array_push($months,"2017-02");
array_push($months,"2017-03");
array_push($months,"2017-04");
array_push($months,"2017-05");
array_push($months,"2017-06");
array_push($months,"2017-07");
array_push($months,"2017-08");
array_push($months,"2017-09");
array_push($months,"2017-10");
array_push($months,"2017-11");
array_push($months,"2017-12");
array_push($months,"2018-01");
array_push($months,"2018-02");
array_push($months,"2018-03");
array_push($months,"2018-04");
array_push($months,"2018-05");
array_push($months,"2018-06");
array_push($months,"2018-07");
array_push($months,"2018-08");
array_push($months,"2018-09");
array_push($months,"2018-10");
array_push($months,"2018-11");
array_push($months,"2018-12");
array_push($months,"2019-01");
array_push($months,"2019-02");
array_push($months,"2019-03");
array_push($months,"2019-04");
array_push($months,"2019-05");
array_push($months,"2019-06");
array_push($months,"2019-07");
array_push($months,"2019-08");
array_push($months,"2019-09");
array_push($months,"2019-10");
array_push($months,"2019-11");
array_push($months,"2019-12");
array_push($months,"2020-01");
array_push($months,"2020-02");
array_push($months,"2020-03");
array_push($months,"2020-04");
array_push($months,"2020-05");
array_push($months,"2020-06");
array_push($months,"2020-07");
array_push($months,"2020-08");
array_push($months,"2020-09");
array_push($months,"2020-10");
array_push($months,"2020-11");
array_push($months,"2020-12");
array_push($months,"2021-01");
array_push($months,"2021-02");
array_push($months,"2021-03");
array_push($months,"2021-04");
array_push($months,"2021-05");
array_push($months,"2021-06");
array_push($months,"2021-07");
array_push($months,"2021-08");
array_push($months,"2021-09");
array_push($months,"2021-10");
array_push($months,"2021-11");
array_push($months,"2021-12");
array_push($months,"2022-01");
array_push($months,"2022-02");
array_push($months,"2022-03");

$last="X";

foreach ($months as $month) {

		print_r($month);

		//==
		//== bring forward last month
	    //==
		
	     $sql="select * from nua_monthly_member_census where month_id = '" . $last . "'";
         $adds=$X->sql($sql);
		 $j=0;		 
         foreach($adds as $add) {
            $j++;   
			//==
			//== Don't bring forward if termed last month.
			//==
			
			$sql="select count(*) as c from nua_monthly_member_terminations where month_id = '" . $last . "' and ";
			$sql.=" employee_code = '" . $add['employee_code'] . "' and ";
			$sql.=" dependent_code = '" . $add['dependent_code'] . "' and ";	
			$sql.=" client_plan = '" . $add['client_plan'] . "'";
			$terms=$X->sql($sql);		 
		    if ($terms[0]['c']==0) {
					$sql="select * from nua_monthly_member_census where month_id = '" . $month . "' and ";
					$sql.=" employee_code = '" . $add['employee_code'] . "' and ";
					$sql.=" dependent_code = '" . $add['dependent_code'] . "' and ";	
					$sql.=" client_plan = '" . $add['client_plan'] . "'";
					$z=$X->sql($sql);		 
					$post=array();
					$post['table_name']="nua_monthly_member_census";
					$post['action']="insert";
					if (sizeof($z)>0) {
						$post['id']=$z[0]['id']; 
					}
					$post['month_id']=$month;
					$post['company_id']=$add['company_id'];
					$post['employee_code']=$add['employee_code'];
					$post['dependent_code']=$add['dependent_code'];
					$post['employee_id']=$add['employee_id'];
					$post['client_id']=$add['client_id'];
					$post['first_name']=$add['first_name'];
					$post['last_name']=$add['last_name'];
					$post['middle_initial']=$add['middle_initial'];
					$post['dob']=$add['dob'];
					$post['ssn']=$add['ssn'];
					$post['gender']=$add['gender'];
					$post['eff_dt']=$add['eff_dt'];
					$post['term_dt']=$add['term_dt'];
					$post['client_plan']=$add['client_plan'];
					$post['apa_plan']=$add['apa_plan'];
					$post['plan_type']=$add['plan_type'];
					$post['coverage_level']=$add['coverage_level'];
					$post['coverage_price']=$add['coverage_price'];
					print_r($post);
					$X->post($post);	
					
				} // not termed		 
				
		 } // foreach
	//==
	//== PROCESS ADDITIONS
	//==
	$j=0;
	
	$sql="select * from nua_monthly_member_additions where month_id = '" . $month . "'";
    $adds=$X->sql($sql);
    foreach($adds as $add) {
		$j++;
         $sql="select * from nua_monthly_member_census where month_id = '" . $month . "' and ";
		 $sql.=" employee_code = '" . $add['employee_code'] . "' and ";
		 $sql.=" dependent_code = '" . $add['dependent_code'] . "' and ";	
		 $sql.=" client_plan = '" . $add['client_plan'] . "'";
		 echo $sql;
         $z=$X->sql($sql);		 
		 $post=array();
		 $post['table_name']="nua_monthly_member_census";
		 $post['action']="insert";
		 if (sizeof($z)>0) {
			$post['id']=$z[0]['id']; 
		 }
		 $post['month_id']=$month;
		 $post['company_id']=$add['company_id'];
		 $post['employee_code']=$add['employee_code'];
		 $post['dependent_code']=$add['dependent_code'];
		 $post['employee_id']=$add['employee_id'];
		 $post['client_id']=$add['client_id'];
		 $post['company_id']=$add['company_id'];
		 $post['first_name']=$add['first_name'];
		 $post['last_name']=$add['last_name'];
		 $post['middle_initial']=$add['middle_initial'];
		 $post['dob']=$add['dob'];
		 $post['ssn']=$add['ssn'];
		 $post['gender']=$add['gender'];
		$post['plan_type']=$add['plan_type'];
		 $post['eff_dt']=$add['eff_dt'];
		 $post['term_dt']=$add['term_dt'];
		 $post['client_plan']=$add['client_plan'];
		 $post['apa_plan']=$add['apa_plan'];
		 $post['coverage_level']=$add['coverage_level'];
		 $post['coverage_price']=$add['coverage_price'];
		 echo "ADDITION-";
    	 print_r($post);
		 $X->post($post);
    }

	//--
	//-- Process Terms by updating term_dt
	//--
	
	$j=0;
	$sql="select * from nua_monthly_member_terminations where month_id = '" . $month . "'";
      $adds=$X->sql($sql);
         foreach($adds as $add) {
			 	$j++;
			 	$sql="select * from nua_monthly_member_census where month_id = '" . $month . "' and ";
				$sql.=" employee_code = '" . $add['employee_code'] . "' and ";
				$sql.=" dependent_code = '" . $add['dependent_code'] . "' and ";	
				$sql.=" client_plan = '" . $add['client_plan'] . "'";
				$z=$X->sql($sql);		 
				$post=array();
				$post['table_name']="nua_monthly_member_census";
				$post['action']="insert";
				if (sizeof($z)>0) {
					$post['id']=$z[0]['id']; 
					$post['month_id']=$month;
					$post['company_id']=$add['company_id'];
					$post['employee_code']=$add['employee_code'];
					$post['dependent_code']=$add['dependent_code'];
					$post['employee_id']=$add['employee_id'];
					$post['company_id']=$add['company_id'];
					$post['client_id']=$add['client_id'];
					$post['first_name']=$add['first_name'];
					$post['last_name']=$add['last_name'];
					$post['middle_initial']=$add['middle_initial'];
					$post['dob']=$add['dob'];
					$post['ssn']=$add['ssn'];
					$post['gender']=$add['gender'];
					$post['eff_dt']=$add['eff_dt'];
					$post['term_dt']=$add['term_dt'];
					$post['client_plan']=$add['client_plan'];
					$post['apa_plan']=$add['apa_plan'];
					$post['coverage_level']=$add['coverage_level'];
					$post['coverage_price']=$add['coverage_price'];
					echo "TERM-";
					print_r($post);
					$X->post($post);	
				}
		 }
		
   	        $last=$month;
		llog($month . " terms " . $j);		

        } 

       // Plan Summaries

$last="X";
foreach ($months as $month) {
	
		$sql="select distinct client_id, client_plan, coverage_level from nua_monthly_member_census where ";
		$sql.="month_id = '" . $month . "' order by 1,2,3";
		$clients=$X->sql($sql);
		
		foreach ($clients as $client) {

		   $client_id=$client['client_id'];
		   $sql="select id from nua_company where infinity_id = '" . $client_id . "'";
		   $e=$X->sql($sql);
		   $company_id=$e[0]['id'];
		   
           $client_plan=$client['client_plan'];
		   $coverage_level=$client['coverage_level'];
		   $sql="select * from nua_monthly_member_census where month_id = '" . $last . "' and client_id = '" . $client_id . "' and client_plan = '" . $client_plan . "' and coverage_level = '" . $coverage_level . "'";
		   $adds=$X->sql($sql);
		   $carry=0;	 
		   $coverage_price="0.00";
           foreach($adds as $add) {
				$sql="select count(*) as c from nua_monthly_member_terminations where month_id = '" . $last . "' and ";
				$sql.=" employee_code = '" . $add['employee_code'] . "' and ";
				$sql.=" dependent_code = '" . $add['dependent_code'] . "' and ";	
				$sql.=" client_plan = '" . $add['client_plan'] . "' and coverage_level = '" . $coverage_level . "'";
				$terms=$X->sql($sql);		 
				if ($terms[0]['c']==0) {
						$carry++;
				}
		   }
		   		
		   $sql="select * from nua_monthly_member_additions where month_id = '" . $month . "' and client_id = '" . $client_id . "' and client_plan = '" . $client_plan . "' and coverage_level = '" . $coverage_level . "'";
		   $adds=$X->sql($sql);
		   $additions=0;	 
           foreach($adds as $add) {
				$additions++;
		   }		   
		   $sql="select * from nua_monthly_member_terminations where month_id = '" . $month . "' and client_id = '" . $client_id . "' and client_plan = '" . $client_plan . "' and coverage_level = '" . $coverage_level . "'";
		   $adds=$X->sql($sql);
		   $terminations=0;	 
           foreach($adds as $add) {
				$terminations++;
		   }	

           $coverage_price='0.00';
           $employees=0;
           $lives=0;		   
		   $total_coverage=0;

		   $sql="select * from nua_monthly_member_census where month_id = '" . $month . "' and client_id = '" . $client_id . "' and client_plan = '" . $client_plan . "' and coverage_level = '" . $coverage_level . "'";
		   $adds=$X->sql($sql);
           foreach($adds as $add) {
			   if ($add['coverage_price']!='0.00'&&$add['coverage_price']) {
				   $coverage_price = $add['coverage_price'];   
			   }
			   if ($add['employee_code']!=''&&$add['dependent_code']=='') { $employees++; $lives++; }
			   if ($add['employee_code']!=''&&$add['dependent_code']!='') { $lives++; }
               $cp=$add['coverage_price'];
               if ($cp=='') $cp='0.00';
               $total_coverage+=floatval($cp);
		   }			

	
		   $post=array();
		   $post['table_name']='nua_monthly_plan_summary';
		   $post['action']="insert";
		   $sql="select * from nua_monthly_plan_summary where month_id = '" . $month . "' and client_id = '" . $client_id . "' and client_plan = '" . $client_plan . "' and coverage_level = '" . $coverage_level . "'";
		   $s=$X->sql($sql);
		   if (sizeof($s)>0) {
			    $post['id']=$s[0]['id'];  
		   }
		   $post['client_id']=$client_id;
		   $post['month_id']=$month;
		   $post['company_id']=$company_id;
		   $post['client_plan']=$client_plan;
		   $post['apa_plan']="";
		   $post['coverage_level']=$coverage_level;
		   $post['carry_forward']=$carry;
		   $post['additions']=$additions;
		   $post['terminations']=$terminations;
		   $post['insured_employees']=$employees;
		   $post['insured_lives']=$lives;
		   $post['total_coverage_price']=number_format($total_coverage,2);
		   $post['coverage_price']=$coverage_price;
		   print_r($post);
		   $X->post($post);
		}
		$last=$month;
}
?>

