<?php

require_once('class.XRDB.php');

class TSD {

        protected $X;
        protected $demo;

        function __construct() {
            $this->X=new XRDB();
        }

     
        function getUser($data) {
            $user=array();
	    $user['forced_off']=0;
	    return $user;
    }

    function getTSDHome($data) {
         $output=$this->start_output($data);
	 $output=$this->getTicketList($data);
	 return $output;
    }

    function showFacility($data) {
         $output=$this->start_output($data);
	 return $output;
    }

    function getFacilityList($data) {

	    // formerly:  /data/data.tickets.facilitylist.php
	    //
	    //
         $output=$this->start_output($data);

         if (isset($data['key'])) { $key=$data['key']; } else { $key=""; }
         if (isset($data['uid'])) { $uid=$data['uid']; } else { $uid=0; }
         if (isset($data['mycheckbox'])) $searchtype = $data['mycheckbox']; else $searchtype="regionfacilities";

     	 $sql = "SELECT ROLE, REGION_ID FROM FPS_USER WHERE USER_ID = " . $uid;
	 $s=$this->X->sql($sql);
	 $a = $s[0];
   	 $role=$a['ROLE'];
	 $region=$a['REGION_ID'];
	 $region_name="";
	 if (isset($data['data']['BUILDING_NBR'])) {
            $output['formData']=$data['data'];
	 } else {
	    $formData=array();
   	    $formData['BUILDING_NBR']="";
	    $formData['FACILITY_NAME']="";
	    $formData['ADDRESS']="";
	    $formData['CITY_NAME']="";
	    $formData['STATE_ABBR']="";
	    $formData['ZIPCODE']="";
	    $formData['REGION']="MY";
	    $output['formData']=$formData;
         }
         $inclause="";	
	      if ($role=="TSD") {
		      if ($output['formData']['REGION']=="MY") {
		          if ($region=="0") { $inclause=" and REGION_ID > 0 "; $region_name="All Regions"; }
		          if ($region=="1"||$region=="2"||$region=="3") { $inclause=" and REGION_ID in (1,2,3) "; $region_name="Regions 1,2, and 3"; }
		          if ($region=="4"||$region=="5"||$region=="6"||$region=="11") { $inclause=" and REGION_ID in (4,5,6,11) "; $region_name="Regions 4,5,6, and 11"; }
		          if ($region=="7"||$region=="8"||$region=="9"||$region=="10") { $inclause=" and REGION_ID in (7,8,9,10) "; $region_name="Regions 7,8,9, and 10"; }
	               } else {
	  	          if ($output['formData']['REGION']=="ALL"||($output['formData']['REGION']=="MY"&&$region=="0")||$output['formData']['REGION']=="0") { $inclause=" and REGION_ID > 0 "; $region_name="All Regions"; }
	  	          if ($output['formData']['REGION']=="MEGACENTER"&&($region=="1"||$region=="2"||$region=="3")) { $inclause=" and REGION_ID in (1,2,3) "; $region_name="Regions 1,2, and 3"; }
	  	          if ($output['formData']['REGION']=="MEGACENTER"&&($region=="4"||$region=="5"||$region=="6"||$region=="11")) { $inclause=" and REGION_ID in (4,5,6,11) "; $region_name="Regions 4,5,6, and 11"; }
	  	          if ($output['formData']['REGION']=="MEGACENTER"&&($region=="7"||$region=="8"||$region=="9"||$region=="10")) { $inclause=" and REGION_ID in (7,8,9,10) "; $region_name="Regions 7,8,9, and 10"; }
		          if ($output['formData']['REGION']=="0") { $inclause=" and REGION_ID > 0 "; $region_name="All Regions"; }
		       }	       
	      } else {
	  	if ($output['formData']['REGION']=="ALL"||($output['formData']['REGION']=="MY"&&$region=="0")||$output['formData']['REGION']=="0") { $inclause=" and REGION_ID > 0 "; $region_name="All Regions"; }
	  	if ($output['formData']['REGION']=="MEGACENTER"&&($region=="1"||$region=="2"||$region=="3")) { $inclause=" and REGION_ID in (1,2,3) "; $region_name="Regions 1,2, and 3"; }
	  	if ($output['formData']['REGION']=="MEGACENTER"&&($region=="4"||$region=="5"||$region=="6"||$region=="11")) { $inclause=" and REGION_ID in (4,5,6,11) "; $region_name="Regions 4,5,6, and 11"; }
	  	if ($output['formData']['REGION']=="MEGACENTER"&&($region=="7"||$region=="8"||$region=="9"||$region=="10")) { $inclause=" and REGION_ID in (7,8,9,10) "; $region_name="Regions 7,8,9, and 10"; }
		if ($output['formData']['REGION']=="0") { $inclause=" and REGION_ID > 0 "; $region_name="All Regions"; }
		if ($output['formData']['REGION']=="1") { $inclause=" and REGION_ID = 1 "; $region_name="Region 1"; }
		if ($output['formData']['REGION']=="2") { $inclause=" and REGION_ID = 2 "; $region_name="Region 2"; }
		if ($output['formData']['REGION']=="3") { $inclause=" and REGION_ID = 3 "; $region_name="Region 3"; }
		if ($output['formData']['REGION']=="4") { $inclause=" and REGION_ID = 4 "; $region_name="Region 4"; }
		if ($output['formData']['REGION']=="5") { $inclause=" and REGION_ID = 5 "; $region_name="Region 5"; }
		if ($output['formData']['REGION']=="6") { $inclause=" and REGION_ID = 6 "; $region_name="Region 6"; }
		if ($output['formData']['REGION']=="7") { $inclause=" and REGION_ID = 7 "; $region_name="Region 7"; }
		if ($output['formData']['REGION']=="8") { $inclause=" and REGION_ID = 8 "; $region_name="Region 8"; }
		if ($output['formData']['REGION']=="9") { $inclause=" and REGION_ID = 9 "; $region_name="Region 9"; }
		if ($output['formData']['REGION']=="10") { $inclause=" and REGION_ID = 10 "; $region_name="Region 10"; }
		if ($output['formData']['REGION']=="11") { $inclause=" and REGION_ID = 11 "; $region_name="Region 11"; }
		if ($output['formData']['REGION']=="MY"&&$region=="0") { $inclause=" and REGION_ID > 0 "; $region_name="All Regions"; }
		if ($output['formData']['REGION']=="MY"&&$region=="1") { $inclause=" and REGION_ID = 1 "; $region_name="Region 1"; }
		if ($output['formData']['REGION']=="MY"&&$region=="2") { $inclause=" and REGION_ID = 2 "; $region_name="Region 2"; }
		if ($output['formData']['REGION']=="MY"&&$region=="3") { $inclause=" and REGION_ID = 3 "; $region_name="Region 3"; }
		if ($output['formData']['REGION']=="MY"&&$region=="4") { $inclause=" and REGION_ID = 4 "; $region_name="Region 4"; }
		if ($output['formData']['REGION']=="MY"&&$region=="5") { $inclause=" and REGION_ID = 5 "; $region_name="Region 5"; }
		if ($output['formData']['REGION']=="MY"&&$region=="6") { $inclause=" and REGION_ID = 6 "; $region_name="Region 6"; }
		if ($output['formData']['REGION']=="MY"&&$region=="7") { $inclause=" and REGION_ID = 7 "; $region_name="Region 7"; }
		if ($output['formData']['REGION']=="MY"&&$region=="8") { $inclause=" and REGION_ID = 8 "; $region_name="Region 8"; }
		if ($output['formData']['REGION']=="MY"&&$region=="9") { $inclause=" and REGION_ID = 9 "; $region_name="Region 9"; }
		if ($output['formData']['REGION']=="MY"&&$region=="10") { $inclause=" and REGION_ID = 10 "; $region_name="Region 10"; }
		if ($output['formData']['REGION']=="MY"&&$region=="11") { $inclause=" and REGION_ID = 11 "; $region_name="Region 11"; }
    	      }

        $sql = "SELECT F.BUILDING_NBR as BUILDING_NBR, ";
        $sql .=" F.ADDRESS AS ADDRESS, ";
        $sql .=" F.CITY_NAME AS CITY_NAME, ";
        $sql .=" F.STATE_ABBR AS STATE_ABBR, ";
        $sql .=" F.ZIPCODE AS ZIPCODE, ";
        $sql .=" F.REGION_ID AS REGION_ID, ";
        $sql .= "F.FACILITY_ID AS FACILITY_ID, ";
        $sql .= "F.FACILITY_NAME AS FACILITY_NAME, ";
        $sql .= "F.FSL AS FSL, ";
        $sql .= "F.BUILDING, ";
        $sql .= " F.ACTIVE_FLAG AS STYLE ";
        $sql .= " from RWH_DIM_FACILITY F ";
        $sql .= " where F.FPS_RESPONSIBLE = 'Y' ";
        $sql .= $inclause;
	if ($output['formData']['BUILDING_NBR']!=""||$output['formData']['FACILITY_NAME']!=""||$output['formData']['ADDRESS']!=""||$output['formData']['CITY_NAME']!=""||$output['formData']['STATE_ABBR']!="") {
      	    $sql .=" AND (1=0 ";
	    if ($output['formData']['BUILDING_NBR']!="") { $sql .= " OR BUILDING_NBR LIKE UPPER('%" . $output['formData']['BUILDING_NBR'] . "%') "; }
	    if ($output['formData']['FACILITY_NAME']!="") { $sql .= " OR UPPER(FACILITY_NAME) LIKE UPPER('%" . $output['formData']['FACILITY_NAME'] . "%') "; }
	    if ($output['formData']['ADDRESS']!="") { $sql .= " OR UPPER(ADDRESS) LIKE UPPER('%" . $output['formData']['ADDRESS'] . "%') "; }
	    if ($output['formData']['CITY_NAME']!="") { $sql .= " OR UPPER(CITY_NAME) LIKE UPPER('%" . $output['formData']['CITY_NAME'] . "%') "; }
	    if ($output['formData']['STATE_ABBR']!="") { $sql .= " OR UPPER(STATE_ABBR) LIKE UPPER('%" . $output['formData']['STATE_ABBR'] . "%') "; }
	    if ($output['formData']['ZIPCODE']!="") { $sql .= " OR ZIPCODE LIKE '%" . $output['formData']['ZIPCODE'] . "%' "; }
	$sql .=" ) ";
	}
        $sql .= " order by BUILDING_NBR";
	$aa=$this->X->sql($sql);
        $output['user']=$a;
        $output['data']=$aa;
	$output['region_name']=$region_name;
        return $output;
    }

