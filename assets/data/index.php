<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('SMTP','spike.dis.anl.gov');
ini_set('memory_limit',-1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type, Authorization');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE,PATCH,OPTIONS');
header('Content-type: application/json');
require_once('class.TSD.php');
$T=new TSD();
$data = file_get_contents("php://input");
$data = json_decode($data, TRUE);
$output=array();

//
// Prevent an Error if 'q' doesnt exist.
//

if (!isset($data['q'])) $data['q']="vertical-menu";

//
// Explode the URL into its parts based on the path.
//
//
$aa=explode("/",$data['q']);
if (isset($aa[1])) {
     $data['q']=$aa[1];
     if (isset($aa[2])) {
         $data['id']=$aa[2];
         }
     if (isset($aa[3])) {
         $data['id2']=$aa[3];
         }
         if (isset($aa[4])) {
         $data['id3']=$aa[4];
         }
}

switch ($data['q']) {
       case 'tsd-home':
           $output=$T->getTSDHome($data);
           break;
       case 'show-facility':
           $output=$T->showFacility($data);
           break;
       case 'facility-list':
           $output=$T->getFacilityList($data);
           break;
       case 'facility-dashboard':
           $output=$T->getFacilityDashboard($data);
           break;
       case 'ticket-list':
           $output=$T->getTicketList($data);
           break;
       case 'account-profile':
           $output=$T->getAccountProfile($data);
           break;
       case 'contact-list':
           $output=$T->getContactList($data);
           break;
       case 'check-contact':
           $output=$T->checkContact($data);
           break;
       case 'add-ticket':
           $output=$T->getAddTicket($data);
           break;
       case 'ticket-dashboard':
           $output=$T->getTicketDashboard($data);
           break;
       case 'post-add-ticket':
           $output=$T->postAddTicket($data);
           break;
       case 'post-add-note':
           $output=$T->postAddNote($data);
           break;
        default:
           $output=$T->getFacilityList($data);
                break;
}
$o=json_encode($output, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
//$o=json_encode($output);
//$o=stripcslashes($o);
$o=str_replace('null','""',$o);
echo $o;
?>


