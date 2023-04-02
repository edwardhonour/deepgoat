<?php

require_once('class.XRDB.php');

class QUADM {

    public $X;
	
    function __construct() {
         $this->X=new XRDB();
    }
    
    function getClaimsData($data,$closed="N") {

      if ($closed!="Y") {
          $sql="SELECT * FROM XO_CLAIM WHERE claim_status <> 'Closed' ORDER BY id";
      } else {
          $sql="SELECT * FROM XO_CLAIM WHERE claim_status = 'Closed' ORDER BY id DESC";
      }

      $s=$this->X->sql($sql);
      $output=array();
      foreach($s as $t) {
         $d=date_create($t['claim_date']);
         $t['claim_date']=date_format($d,"m/d/Y");
         array_push($output,$t);
      }

       return $output;
    }

    function getLogin($data) {
        $o=array();
        $o['result']="success";
        $o['uid']=1;
        $o['role']=$data['username'];
        return $o;
    }

    function getLocality($id) {

      $sql="SELECT * FROM XO_ZIPCODE WHERE zipcode = '" . $id . "'";
      $s=$this->X->sql($sql);
      $output=array();
      if (sizeof($s)==0) {
         $output['locality']="NOT FOUND";
      } else {
         $output['locality']=$s[0]['locality'];
      }

       return $output;
    }