    function getFacilityDashboard($data) {
         $output=$this->start_output($data);
	 $sql="select * from RWH_DIM_FACILITY WHERE FACILITY_ID = " . $data['id'];
	 $f=$this->X->sql($sql);
	 $output['formData']=$f[0];
	 $facility=$f[0];
	 foreach($f[0] as $name=>$value) {
             $output[$name]=$value;
	 }

	 

        $sql = "SELECT ";
        $sql .= " VULNERABILITY_ID, ";
        $sql .= " VULNERABILITY, ";
        $sql .= " TCM_STATUS, ";
        $sql .= " CONTACT_PHONE, ";
        $sql .= " CONTACT_CALL_SIGN, TICKET_TYPE, ";
        $sql .= " CREATED_DT ";
        $sql .= " FROM FORMVULNERABILITY ";
	$sql .= " WHERE BUILDING_NBR = '" . $output['BUILDING_NBR'] . "' ";
	$sql .= " AND TCM_STATUS NOT IN ('COMPLETED','CLOSED') AND SOURCE = 'TSD' ORDER BY VULNERABILITY_ID ";
	$f=$this->X->sql($sql);
	$output['tickets']=$f;
        $sql = "SELECT ";
        $sql .= " VULNERABILITY_ID, ";
        $sql .= " VULNERABILITY, ";
        $sql .= " TCM_STATUS, ";
        $sql .= " CONTACT_PHONE, ";
        $sql .= " CONTACT_CALL_SIGN, TICKET_TYPE, ";
        $sql .= " CREATED_DT ";
        $sql .= " FROM FORMVULNERABILITY ";
	$sql .= " WHERE BUILDING_NBR = '" . $output['BUILDING_NBR'] . "' AND TCM_STATUS ";
	$sql .= " IN ('COMPLETED','CLOSED') AND SOURCE = 'TSD' ORDER BY VULNERABILITY_ID ";
	$f=$this->X->sql($sql);
	$output['closed']=$f;

	 $contacts=array();

	 $sql="SELECT * FROM FPS_USER WHERE USER_ID = " . $facility['ASSIGNED_INSPECTOR'];
	 $f=$this->X->sql($sql);
	 $list=array();
	 foreach ($f as $e) {
             $h=array();
	     $h['CONTACT_ID']=$e['USER_ID'];
	     $h['CONTACT_TYPE']="FPS";
	     $h['CONTACT_NAME']=$e['FIRST_NAME'] . ' ' . $e['LAST_NAME'];
	     $h['CONTACT_PHONE']=$e['CELL_PHONE'];
	     $h['CONTACT_EMAIL']=$e['EMAIL'];
	     $h['CONTACT_TITLE']="Assigned Inspector";
	     $h['CONTACT_AGENCY_CODE']="FPS";
	     $h['CONTACT_AGENCY_NAME']="Federal Protective Service";
	     array_push($list,$h);

	}
	 $sql="SELECT * FROM FPS_USER WHERE USER_ID = " . $facility['AREA_COMMANDER_ID'];
	 $f=$this->X->sql($sql);
	 $list=array();
	 foreach ($f as $e) {
             $h=array();
	     $h['CONTACT_ID']=$e['USER_ID'];
	     $h['CONTACT_TYPE']="FPS";
	     $h['CONTACT_NAME']=$e['FIRST_NAME'] . ' ' . $e['LAST_NAME'];
	     $h['CONTACT_PHONE']=$e['CELL_PHONE'];
	     $h['CONTACT_EMAIL']=$e['EMAIL'];
	     $h['CONTACT_TITLE']="Area Commander";
	     $h['CONTACT_AGENCY_CODE']="FPS";
	     $h['CONTACT_AGENCY_NAME']="Federal Protective Service";
	     array_push($list,$h);

	}
	 $sql="SELECT * FROM FPS_USER WHERE USER_ID = " . $facility['DISTRICT_COMMANDER_ID'];
	 $f=$this->X->sql($sql);
	 $list=array();
	 foreach ($f as $e) {
             $h=array();
	     $h['CONTACT_ID']=$e['USER_ID'];
	     $h['CONTACT_TYPE']="FPS";
	     $h['CONTACT_NAME']=$e['FIRST_NAME'] . ' ' . $e['LAST_NAME'];
	     $h['CONTACT_PHONE']=$e['CELL_PHONE'];
	     $h['CONTACT_EMAIL']=$e['EMAIL'];
	     $h['CONTACT_TITLE']="District Commander";
	     $h['CONTACT_AGENCY_CODE']="FPS";
	     $h['CONTACT_AGENCY_NAME']="Federal Protective Service";
	     array_push($list,$h);
	}

	$sql="SELECT * FROM FPS_BUILDING_CONTACT WHERE BUILDING_NBR = '" . $output['BUILDING_NBR'] . "' ORDER BY CONTACT_NAME";
	$f=$this->X->sql($sql);
	foreach($f as $g) array_push($list,$g);
	$output['contacts']=$list;

	$ticketData=array();
	$ticketData['VULNERABILITY_ID']="0";
	$ticketData['SURVEY_ID']="0";
	$ticketData['SECTIONID']="";
	$ticketData['QUESTIONID']="";
	$ticketData['TENANT']="";
	$ticketData['ENTRYID']="";
	$ticketData['EVENT_ID']="";
	$ticketData['VULNERABILITY']="";
	$ticketData['RECOMMENDATION']="";
	$ticketData['TCM_STATUS']="";
	$ticketData['TCM_STATUS_DATE']="";
	$ticketData['TCM_STATUS_UID']="";
	$ticketData['SOURCE']="TSD";
	$ticketData['BUILDING_NBR']=$output['BUILDING_NBR'];
	$ticketData['ASSET_ID']="";
	$ticketData['CONTACT_ID']="";
	$ticketData['ADD_CONTACT']="N";
	$ticketData['CONTACT_PHONE']="";
	$ticketData['CONTACT_EMAIL']="";
	$ticketData['CONTACT_NAME']="";
	$ticketData['CONTACT_TITLE']="";
	$ticketData['CONTACT_AGENCY_CODE']="";
	$ticketData['CONTACT_AGENCY_NAME']="";
	$ticketData['CONTACT_TYPE']="";
	$ticketData['CONTACT_CALL_SIGN']="";
	$ticketData['TICKET_TYPE']="";
	$ticketData['MFG']="";
	$ticketData['MODEL']="";
	$ticketData['LOCATION']="";
	$output['ticketData']=$ticketData;

	$sql="select * FROM FPS_BSA WHERE BUILDING_NBR = '" . $output['BUILDING_NBR'] . "' ";
	$sql.=" AND IS_SYSTEM IN ('Y','N') AND ASSET_CLASS IN ('XRAY SYSTEM','XRAY EQUIPMENT') ORDER BY ASSET_ID ";
	$h=$this->X->sql($sql);
	$f=array();
	foreach($h as $g) {
            if ($g['ASSET_NAME']=="") $g['ASSET_NAME']=$g['ASSET_TYPE'];
	    array_push($f,$g); 
	}
	$output['xray']=$f;

	$sql="select * FROM FPS_BSA WHERE BUILDING_NBR = '" . $output['BUILDING_NBR'] . "' ";
	$sql.=" AND IS_SYSTEM IN ('Y','N') AND ASSET_CLASS IN ('PHYSICAL ACCESS CONTROL SYSTEM','PHYSICAL ACCESS CONTROL','PHYSICAL ACCESS CONTROL COMPONENT','PHYSICAL ACESS CONTROL SYSTEM','ALARM EQUIPMENT','ALARM SYSTEM') ORDER BY ASSET_ID ";
	$h=$this->X->sql($sql);
	$f=array();
	foreach($h as $g) {
            if ($g['ASSET_NAME']=="") $g['ASSET_NAME']=$g['ASSET_TYPE'];
	    array_push($f,$g); 
	}
	$output['pacs']=$f;

	$sql="select * FROM FPS_BSA WHERE BUILDING_NBR = '" . $output['BUILDING_NBR'] . "' ";
	$sql.=" AND IS_SYSTEM IN ('Y','N') AND ASSET_CLASS IN ('METAL DETECTION EQUIPMENT','METAL DETECTION EQUIPMENT') ORDER BY ASSET_ID ";
	$h=$this->X->sql($sql);
	$f=array();
	foreach($h as $g) {
            if ($g['ASSET_NAME']=="") $g['ASSET_NAME']=$g['ASSET_TYPE'];
	    array_push($f,$g); 
	}
	    $output['mags']=$f;

	$sql="select * FROM FPS_BSA WHERE BUILDING_NBR = '" . $output['BUILDING_NBR'] . "' ";
	$sql.=" AND IS_SYSTEM IN ('Y','N') AND ASSET_CLASS IN ('INTRUSION DETECTION SYSTEM','INTRUSION DETECTION EQUIPMENT') ORDER BY ASSET_ID ";
	$h=$this->X->sql($sql);
	$f=array();
	foreach($h as $g) {
            if ($g['ASSET_NAME']=="") $g['ASSET_NAME']=$g['ASSET_TYPE'];
	    array_push($f,$g); 
	}
	    $output['ids']=$f;

	$sql="select * FROM FPS_BSA WHERE BUILDING_NBR = '" . $output['BUILDING_NBR'] . "' ";
	$sql.=" AND IS_SYSTEM IN ('Y','N') AND ASSET_CLASS IN ('VIDEO SURVEILLANCE SYSTEM','VIDEO SURVEILLANCE SYSTEM EQUIPMENT','VIDEO SURVEILLANCE EQUIPMENT') ORDER BY ASSET_ID ";
	$h=$this->X->sql($sql);
	$f=array();
	foreach($h as $g) {
            if ($g['ASSET_NAME']=="") $g['ASSET_NAME']=$g['ASSET_TYPE'];
	    array_push($f,$g); 
	}
	$output['vss']=$f;



	 return $output;
    }

