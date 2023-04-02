<?php

//--
//-- Get ERC20 and BEP20 token assets for a wallet.
//--

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('max_execution_time',800);
ini_set('memory_limit', '2048M');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json'); 
require_once('class.XRDB.php');
require_once('class.ETHScan.php');
$X=new XRDB();
$B=new ETHScan();
 
$sql = "select * from nua_census_audit where confirmed = 'N' order by company_id";
$z=$X->sql($sql);

foreach ($z as $a) {
    //--
    //-- Get the employee from the list of unassined
    //--
    print_r($a);
    $sql="select * from nua_employee where first_name = '" . str_replace("'","''",str_replace(" ","",$a['first_name'])) . "' and ";
    $sql.=" last_name = '" . str_replace("'","''",str_replace(" ","",$a['last_name'])) . "' and org_id <> 17 order by id desc";
    $h=$X->sql($sql);
    if (sizeof($h)>0) {
        $employee=$h[0];
        print_r($employee);
        $company_id = $employee['company_id'];
        $sql="select * from nua_company where id = " . $company_id;
        $i=$X->sql($sql);
        if (sizeof($i)>0) {
             $company=$i[0];
        print_r($company);
             $sql="update nua_company set org_id = 17, user_id = 1, company_name = '**" . $company['company_name'] . "**' where id = " . $company['id'];
             $X->execute($sql);
             $sql="update nua_census_audit set confirmed = 'P', employee_id = " . $employee['id'] . " where id = " . $a['id'];
             $X->execute($sql);

        }
    }
    
    /* 
    $sql="select * from nua_employee where id = " . $a['employee_id'] . " and org_id = 17";
    $h=$X->sql($sql);
    if (sizeof($h)==0) {
            $sql="select * from nua_employee where first_name = '" . str_replace("'","''",str_replace(" ","",$a['first_name'])) . "' and ";
            $sql.=" last_name = '" . str_replace("'","''",str_replace(" ","",$a['last_name'])) . "' and company_id = " . $a['company_id'];
            $h=$X->sql($sql);
            if (sizeof($h)==0) {
                if (sizeof($h)==0) {
                   echo "EMPLOYEE NOT FOUND IN COMPANY OR ORG";
                } else {
                   echo "EMPLOYEE FOUND IN ORG";
                }
            } else {
                 echo "EMPLOYEE FOUND IN COMPANY:";
           }
    } else {
                $sql="select * from nua_monthly_member_census where employee_id = " . $a['employee_id'] . " and plan_type = '*MEDICAL*' and month_id = '2022-02'";
                echo $sql;
                $z=$X->sql($sql);
                if (sizeof($z)>0) {
                      echo "FOUND";
                      $sql="update nua_census_audit set client_plan = '" . $z[0]['client_plan'] . "'";
                      $sql.=" where id = " . $a['id'];
                      echo $sql;
                      $X->execute($sql); 
                      $sql="update nua_census_audit set prism_company_id = '" . $z[0]['company_id'] . "'";
                      $sql.=" where id = " . $a['id'];
                      echo $sql;
                      $X->execute($sql); 
                      $sql="update nua_census_audit set prism_company_name = '" . $z[0]['company_name'] . "'";
                      $sql.=" where id = " . $a['id'];
                      echo $sql;
                      $X->execute($sql); 
                      $sql="update nua_census_audit set coverage_price = '" . $z[0]['coverage_price'] . "'";
                      $sql.=" where id = " . $a['id'];
                      echo $sql;
                      $X->execute($sql); 
                      $sql="update nua_monthly_member_census set apa_plan = '" . $a['plan'] . "'";
                      $sql.=" where employee_id = " . $a['employee_id'] . " and client_plan = '" . $z[0]['client_plan'] . "'";
                      echo $sql;
                      $X->execute($sql); 
              } else {
echo "NOT FOUND";

          $sql="select * from nua_monthly_member_census where first_name = '" . str_replace("'","''",str_replace(" ","",$a['first_name'])) . "' and ";
          $sql.=" last_name = '" . str_replace("'","''",str_replace(" ","",$a['last_name'])) . "' and plan_type = '*MEDICAL*' and month_id = '2022-02'";
          $z=$X->sql($sql);
          if (sizeof($z)>0) { 
                      $sql="update nua_census_audit set employee_id = '" . $z[0]['employee_id'] . "'";
                      $sql.=" where id = " . $a['id'];
                      echo $sql;
                      $X->execute($sql); 
                      $sql="update nua_census_audit set client_plan = '" . $z[0]['client_plan'] . "'";
                      $sql.=" where id = " . $a['id'];
                      echo $sql;
                      $X->execute($sql); 
                      $sql="update nua_census_audit set prism_company_id = '" . $z[0]['company_id'] . "'";
                      $sql.=" where id = " . $a['id'];
                      echo $sql;
                      $X->execute($sql); 
                      $sql="update nua_census_audit set prism_company_name = '" . $z[0]['company_name'] . "'";
                      $sql.=" where id = " . $a['id'];
                      echo $sql;
                      $X->execute($sql); 
                      $sql="update nua_census_audit set coverage_price = '" . $z[0]['coverage_price'] . "'";
                      $sql.=" where id = " . $a['id'];
                      echo $sql;
                      $X->execute($sql); 
                      $sql="update nua_monthly_member_census set apa_plan = '" . $a['plan'] . "'";
                      $sql.=" where employee_id = " . $a['employee_id'] . " and client_plan = '" . $z[0]['client_plan'] . "'";
                      echo $sql;
                      $X->execute($sql); 

          }
         }
    } 
   */
}
?>