    function getNewClaimData($data) {
       $output='{
         "formData": {
             "claim_date": "",
             "policy_id": "",
             "account_id": "",
             "provider_name": "",
             "service_date": "",
             "provder_date": "",
             "total_billed_amount": "0",
             "total_medicare_amount": "0",
             "total_medicare_140_amount": "0",
             "agreement_amount": "0",
             "provider_zip": "",
             "provider_locality": "",
             "claim_status": "New",
             "notes": "",
             "procedure_date_1": "",
             "procedure_code_1": "",
             "billed_amount_1": "",
             "procedure_notes_1": "",
             "procedure_date_2": "",
             "procedure_code_2": "",
             "billed_amount_2": "",
             "procedure_notes_2": "",
             "procedure_date_3": "",
             "procedure_code_3": "",
             "billed_amount_3": "",
             "procedure_notes_3": "",
             "procedure_date_4": "",
             "procedure_code_4": "",
             "billed_amount_4": "",
             "procedure_notes_4": "",
             "procedure_date_5": "",
             "procedure_code_5": "",
             "billed_amount_5": "",
             "procedure_notes_5": "",
             "procedure_date_6": "",
             "procedure_code_6": "",
             "billed_amount_6": "",
             "procedure_notes_6": "",
             "procedure_date_7": "",
             "procedure_code_7": "",
             "billed_amount_7": "",
             "procedure_notes_7": "",
             "procedure_date_8": "",
             "procedure_code_8": "",
             "billed_amount_8": "",
             "procedure_notes_8": "",
             "procedure_date_9": "",
             "procedure_code_9": "",
             "billed_amount_9": "",
             "procedure_notes_9": "",
             "procedure_date_10": "",
             "procedure_code_10": "",
             "billed_amount_10": "",
             "procedure_notes_10": "",
             "entered_by_id" : ""
         },
         "TABLE_NAME":"XO_CLAIM",
         "KEY":"id",
         "SEQUENCE":"",
         "select": [
            {"value":"1", "name": "1"},
            {"value":"2", "name": "2"},
            {"value":"3", "name": "3"},
            {"value":"4", "name": "4"},
            {"value":"5", "name": "5"}
         ]
       }';
       return json_decode($output,true);
    }

    function getClaimDashboard($data) {
       $sql="SELECT * FROM XO_CLAIM WHERE id = " . $data['id'];
       $c=$this->X->sql($sql);
       $o=array();
       $o['formData']=array();

       $o['formData']['id']=$data['id'];
       $o['formData']['policy_id']="";
       $o['formData']['account_id']="";
       $o['formData']['service_date']="";
       $o['formData']['claim_date']="";
       $o['formData']['provider_name']="";
       $o['formData']['provider_date']="";
       $o['formData']['total_billed_amount']="0.00";
       $o['formData']['total_medicare_amount']="0.00";
       $o['formData']['total_medicare_140_amount']="0.00";
       $o['formData']['agreement_amount']="0.00";
       $o['formData']['provider_zip']="0.00";
       $o['formData']['provider_locality']="";
       $o['formData']['claim_status']="";
       $o['formData']['notes']="";

if (sizeof($c)>0) {
       if ($c[0]['claim_date']=="") {
          $o['formData']['claim_date']=$c[0]['claim_date'];
       } else {
          $y=date_create($c[0]['claim_date']);
          $o['formData']['claim_date']=date_format($y,"m/d/Y");
       }

       $o['formData']['policy_id']=$c[0]['policy_id'];
       $o['formData']['account_id']=$c[0]['account_id'];

       if ($c[0]['service_date']=="") {
          $o['formData']['service_date']=$c[0]['service_date'];
       } else {
          $y=date_create($c[0]['service_date']);
          $o['formData']['service_date']=date_format($y,"m/d/Y");
       }

       if ($c[0]['provider_date']=="") {
          $o['formData']['provider_date']=$c[0]['provider_date'];
       } else {
          $y=date_create($c[0]['provider_date']);
          $o['formData']['provider_date']=date_format($y,"m/d/Y");
       }

       $o['formData']['provider_name']=$c[0]['provider_name'];
       $o['formData']['total_billed_amount']=$c[0]['total_billed_amount'];
       $o['formData']['total_medicare_amount']=$c[0]['total_medicare_amount'];
       $o['formData']['total_medicare_140_amount']=$c[0]['total_medicare_140_amount'];
       $o['formData']['agreement_amount']=$c[0]['agreement_amount'];
       $o['formData']['provider_zip']=$c[0]['provider_zip'];
       $o['formData']['provider_locality']=$c[0]['provider_locality'];
       $o['formData']['claim_status']=$c[0]['claim_status'];
       $o['formData']['notes']=$c[0]['notes'];
} 

       $o['formData']['id_1']="";
       $o['formData']['service_date_1']="";
       $o['formData']['code_1']="";
       $o['formData']['billed_amount_1']="";
       $o['formData']['medicare_amount_1']="";
       $o['formData']['medicare_140_amount_1']="";
       $o['formData']['agreement_amount_1']="";
       $o['formData']['medicare_description_1']="";
       $o['formData']['provider_description_1']="";
       $o['formData']['error_status_1']="";
       $o['formData']['notes_1']="";
       $o['formData']['claim_status_1']="";

       $o['formData']['id_2']="";
       $o['formData']['service_date_2']="";
       $o['formData']['code_2']="";
       $o['formData']['billed_amount_2']="";
       $o['formData']['medicare_amount_2']="";
       $o['formData']['medicare_140_amount_2']="";
       $o['formData']['agreement_amount_2']="";
       $o['formData']['medicare_description_2']="";
       $o['formData']['provider_description_2']="";
       $o['formData']['error_status_2']="";
       $o['formData']['notes_2']="";
       $o['formData']['claim_status_2']="";

       $o['formData']['id_3']="";
       $o['formData']['service_date_3']="";
       $o['formData']['code_3']="";
       $o['formData']['billed_amount_3']="";
       $o['formData']['medicare_amount_3']="";
       $o['formData']['medicare_140_amount_3']="";
       $o['formData']['agreement_amount_3']="";
       $o['formData']['medicare_description_3']="";
       $o['formData']['provider_description_3']="";
       $o['formData']['error_status_3']="";
       $o['formData']['notes_3']="";
       $o['formData']['claim_status_3']="";

       $o['formData']['id_4']="";
       $o['formData']['service_date_4']="";
       $o['formData']['code_4']="";
       $o['formData']['billed_amount_4']="";
       $o['formData']['medicare_amount_4']="";
       $o['formData']['medicare_140_amount_4']="";
       $o['formData']['agreement_amount_4']="";
       $o['formData']['medicare_description_4']="";
       $o['formData']['provider_description_4']="";
       $o['formData']['error_status_4']="";
       $o['formData']['notes_4']="";
       $o['formData']['claim_status_4']="";

       $o['formData']['id_5']="";
       $o['formData']['service_date_5']="";
       $o['formData']['code_5']="";
       $o['formData']['billed_amount_5']="";
       $o['formData']['medicare_amount_5']="";
       $o['formData']['medicare_140_amount_5']="";
       $o['formData']['agreement_amount_5']="";
       $o['formData']['medicare_description_5']="";
       $o['formData']['provider_description_5']="";
       $o['formData']['error_status_5']="";
       $o['formData']['notes_5']="";
       $o['formData']['claim_status_5']="";

       $o['formData']['id_6']="";
       $o['formData']['service_date_6']="";
       $o['formData']['code_6']="";
       $o['formData']['billed_amount_6']="";
       $o['formData']['medicare_amount_6']="";
       $o['formData']['medicare_140_amount_6']="";
       $o['formData']['agreement_amount_6']="";
       $o['formData']['medicare_description_6']="";
       $o['formData']['provider_description_6']="";
       $o['formData']['error_status_6']="";
       $o['formData']['notes_6']="";
       $o['formData']['claim_status_6']="";

       $o['formData']['id_7']="";
       $o['formData']['service_date_7']="";
       $o['formData']['code_7']="";
       $o['formData']['billed_amount_7']="";
       $o['formData']['medicare_amount_7']="";
       $o['formData']['medicare_140_amount_7']="";
       $o['formData']['agreement_amount_7']="";
       $o['formData']['medicare_description_7']="";
       $o['formData']['provider_description_7']="";
       $o['formData']['error_status_7']="";
       $o['formData']['notes_7']="";
       $o['formData']['claim_status_7']="";

       $o['formData']['id_8']="";
       $o['formData']['service_date_8']="";
       $o['formData']['code_8']="";
       $o['formData']['billed_amount_8']="";
       $o['formData']['medicare_amount_8']="";
       $o['formData']['medicare_140_amount_8']="";
       $o['formData']['agreement_amount_8']="";
       $o['formData']['medicare_description_8']="";
       $o['formData']['provider_description_8']="";
       $o['formData']['error_status_8']="";
       $o['formData']['notes_8']="";
       $o['formData']['claim_status_8']="";

       $o['formData']['id_9']="";
       $o['formData']['service_date_9']="";
       $o['formData']['code_9']="";
       $o['formData']['billed_amount_9']="";
       $o['formData']['medicare_amount_9']="";
       $o['formData']['medicare_140_amount_9']="";
       $o['formData']['agreement_amount_9']="";
       $o['formData']['medicare_description_9']="";
       $o['formData']['provider_description_9']="";
       $o['formData']['error_status_9']="";
       $o['formData']['notes_9']="";
       $o['formData']['claim_status_9']="";

       $o['formData']['id_10']="";
       $o['formData']['service_date_10']="";
       $o['formData']['code_10']="";
       $o['formData']['billed_amount_10']="";
       $o['formData']['medicare_amount_10']="";
       $o['formData']['medicare_140_amount_10']="";
       $o['formData']['agreement_amount_10']="";
       $o['formData']['medicare_description_10']="";
       $o['formData']['provider_description_10']="";
       $o['formData']['error_status_10']="";
       $o['formData']['notes_10']="";
       $o['formData']['claim_status_10']="";

       $sql="SELECT * FROM XO_CLAIM_PROCEDURE WHERE claim_id = " . $data['id'] . " ORDER BY id";
       $c=$this->X->sql($sql);
       $i=0;
       foreach($c as $d) {
          $i++;
          $ix="id_".$i; 
          $service_date="service_date_".$i; 
          $code="code_".$i; 
          $billed_amount="billed_amount_".$i; 
          $medicare_amount="medicare_amount_".$i; 
          $medicare_140_amount="medicare_140_amount_".$i; 
          $agreement_amount="agreement_amount_".$i; 
          $medicare_description="medicare_description_".$i; 
          $provider_description="provider_description_".$i; 
          $procedure_claim_status="claim_status_".$i; 
          $error_status="error_status_".$i; 
          $notes="notes_".$i;
          $o['formData'][$ix]=$d['id'];
          $o['formData'][$code]=$d['code'];
          if ($d['service_date']=="") {
               $o['formData'][$service_date]=$d['service_date'];
           } else {
               $y=date_create($d['service_date']);
               $o['formData'][$service_date]=date_format($y,"m/d/Y");
           }
           $o['formData'][$billed_amount]=$d['billed_amount'];
           $o['formData'][$medicare_amount]=$d['medicare_amount'];
           $o['formData'][$medicare_140_amount]=$d['medicare_140_amount'];
           $o['formData'][$agreement_amount]=$d['agreement_amount'];
           $o['formData'][$medicare_description]=$d['medicare_description'];
           $o['formData'][$provider_description]=$d['provider_description'];
           $o['formData'][$error_status]=$d['error_status'];
           $o['formData'][$procedure_claim_status]=$d['procedure_claim_status'];
           $o['formData'][$notes]=$d['notes'];
       }

       return $o;
    }


    function getMedicare($code,$mac) {

$data_string = '{"resources":[{"id":"bf3006a2-96cd-5702-8ca5-01df04d9d92d","alias":"i"},{"id":"ad6c4f0e-8a3b-53aa-b94b-885df0540150","alias":"l"}],"properties":[{"resource":"i","property":"hcpc"},{"resource":"i","property":"modifier"},{"resource":"i","property":"proc_stat"},{"resource":"i","property":"pctc"},{"resource":"i","property":"nused_for_med"},{"resource":"i","property":"rvu_work"},{"resource":"i","property":"trans_nfac_pe_naflag"},{"resource":"i","property":"trans_nfac_pe"},{"resource":"i","property":"full_nfac_pe_naflag"},{"resource":"i","property":"full_nfac_pe"},{"resource":"i","property":"trans_fac_pe_naflag"},{"resource":"i","property":"trans_fac_pe"},{"resource":"i","property":"full_fac_pe_naflag"},{"resource":"i","property":"full_fac_pe"},{"resource":"i","property":"rvu_mp"},{"resource":"i","property":"opps_nfac_pe"},{"resource":"i","property":"opps_fac_pe"},{"resource":"i","property":"opps_mp"},{"resource":"i","property":"global"},{"resource":"i","property":"mult_surg"},{"resource":"i","property":"bilt_surg"},{"resource":"i","property":"asst_surg"},{"resource":"i","property":"co_surg"},{"resource":"i","property":"team_surg"},{"resource":"i","property":"phy_superv"},{"resource":"i","property":"family_ind"},{"resource":"i","property":"sdesc"},{"resource":"i","property":"conv_fact"},{"resource":"i","property":"nfac_pe_naflag"},{"resource":"i","property":"fac_pe_naflag"},{"resource":"i","property":"trans_nfac_total"},{"resource":"i","property":"trans_fac_total"},{"resource":"i","property":"full_nfac_total"},{"resource":"i","property":"full_fac_total"},{"resource":"i","property":"pre_op"},{"resource":"i","property":"intra_op"},{"resource":"i","property":"post_op"},{"resource":"i","property":"endobase"},{"resource":"i","property":"nfac_pe"},{"resource":"i","property":"fac_pe"},{"resource":"i","property":"nfac_total"},{"resource":"i","property":"fac_total"},{"resource":"l","property":"locality"},{"resource":"l","property":"mac"},{"resource":"l","property":"gpci_work"},{"resource":"l","property":"gpci_pe"},{"resource":"l","property":"gpci_mp"},{"alias":"nfac_price","expression":{"operator":"*","operands":[{"expression":{"operator":"+","operands":[{"expression":{"operator":"*","operands":[{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_work"},{"resource":"i","property":"work_adjustor"}]}},{"resource":"l","property":"gpci_work"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"trans_nfac_pe"},{"resource":"l","property":"gpci_pe"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_mp"},{"resource":"l","property":"gpci_mp"}]}}]}},{"resource":"i","property":"conv_fact"}]}},{"alias":"fac_price","expression":{"operator":"*","operands":[{"expression":{"operator":"+","operands":[{"expression":{"operator":"*","operands":[{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_work"},{"resource":"i","property":"work_adjustor"}]}},{"resource":"l","property":"gpci_work"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"trans_fac_pe"},{"resource":"l","property":"gpci_pe"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_mp"},{"resource":"l","property":"gpci_mp"}]}}]}},{"resource":"i","property":"conv_fact"}]}},{"alias":"nfac_limiting_charge","expression":{"operator":"*","operands":[{"expression":{"operator":"*","operands":[{"expression":{"operator":"+","operands":[{"expression":{"operator":"*","operands":[{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_work"},{"resource":"i","property":"work_adjustor"}]}},{"resource":"l","property":"gpci_work"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"trans_nfac_pe"},{"resource":"l","property":"gpci_pe"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_mp"},{"resource":"l","property":"gpci_mp"}]}}]}},{"resource":"i","property":"conv_fact"}]}},1.0925]}},{"alias":"fac_limiting_charge","expression":{"operator":"*","operands":[{"expression":{"operator":"*","operands":[{"expression":{"operator":"+","operands":[{"expression":{"operator":"*","operands":[{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_work"},{"resource":"i","property":"work_adjustor"}]}},{"resource":"l","property":"gpci_work"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"trans_fac_pe"},{"resource":"l","property":"gpci_pe"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_mp"},{"resource":"l","property":"gpci_mp"}]}}]}},{"resource":"i","property":"conv_fact"}]}},1.0925]}},{"alias":"opps_nfac_pmt_amt","expression":{"operator":"*","operands":[{"expression":{"operator":"+","operands":[{"expression":{"operator":"*","operands":[{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_work"},{"resource":"i","property":"work_adjustor"}]}},{"resource":"l","property":"gpci_work"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"opps_nfac_pe"},{"resource":"l","property":"gpci_pe"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"opps_mp"},{"resource":"l","property":"gpci_mp"}]}}]}},{"resource":"i","property":"conv_fact"}]}},{"alias":"opps_fac_pmt_amt","expression":{"operator":"*","operands":[{"expression":{"operator":"+","operands":[{"expression":{"operator":"*","operands":[{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_work"},{"resource":"i","property":"work_adjustor"}]}},{"resource":"l","property":"gpci_work"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"opps_fac_pe"},{"resource":"l","property":"gpci_pe"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"opps_mp"},{"resource":"l","property":"gpci_mp"}]}}]}},{"resource":"i","property":"conv_fact"}]}}],"conditions":[{"resource":"i","property":"hcpc","value":"99214","operator":"="},{"resource":"l","property":"locality","value":"0111205","operator":"="}],"joins":[{"resource":"l","condition":{"resource":"i","property":"year","operator":"=","value":{"resource":"l","property":"year"}}}],"offset":0,"limit":10,"sorts":[{"property":"hcpc","order":"asc"},{"property":"locality","order":"asc"},{"property":"proc_stat","order":"asc"},{"property":"modifier","order":"asc"}],"keys":true}
';

$data_array=json_decode($data_string,true);

$data_array['conditions'][0]['value']=$code;
$data_array['conditions'][1]['value']=$mac;

$data_string=json_encode($data_array);

$ch = curl_init('https://datacms.prod.acquia-sites.com/api/1/datastore/query?search=pricing_single_'.$code);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);   
curl_setopt($ch, CURLOPT_HTTPHEADER, array(      
    'Content-Type: application/json',           
    'Content-Length: ' . strlen($data_string)) 
);                                            
try {
  $result = curl_exec($ch);
}
catch(Exception $e) {
  $result=array();
  $result['error_status']=$e->getMessage();
  return $result;
}
   $r=json_decode($result,true);
   if (sizeof($r['results'])!=0) {
        return $r['results'][0];
   } else {
        $result=array();
        $result['error_status']="Results Not Found";
        return $result;
   }
}

    function postClaimProcedure($claim_id, $locality, $procedure_date,  $procedure_code, $amount_billed, $notes) {
         $post=array();
         $post['action']="insert";
         $post['table_name']="XO_CLAIM_PROCEDURE";
         $post['claim_id']=$claim_id;
         $post['code']=$procedure_code;
         $post['billed_amount']=$amount_billed;
         $post['provider_locality']=$locality;
         $post['service_date']=$procedure_date;
         $post['notes']=$notes;
         $cid=$this->X->post($post);
         $u=$this->getMedicare($procedure_code,"0610216");
         if (isset($u['error_status'])) {
             $sql="UPDATE XO_CLAIM_PROCEDURE SET error_status = '" . $u['error_status'] . "' WHERE id = " . $cid;
             $this->X->execute($sql);
         } else {
            $post=array();
            $post['action']="insert";
            $post['table_name']="XO_CLAIM_PROCEDURE";
            $post['id']=$cid;
            $post['medicare_amount']=round(floatval($u['nfac_price']),2);
            $post['medicare_140_amount']=round(floatval($u['nfac_price']),2)*1.4;
            $post['agreement_amount']=round(floatval($u['nfac_price']),2)*1.4;
            $post['medicare_description']=$u['sdesc'];
            $this->X->post($post);
         }
    }


    function postUpdateClaim($data) {
         $post=array();
         $post['action']="insert";
         $post['table_name']="XO_CLAIM";
         $post['id']=$data['data']['formData']['id'];
         $post['claim_date']=$data['data']['formData']['claim_date'];
         $post['policy_id']=$data['data']['formData']['policy_id'];
         $post['account_id']=$data['data']['formData']['account_id'];
         $post['provider_name']=$data['data']['formData']['provider_name'];
         $post['total_billed_amount']=$data['data']['formData']['total_billed_amount'];
         $post['provider_zip']=$data['data']['formData']['provider_zip'];
         $post['provider_locality']=$data['data']['formData']['provider_zip'];
         $post['notes']=$data['data']['formData']['notes'];
         $post['claim_status']=$data['data']['formData']['claim_status'];
         $this->X->post($post);
         $results=array();
         $results['error_code']=0;
         return($results);
    }
 
    function postDeleteClaim($data) {

         $sql="DELETE FROM XO_CLAIM_PROCEDURE WHERE claim_id = " . $data['data']['formData']['id'];
         $this->X->execute($sql);

         $sql="DELETE FROM XO_CLAIM WHERE id = " . $data['data']['formData']['id'];
         $this->X->execute($sql);

         $results=array();
         $results['error_code']=0;
         return($results);
    }


    function postUpdateEdit($data,$c) {
         $post=array();
         $post['action']="insert";
         $post['table_name']="XO_CLAIM_PROCEDURE";
         $idm="id_".$c;
         $post['id']=$data['data']['formData'][$idm];
         $idm="service_date_".$c;
         $post['service_date']=$data['data']['formData'][$idm];
         $idm="code_".$c;
         $post['code']=$data['data']['formData'][$idm];
         $idm="provider_description_".$c;
         $post['provider_description']=$data['data']['formData'][$idm];
         $idm="billed_amount_".$c;
         $post['billed_amount']=$data['data']['formData'][$idm];
         $idm="medicare_amount_".$c;
         $post['medicare_amount']=$data['data']['formData'][$idm];
         $idm="medicare_140_amount_".$c;
         $post['medicare_140_amount']=$data['data']['formData'][$idm];
         $idm="agreement_amount_".$c;
         $post['agreement_amount']=$data['data']['formData']['agreement_amount'];
         $idm="agreement_amount_".$c;
         $post['agreement_amount']=$data['data']['formData'][$idm];
         $idm="claim_status_".$c;
         $post['procedure_claim_status']=$data['data']['formData'][$idm];
         $idm="notes_".$c;
         $post['notes']=$data['data']['formData'][$idm];

         $sql="SELECT * from XO_CLAIM_PROCEDURE WHERE id = " . $post['id'];
         $z=$this->X->sql($sql);

         if ($z[0]['code']!=$post['code']) {
             $u=$this->getMedicare($procedure_code,$locality);
             if (isset($u['error_status'])) {
                 $post['error_status']=$u['error_status'];
                 $post['medicare_amount']=0;
                 $post['medicare_140_amount']=0;
                 $post['agreement_amount']=0;
                 $post['medicare_description']="Lookup Error";
             } else {
                 $post['medicare_amount']=round(floatval($u['nfac_price']),2);
                 $post['medicare_140_amount']=round(floatval($u['nfac_price']),2)*1.4;
                 $post['agreement_amount']=round(floatval($u['nfac_price']),2)*1.4;
                 $post['medicare_description']=$u['sdesc'];
              }
         }

         $this->X->post($post);
         $results=array();
         $results['error_code']=0;
         return($results);
    }
 
    function postNewClaim($data) {
         $post=array();
         $post['action']="insert";
         $post['table_name']="XO_CLAIM";
         $post['claim_date']=$data['data']['formData']['claim_date'];
         $post['policy_id']=$data['data']['formData']['policy_id'];
         $post['account_id']=$data['data']['formData']['account_id'];
         $post['provider_name']=$data['data']['formData']['provider_name'];
         $post['total_billed_amount']=$data['data']['formData']['total_billed_amount'];
         $post['provider_zip']=$data['data']['formData']['provider_zip'];
         $post['provider_locality']=$data['data']['formData']['provider_locality'];
         $post['notes']=$data['data']['formData']['notes'];
         $post['entered_by_id']=$data['uid'];
         $claim_id=$this->X->post($post);
         $locality=$data['data']['formData']['provider_zip'];

         if ($data['data']['formData']['procedure_date_1']!="") { 
              $this->postClaimProcedure(
                     $claim_id, 
                     $locality, 
                     $data['data']['formData']['procedure_date_1'],
                     $data['data']['formData']['procedure_code_1'],
                     $data['data']['formData']['billed_amount_1'],
                     $data['data']['formData']['procedure_notes_1']);
         }

         if ($data['data']['formData']['procedure_date_2']!="") { 
              $this->postClaimProcedure(
                     $claim_id, 
                     $locality, 
                     $data['data']['formData']['procedure_date_2'],
                     $data['data']['formData']['procedure_code_2'],
                     $data['data']['formData']['billed_amount_2'],
                     $data['data']['formData']['procedure_notes_2']);
         }

         if ($data['data']['formData']['procedure_date_3']!="") { 
              $this->postClaimProcedure(
                     $claim_id, 
                     $locality, 
                     $data['data']['formData']['procedure_date_3'],
                     $data['data']['formData']['procedure_code_3'],
                     $data['data']['formData']['billed_amount_3'],
                     $data['data']['formData']['procedure_notes_3']);
         }

         if ($data['data']['formData']['procedure_date_4']!="") { 
              $this->postClaimProcedure(
                     $claim_id, 
                     $locality, 
                     $data['data']['formData']['procedure_date_4'],
                     $data['data']['formData']['procedure_code_4'],
                     $data['data']['formData']['billed_amount_4'],
                     $data['data']['formData']['procedure_notes_4']);
         }

         if ($data['data']['formData']['procedure_date_5']!="") { 
              $this->postClaimProcedure(
                     $claim_id, 
                     $locality, 
                     $data['data']['formData']['procedure_date_5'],
                     $data['data']['formData']['procedure_code_5'],
                     $data['data']['formData']['billed_amount_5'],
                     $data['data']['formData']['procedure_notes_5']);
         }

         if ($data['data']['formData']['procedure_date_6']!="") { 
              $this->postClaimProcedure(
                     $claim_id, 
                     $locality, 
                     $data['data']['formData']['procedure_date_6'],
                     $data['data']['formData']['procedure_code_6'],
                     $data['data']['formData']['billed_amount_6'],
                     $data['data']['formData']['procedure_notes_6']);
         }

         if ($data['data']['formData']['procedure_date_7']!="") { 
              $this->postClaimProcedure(
                     $claim_id, 
                     $locality, 
                     $data['data']['formData']['procedure_date_7'],
                     $data['data']['formData']['procedure_code_7'],
                     $data['data']['formData']['billed_amount_7'],
                     $data['data']['formData']['procedure_notes_7']);
         }

         if ($data['data']['formData']['procedure_date_8']!="") { 
              $this->postClaimProcedure(
                     $claim_id, 
                     $locality, 
                     $data['data']['formData']['procedure_date_8'],
                     $data['data']['formData']['procedure_code_8'],
                     $data['data']['formData']['billed_amount_8'],
                     $data['data']['formData']['procedure_notes_8']);
         }

         if ($data['data']['formData']['procedure_date_9']!="") { 
              $this->postClaimProcedure(
                     $claim_id, 
                     $locality, 
                     $data['data']['formData']['procedure_date_9'],
                     $data['data']['formData']['procedure_code_9'],
                     $data['data']['formData']['billed_amount_9'],
                     $data['data']['formData']['procedure_notes_9']);
         }

         if ($data['data']['formData']['procedure_date_10']!="") { 
              $this->postClaimProcedure(
                     $claim_id, 
                     $locality, 
                     $data['data']['formData']['procedure_date_10'],
                     $data['data']['formData']['procedure_code_10'],
                     $data['data']['formData']['billed_amount_10'],
                     $data['data']['formData']['procedure_notes_10']);
         }
         $sql="SELECT sum(medicare_amount) AS A, sum(medicare_140_amount) AS B FROM ";
         $sql.=" XO_CLAIM_PROCEDURE WHERE claim_id = " . $claim_id;
         $y=$this->X->sql($sql);
         $sql="UPDATE XO_CLAIM SET total_medicare_amount = " . $y[0]['A'] . ", ";
         $sql.="total_medicare_140_amount = " . $y[0]['B'] . ", agreement_amount = " . $y[0]['B'];
         $sql.=" WHERE id = " .  $claim_id;
         $this->X->execute($sql);
         $d=array();
         $d['error_code']=0;
         $d['id']=$claim_id;
         return $d;
    }

    function postForm($data) {
         $this->post($data['formData']);
         $results=array();
         $results['error_code']=0;
         $results['error_message']="Save Complete";
         return $results;
    }

    function post($data) {

    } 

}

