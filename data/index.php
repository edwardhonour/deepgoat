<?php

//---------------------------------------------------------------------
// Main API Router for this angular directory.
// Author:  Edward Honour
// Date: 07/18/2021
//---------------------------------------------------------------------

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json'); 

//
// Require and initialize the class libraries necessary for this module.
//
require_once('class.menus.php');
require_once('class.pages.php');
require_once('class.quadm.php');
require_once('class.forms.php');
require_once('class.user.php');

$M=new MENUS();
$P=new PAGES();
$F=new FORMS();
$U=new USERS();
$Q=new QUADM();

//
// Get the Data from the POST.  Note:  This is not how PHP normally sends POST data.
//
$data = file_get_contents("php://input");
$data = json_decode($data, TRUE);
//if (!isset($data['q'])) die('[]');

$output=array();
if (!isset($data['q'])) $data['q']="vertical-menu";
switch ($data['q']) {
        case 'edit.user':
                $output=$data;
                $output['error']="0";
                break;
	case 'vertical-menu':
		$output=$M->getVerticalMenu($data);
		break;				
        case 'v':
		$output=$P->getDashboardData($data);
                break;
        case 'h':
		$output=$P->getDashboardData($data);
                break;
        case 'login':
		$output=$Q->getLogin($data);
                break;
        case 'new-claim':
                $output=$Q->getNewClaimData($data);
        case 'new-claim':
                $output=$Q->getNewClaimData($data);
                break;
        case 'get-zipcode':
                $output=$Q->getLocality($data['id']);
                break;
        case 'claim-dashboard':
                $output=$Q->getClaimDashboard($data);
                break;
        case 'invoice':
                $output=$Q->getClaimDashboard($data);
                break;
        case 'post-new-claim':
                $output=$Q->postNewClaim($data);
                break;
        case 'post-delete-claim':
                $output=$Q->postDeleteClaim($data);
                break;
        case 'post-update-claim':
                $output=$Q->postUpdateClaim($data);
                break;
        case 'post-update-edit1':
                $output=$Q->postUpdateEdit($data,"1");
                break;
        case 'post-update-edit2':
                $output=$Q->postUpdateEdit($data,"2");
                break;
        case 'post-update-edit3':
                $output=$Q->postUpdateEdit($data,"3");
                break;
        case 'post-update-edit4':
                $output=$Q->postUpdateEdit($data,"4");
                break;
        case 'post-update-edit5':
                $output=$Q->postUpdateEdit($data,"5");
                break;
        case 'post-update-edit6':
                $output=$Q->postUpdateEdit($data,"6");
                break;
        case 'post-update-edit7':
                $output=$Q->postUpdateEdit($data,"7");
                break;
        case 'post-update-edit8':
                $output=$Q->postUpdateEdit($data,"8");
                break;
        case 'post-update-edit9':
                $output=$Q->postUpdateEdit($data,"9");
                break;
        case 'post-update-edit10':
                $output=$Q->postUpdateEdit($data,"10");
                break;
        case 'tables-basic':
        case 'tables-basic':
                $output=$P->getTableData($data);
                break;
        case 'active-claims':
                $output=$Q->getClaimsData($data);
                break;
        case 'closed-claims':
                $output=$Q->getClaimsData($data,"Y");
                break;
        case 'users':
                $output=$U->getUserList($data);
                break;
	default:
		$output=$P->getDashboardData($data);
                break;
}
$o=json_encode($output);
$o=stripcslashes($o);
$o=str_replace('null','""',$o);
echo $o;

?>
