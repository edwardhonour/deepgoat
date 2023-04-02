<?php
// goatx_research_categories
// goatx_research_exchanges
// goatx_research_finance_platforms
// goatx_research_finance_products
// goatx_research_indexes
// goatx_research_derivatives
// goatx_research_derivative_exchanges
// goatx_research_events
// goatx_research_exchange_rates
// goatx_research_trending
// goatx_research_global
// goatx_research_global_market_cap
// goatx_research_global_volume
// goatx_research_global_pct
// goatx_research_global_defi
// goatx_research_companies

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
ini_set('max_execution_time', '900');
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
header('Content-Type: application/json'); 
require_once('class.XRDB.php');
require_once('class.BSCScan.php');

function do_one($name,$n0,$x0) {
        $ss="";
	if (isset($n0[$name])) {
		$w=$n0[$name];
	} else {
		$w="";
	}
		
	$w=str_replace("'","", $w);
	$w=str_replace('"','', $w);
	$w=str_replace('\\','', $w);
	if ($w!=$x0[$name]) {
		$ss=" " . $name . " = '" . $w . "', ";	
	}	
        return $ss;
}


$X=new XRDB();

$B=new BSCScan();
$l=$B->getGeckoEvents();
$dateTime = time();
$list=json_decode($l,true);
$counter=0;
$table_name="GOATX_RESEARCH_EVENTS";
print_r($list['data']);
foreach($list['data'] as $n) {
$counter++;
		$post=array();
		$t=time();
		
		$sql="SELECT * FROM " . $table_name . " WHERE id = '" . strtolower($n['title']) . "'";
		$x=$X->sql($sql);
		if (sizeof($x)>0) {
				$post['id']=$x[0]['id'];
		} else {
			$sql="INSERT INTO " . $table_name . " (id) VALUES ('" . strtolower($n['title']) . "')";
			$X->execute($sql);
			$sql2="SELECT * FROM " . $table_name . " WHERE id = '" . strtolower($n['title']) . "'";
			$x=$X->sql($sql2);			
                }
		
$sq10="UPDATE " . $table_name . " SET ";
$sql0="UPDATE " . $table_name . " SET ";

             $sql0 .= do_one("type",$n,$x[0]);
             $sql0 .= do_one("title",$n,$x[0]);
             $sql0 .= do_one("description",$n,$x[0]);
             $sql0 .= do_one("organizer",$n,$x[0]);
             $sql0 .= do_one("start_date",$n,$x[0]);		
             $sql0 .= do_one("end_date",$n,$x[0]);
             $sql0 .= do_one("website",$n,$x[0]);
             $sql0 .= do_one("email",$n,$x[0]);
             $sql0 .= do_one("venue",$n,$x[0]);
             $sql0 .= do_one("address",$n,$x[0]);
             $sql0 .= do_one("city",$n,$x[0]);
             $sql0 .= do_one("country",$n,$x[0]);
             $sql0 .= do_one("screenshot",$n,$x[0]);			 
		$sql0.=" timestamp = " . $t . " where id = '" .  strtolower($n['title']) . "'";	
	
		try {
			$X->execute($sql0);
		} catch(Exception $e) {
				echo 'Message: ' .$e->getMessage();			
				echo $sql0;
		}
}
?>