    function getTicketList($data) {
         $output=$this->start_output($data);
   	 $uid=$data['uid'];
         if (isset($data['data']['mycheckbox'])) $searchtype = $data['data']['mycheckbox']; else $searchtype="regionfacilities";

         if ($uid!=0) {
	     $sql = "SELECT ROLE, REGION_ID FROM FPS_USER WHERE USER_ID = " . $uid;
	     $s=$this->X->sql($sql);
	     $a=$s[0];
	     $role=$a['ROLE'];
	     $region=$a['REGION_ID'];
	     if ($role=="TSD") {
		if ($region=="0") $inclause=" and REGION_ID > 0 ";
		if ($region=="1") $inclause=" and REGION_ID in (1,2,3) ";
		if ($region=="2") $inclause=" and REGION_ID in (1,2,3) ";
		if ($region=="3") $inclause=" and REGION_ID in (1,2,3) ";
		if ($region=="4") $inclause=" and REGION_ID in (4,5,6,11) ";
		if ($region=="5") $inclause=" and REGION_ID in (4,5,6,11) ";
		if ($region=="6") $inclause=" and REGION_ID in (4,5,6,11) ";
		if ($region=="7") $inclause=" and REGION_ID in (7,8,9,10) ";
		if ($region=="8") $inclause=" and REGION_ID in (7,8,9,10) ";
		if ($region=="9") $inclause=" and REGION_ID in (7,8,9,10) ";
		if ($region=="10") $inclause=" and REGION_ID in (7,8,9,10) ";
		if ($region=="11") $inclause=" and REGION_ID in (11) ";
	      } else {
		$inclause=" and region_id in (" . $region . ") ";
		if ($region=="0"||$role=="AD") $inclause=" and region_id > 0 ";
	     }
         } else {
      	    $role="ER";
	    $region=0;
	    if ($region=="0") $inclause=" and REGION_ID > 0 ";
        }

        $sql = "SELECT F.BUILDING_NBR as BUILDING_NBR, ";
        $sql .=" F.ADDRESS AS ADDRESS, ";
        $sql .=" F.CITY_NAME AS CITY_NAME, ";
        $sql .=" F.STATE_ABBR AS STATE_ABBR, ";
        $sql .= "F.FACILITY_ID AS FACILITY_ID, ";
        $sql .= "F.FACILITY_NAME AS FACILITY_NAME, ";
        $sql .= " F.FSL AS FSL, ";
        $sql .= " VV.VULNERABILITY_ID AS VULNERABILITY_ID, ";
        $sql .= " VV.VULNERABILITY AS VULNERABILITY, ";
        $sql .= " VV.TCM_STATUS AS TCM_STATUS, ";
        $sql .= " VV.CONTACT_PHONE AS CONTACT_PHONE, ";
        $sql .= " VV.CONTACT_CALL_SIGN AS CONTACT_CALL_SIGN, VV.TICKET_TYPE AS TICKET_TYPE, ";
        $sql .= " VV.CREATED_DT AS CREATED_DT ";
        $sql .= " FROM RWH_DIM_FACILITY F, FORMVULNERABILITY VV ";
	 if ($data['id']=="1") {
        $sql .= " WHERE VV.TCM_STATUS IN ('CLOSED','COMPLETE') AND F.FPS_RESPONSIBLE = 'Y' AND F.BUILDING_NBR = VV.BUILDING_NBR AND VV.SOURCE = 'TSD' ";
	 } else {
        $sql .= " WHERE VV.TCM_STATUS NOT IN ('CLOSED','COMPLETE') AND F.FPS_RESPONSIBLE = 'Y' AND F.BUILDING_NBR = VV.BUILDING_NBR AND VV.SOURCE = 'TSD' ";
        }
 //       if ($searchtype == "regionfacilities") { $sql .= $inclause; }
        $sql .= " ORDER BY BUILDING_NBR";
//	echo $sql;
	$aa=$this->X->sql($sql);
        $output=array();
        $output['user']=$a;
        $output['data']=$aa;
	$output['id']=$data['id'];
	$new=0;
	$assigned=0;
	$active=0;
	$tenant=0;
	foreach($aa as $b) {
            if ($b['TCM_STATUS']=="NEW") $new++;
            if ($b['TCM_STATUS']=="ASSIGNED") $assigned++;
            if ($b['TCM_STATUS']=="TENANT") $tenant++;
	    $active++;
	}
	$output['new_count']=$new;
	$output['assigned_count']=$assigned;
	$output['open_count']=$active;
	$output['tenant_count']=$tenant;

        $sql = "SELECT DISTINCT F.BUILDING_NBR as BUILDING_NBR ";
        $sql .= " FROM RWH_DIM_FACILITY F, FORMVULNERABILITY VV ";
        $sql .= " WHERE VV.TCM_STATUS NOT IN ('CLOSED','COMPLETE') AND F.FPS_RESPONSIBLE = 'Y' AND F.BUILDING_NBR = VV.BUILDING_NBR AND VV.SOURCE = 'TSD' ";

//        if ($searchtype == "regionfacilities") { $sql .= $inclause; }
        $sql .= " ORDER BY BUILDING_NBR";
	$aa=$this->X->sql($sql);
	$fac=0;
	foreach($aa as $b) {
	    $fac++;
	}
	$output['affected_count']=$fac;

	return $output;
    }

