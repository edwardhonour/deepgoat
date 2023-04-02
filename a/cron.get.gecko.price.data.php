<?php
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
$l=$B->getGeckoTokenMarket();
$dateTime = time();
$list=json_decode($l,true);
$counter=0;
foreach($list as $n) {
$counter++;
		$post=array();
		$t=time();
		
		$sql="SELECT * FROM GOATX_RESEARCH_TOKEN WHERE id = '" . strtolower($n['id']) . "'";
		$x=$X->sql($sql);
		if (sizeof($x)>0) {
				$post['id']=$x[0]['id'];
		} else {
			$sql="INSERT INTO GOATX_RESEARCH_TOKEN (id) VALUES ('" . strtolower($n['id']) . "')";
			$X->execute($sql);
			$sql2="SELECT * FROM GOATX_RESEARCH_TOKEN WHERE id = '" . strtolower($n['id']) . "'";
			$x=$X->sql($sql2);			
                }
		
$sq10="UPDATE GOATX_RESEARCH_TOKEN SET ";
$sql0="UPDATE GOATX_RESEARCH_TOKEN SET ";

             $sql0 .= do_one("name",$n,$x[0]);
             $sql0 .= do_one("symbol",$n,$x[0]);
             $sql0 .= do_one("image",$n,$x[0]);
             $sql0 .= do_one("current_price",$n,$x[0]);
             $sql0 .= do_one("market_cap",$n,$x[0]);
             $sql0 .= do_one("market_cap_rank",$n,$x[0]);
             $sql0 .= do_one("fully_diluted_valuation",$n,$x[0]);
             $sql0 .= do_one("total_volume",$n,$x[0]);
             $sql0 .= do_one("high_24h",$n,$x[0]);
             $sql0 .= do_one("low_24h",$n,$x[0]);
             $sql0 .= do_one("price_change_24h",$n,$x[0]);
             $sql0 .= do_one("price_change_percentage_24h",$n,$x[0]);
             $sql0 .= do_one("market_cap_change_24h",$n,$x[0]);
             $sql0 .= do_one("market_cap_change_percentage_24h",$n,$x[0]);
             $sql0 .= do_one("circulating_supply",$n,$x[0]);
             $sql0 .= do_one("total_supply",$n,$x[0]);
             $sql0 .= do_one("max_supply",$n,$x[0]);
             $sql0 .= do_one("ath",$n,$x[0]);
             $sql0 .= do_one("ath_change_percentage",$n,$x[0]);
             $sql0 .= do_one("ath_date",$n,$x[0]);
             $sql0 .= do_one("atl",$n,$x[0]);
             $sql0 .= do_one("atl_change_percentage",$n,$x[0]);
             $sql0 .= do_one("atl_date",$n,$x[0]);
             $sql0 .= do_one("last_updated",$n,$x[0]);
             $sql0 .= " order_id = " . $counter . ", ";
		$sql0.=" timestamp = " . $t . " where id = '" . $n['id'] . "'";	
	
		try {
			$X->execute($sql0);
		} catch(Exception $e) {
				echo 'Message: ' .$e->getMessage();			
				echo $sql0;
		}
	$myfile = fopen("/var/www/html/api/svg/".$n['id'].".svg", "w") or die("Unable to open file!");
	fwrite($myfile,"<?xml version=\"1.0\" standalone=\"no\"?>\n");
	fwrite($myfile,"<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.1//EN\" \"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd\">\n\n");
	fwrite($myfile,"<svg width=\"135\" height=\"50\" viewBox=\"0 0 135 50\"\n");
	fwrite($myfile,"  xmlns=\"http://www.w3.org/2000/svg\"\n"); 
	fwrite($myfile,"  xmlns:xlink=\"http://www.w3.org/1999/xlink\">\n\n\n\n");
	fwrite($myfile,"<polyline points=\"0.0, ");
       
	 $priceArray = $n['sparkline_in_7d']['price'];

	$priceMin = 9999999999999999999999999999999;
	$priceMax = 0;
	$priceXmove = 0.7988165680473372;
	$priceX = 0;	
	$priceFirst = 0;
	$priceLast = 0;
	$priceCount = 0;

	foreach ($priceArray as $singleprice)
	{
	if ($priceCount < 1)
	{
		$priceFirst = $singleprice;
	}
	$priceCount = $priceCount + 1;

	if ($singleprice > $priceMax)
		{
		$priceMax = $singleprice;
		}
	if ($singleprice < $priceMin)
		{
		$priceMin = $singleprice;
		}
	$priceLast = $singleprice;
	}
//	fwrite($myfile,"Min " . $priceMin . "\n");
//	fwrite($myfile,"Max " . $priceMax . "\n");
	$priceMultiplier = ($priceMax - $priceMin)/50;

        foreach ($priceArray as $singleprice)
        {
		$priceX = $priceX + $priceXmove;
		$scalePrice = ($singleprice - $priceMin)/$priceMultiplier;
		$scalePrice = abs($scalePrice-50);
		fwrite($myfile,$scalePrice." ".$priceX.", ");
				
	}
	if ($priceFirst > $priceLast)
	{	
		fwrite($myfile,"50.0\" fill=\"none\" stroke=\"#ed5565\" stroke-width=\"1.25\"/>\n");
	}
	else
	{
		fwrite($myfile,"50.0\" fill=\"none\" stroke=\"#57bd0f\" stroke-width=\"1.25\"/>\n");
	}

	fwrite($myfile,"\n\n</svg>");

//	die();
}
?>


