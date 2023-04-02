<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Content-Type');
header('Access-Control-Allow-Origin: *'); 
ini_set("max_execution_time", -1);
ini_set('memory_limit','-1');
require_once('class.XRDB.php');
require_once('class.BSCScan.php');

$X=new XRDB();
$B=new BSCScan();

$sql="SELECT CONTRACT_ADDRESS, CONTRACT_TYPE, PLATFORM FROM BEP20_CONTRACT WHERE CONTRACT_TYPE  <> 'TOKEN'";
$r=$X->sql($sql);
// LP 1 
// FARM 2 / BSC
// CHEF 3 / BSC-ETH
// farm-BEP20 
// FLAGGED
// GOV / BSC
// ROUTER - BSC/ETH


// 0 - Unknown
// 1 - LP
// 2 - FARM
// 3 - ROUTER
// 4 - GOV
// 5 - STAKING
// 6 - WALLET
// 99 - FLAGGED
$count=0;
foreach($r as $j) {
	$contract=$j['CONTRACT_ADDRESS'];
        if ($j['PLATFORM']=='ETH') { $chain_id=56; } else { $chain_id=0; }
        $node_type=0;
        if ($j['CONTRACT_TYPE']=='LP') $contract_type=1;
        if ($j['CONTRACT_TYPE']=='lp-BEP20') $contract_type=1;
        if ($j['CONTRACT_TYPE']=='farm-BEP20') $contract_type=2;
        if ($j['CONTRACT_TYPE']=='FARM') $contract_type=2;
        if ($j['CONTRACT_TYPE']=='ROUTER') $contract_type=3;
        if ($j['CONTRACT_TYPE']=='GOV') $contract_type=4;
        if ($j['CONTRACT_TYPE']=='STAKING') $contract_type=5;
        if ($j['CONTRACT_TYPE']=='WALLET') $contract_type=6;
        if ($j['CONTRACT_TYPE']=='FLAGGED') $contract_type=99;
        $node_type=3;
        $sql="insert into GNODE (address, chain_id, node_type, node_subtype) values ('";
        $sql .= $contract . "'," . $chain_id . ",3," . $contract_type . ")";
        $B->execute($sql);
}

?>
Finished