    function getAccountProfile($data) {
         $output=$this->start_output($data);
         $uid=$data['uid'];
	 $sql="SELECT * FROM FPS_USER WHERE USER_ID = " . $uid;
	 $t=$this->X->sql($sql);
	 if (sizeof($t)>0) {
            $formData=$t[0];
	 } else {
	    $formData=array();
	 }
         $output['formData']=$formData;
	 return $output;
    }

    function getAddTicket($data) {
         $output=$this->start_output($data);
         $uid=$data['uid'];
	 $id=$data['id'];

         $out=array();
         $out['BUILDING_NBR'] = "";
         $out['VULNERABILITY_ID'] = "0";
         $out['SURVEY_ID'] = "0";
         $out['SECTIONID'] = "190";
         $out['QUESTIONID'] = "0";
         $out['TENANT'] = "0";
         $out['ENTRYID'] = "0";
         $out['VULNERABILITY'] = "";
         $out['RECOMMENDATION'] = "";
         $out['NO_RECOMMENDATION'] = "N";
         $out['RESPONSIBILITY'] = "FPS";
         $out['TCM_STATUS'] = "REPAIR";
         $out['TCM_ID'] = "0";
         $out['CREATED_BY'] = $uid;

         if ($id!="") {
	      $sql="SELECT BUILDING_NBR FROM RWH_DIM_FACILITY WHERE FACILITY_ID = " . $id;
	      $g=$this->X->sql($sql);
	      $rrr2=$g[0];
	      $out['BUILDING_NBR'] = $rrr2['BUILDING_NBR'];
          }


	$sql="SELECT ASSET_ID, ASSET_CLASSS,ASSET_TYPE,ASSET_NAME,SERIAL FROM FPS_BSA WHERE BUILDING_NBR = '" . $output['BUILDING_NBR'] . "' ORDER BY ASSET_CLASS, ASSET_TYPE, ASSET_ID";
	$rrr3 = $this->X->sql($sql);
	$output['assets'] = $rrr3;

	$sql = "SELECT 'FPS' AS AGENCY_CODE, 'FPS' AS AGENCY_NAME FROM ";
	$sql .= " DUAL ";
	$sql .= " UNION ";
	$sql .= "SELECT 'GSA' AS AGENCY_CODE, 'GSA' AS AGENCY_NAME FROM ";
	$sql .= " DUAL ";
	$sql .= " UNION ";
	$sql .= "SELECT DISTINCT RT.AGENCY_CODE AS AGENCY_CODE, AB.AGENCY_NAME_LONG AS AGENCY_NAME FROM ";
	$sql .= " REXUS_TENANTS RT, FPS_AGENCY_BUREAU AB WHERE RT.AGENCY_CODE = AB.AGENCY_CODE AND RT.BUILDING_NUMBER = ? ";
	$sql .= " UNION ";
	$sql .= "SELECT DISTINCT FT.AGENCY_CODE AS AGENCY_CODE, AB.AGENCY_NAME_LONG AS AGENCY_NAME FROM ";
	$sql .= " FPS_FACILITY_TENANTS FT, FPS_AGENCY_BUREAU AB WHERE FT.AGENCY_CODE = AB.AGENCY_CODE AND FT.FACILITY_ID = ? ";
	$sql .= " ORDER BY 1,2";
	$rrr3 = $this->X->sql($sql);
	$output['agencies'] = $rrr3;
	$output['contacts'] = array();
	$output['formData']=$out;
	 return $output;
    }

