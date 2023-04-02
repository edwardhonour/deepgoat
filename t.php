<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Content-Type: application/json');

$data_string = '{"resources":[{"id":"bf3006a2-96cd-5702-8ca5-01df04d9d92d","alias":"i"},{"id":"ad6c4f0e-8a3b-53aa-b94b-885df0540150","alias":"l"}],"properties":[{"resource":"i","property":"hcpc"},{"resource":"i","property":"modifier"},{"resource":"i","property":"proc_stat"},{"resource":"i","property":"pctc"},{"resource":"i","property":"nused_for_med"},{"resource":"i","property":"rvu_work"},{"resource":"i","property":"trans_nfac_pe_naflag"},{"resource":"i","property":"trans_nfac_pe"},{"resource":"i","property":"full_nfac_pe_naflag"},{"resource":"i","property":"full_nfac_pe"},{"resource":"i","property":"trans_fac_pe_naflag"},{"resource":"i","property":"trans_fac_pe"},{"resource":"i","property":"full_fac_pe_naflag"},{"resource":"i","property":"full_fac_pe"},{"resource":"i","property":"rvu_mp"},{"resource":"i","property":"opps_nfac_pe"},{"resource":"i","property":"opps_fac_pe"},{"resource":"i","property":"opps_mp"},{"resource":"i","property":"global"},{"resource":"i","property":"mult_surg"},{"resource":"i","property":"bilt_surg"},{"resource":"i","property":"asst_surg"},{"resource":"i","property":"co_surg"},{"resource":"i","property":"team_surg"},{"resource":"i","property":"phy_superv"},{"resource":"i","property":"family_ind"},{"resource":"i","property":"sdesc"},{"resource":"i","property":"conv_fact"},{"resource":"i","property":"nfac_pe_naflag"},{"resource":"i","property":"fac_pe_naflag"},{"resource":"i","property":"trans_nfac_total"},{"resource":"i","property":"trans_fac_total"},{"resource":"i","property":"full_nfac_total"},{"resource":"i","property":"full_fac_total"},{"resource":"i","property":"pre_op"},{"resource":"i","property":"intra_op"},{"resource":"i","property":"post_op"},{"resource":"i","property":"endobase"},{"resource":"i","property":"nfac_pe"},{"resource":"i","property":"fac_pe"},{"resource":"i","property":"nfac_total"},{"resource":"i","property":"fac_total"},{"resource":"l","property":"locality"},{"resource":"l","property":"mac"},{"resource":"l","property":"gpci_work"},{"resource":"l","property":"gpci_pe"},{"resource":"l","property":"gpci_mp"},{"alias":"nfac_price","expression":{"operator":"*","operands":[{"expression":{"operator":"+","operands":[{"expression":{"operator":"*","operands":[{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_work"},{"resource":"i","property":"work_adjustor"}]}},{"resource":"l","property":"gpci_work"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"trans_nfac_pe"},{"resource":"l","property":"gpci_pe"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_mp"},{"resource":"l","property":"gpci_mp"}]}}]}},{"resource":"i","property":"conv_fact"}]}},{"alias":"fac_price","expression":{"operator":"*","operands":[{"expression":{"operator":"+","operands":[{"expression":{"operator":"*","operands":[{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_work"},{"resource":"i","property":"work_adjustor"}]}},{"resource":"l","property":"gpci_work"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"trans_fac_pe"},{"resource":"l","property":"gpci_pe"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_mp"},{"resource":"l","property":"gpci_mp"}]}}]}},{"resource":"i","property":"conv_fact"}]}},{"alias":"nfac_limiting_charge","expression":{"operator":"*","operands":[{"expression":{"operator":"*","operands":[{"expression":{"operator":"+","operands":[{"expression":{"operator":"*","operands":[{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_work"},{"resource":"i","property":"work_adjustor"}]}},{"resource":"l","property":"gpci_work"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"trans_nfac_pe"},{"resource":"l","property":"gpci_pe"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_mp"},{"resource":"l","property":"gpci_mp"}]}}]}},{"resource":"i","property":"conv_fact"}]}},1.0925]}},{"alias":"fac_limiting_charge","expression":{"operator":"*","operands":[{"expression":{"operator":"*","operands":[{"expression":{"operator":"+","operands":[{"expression":{"operator":"*","operands":[{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_work"},{"resource":"i","property":"work_adjustor"}]}},{"resource":"l","property":"gpci_work"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"trans_fac_pe"},{"resource":"l","property":"gpci_pe"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_mp"},{"resource":"l","property":"gpci_mp"}]}}]}},{"resource":"i","property":"conv_fact"}]}},1.0925]}},{"alias":"opps_nfac_pmt_amt","expression":{"operator":"*","operands":[{"expression":{"operator":"+","operands":[{"expression":{"operator":"*","operands":[{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_work"},{"resource":"i","property":"work_adjustor"}]}},{"resource":"l","property":"gpci_work"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"opps_nfac_pe"},{"resource":"l","property":"gpci_pe"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"opps_mp"},{"resource":"l","property":"gpci_mp"}]}}]}},{"resource":"i","property":"conv_fact"}]}},{"alias":"opps_fac_pmt_amt","expression":{"operator":"*","operands":[{"expression":{"operator":"+","operands":[{"expression":{"operator":"*","operands":[{"expression":{"operator":"*","operands":[{"resource":"i","property":"rvu_work"},{"resource":"i","property":"work_adjustor"}]}},{"resource":"l","property":"gpci_work"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"opps_fac_pe"},{"resource":"l","property":"gpci_pe"}]}},{"expression":{"operator":"*","operands":[{"resource":"i","property":"opps_mp"},{"resource":"l","property":"gpci_mp"}]}}]}},{"resource":"i","property":"conv_fact"}]}}],"conditions":[{"resource":"i","property":"hcpc","value":"99214","operator":"="},{"resource":"l","property":"locality","value":"0111205","operator":"="}],"joins":[{"resource":"l","condition":{"resource":"i","property":"year","operator":"=","value":{"resource":"l","property":"year"}}}],"offset":0,"limit":10,"sorts":[{"property":"hcpc","order":"asc"},{"property":"locality","order":"asc"},{"property":"proc_stat","order":"asc"},{"property":"modifier","order":"asc"}],"keys":true}
';

$data_array=json_decode($data_string,true);

$code="99214";
$mic="0111205";
$code=$_POST['code'];
$mac=$_POST['mac'];
$result=array();
$result['code']=$code;
$result['mac']=$mac;
//die();
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
  echo 'Message: ' .$e->getMessage();
}
$r=json_decode($result,true);
if (sizeof($r)==0) {
   echo json_encode($array);
} else {
$r2=json_encode($r['results'][0]);
   echo $r2;
}

?>
