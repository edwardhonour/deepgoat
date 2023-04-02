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
$l=$B->getArmyFarms();
$dateTime = time();
$list=json_decode($l,true);
foreach($list as $n) {

		$post=array();
		$t=time();
		
		$sql="SELECT * FROM GOATX_FARMS WHERE id = '" . strtolower($n['id']) . "'";
		$x=$X->sql($sql);
		if (sizeof($x)>0) {
				$post['id']=$x[0]['id'];
		} else {
			$sql="INSERT INTO GOATX_FARMS (id) VALUES ('" . strtolower($n['id']) . "')";
			$X->execute($sql);
			$sql2="SELECT * FROM GOATX_FARMS WHERE id = '" . strtolower($n['id']) . "'";
			$x=$X->sql($sql2);			
                }
		
$sq10="UPDATE GOATX_FARMS SET ";
$sql0="UPDATE GOATX_FARMS SET ";


		$w=$n['name'];
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
			
		if ($n['name']!=$x[0]['name']) {
           	    $sql0.=" name = '" . $w . "', ";	
		}
		
        if (!isset($n['token'])) {
             $w=$n['name'];
        } else {
  		     $w=$n['token'];
        }
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
		if ($w!=$x[0]['token']) {
			$sql0.=" token = '" . $w . "', ";	
		}

		if (isset($n['platform'])) {
			$w=$n['platform'];
		} else {
			$w="";
		}
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
		if ($w!=$x[0]['platform']) {
			$sql0.=" platform = '" . $w . "', ";	
		}
		
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
		
		if (isset($n['provider'])) {
			$w=$n['provider']['id'];
			$w=str_replace("'","", $w);
			$w=str_replace('"','', $w);
			$w=str_replace('\\','', $w);
		
			$sq="SELECT COUNT(*) AS C FROM GOATX_FARM_PROVIDER WHERE id = '" . $w . "'";
			$i=$X->sql($sq);
			if ($i[0]['C']==0) {
				$s2="INSERT INTO GOATX_FARM_PROVIDER (id, label, url, token, icon, timestamp) VALUES ('" . $w . "', ";
				if (isset($n['provider']['label'])) {
					$w=$n['provider']['label'];
					$w=str_replace("'","", $w);
					$w=str_replace('"','', $w);
					$w=str_replace('\\','', $w);
					$s2 .= "'" . $w . "',";
				} else {
					$s2 .= "'',";					
				}
				if (isset($n['provider']['url'])) {
					$w=$n['provider']['url'];
					$w=str_replace("'","", $w);
					$w=str_replace('"','', $w);
					$w=str_replace('\\','', $w);					
					$s2 .= "'" . $w . "',";
				} else {
					$s2 .= "'',";					
				}
				if (isset($n['provider']['token'])) {
					$w=$n['provider']['token'];
					$w=str_replace("'","", $w);
					$w=str_replace('"','', $w);
					$w=str_replace('\\','', $w);					
					$s2 .= "'" . $w . "',";					
				} else {
					$s2 .= "'',";					
				}
				if (isset($n['provider']['icon'])) {
					$w=$n['provider']['icon'];
					$w=str_replace("'","", $w);
					$w=str_replace('"','', $w);
					$w=str_replace('\\','', $w);					
					$s2 .= "'https://farm.army" . $w . "',";								
				} else {
					$s2 .= "'',";					
				}				
                                $s2 .= $t . ")";
				try {
					$X->execute($s2);
				} catch(Exception $e) {
						echo 'Message: ' .$e->getMessage();			
						echo $s2;
				}
			}
		} else {
			$w="";
		}

		if ($w!=$x[0]['provider_id']) {
			$sql0.=" provider_id = '" . $w . "', ";	
		}
		

		
		
		if (isset($n['earns'][0])) {
			$w=$n['earns'][0];
		} else {
			$w="";
		}
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
		if ($w!=$x[0]['earns_0']) {
			$sql0.=" earns_0 = '" . $w . "', ";	
		}
		
		
		if (isset($n['earns'][1])) {
			$w=$n['earns'][1];
		} else {
			$w="";
		}
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
		if ($w!=$x[0]['earns_1']) {
			$sql0.=" earns_1 = '" . $w . "', ";	
		}
		
		if (isset($n['earns'][2])) {
			$w=$n['earns'][2];
		} else {
			$w="";
		}
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
		if ($w!=$x[0]['earns_2']) {
			$sql0.=" earns_2 = '" . $w . "', ";	
		}
		
		if (isset($n['earns'][3])) {
			$w=$n['earns'][3];
		} else {
			$w="";
		}
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
		if ($w!=$x[0]['earns_3']) {
			$sql0.=" earns_3 = '" . $w . "', ";	
		}		
		
		if (isset($n['link'])) {
			$w=$n['link'];
		} else {
			$w="";
		}
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
		if ($w!=$x[0]['link']) {
			$sql0.=" link = '" . $w . "', ";	
		}
		
		if (isset($n['extra']['transactionToken'])) {
			$w=$n['extra']['transactionToken'];
		} else {
			$w="";
		}
		$w=strtolower($w);
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
		if ($w!=$x[0]['transaction_token']) {
			$sql0.=" transaction_token = '" . $w . "', ";	
		}
		
		if (isset($n['extra']['transactionAddress'])) {
			$w=$n['extra']['transactionAddress'];
		} else {
			$w="";
		}

		$w=strtolower($w);		
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
		if ($w!=$x[0]['transaction_address']) {
			$sql0.=" transaction_address = '" . $w . "', ";	
		}
		
		
		if (isset($n['extra']['lpAddress'])) {
			$w=$n['extra']['lpAddress'];
		} else {
			$w="";
		}
		
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
		if ($w!=$x[0]['lp_address']) {
			$sql0.=" lp_address = '" . $w . "', ";	
		}	
		
		if (isset($n['chain'])) {
			$w=$n['chain'];
		} else {
			$w="";
		}
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
		if ($w!=$x[0]['chain']) {
			$sql0.=" chain = '" . $w . "', ";	
		}	
		
	    if (isset($n['tvl']['amount'])) {
			$w=$n['tvl']['amount'];
		} else {
			$w="";
		}
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
		if ($w!=$x[0]['tvl_amount']) {
			$sql0.=" tvl_amount = '" . $w . "', ";	
		}			
		
		if (isset($n['tvl']['usd'])) {
			$w=$n['tvl']['usd'];
		} else {
			$w="";
		}
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
		if ($w!=$x[0]['tvl_usd']) {
			$sql0.=" tvl_usd = '" . $w . "', ";	
		}		
		
		
		if (isset($n['yield']['apy'])) {
			$w=$n['yield']['apy'];
		} else {
			$w="";
		}
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
		if ($w!=$x[0]['yield_apy']) {
			$sql0.=" yield_apy = '" . $w . "', ";	
		}				
		
		if (isset($n['yield']['daily'])) {
			$w=$n['yield']['daily'];
		} else {
			$w="";
		}
		
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
		
		if ($w!=$x[0]['yield_daily']) {
			$sql0.=" yield_daily = '" . $w . "', ";	
		}		
		
		
		$w=$n['icon'];
		$w=str_replace("'","", $w);
		$w=str_replace('"','', $w);
		$w=str_replace('\\','', $w);
                $w="https://farm.army".$w;
		if ($w!=$x[0]['icon']) {
			$sql0.=" icon = '" . $w . "', ";	
		}	
		$sql0.=" timestamp = " . $t . " where id = '" . $n['id'] . "'";	
	
		try {
			$X->execute($sql0);
		} catch(Exception $e) {
				echo 'Message: ' .$e->getMessage();			
				echo $sql;
		}
}
?>