    function getTicketDashboard($data) {
	 //
	 // formerly /data/data.showticket.php
	 //

         $output=$this->start_output($data);
	 if (isset($_COOKIE['uid'])) {
              $uid=$_COOKIE['uid'];
	 } else {
              $uid=55009;
         }
 
         if (isset($_COOKIE['FID'])) {
              $fid=$_COOKIE['FID'];
         } else {
            if (isset($_COOKIE['fid'])) {
		$fid=$_COOKIE['fid'];
	    } else {
		$fid="";
	    }
         }

	 $id=$data['id'];

         $sql="SELECT * FROM FORMVULNERABILITY WHERE VULNERABILITY_ID = " . $id;
         $r = $this->X->sql($sql);
         $output=$r[0];
	 $formData=$r[0];
	 $output['formData']=$formData;
	 $noteData=array();
	 $noteData['VULNERABILITY_ID']=$id;
	 $noteData['CALL_TYPE']="";
	 $noteData['NOTE']="";
	 $output['noteData']=$noteData;
/*
         $sql="SELECT NOTE_ID FROM FPS_TCM_REC_NOTE WHERE VULNERABILITY_ID = " . $id . " ORDER BY NOTE_ID";
         $r = $this->X->sql($sql);
         if (sizeof($r)>0) {
            $nid = $r[0]['NOTE_ID'];

	    $sql="SELECT * FROM FPS_TCM_REC_NOTE WHERE NOTE_ID = " . $nid;
            $s = $this->X->sql($sql);
	    $rrr3=$s[0];
	    $output['CALL_TYPE'] = $rrr3['CALL_TYPE'];
	    $output['CONTACT_TYPE'] = $rrr3['CONTACT_TYPE'];
	    $output['CONTACT_NAME'] = $rrr3['CONTACT_NAME'];
	    $output['CONTACT_AGENCY_CODE'] = $rrr3['CONTACT_AGENCY_CODE'];
	    $output['CONTACT_AGENCY_NAME'] = $rrr3['CONTACT_AGENCY_NAME'];
	    $output['CONTACT_TITLE'] = $rrr3['CONTACT_TITLE'];
	    $output['CONTACT_EMAIL'] = $rrr3['CONTACT_EMAIL'];
	    $output['CONTACT_ID'] = $rrr3['CONTACT_ID'];
	    $output['EVENT_ID'] = $rrr3['EVENT_ID'];
	    $output['PRIORITY'] = $rrr3['PRIORITY'];
          }
 */

          $sql="SELECT FACILITY_NAME, ADDRESS, CITY_NAME, STATE_ABBR, ZIPCODE FROM RWH_DIM_FACILITY WHERE ";
          $sql .= " BUILDING_NBR = '" . $output['BUILDING_NBR'] . "'";
          $s = $this->X->sql($sql);
	  $rrr2=$s[0];
          $output['FACILITY_NAME'] = $rrr2['FACILITY_NAME'];
          $output['ADDRESS'] = $rrr2['ADDRESS'];
          $output['CITY_NAME'] = $rrr2['CITY_NAME'];
          $output['STATE_ABBR'] = $rrr2['STATE_ABBR'];
          $output['ZIPCODE'] = $rrr2['ZIPCODE'];

          $sql="SELECT LAST_NAME, FIRST_NAME FROM FPS_USER WHERE USER_ID = " . $output['CREATED_BY'];
          $s = $this->X->sql($sql);
	  if (sizeof($s)>0) {
              $rrr3 = $s[0];
              $output['ENTERED_BY'] = $rrr3['LAST_NAME'] . ', ' . $rrr3['FIRST_NAME'];
	  } else {
              $output['ENTERED_BY'] = "Not Found";
	  }

          if ($output['SECTIONID']!="") {
               $sql="SELECT SECTIONNAME FROM FORMSECTIONS WHERE SECTIONID = " . $output['SECTIONID'];
               $s = $this->X->sql($sql);
	       if (sizeof($s)>0) {
                   $rrr2 = $s[0];
                   $output['SECTIONNAME'] = $rrr2['SECTIONNAME'];
		} else {
                   $output['SECTIONNAME'] = $output['SECTIONID'];
		}
          } else {
               $output['SECTIONNAME']="";
          }

           $sql="SELECT TCM_ASSIGNED_TO, TCM_ASSIGNED_DT, TCM_DATE FROM FORMVULNERABILITY WHERE VULNERABILITY_ID = " . $id;
           $s = $this->X->sql($sql);
           $rrr2 = $s[0];
           $output['TCM_ASSIGNED_DT']=$rrr2['TCM_ASSIGNED_DT'];
           $output['TCM_DATE']=$rrr2['TCM_DATE'];

           if ($rrr2['TCM_ASSIGNED_TO']!="0") {
	          $sql="SELECT LAST_NAME, FIRST_NAME FROM FPS_USER WHERE USER_ID = " . $output['TCM_ASSIGNED_TO'];
                  $s = $this->X->sql($sql);
                  $rrr3 = $s[0];
		  $output['TCM_ASSIGNED_TO'] = $rrr3['LAST_NAME'] . ', ' . $rrr3['FIRST_NAME'];
           } else {
	          $output['TCM_ASSIGNED_TO'] = "Not Assigned";
           }

           $sql="SELECT NOTE_DATE, ";
	   $sql .= " LAST_NAME, FIRST_NAME, NOTE, CONTACT_NAME, CONTACT_TITLE, CONTACT_AGENCY_CODE, ";
	   $sql .= " CONTACT_AGENCY_NAME, CONTACT_PHONE, CONTACT_EMAIL, CALL_TYPE, PRIORITY FROM FPS_TCM_REC_NOTE, FPS_USER ";
           $sql .= " WHERE FPS_TCM_REC_NOTE.USER_ID = FPS_USER.USER_ID AND ";
           $sql .= " VULNERABILITY_ID = " . $output['VULNERABILITY_ID'] . " ORDER BY NOTE_DATE";

	   $sql="SELECT * FROM FPS_TCM_REC_NOTE WHERE VULNERABILITY_ID = " . $output['VULNERABILITY_ID'] . " ORDER BY NOTE_ID";
           $s = $this->X->sql($sql);
           $output['notes']=$s;

	   $formData=array();
	   
	   return $output;
    }

