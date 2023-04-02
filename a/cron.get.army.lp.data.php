<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('max_execution_time', '900');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json'); 
require_once('class.XRDB.php');
require_once('class.BSCScan.php');
$X=new XRDB();
$B=new BSCScan();
$l=$B->getArmyLPTokens();
echo $l;
die();
$list=json_decode($l,true);
$TIMESTAMP=time();
foreach($list as $name=>$value) {
		$post=array();
		$post['TABLE_NAME']="ARMY_BEP20_LP";
 //               $sql="SELECT ID FROM ARMY_BEP20_LP WHERE ADDRESS = '" . strtolower($value['address']) . "'";
 //               $i=$X->sql($sql);
 //               if (sizeof($i)>0) $post['ID']=$i[0]['ID'];

                $post['ADDRESS']=strtolower($value['address']);
                $post['TOKEN0_ADDRESS']=strtolower($value['tokens'][0]['address']);
                $post['TOKEN0_SYMBOL']=strtoupper($value['tokens'][0]['symbol']);
                $post['TOKEN0_AMOUNT']=$value['tokens'][0]['amount'];
		$post['TIMESTAMP']=$TIMESTAMP;

		$zz=$B->getTokenPrice($post['TOKEN0_SYMBOL'],$post['TIMESTAMP'],$post['TOKEN0_AMOUNT']);	
		$post['TOKEN0_PRICE']=$zz['price'];
		$post['TOKEN0_TIMESTAMP']=$zz['timestamp'];
		$post['TOKEN0_VALUE']=$zz['total'];

                $post['TOKEN1_ADDRESS']=strtolower($value['tokens'][1]['address']);
                $post['TOKEN1_SYMBOL']=strtoupper($value['tokens'][1]['symbol']);
                $post['TOKEN1_AMOUNT']=$value['tokens'][1]['amount'];
		$zz=$B->getTokenPrice($post['TOKEN1_SYMBOL'],$post['TIMESTAMP'],$post['TOKEN1_AMOUNT']);	
		$post['TOKEN1_PRICE']=$zz['price'];
		$post['TOKEN1_TIMESTAMP']=$zz['timestamp'];
		$post['TOKEN1_VALUE']=$zz['total'];

                echo strtoupper($name) . ",";
		$post['ACTION']="insert";
		$X->post($post);
}
?>


