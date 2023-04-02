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
// goatx_research_finance_trending
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
$l=$B->getGeckoFinancePlatforms();
$dateTime = time();
$list=json_decode($l,true);
$counter=0;
$table_name="GOATX_RESEARCH_FINANCE_PLATFORMS";

foreach($list as $n) {
$counter++;
		$post=array();
		$t=time();
		
		$sql="SELECT * FROM " . $table_name . " WHERE id = '" . strtolower($n['name']) . "'";
		$x=$X->sql($sql);
		if (sizeof($x)>0) {
				$post['id']=$x[0]['id'];
		} else {
			$sql="INSERT INTO " . $table_name . " (id) VALUES ('" . strtolower($n['name']) . "')";
			$X->execute($sql);
			$sql2="SELECT * FROM " . $table_name . " WHERE id = '" . strtolower($n['name']) . "'";
			$x=$X->sql($sql2);			
                }
		
$sq10="UPDATE " . $table_name . " SET ";
$sql0="UPDATE " . $table_name . " SET ";

             $sql0 .= do_one("name",$n,$x[0]);
             $sql0 .= do_one("facts",$n,$x[0]);
             $sql0 .= do_one("category",$n,$x[0]);
             $sql0 .= do_one("centralized",$n,$x[0]);
             $sql0 .= do_one("website_url",$n,$x[0]);		 
		$sql0.=" timestamp = " . $t . " where id = '" . strtolower($n['name']) . "'";	
	
		try {
			$X->execute($sql0);
		} catch(Exception $e) {
				echo 'Message: ' .$e->getMessage();			
				echo $sql0;
		}
}
?>