    function postAddTicket($data) {
	    $post=$data['data'];

            $output=$this->start_output($data);
            $uid=$data['uid'];

	    if ($post['ADD_CONTACT']=='Y') {
		$p=array();
		$p['table_name']="FPS_BUILDING_CONTACT";
		$p['action']="insert";

		$p['CONTACT_ID']=$this->getNextKey();
		$p['BUILDING_NBR']=$post['BUILDING_NBR'];
		$p['CONTACT_PHONE']=$post['CONTACT_PHONE'];
		$p['CONTACT_NAME']=$post['CONTACT_NAME'];
		$p['CONTACT_TITLE']=$post['CONTACT_TITLE'];
		$p['CONTACT_EMAIL']=$post['CONTACT_EMAIL'];
		$p['CONTACT_AGENCY_NAME']=$post['CONTACT_AGENCY_NAME'];
		$p['CONTACT_AGENCY_CODE']=$post['CONTACT_AGENCY_CODE'];
		$p['CONTACT_CALL_SIGN']=$post['CONTACT_CALL_SIGN'];
		$p['CONTACT_NOTE']=$post['CONTACT_NOTE'];
		$c=$this->X->post($post);
		$post['CONTACT_ID']=$c;
	    }

	$sql = "SELECT REGION_ID ";
	$sql .= " FROM RWH_DIM_FACILITY WHERE BUILDING_NBR = '" . $post['BUILDING_NBR'] . "'";
	$s=$this->X->sql($sql);
	$region=$s[0];

	$post['VULNERABILITY_ID']=$this->getNextKey();

	$post=array();
	$post=$data['data'];
	$post['table_name']="FORMVULNERABILITY";
	$post['action']="insert";
	print_r($post);
	die();

	$v=$this->X->post($post);

	$p=array();
	$p['table_name']="FPS_TCM_REC_NOTE";
	$p['action']="insert";
	$nid=$this->getNextKey();
	$p['NOTE_ID']=$nid;
	$p['CONTACT_ID']=$post['CONTACT_ID'];
	$p['CONTACT_NAME']=$post['CONTACT_NAME'];
	$p['CONTACT_AGENCY_CODE']=$post['CONTACT_AGENCY_CODE'];
	$p['CONTACT_AGENCY_NAME']=$post['CONTACT_AGENCY_NAME'];
	$p['CONTACT_TITLE']=$post['CONTACT_TITLE'];
	$p['CONTACT_EMAIL']=$post['CONTACT_EMAIL'];
	$p['EVENT_ID']=$post['EVENT_ID'];
	$p['PRIORITY']=$post['PRIORITY'];
	$note="Initial Entry";
	$p['CONTACT_NOTE']=$note;
	$p['CALL_TYPE']=$post['CALL_TYPE'];
	$p['CONTACT_TYPE']=$post['CONTACT_TYPE'];
	$p['TCM_STATUS']="NEW";

	$sql = "SELECT * FROM RWH_DIM_FACILITY WHERE BUILDING_NBR = '" . $data['data']['BUILDING_NBR'] . "'";
	$data = $this->X->sql($sql);
	$results = $data[0];
	$region=$results['REGION_ID'];

/*
	$sql = "SELECT NVL(ASSIGNED_INSPECTOR,0) AS ASSIGNED_INSPECTOR, NVL(AREA_COMMANDER_ID,0) AS AREA_COMMANDER_ID FROM ";
	$sql.=" RWH_DIM_FACILITY WHERE BUILDING_NBR = '" . $data['data']['BUILDING_NBR'] . "'";
	$s=$this->X->sql($sql);
	$results22 = $s[0];
	$inspector=$results22['ASSIGNED_INSPECTOR'];
	$ac=$results22['AREA_COMMANDER_ID'];

        if ($inspector!="0") {
	     $sql = "SELECT EMAIL FROM FPS_USER WHERE USER_ID = " . $inspector;
	     $data = $this->X->sql($sql);
	     $inspector_email=$data[0]['EMAIL'];
         } else {
	     $inspector_email = "";
         }

         if ($ac!="0") {
             $sql = "SELECT EMAIL FROM FPS_USER WHERE USER_ID = " . $ac;
	      $data = $this->X->sql($sql);
              $ac_email=$data[0]['EMAIL'];
         } else {
	      $ac_email = "";
         }


	$to="";
	$sql = "SELECT EMAIL FROM FPS_REGION_TCM_EMAIL WHERE REGION_ID = " . $region . " AND NEW_TCM = 'Y'";
	$res = $this->X->sql($sql);
	$f='Y';
	foreach ($res as $r) {
		if ($f!='Y') $to .= ',';
		$f='N';
		$to .= $r['EMAIL'];
	}

	if ($inspector_email!="") $to .= ", " . $inspector_email;
	if ($ac_email!="") $to .= ", " . $ac_email;
	if ($to=="") $to="fpsncp@associates.ice.dhs.gov, ehonour@anl.gov";

	$subject = "Ticket #" . $data['data']['VULNERABILITY_ID'] . " for " . $data['data']['BUILDING_NBR'];

	$message = '<html><head><title>Trouble Ticket added for ' . $data['data']['BUILDING_NBR'] . '</title></head><body>';
	$message .= '<p style="font-face:verdana; font-size:14px;">A new Trouble ticket ' . $data['data']['VULNERABILITY_ID'] . ' was created.</p>';
	$message .= '<table><tr><td style="width:130px;font-face:verdana; font-size:14px;">Building:</td><td  style="width:400px;font-face:verdana; font-size:14px;">';
	$message .= $data['data']['BUILDING_NBR'] . '</td></tr><tr><td style="width:130px;font-face:verdana; font-size:14px;"></td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $results['FACILITY_NAME'] . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;"></td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $results['ADDRESS'] . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;"></td>';

	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">';
	$message .= $results['CITY_NAME'];

	$message .= ', ';
	$message .= $results['STATE_ABBR'];
	$message .= ' ' . $results['ZIPCODE'] . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;">&nbsp;</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">&nbsp;</td>';
	$message .= '</tr>';

	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">Ticket:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $data['data']['VULNERABILITY_ID'] . '</td>';
	$message .= '</tr>';

	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">Ticket Type:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $data['data']['TICKET_TYPE'] . '</td>';
	$message .= '</tr>';


	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">Interaction:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $data['data']['CALL_TYPE'] . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">Call Source:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $data['data']['CONTACT_TYPE'] . '</td>';
	$message .= '</tr>';

	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">Contact:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $data['data']['CONTACT_PHONE'] . '</td>';
	$message .= '</tr>';


        if ($data['data']['CONTACT_NAME']!="") {
		$message .= '<tr>';
		$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">&nbsp; </td>';
		$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $data['data']['CONTACT_NAME'] . '</td>';
		$message .= '</tr>';
        }
        if ($data['data']['CONTACT_TITLE']!="") {
		$message .= '<tr>';
		$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">&nbsp; </td>';
		$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $data['data']['CONTACT_TITLE'] . '</td>';
		$message .= '</tr>';
        }

        if ($data['data']['CONTACT_EMAIL']!="") {
		$message .= '<tr>';
		$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">&nbsp; </td>';
		$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $data['data']['CONTACT_EMAIL'] . '</td>';
		$message .= '</tr>';
        }
        if ($data['data']['CONTACT_NOTE']!="") {
		$message .= '<tr>';
		$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">&nbsp; </td>';
		$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $data['data']['CONTACT_NOTE'] . '</td>';
		$message .= '</tr>';
        }

	$message .= '<tr><td style="width:130px;">&nbsp;</td><td  style="width:400px;">&nbsp;</td></tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">Priority:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $data['data']['PRIORITY'] . '</td>';
	$message .= '</tr>';

        if ($data['data']['MAGS_MFG']!=""&&$data['data']['X_RAY_MFG']!="? string: ?") {
	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">Manufacturer:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $data['data']['MAGS_MFG'] . '</td>';
	$message .= '</tr>';
        }

        if ($data['data']['X_RAY_MFG']!=""&&$data['X_RAY_MFG']!="? string: ?") {
	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">Manufacturer:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $data['X_RAY_MFG'] . '</td>';
	$message .= '</tr>';
        }

	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">Model:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $data['data']['MODEL'] . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">Serial:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $data['data']['SERIAL'] . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">Serial:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $data['data']['LOCATION'] . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;">&nbsp;</td>';
	$message .= '<td  style="width:400px;"></td>';
	$message .= '</tr>';
 	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">Issue:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $data['data']['VULNERABILITY'] . '<br></td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">&nbsp;</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;"></td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">&nbsp;</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;"></td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">&nbsp;</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">MIST Support</td>';
	$message .= '</tr>';
	$message .= '</table>';
	$message .= '</body>';
	$message .= '</html>';

	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	$headers .= 'From: fpsgateway@anl.gov' . "\r\n";
	$headers .= 'Cc: ehonour@anl.gov' . "\r\n";

	mail($to,$subject,$message,$headers);
 */
	 return $output;
    }

