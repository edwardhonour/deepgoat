<?php


ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('max_execution_time',12000);
ini_set('memory_limit','256M');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json'); 
require_once('class.BSCScan.php');
$X=new BSCScan();
$j=time();
/*
{
  "type": "subscribe",
  "fired_at": "2009-03-26 21:35:57",
  "data": {
    "id": "8a25ff1d98",
    "list_id": "a6b5da1054",
    "email": "api@mailchimp.com",
    "email_type": "html",
    "ip_opt": "10.20.10.30",
    "ip_signup": "10.20.10.30"
    "merges": {
      "EMAIL": "api@mailchimp.com",
      "FNAME": "Mailchimp",
      "LNAME": "API",
      "INTERESTS": "Group1,Group2"
    }
  }
}
*/

$t=time();
$email=$_POST['data']['email'];

$s="UPDATE GOATX_USER SET verified = 'Y', verified_timestamp = " . $t . " where email = '" . strtolower($email) . "'";
$X->execute($s);
$sql="COMMIT";
$X->execute($sql);
?>
[]
