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

$sql="SELECT chain, address, exclude, token_symbol, token_name FROM GOATX_TOKEN_CONTRACT WHERE address <> ''";
$r=$X->sql($sql);

$count=0;
foreach($r as $j) {
	$contract=$j['address'];
        if ($j['chain']=='BSC') $chain_id=56;
        if ($j['chain']=='ETH') $chain_id=0;
        if ($j['exclude']=='N') { $token_data_exists = 'Y'; } else { $token_data_exists = 'N'; }
        $note=$j['token_name'];
        $note2=$j['token_symbol'];

        $node_type=3;
        $sql="insert into GNODE (address, chain_id, node_type, note2, note, token_data_exists) values ('";
        $sql.= $contract . "'," . $chain_id . ",2,'" . $note . "','" . $note2 . "','" . $token_data_exists . "')";
        $B->execute($sql);
}

?>
Finished