    function getNextKey() {
        // 
	// $sql="SELECT MEANINGLESS_KEY_SEQ.NEXTVAL AS C FROM DUAL";
	// $data=$this->X->sql($sql);	
	// return $data[0]['C'];
	    
	$post=array();
	$post['table_name']="MEANINGLESS_KEY_SEQ";
	$post['action']="insert";
	$post['SEGMENT_ID']="";
	$id=$this->X->post($post);
	return $id;

    }

    function checkContact($data) {
          $phone=$data['data']['CONTACT_PHONE'];
	  $phone=str_replace("(","",$phone);
          $phone=str_replace(")","",$phone);
          $phone=str_replace("-","",$phone);
          $phone=str_replace(" ","",$phone);
          $phone=str_replace("+","",$phone);
          $phone=str_replace(".","",$phone);
	  
	  $output=array();
	  $output['CONTACT_ID']=0;
	  $output['CONTACT_NAME']="";
	  $output['CONTACT_TITLE']="";
	  $output['CONTACT_AGENCY_CODE']="";
	  $output['CONTACT_AGENCY_NAME']="";
	  $output['CONTACT_PHONE']=$phone;
  	  $output['CONTACT_EMAIL']="";
	  $output['CONTACT_CALL_SIGN']="";
	  $output['CONTACT_TYPE']="";
          $output['error_code']="1";

	  $sql="SELECT * FROM FPS_USER";
	  $list=$this->X->sql($sql);
	  foreach ($list as $l) {
       	     $contact_phone=$l['CELL_PHONE'];
	     $contact_phone=str_replace("(","",$contact_phone);
	     $contact_phone=str_replace(")","",$contact_phone);
	     $contact_phone=str_replace("-","",$contact_phone);
	     $contact_phone=str_replace(" ","",$contact_phone);
	     $contact_phone=str_replace("+","",$contact_phone);
	     if ($phone==$contact_phone) {
		     $output=$l;
		     $output['CONTACT_TYPE']="FPS";
		     $output['CONTACT_NAME']=$l['FIRST_NAME'] . ' ' . $l['LAST_NAME'];
		     $output['CONTACT_PHONE']=$l['CELL_PHONE'];
		     $output['CONTACT_EMAIL']=$l['EMAIL'];
		     $output['CONTACT_CALL_SIGN']=$l['CALL_SIGN'];
		     $output['CONTACT_AGENCY_CODE']="FPS";
		     $output['CONTACT_AGENCY_NAME']="Federal Protective Service";
		     $output['error_code']="0";
	     }
          }
	  return $output;
    }
    function postAddNote($data) {
         $output=$this->start_output($data);
         $uid=$data['uid'];
	 $post=$data['data'];
         $note=$data['data']['NOTE'];
         $id=$data['data']['VULNERABILITY_ID'];
         if (!isset($data['data']['CALL_TYPE'])) $data['data']['CALL_TYPE']="";

	 $nid=$this->getNextKey();

	 $sql="SELECT FIRST_NAME, LAST_NAME, EMAIL FROM FPS_USER WHERE USER_ID = " . $uid;
	 $s=$this->X->sql($sql);
	 $user = $s[0];

	 $sql="SELECT * FROM FORMVULNERABILITY WHERE VULNERABILITY_ID = " . $id;
	 $s=$this->X->sql($sql);
	 $vulns = $s[0];

	 $post=array();
	 $post['table_name']="FPS_TCM_REC_NOTE";
	 $post['action']="insert";
	 $post['NOTE_ID']=$nid;
	 $post['USER_ID']=$uid;
	 $post['NOTE']=$note;
	 $post['VULNERABILITY_ID']=$id;
	 $post['CALL_TYPE']=$data['data']['CALL_TYPE'];
	 print_r($post);

         $this->X->post($post);
	 $output['error_code']=0;
/*
         if ($vulns['SOURCE']=="TSD") {
	     $sql = "SELECT * FROM RWH_DIM_FACILITY WHERE BUILDING_NBR = '" . $vulns['BUILDING_NBR'] . "'";
             $d=$this->X->sql($sql);
	     $results=$d[0];

   	     $to="";
	     $sql = "SELECT EMAIL FROM FPS_REGION_TCM_EMAIL WHERE REGION_ID = " . $results['REGION_ID'] . " AND NEW_NOTE = 'Y'";
	     $resd=$this->X->sql($sql);
	     $f='Y';
	     foreach ($res as $r) {
		if ($f!='Y') $to .= ',';
		$f='N';
		$to .= $r['EMAIL'];
	     }

	$subject = "NOTE ADDED to Trouble Ticket for " . $results['BUILDING_NBR'];
	$message = '<html><head><title>NOTE ADDED for ' . $vulns['BUILDING_NBR'] . '</title></head><body>';
	$message .= '<p style="font-face:verdana; font-size:14px;">A note was added to Trouble ticket ' . $data['VULNERABILITY_ID'] . ' was created.</p>';
	$message .= '<table><tr><td style="width:130px;font-face:verdana; font-size:14px;">Building:</td><td  style="width:400px;font-face:verdana; font-size:14px;">';
	$message .= $results['BUILDING_NBR'] . '</td></tr><tr><td style="width:130px;font-face:verdana; font-size:14px;"></td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $results['FACILITY_NAME'] . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;"></td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $results['ADDRESS'] . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;"></td>';

	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">';
	$message .= $results['CITY_NAME'];

	$message .= ', ';
	$message .= $results['STATE_ABBR'];
	$message .= ' ' . $results['ZIPCODE'] . '</td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;">&nbsp;</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">&nbsp;</td>';
	$message .= '</tr>';

	$message .= '<tr>';
	$message .= '<td style="width:130px;">ADDED BY:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $user['FULL_NAME'] . '</td>';
	$message .= '</tr>';

	$message .= '<tr>';
	$message .= '<td style="width:130px;">NOTE:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;"><b>' . $data['NOTE'] . '</b></td>';
	$message .= '</tr>';


	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">Ticket:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $id . '</td>';
	$message .= '</tr>';

	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">Ticket Type:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $vulns['TICKET_TYPE'] . '</td>';
	$message .= '</tr>';

	$message .= '<tr><td style="width:130px;">&nbsp;</td><td  style="width:400px;">&nbsp;</td></tr>';

	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">Issue:</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">' . $vulns['VULNERABILITY'] . '<br></td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">&nbsp;</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;"></td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">&nbsp;</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;"></td>';
	$message .= '</tr>';
	$message .= '<tr>';
	$message .= '<td style="width:130px;font-face:verdana; font-size:14px;">&nbsp;</td>';
	$message .= '<td  style="width:400px;font-face:verdana; font-size:14px;">MIST Support</td>';
	$message .= '</tr>';
	$message .= '</table>';
	$message .= '</body>';
	$message .= '</html>';

	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	$headers .= 'From: ' . $user['EMAIL'] . "\r\n";
	$headers .= 'Cc: ehonour@anl.gov' . "\r\n";

//	mail($to,$subject,$message,$headers);
         
	 }
 */
	 return $output;
    }

    function getDashboardData($data) {
         $output=$this->start_output($data);
	 return $output;
    }


    function getContactList($data) {
	    //
	    // formerly:  /data/data.tickets.contactlist.php
	    //
	    $output=$this->start_output($data);

            if (isset($data['uid'])) { $uid=$_COOKIE['uid']; } else { $uid=0; }
            if (isset($data['mycheckbox'])) $searchtype = $data['mycheckbox']; else $searchtype="regionfacilities";

            if ($uid!=0) {
             	$sql = "SELECT ROLE, REGION_ID FROM FPS_USER WHERE USER_ID = " . $uid;
		$s=$this->X->sql($sql);
		$a=$s[0];
	        $role=$a['ROLE'];
	        $region=$a['REGION_ID'];
	        if ($region=="0") $inclause=" and region_id > 0 ";
	        if ($region=="1") $inclause=" and region_id in (1,2,3) ";
	        if ($region=="2") $inclause=" and region_id in (1,2,3) ";
  	        if ($region=="3") $inclause=" and region_id in (1,2,3) ";
	        if ($region=="4") $inclause=" and region_id in (4,5,6,11) ";
	        if ($region=="5") $inclause=" and region_id in (4,5,6,11) ";
	        if ($region=="6") $inclause=" and region_id in (4,5,6,11) ";
	        if ($region=="7") $inclause=" and region_id in (7,8,9,10) ";
	        if ($region=="8") $inclause=" and region_id in (7,8,9,10) ";
	        if ($region=="9") $inclause=" and region_id in (7,8,9,10) ";
	        if ($region=="10") $inclause=" and region_id in (7,8,9,10) ";
	        if ($region=="11") $inclause=" and region_id in (11) ";
             } else {
                $role="ER";
	        $region=0;
	        if ($region=="0") $inclause=" and region_id > 0 ";
             }

           $sql = "select F.BUILDING_NBR as BUILDING_NBR, ";
           $sql .=" F.ADDRESS AS ADDRESS, ";
           $sql .=" F.CITY_NAME AS CITY_NAME, ";
           $sql .=" F.STATE_ABBR AS STATE_ABBR, ";
           $sql .= "F.FACILITY_ID AS FACILITY_ID, ";
           $sql .= "F.FACILITY_NAME AS FACILITY_NAME, ";
           $sql .= " F.FSL AS FSL, ";
           $sql .= " CC.CONTACT_ID AS CONTACT_ID, ";
           $sql .= " CC.CONTACT_NAME AS CONTACT_NAME, ";
           $sql .= " CC.CONTACT_PHONE AS CONTACT_PHONE, ";
           $sql .= " CC.CONTACT_EMAIL AS CONTACT_EMAIL, ";
           $sql .= " CC.CONTACT_AGENCY_NAME AS CONTACT_AGENCY_NAME, ";
           $sql .= " CC.CONTACT_AGENCY_CODE AS CONTACT_AGENCY_CODE ";
           $sql .= " FROM RWH_DIM_FACILITY F, FPS_BUILDING_CONTACT CC ";
           $sql .= " WHERE F.FPS_RESPONSIBLE = 'Y' AND F.BUILDING_NBR = CC.BUILDING_NBR ";

           if ($searchtype == "regionfacilities") {
              $sql .= $inclause;
           }

           $sql .= " ORDER BY BUILDING_NBR, CONTACT_NAME";
           $aa=$this->X->sql($sql);
           $output['user']=$a;
           $output['data']=$aa;
	   return $output;
    }


    function start_output($data) {
                $output=array();
                $output['user']=$this->getUser($data);
                if (!isset($output['user']['forced_off'])) $output['user']['forced_off']=0;
                return $output;
    }
    }
