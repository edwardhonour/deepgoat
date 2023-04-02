<?php

//------------------------------------------------------------------------------------
// Copyright 2021 - GEX Data Labs Inc.
// Data Processing utilities for BSCScan, Etherscan, CoinGecko API, and 
//     Farm.Army API
// Created for Amazon Aurora Database 
// Author: Edward Honour
// Date:  7/18/2021
//------------------------------------------------------------------------------------

class BSCUtils {

}

class BSCScan {

    protected $utils;
	protected $dbh;
	protected $db;
	protected $goatx_address;
	protected $grain_address;
	protected $symbol_list;
	protected $bsc_key;
	protected $eth_key;
	protected $lp_list;
	protected $farm_list;
	protected $staking_array;
	protected $syrup_array;	
	protected $pancake_swap_router;
	protected $master_chef;
	protected $current_version;
        protected $last_microtime;  
        protected $start_microtime;  
        protected $end_microtime;  
	protected $logfile;	
        protected $t;
        protected $address;
        
        function startlog($address,$process) {
//                $this->last_microtime=microtime(true);
                //$this->start_microtime=$this->last_microtime;
                //$this->logfile=fopen('/var/www/logs/' . substr($address,0,7) . '-' . $process.'.log',"w");
//                fwrite($this->logfile,$process . " started at " . $this->last_microtime . "\r\n");
        }

        function writelog($msg) {
 		//$n=microtime(true);
                //$t=$n-$this->last_microtime;
                //$this->last_microtime=$n;
                //fwrite($this->logfile,$t . " - " . $msg . "\r\n");
        }

        function closelog() {
                fclose($this->logfile);
        }

	function __construct() {
		$this->goatx_address="0x6D2baf9b208967Bd769Ce71D6c772CD7FeFE4C28";
		$this->grain_address="0x9C9ecdb228303d3106A5a5A8eC5a0aC87903D46A";
		$this->bsc_key="K14YJFYWC3NAWWU7J2T2G7KBXNDV993H85";
		$this->eth_key="QY1P3CGUEF5BMMXB2NFEQGIN5N77RC574E";
		$this->lp_list=array();
		$this->farm_list=array();
		$this->pancake_swap_router="0x10ED43C718714eb63d5aA57B78B54704E256024E";
		$this->staking_array=array();
		$this->syrup_array=array();		
		$this->master_chef=strtolower("0xa2807727A626a2E2212Af9498bb07746ADc244c7");
		$this->current_version=2;
		array_push($this->staking_array,strtolower("0x73feaa1eE314F8c655E354234017bE2193C9E24E"));
		array_push($this->syrup_array,strtolower("0x009cF7bC57584b7998236eff51b98A168DceA9B0"));		
                $this->last_microtime=microtime(true);
                $t="N";
                $this->db=$this->connect();
	}
	
	function populate_raw_transactions($address,$chain="BSC",$force="N") {
 			$this->populateTxInternalList($address,$chain,$force);	
			$this->populateTxList($address,$chain,$force);
			$this->populateTokenTxList($address,$chain,$force);
	}
	
	function is_staking_contract($address) {
			$t=false;
			foreach($this->staking_array as $a) {
				if ($a==$address) $t=true;
			}
			return $t;
	}

	function is_syrup_contract($address) {
			$t=false;
			foreach($this->syrup_array as $a) {
				if ($a==$address) $t=true;
			}
			return $t;
	}
	
	function spread($records) {
			$output['internal']=0;
			$output['txlist']=0;
			$output['tokentx']=0;
			foreach($records as $r) {
					if ($r['source']=='internal') $output['internal']++;
					if ($r['source']=='txlist') $output['txlist']++;		
					if ($r['source']=='tokentx') $output['tokentx']++;							
			}
			return $output;
	}
	
	function postBasic($record,$seq=0,$chain="BSC") {
			$post=array();
			$post['TABLE_NAME']="BEP20_TRANSACTIONS";
			$post['ACTION']="insert";
			$post['timestamp']=$record['timeStamp'];
			$post['hash']=$record['hash'];
			$post['blockNumber']=$record['blockNumber'];
			$post['gas']=$record['gas'];
			$post['gasPrice']=$this->convertBigNumber($record['gasPrice'],18,18);
			$post['gasUsed']=$record['gasUsed'];
			$post['gasBNB']=number_format(floatval($post['gasPrice'])*floatval($post['gasUsed']),18);
			$zz=$this->getTokenPrice("BNB",$post['timestamp'],$post['gasBNB']);	
			$post['bnbPrice']=$zz['price'];
			$post['gasTotal']=$zz['total'];
			$post['bnbValue']=$this->convertBigNumber($record['bnbValue'],18,6);
			$post['bnbTotal']=number_format(floatval($post['bnbValue'])*floatval($post['bnbPrice']),4);
                        $post['bnbTimestamp']=$zz['timestamp'];
			$post['wallet_address']=strtolower($record['walletAddress']);
			$post['VERSION_ID']=$this->current_version;
			$post['LAST_TIMESTAMP']=time();
			$post['CHAIN']=$chain;
			return $post;
	}
        function get_token_from_json($address) {
                $sql="SELECT DISTINCT fromAddress, CHAIN, COUNT(*) AS C FROM BEP20_TRANSACTIONS where wallet_address = '" . $address . "'";
                $sql.=" GROUP BY fromAddress, CHAIN ORDER BY 1,2";
                $output=array();
                $l=$this->sql($sql);
                foreach($l as $m) {
                    $k=array();
		    if ($m['fromAddress']!=""&&$m['fromAddress']!="ERROR") {
                        array_push($k,$m['fromAddress']);
                        array_push($k,$m['CHAIN']);
                        array_push($k,$m['C']);
                        array_push($output,$k);
                    }
                }
            return $output;
        }
	
        function get_token_to_json($address) {
                $sql="SELECT DISTINCT toAddress, CHAIN, COUNT(*) AS C FROM BEP20_TRANSACTIONS where wallet_address = '" . $address . "'";
                $sql.=" GROUP BY toAddress, CHAIN ORDER BY 1,2";
                $output=array();
                $l=$this->sql($sql);
                foreach($l as $m) {
                    $k=array();
		    if ($m['toAddress']!=""&&$m['toAddress']!="ERROR") {
                        array_push($k,$m['toAddress']);
                        array_push($k,$m['CHAIN']);
                        array_push($k,$m['C']);
                        array_push($output,$k);
                    }
                }
            return $output;
        }
	
        function get_coin_to_json($address) {
                $sql="SELECT DISTINCT bnbTo, CHAIN, COUNT(*) AS C FROM BEP20_TRANSACTIONS where wallet_address = '" . $address . "'";
                $sql.=" GROUP BY bnbTo, CHAIN ORDER BY 1,2";
                $output=array();
                $l=$this->sql($sql);
                foreach($l as $m) {
                    $k=array();
		    if ($m['bnbTo']!=""&&$m['bnbTo']!="ERROR") {
                        array_push($k,$m['bnbTo']);
                        array_push($k,$m['CHAIN']);
                        array_push($k,$m['C']);
                        array_push($output,$k);
                    }
                }
            return $output;
        }

        function get_coin_from_json($address) {
                $sql="SELECT DISTINCT bnbFrom, CHAIN, COUNT(*) AS C FROM BEP20_TRANSACTIONS where wallet_address = '" . $address . "'";
                $sql.=" GROUP BY bnbFrom, CHAIN ORDER BY 1,2";
                $output=array();
                $l=$this->sql($sql);
                foreach($l as $m) {
                    $k=array();
		    if ($m['bnbTo']!=""&&$m['bnbFrom']!="ERROR") {
                        array_push($k,$m['bnbFrom']);
                        array_push($k,$m['CHAIN']);
                        array_push($k,$m['C']);
                        array_push($output,$k);
                    }
                }
            return $output;
        }

	function get_bep20_contract($address,$chain="BSC") {
			if ($chain=="BSC") $sql="SELECT * FROM BEP20_TOKEN WHERE LOWER(CONTRACT_ADDRESS) = '" . strtolower($address) . "'";
			if ($chain=="ETH") $sql="SELECT * FROM ERC20_TOKEN WHERE LOWER(CONTRACT_ADDRESS) = '" . strtolower($address) . "'";			
			$list=$this->sql($sql);
			if (sizeof($list)>0) {
				$a=array();
				$a['SYMBOL']=$list[0]['SYMBOL'];
				$a['CONTRACT_NAME']=$list[0]['TOKEN_NAME'];
				if ($chain=="BSC") {
					$a['CONTRACT_TYPE']="BEP20";
					$a['PLATFORM']="BEP20";
				}
				if ($chain=="ETH") {
					$a['CONTRACT_TYPE']="ERC20";
					$a['PLATFORM']="ERC20";
				}				
				return $a;
			} else {
	            if ($chain=="BSC")  {
					$sql="SELECT * FROM BEP20_CONTRACT where ";
					$sql.="CONTRACT_ADDRESS = '" . strtolower($address) . "'";
				}
	            if ($chain=="ETH")  {
					$sql="SELECT * FROM BEP20_CONTRACT where ";
					$sql.="CONTRACT_ADDRESS = '" . strtolower($address) . "'";
				}				
				$list=$this->sql($sql);
				if (sizeof($list)>0) {
					return $list[0];
				} else {
					$a=array();
					$a['SYMBOL']="";
					$a['CONTRACT_NAME']="";
					$a['CONTRACT_TYPE']="";
					$a['PLATFORM']="";
					return $a;
				}			
			}
	}
	
	function post_receive_bnb_from_another_wallet($chain, $transaction) {
			// Query BEP20_TRANSACTIONS to get Cost Basis.
			
			// Scenario 1:  Wallet Transfer when I own both wallets.
			
			//	DR - TOKENS-HELD for value of Tokens when they are received. 
			//  DR - GAS Fees.
			//  CR - REVENUE gain or loss of value COST_BASIS - TOKENS-HELD 
			
			// Scenario 2:  Wallet Transfer to exchange wallet.
			
			//	DR - TOKENS-HELD for value of Tokens when they are received. 
			//  DR - GAS Fees.
			//  DR - INVESTMENT (reduce my investment balance.
			//  CR - REVENUE gain or loss of value COST_BASIS - TOKENS-HELD
			
	}
	
	function receive_bnb_from_another_wallet($local, $chain="BSC") {
		//
		//Received BNB from Another Wallet.
		//
		// There is only one record.
		// The one record is txlist.
		// bnbTo is the wallet address.
		// bnbValues is not 0.
		// There is no contract address.
		//

		$l=array();			
		if ($chain=="BSC") $l['description']="Received BNB from Another Wallet";
		if ($chain=="ETH") $l['description']="Received ETH from Another Wallet";		
		$post=$this->postBasic($local[0],$chain);
		$post['bnbFrom']=$local[0]['bnbFrom'];
		$post['bnbTo']=$local[0]['bnbTo'];
		$post['bnbValue']=$this->convertBigNumber($local[0]['bnbValue'],18);
		$post['fromAddress']=$local[0]['bnbFrom'];
		
		if ($chain=="BSC") { 
			$post['fromTokenSymbol']="BNB";				
			$post['fromTokenName']="BNB";
		} else {
			$post['fromTokenSymbol']="ETH";				
			$post['fromTokenName']="ETH";			
		}
		
		$post['fromTokenDecimal']=18;		
		$post['fromValue']=$this->convertBigNumber($local[0]['bnbValue'],18);
		$post['fromValue']=number_format($post['fromValue'],4);		
		$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
		$post['fromPrice']=$zz['price'];

		$post['fromTimestamp']=$zz['timestamp'];
		$post['fromValueUSD']=$zz['total'];
		$post['toPrice']=$zz['price'];
		$post['toTimestamp']=$zz['timestamp'];
		$post['toValueUSD']=$zz['total'];

		if ($chain=="BSC") { 
			$post['toTokenSymbol']="BNB";				
			$post['toTokenName']="BNB";
		} else {
			$post['toTokenSymbol']="ETH";				
			$post['toTokenName']="ETH";			
		}
		
		$post['toTokenDecimal']=18;	
		$post['toAddress']=$local[0]['bnbTo'];
		$post['toValue']=$this->convertBigNumber($local[0]['bnbValue'],18);
		$post['toValue']=number_format($post['toValue'],4);									
		$post['description']=$l['description'];
		//$post['transaction_type']="REC-BNB";
		$post['transaction_type']="RECEIVE";
		$this->bep20PostN($post,$chain);
											
	}
	
	function post_send_bnb_to_another_wallet($address) {
		
		
	}
	
	function send_bnb_to_another_wallet($local, $chain="BSC") {
		//
		// Sent BNB to Another Wallet 							
		// There is one record.
		// The one record is txlist.
		// bnbFrom is the wallet address.
		// bnbValue is not 0.
		// There is no contract address
		//
		$l=array();	
		if ($chain=="BSC") $l['description']="Sent BNB to Another Wallet";									
		if ($chain=="ETH") $l['description']="Sent ETH to Another Wallet";	
		
		$post=$this->postBasic($local[0],$chain);
		$post['bnbFrom']=$local[0]['bnbFrom'];
		$post['bnbTo']=$local[0]['bnbTo'];
		$post['bnbValue']=$this->convertBigNumber($local[0]['bnbValue'],18);	
		$post['fromAddress']=$local[0]['bnbFrom'];
if ($chain=="BNB") {
		$post['fromTokenSymbol']="BNB";				
		$post['fromTokenName']="BNB";
} else {
		$post['fromTokenSymbol']="ETH";				
		$post['fromTokenName']="ETH";
}
		$post['fromTokenDecimal']=18;	
		$post['fromValue']=$post['bnbValue'];
		$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
		$post['fromValue']=number_format($post['fromValue'],4);									
		$post['fromPrice']=$zz['price'];
		$post['fromTimestamp']=$zz['timestamp'];
		$post['fromValueUSD']=$zz['total'];
								
        if ($chain=="BSC") {								
			$post['toTokenSymbol']="BNB";				
			$post['toTokenName']="BNB";
		}
        if ($chain=="ETH") {								
			$post['toTokenSymbol']="ETH";				
			$post['toTokenName']="ETH";
		}
		
		$post['toTokenDecimal']=18;		
		$post['toAddress']=$local[0]['bnbTo'];
		$post['toValue']=$post['bnbValue'];
		$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
		$post['toValue']=number_format($post['toValue'],4);									
		$post['toPrice']=$zz['price'];
		$post['toTimestamp']=$zz['timestamp'];
		$post['toValueUSD']=$zz['total'];
		
		$post['description']=$l['description'];
		//$post['transaction_type']="SENT-BNB";
		$post['transaction_type']="SEND";
		$this->bep20PostN($post,$chain);			
	}
	
	function xpost_send_bnb_to_another_wallet($chain, $transaction) {
			// Query BEP20_TRANSACTIONS to get Cost Basis.
			//	DR - TOKENS-HELD for value of Tokens when they are received. 
			//  CR - INVESTMENT for the cost basis entered by user.
			//  CR - REVENUE gain or loss of value COST_BASIS - TOKENS-HELD 
	}
		
	
	function ppost_send_bnb_to_another_wallet($chain, $transaction) {
			//	CR - TOKENS-HELD for value of Tokens when they are sent. 
			//  CR - REVENUE gain or loss of value - TOKENS-HELD 
			
			//  DR - INVESTMENT if transfer is to my own exchange wallet.
			//  or 
			//  DR - RETAINED-EARNINGS 
	}
	
	function received_bep20_token_from_another_wallet($local, $chain="BSC") {
		//
		// Received BEP20 Token from Another Wallet
		// There is one record.
		// The one record is a tokentx.
		// The contract address is the token contract.
		//		
		$l=array();		
		$l['description']="Received " . $local[0]['tokenSymbol'] . " from Another Wallet";	
		$post=$this->postBasic($local[0],$chain);
		$post['bnbFrom']=$local[0]['bnbFrom'];
		$post['bnbTo']=$local[0]['bnbTo'];
		$post['bnbValue']=$this->convertBigNumber($local[0]['bnbValue'],18);	
		$post['fromAddress']=$local[0]['tokenFrom'];
		$post['fromTokenSymbol']=$local[0]['tokenSymbol'];				
		$post['fromTokenName']=$local[0]['tokenName'];
		$post['fromTokenDecimal']=$local[0]['tokenDecimal'];
		$post['fromValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);
		$post['fromValue']=number_format($post['fromValue'],4);									
		$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
		$post['fromPrice']=$zz['price'];
		$post['fromTimestamp']=$zz['timestamp'];
		$post['fromValueUSD']=$zz['total'];

		$post['toPrice']=$zz['price'];
		$post['toTimestamp']=$zz['timestamp'];
		$post['toValueUSD']=$zz['total'];
		$post['toValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);
		$post['toValue']=number_format($post['toValue'],4);	
		$post['toTokenSymbol']=$local[0]['tokenSymbol'];				
		$post['toTokenName']=$local[0]['tokenName'];
		$post['toTokenDecimal']=$local[0]['tokenDecimal'];
		$post['toAddress']=$local[0]['tokenTo'];
		$post['contractAddress']=$local[0]['contractAddress'];

		$array=$this->get_bep20_contract($post['contractAddress'],$chain);
		$post['contractName']=$array['CONTRACT_NAME'];
		$post['contractType']=$array['CONTRACT_TYPE'];
		$post['contractSymbol']=$array['SYMBOL'];
								
		$post['description']=$l['description'];
		//$post['transaction_type']="REC-BEP20";
		$post['transaction_type']="RECEIVE";
                $post['CHAIN']=$chain;
		$this->bep20PostN($post,$chain);
								
	}
	
	function contract_interaction_approval($local, $chain="BSC") {
		//
		// Contract Interaction/Approval	
		// One record.
		// The record is a txlist record.
		// bnbFrom is the wallet address.
		// bnbValue is 0.
		//	
		$l=array();	
		$post=$this->postBasic($local[0],$chain);
		$post['bnbFrom']=$local[0]['bnbFrom'];
		$post['bnbTo']=$local[0]['bnbTo'];
		$post['bnbValue']=$this->convertBigNumber($local[0]['bnbValue'],18);	
		$post['fromAddress']=$this->address;
		$post['fromTokenSymbol']="";				
		$post['fromTokenName']="";
		$post['fromTokenDecimal']=18;		
		$post['toTokenSymbol']="";				
		$post['toTokenName']="";
		$post['toTokenDecimal']=18;	
		$post['toAddress']=$local[0]['tokenTo'];
		$post['contractAddress']=strtolower($local[0]['bnbTo']);	
						
		$array=$this->get_bep20_contract($post['contractAddress'],$chain);
		$post['contractName']=$array['CONTRACT_NAME'];
		$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
		$l['description']="Contract Interaction-Approval";									
		$post['description']=$l['description'];
		$post['transaction_type']="APPROVE";								
		$this->bep20PostN($post,$chain);		
	}

	function stake_tokens($local, $chain="BSC") {
						//
						// Stake Tokens.
						// There are two records.  
						// One txlist (array 1) and one tokentx (array 0) 
						// Stake in Farm.	
						// tokenFrom is wallet address.
						// bnbFrom is wallet address.
						// bnbValue is 0.
						// bnbTo address is a staking contract.
						//		
							$l=array();		
									$l['description']="Stake in Farm - " . $local[0]['tokenSymbol'];
									$post=$this->postBasic($local[1],$chain);
									$post['bnbFrom']=$local[1]['bnbFrom'];
									$post['bnbTo']=$local[1]['bnbTo'];
									$post['bnbValue']=$this->convertBigNumber($local[1]['bnbValue'],18);	
									$post['fromAddress']=$local[0]['tokenFrom'];
									$post['fromTokenSymbol']=$local[0]['tokenSymbol'];	
									$post['fromTokenName']=$local[0]['tokenName'];
									$post['fromTokenDecimal']=$local[0]['tokenDecimal'];
									$post['fromValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);									
									$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
									$post['fromPrice']=$zz['price'];
									$post['fromTimestamp']=$zz['timestamp'];
									$post['fromValueUSD']=$zz['total'];									
									$post['toTokenSymbol']=$local[0]['tokenSymbol'];				
									$post['toTokenName']=$local[0]['tokenName'];
									$post['toTokenDecimal']=$local[0]['tokenDecimal'];	
									$post['toAddress']=$local[0]['tokenTo'];	
									$post['contractAddress']=$local[0]['contractAddress'];	

									$array=$this->get_bep20_contract($post['contractAddress'],$chain);
									$post['contractName']=$array['CONTRACT_NAME'];
									$post['contractType']=$array['CONTRACT_TYPE'];
									$post['contractSymbol']=$array['SYMBOL'];
								
									$post['toValue']=$this->convertBigNumber($local[0]['tokenValue'],18);
									$post['toPrice']=$zz['price'];
									$post['toTimestamp']=$zz['timestamp'];
									$post['toValueUSD']=$zz['total'];											
									$post['description']=$l['description'];
									$post['transaction_type']="STAKE-BEP20";	
									$post['transaction_type']="STAKE";									
                $post['CHAIN']=$chain;
									$this->bep20PostN($post,$chain);							
	}
	
	function send_bep20($local, $chain="BSC") {
									// 
									// Send a BEP20 token to another wallet.
									// Two records.
									// tokentx is $local[0] and txlist is $local[1]
									// fromAddress is wallet address.
									// 
					$l=array();									
									$l['description']="Sent " . $local[0]['tokenSymbol'] . " to another wallet";
									$post=$this->postBasic($local[1],$chain);	
									$post['bnbFrom']=$local[1]['bnbFrom'];
									$post['bnbTo']=$local[1]['bnbTo'];
									$post['bnbValue']=$this->convertBigNumber($local[1]['bnbValue'],18);	
									$post['fromAddress']=$this->address;
									$post['fromTokenSymbol']=$local[0]['tokenSymbol'];			
									$post['fromTokenName']=$local[0]['tokenName'];
									$post['fromTokenDecimal']=$local[0]['tokenDecimal'];
									$post['fromValue']=$this->convertBigNumber($local[0]['tokenValue'],$post['fromTokenDecimal']);
									$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);
									$post['fromPrice']=$zz['price'];
									$post['fromTimestamp']=$zz['timestamp'];
									$post['fromValueUSD']=$zz['total'];										
									$post['toTokenSymbol']=$local[0]['tokenSymbol'];				
									$post['toTokenName']=$local[0]['tokenName'];
									$post['toTokenDecimal']=$local[0]['tokenDecimal'];	
									$post['toAddress']=$local[0]['tokenTo'];
									$post['contractAddress']=$local[0]['contractAddress'];		

									$array=$this->get_bep20_contract($post['contractAddress'],$chain);
									$post['contractName']=$array['CONTRACT_NAME'];
									$post['contractType']=$array['CONTRACT_TYPE'];
									$post['contractSymbol']=$array['SYMBOL'];
									
									$post['toValue']=$this->convertBigNumber($local[0]['tokenValue'],$post['fromTokenDecimal']);
									$post['toPrice']=$zz['price'];
									$post['toTimestamp']=$zz['timestamp'];
									$post['toValueUSD']=$zz['total'];										
									$post['description']=$l['description'];
									$post['transaction_type']="SENT-BEP20";
									$post['transaction_type']="SEND";
									$this->bep20PostN($post,$chain);
	}
		
	function harvest_farm_rewards($local, $chain="BSC") {
		
								// 
								// Harvest only from a farm.
								// Two records.
								// tokentx [0] and txlist[1].
								// tokenFrom is a syrup contract.
								// bnbFrom is the wallet address.
								// bnbValue is 0
								//							
$l=array();							
								$l['description']="Harvest FARM Rewards - " . $local[0]['tokenSymbol'];
								
									$post=$this->postBasic($local[1],$chain);	
									$post['bnbFrom']=$local[1]['bnbFrom'];
									$post['bnbTo']=$local[1]['bnbTo'];
									$post['bnbValue']=$this->convertBigNumber($local[1]['bnbValue'],18);	
									$post['fromAddress']=$local[0]['tokenFrom'];
									$post['fromTokenSymbol']=$local[0]['tokenSymbol'];				
									$post['fromTokenName']=$local[0]['tokenName'];
									$post['fromTokenDecimal']=$local[0]['tokenDecimal'];
									$post['fromValue']="0.0000";
									$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
									$post['fromPrice']=$zz['price'];
									$post['fromTimestamp']=$zz['timestamp'];
									$post['fromValueUSD']=$zz['total'];											
									$post['toTokenSymbol']=$local[0]['tokenSymbol'];				
									$post['toTokenName']=$local[0]['tokenName'];
									$post['toTokenDecimal']=$local[0]['tokenDecimal'];	
									$post['toAddress']=$local[0]['tokenTo'];	
									$post['contractAddress']=$local[0]['contractAddress'];
									
									$array=$this->get_bep20_contract($post['contractAddress'],$chain);
									$post['contractName']=$array['CONTRACT_NAME'];
									$post['contractType']=$array['CONTRACT_TYPE'];
									$post['contractSymbol']=$array['SYMBOL'];
									
									$post['toValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);										
									$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
									$post['toPrice']=$zz['price'];
									$post['toTimestamp']=$zz['timestamp'];
									$post['toValueUSD']=$zz['total'];	
									
									$post['description']=$l['description'];
									$post['transaction_type']="HARVEST-PS";
									$post['transaction_type']="RECEIVE";
									$this->bep20PostN($post,$chain);	
	}
	
	function send_bnb_receive_bep20($local, $chain="BSC") {
								//
								// Token Swap sending BNB receiving BEP20. 
								// Two records.
								// bnbFrom is the wallet address.
								// tokenTo is the wallet address.
								// bnbValue IS NOT 0. This is amount of BNB sent.
								// toValue IS NOT 0. This is amount of Token Received.
								//
$l=array();	
		
										
								if ($chain=="BSC") $l['description']="Token Swap sending BNB receiving " . $local[0]['tokenSymbol'];
								if ($chain=="ETH") $l['description']="Token Swap sending ETH receiving " . $local[0]['tokenSymbol'];
								
								$post=$this->postBasic($local[1],$chain);	
								$post['bnbFrom']=$local[1]['bnbFrom'];
								$post['bnbTo']=$local[1]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[1]['bnbValue'],18);	
								$post['fromAddress']=$this->address;
								if ($chain=="BSC") {
									$post['fromTokenSymbol']="BNB";				
									$post['fromTokenName']="BNB";
									$post['fromTokenDecimal']=18;
								} 
								$post['fromAddress']=$this->address;
								if ($chain=="ETH") {
									$post['fromTokenSymbol']="ETH";				
									$post['fromTokenName']="ETH";
									$post['fromTokenDecimal']=18;
								} 								
								$post['fromValue']=$this->convertBigNumber($local[1]['bnbValue'],18);
								$post['fromPrice']=$post['bnbPrice'];
								$post['fromTimestamp']=$post['bnbTimestamp'];
								$post['fromValueUSD']=$post['bnbTotal'];	
								$post['toTokenSymbol']=$local[0]['tokenSymbol'];				
								$post['toTokenName']=$local[0]['tokenName'];
								$post['toTokenDecimal']=$local[0]['tokenDecimal'];		
								$post['toAddress']=$this->address;
								$post['contractAddress']=$local[0]['contractAddress'];
								
								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$post['toValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);												
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								$post['description']=$l['description'];
								$post['transaction_type']="BNB->BEP20";
								$post['transaction_type']="SWAP";
								$this->bep20PostN($post,$chain);
	}
	
	function unstake_farm_with_no_reward($local, $chain="BSC") {
							//
							// This is rare.
							// Unstake Farm with NO REWARD.
							// There are 2 records.
							// tokenFrom is a syurp contract.
							// tokenTo is the wallet address.
							// bnbFrom is the wallet address.
							// bnbValue is 0.
							//
							$l['description']="Unstaking FARM - " . $local[0]['tokenSymbol'];
								$post=$this->postBasic($local[1],$chain);								
								$post['bnbFrom']=$local[1]['bnbFrom'];
								$post['bnbTo']=$local[1]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[1]['bnbValue'],18);	
								$post['fromAddress']=$local[0]['tokenFrom'];
								$post['fromTokenSymbol']=$local[0]['tokenSymbol'];				
								$post['fromTokenName']=$local[0]['tokenName'];
								$post['fromTokenDecimal']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);
								$post['fromValue']=$this->convertBigNumber($local[1]['bnbValue'],18);
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];	
								
								$post['toTokenSymbol']=$local[0]['tokenSymbol'];			
								$post['toTokenName']=$local[0]['tokenName'];
								$post['toTokenDecimal']=$local[0]['tokenDecimal'];
								$post['toAddress']=$this->address;
								$post['contractAddress']=$local[0]['contractAddress'];		
								
								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$post['toValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);												
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];
								$post['description']=$l['description'];
								$post['transaction_type']="US-FARM";
								$post['transaction_type']="UNSTAKE";
								$this->bep20PostN($post,$chain);								
	}
	
	function send_bep20_receive_bnb($local, $chain="BSC") {
							//
							// BEP20 swapped for BNB.
							// Three records.
							// One is an internal records.
							// bnbTo is the wallet address.
							// tokenFrom is wallet address.
							// bnbFrom 
							//
$l=array();
                                $this->t='Y';
								if ($chain=="BSC") $l['description']="Swapped " . $local[1]['tokenSymbol'] . " for BNB";
								if ($chain=="ETH") $l['description']="Swapped " . $local[1]['tokenSymbol'] . " for ETH";								
								$post=$this->postBasic($local[2],$chain);								
								$post['bnbFrom']=$local[2]['bnbFrom'];
								$post['bnbTo']=$local[2]['bnbTo'];
								$z=$this->convertBigNumber($local[0]['bnbValue'],18);
								$z2=$this->convertBigNumber($local[2]['bnbValue'],18);
								$z3=floatval($z2)-floatval($z);
								$post['bnbValue']=$this->convertBigNumber($z3,18);	
								$post['fromAddress']=$this->address;
								$post['fromTokenSymbol']=$local[1]['tokenSymbol'];				
								$post['fromTokenName']=$local[1]['tokenName'];
								$post['fromTokenDecimal']=$local[1]['tokenDecimal'];	
								$post['fromValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];	
								
								if ($chain=="BSC") {
									$post['toTokenSymbol']="BNB";				
									$post['toTokenName']="BNB";
									$post['toTokenDecimal']=18;	
								}
								if ($chain=="ETH") {
									$post['toTokenSymbol']="ETH";				
									$post['toTokenName']="ETH";
									$post['toTokenDecimal']=18;	
								}
								
								$post['toAddress']=$this->address;
								$post['contractAddress']=$local[1]['contractAddress'];
								
								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
												
								
								$post['toValue']=$this->convertBigNumber($local[0]['bnbValue'],18);		
								$post['toPrice']=$post['bnbPrice'];
								$post['toValueUSD']=number_format(floatval($post['toValue'])*floatval($post['toPrice']),4);	
								$post['toTimestamp']=$post['bnbTimestamp'];
								$post['description']=$l['description'];
								$post['transaction_type']="BEP20->BNB";
                                                                $post['seq']=1;
								$post['transaction_type']="SWAP";
								$this->bep20PostN($post,$chain);								
	}
	
	function swapped_bnb_for_bep20_token($local,$chain="BSC") {
		$l=array();
		
								if ($chain=="BSC") $l['description']="Swapped BNB for " . $local[1]['tokenSymbol'];
								if ($chain=="ETH") $l['description']="Swapped ETH for " . $local[1]['tokenSymbol'];								
								$post=$this->postBasic($local[2],$chain);
								$post['bnbFrom']=$local[2]['bnbFrom'];
								$post['bnbTo']=$local[2]['bnbTo'];
								$z=$this->convertBigNumber($local[0]['bnbValue'],18);
								$z2=$this->convertBigNumber($local[2]['bnbValue'],18);
								$z3=floatval($z2)-floatval($z);
								$post['bnbValue']=$this->convertBigNumber($z3,18);	
								$post['toAddress']=$this->address;
								$post['toTokenSymbol']=$local[1]['tokenSymbol'];				
								$post['toTokenName']=$local[1]['tokenName'];
								$post['toTokenDecimal']=$local[1]['tokenDecimal'];	
								$post['toValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								
								if ($chain=="BSC") {
									$post['fromTokenSymbol']="BNB";				
									$post['fromTokenName']="BNB";
									$post['fromTokenDecimal']=18;		
								}
								if ($chain=="ETH") {
									$post['fromTokenSymbol']="ETH";				
									$post['fromTokenName']="ETH";
									$post['fromTokenDecimal']=18;		
								}
								
								$post['fromAddress']=$this->address;
								$post['contractAddress']=$local[1]['contractAddress'];		
								
								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$post['fromValue']=$this->convertBigNumber($local[0]['bnbValue'],18);		
								$post['fromPrice']=$post['bnbPrice'];
								$post['fromValueUSD']=number_format(floatval($post['fromValue'])*floatval($post['fromPrice']),4);	
								$post['fromTimestamp']=$post['bnbTimestamp'];
								$post['description']=$l['description'];
								$post['transaction_type']="BNB->BEP20(2)";
								$post['transaction_type']="SWAP";
								$this->bep20PostN($post,$chain);	
		
	}
	
	function unstaked_bnb_and_bep20($local, $chain="BSC") {
			$l=array();
								if ($chain=="BSC") $l['description']="Unstaked BNB and " . $local[1]['tokenSymbol'];
								if ($chain=="ETH") $l['description']="Unstaked ETH and " . $local[1]['tokenSymbol'];								
								$post=$this->postBasic($local[0],$chain);
								$post['bnbFrom']=$local[0]['bnbFrom'];
								$post['bnbTo']=$local[0]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[0]['bnbValue'],18);	
								if (isset($local[1]['fromAddress'])) {
                                                                    $post['fromAddress']=$local[1]['fromAddress'];
                                                                } else {
                                                                    $post['fromAddress']="ERROR";
                                                                }
								$post['fromTokenSymbol']=$local[1]['tokenSymbol'];				
								$post['fromTokenName']=$local[1]['tokenName'];
								$post['fromTokenDecimal']=$local[1]['tokenDecimal'];
								$post['fromValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];	
								
								$post['toTokenSymbol']=$local[1]['tokenSymbol'];				
								$post['toTokenName']=$local[1]['tokenName'];
								$post['toTokenDecimal']=$local[1]['tokenDecimal'];	
								$post['toAddress']=$this->address;
								$post['contractAddress']=$local[1]['contractAddress'];	
								
								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$post['toValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);												
								$post['description']=$l['description'];
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								
								$post['transaction_type']="US-BNB-BEP20";
								$post['transaction_type']="UNSTAKE";
								$this->bep20PostN($post,$chain);				

								//-- Sequence 1 Receving BEP20
								$post=$this->postBasic($local[0],$chain);
								$post['seq']="1";
								$post['bnbFrom']=$local[0]['bnbFrom'];
								$post['bnbTo']=$local[0]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[0]['bnbValue'],18);	
								if (isset($local[1]['fromAddress'])) {
                                                                    $post['fromAddress']=$local[1]['fromAddress'];
                                                                } else {
                                                                    $post['fromAddress']="ERROR";
                                                                }
								if ($chain=="BSC")  {
									$post['fromTokenSymbol']="BNB";				
									$post['fromTokenName']="BNB";
									$post['fromTokenDecimal']=18;
								}
								if ($chain=="ETH")  {
									$post['fromTokenSymbol']="ETH";				
									$post['fromTokenName']="ETH";
									$post['fromTokenDecimal']=18;
								}								
								
								$post['fromValue']=$this->convertBigNumber($local[0]['bnbValue'],18);
								$post['fromPrice']=$post['bnbPrice'];								
								$post['fromValueUSD']=number_format(floatval($post['fromValue'])*floatval($post['fromPrice']),4);								
								$post['toTokenSymbol']="BNB";				
								$post['toTokenName']="BNB";
								$post['toTokenDecimal']=18;		
								$post['toAddress']=$this->address;
								$post['contractAddress']=$local[1]['contractAddress'];									
								
								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$post['toValue']=$this->convertBigNumber($local[0]['bnbValue'],18);	
								$post['toPrice']=$post['bnbPrice'];
								$post['toValueUSD']=number_format(floatval($post['toValue'])*floatval($post['toPrice']),4);	
								$post['toTimestamp']=$post['bnbTimestamp'];
								$post['description']=$l['description'];
								$post['transaction_type']="";
								$this->bep20PostN($post,$chain);				
	}
	
	function staked_goatx_first_time($local,$chain="BSC") {
								$l=array();
								
								$l['description']="Staked GOATX received GRAIN (LP)";
								$post=$this->postBasic($local[2],$chain);
								$post['bnbFrom']=$local[2]['bnbFrom'];
								$post['bnbTo']=$local[2]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[2]['bnbValue'],18);	
								$post['fromAddress']=$this->address;
								$post['fromTokenSymbol']="GOATX";				
								$post['fromTokenName']="GOATX";
								$post['fromTokenDecimal']=18;							
								$post['toTokenSymbol']="GRAIN";				
								$post['toTokenName']="GRAIN LP Token";
								$post['toTokenDecimal']=18;
								$post['toAddress']=$this->address;
								$post['contractAddress']=$local[1]['contractAddress'];
								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								if ($local[0]['tokenSymbol']="GOATX") {
									$post['fromValue']=$this->convertBigNumber($local[0]['tokenValue'],18);
									$post['toValue']=$this->convertBigNumber($local[1]['tokenValue'],18);												
								} else {
									$post['fromValue']=$this->convertBigNumber($local[1]['tokenValue'],18);
									$post['toValue']=$this->convertBigNumber($local[0]['tokenValue'],18);		
								}
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];	
								
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];										
								$post['description']=$l['description'];
								$post['transaction_type']="STAKE-GOATX";
								$post['transaction_type']="STAKE";
								$this->bep20PostN($post,$chain);			
	}
	
	function swap_bep20_for_bep20($local, $chain="BSC") {
		$l=array();
										//Token Swap BEP20 for BEP20.	
                                                                $sent="";
                                                                $received="";
                                                                $received_address="ERROR";
                                                                $received_value="0";
                                                                $received_decimal=18;
                                                                $received_name="";
                                                                $sent_address="";
                                                                $sent_value="0";
                                                                $sent_name="";
                                                                $contract_address="ERROR(1)";
                                                                $sent_decimal="0";
 
								if ($local[0]['tokenTo']==$this->address) {
										$received=$local[0]['tokenSymbol'];
										$received_address=$local[0]['tokenTo'];
										$received_value=$this->convertBigNumber($local[0]['tokenValue'],18);
										$received_decimal=$local[0]['tokenDecimal'];
										$received_name=$local[0]['tokenName'];
										$sent=$local[1]['tokenSymbol'];
										$sent_address=$local[1]['tokenFrom'];
										$sent_value=$this->convertBigNumber($local[1]['tokenValue'],18);
										$sent_decimal=$local[1]['tokenDecimal'];
										$sent_name=$local[1]['tokenName'];
										$contract_address=$local[1]['contractAddress'];
								}
								if ($local[1]['tokenTo']==$this->address) {
										$sent=$local[0]['tokenSymbol'];								
										$sent_address=$local[0]['tokenTo'];
										$sent_decimal=$local[0]['tokenDecimal'];
										$sent_name=$local[0]['tokenName'];
										$sent_value=$this->convertBigNumber($local[0]['tokenValue'],18);
										$received=$local[1]['tokenSymbol'];
										$received_address=$local[1]['tokenTo'];
										$received_decimal=$local[1]['tokenDecimal'];
										$received_name=$local[1]['tokenName'];
										$received_value=$this->convertBigNumber($local[1]['tokenValue'],18);	
										$contract_address=$local[0]['contractAddress'];										
								}
								$l['description']="Swapped " . $sent . " for " . $received;
								$post=$this->postBasic($local[2],$chain);
								$post['bnbFrom']=$local[2]['bnbFrom'];
								$post['bnbTo']=$local[2]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[2]['bnbValue'],18);
								$post['fromAddress']=$sent_address;
								$post['fromTokenSymbol']=$sent;				
								$post['fromTokenName']=$sent_name;
								$post['fromTokenDecimal']=$sent_decimal;	
								$post['fromValue']=$sent_value;	
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];									
								$post['toTokenSymbol']=$received;			
								$post['toTokenName']=$received_name;
								$post['toTokenDecimal']=$received_decimal;	
								$post['toValue']=$received_value;	
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];									
								$post['toAddress']=$this->address;
								$post['description']=$l['description'];
								$post['transaction_type']="BEP20->BEP20";
								$post['transaction_type']="SWAP";
								$post['contractAddress']=$contract_address;
								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$this->bep20PostN($post,$chain);
	}
	
	function lp_staking_with_bnb($local,$chain="BSC") {
	$l=array();
									$received="";
									$received_address="";
									$received_value="";
									$received_decimal="18";
									$received_name="";
										
									$staked="";
									$staked_address="";
									$staked_value="";
									$staked_decimal="18";
									$staked_name="";		
									$contract_address="";									
							if ($local[0]['tokenFrom']=="0x0000000000000000000000000000000000000000") {
									$received=$local[0]['tokenSymbol'];
									$received_address=$local[0]['tokenTo'];
									$received_value=$this->convertBigNumber($local[0]['tokenValue'],18);
									$received_decimal=$local[0]['tokenDecimal'];
									$received_name=$local[0]['tokenName'];
										
									$staked=$local[1]['tokenSymbol'];
									$staked_address=$local[1]['tokenTo'];
									$staked_value=$this->convertBigNumber($local[1]['tokenValue'],18);
									$staked_decimal=$local[1]['tokenDecimal'];
									$staked_name=$local[1]['tokenName'];		
									$contract_address=$local[1]['contractAddress'];									

							}
							if ($local[1]['tokenFrom']=="0x0000000000000000000000000000000000000000") {
									$received=$local[1]['tokenSymbol'];
									$received_address=$local[1]['tokenTo'];
									$received_value=$this->convertBigNumber($local[1]['tokenValue'],18);
									$received_decimal=$local[1]['tokenDecimal'];
									$received_name=$local[1]['tokenName'];
									
									$staked=$local[0]['tokenSymbol'];
									$staked_address=$local[0]['tokenTo'];
									$staked_value=$this->convertBigNumber($local[0]['tokenValue'],18);
									$staked_decimal=$local[0]['tokenDecimal'];
									$staked_name=$local[0]['tokenName'];	
									$contract_address=$local[0]['contractAddress'];	
							}							
								if ($chain=="BSC") $l['description']="Staked BNB and " . $staked . " received " . $received;
								if ($chain=="ETH") $l['description']="Staked ETH and " . $staked . " received " . $received;								
								
								$post=$this->postBasic($local[2],$chain);
								$post['bnbFrom']=$local[2]['bnbFrom'];
								$post['bnbTo']=$local[2]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[2]['bnbValue'],18);
								$post['fromAddress']=$this->address;
								$post['fromTokenSymbol']=$staked;				
								$post['fromTokenName']=$staked_name;
								$post['fromTokenDecimal']=$staked_decimal;	
								$post['fromValue']=$staked_value;	
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];									
								$post['toTokenSymbol']=$received;			
								$post['toTokenName']=$received_name;
								$post['toTokenDecimal']=$received_decimal;	
								$post['toValue']=$received_value;	
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];									
								$post['toAddress']=$received_address;
								$post['contractAddress']=$contract_address;

								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$post['description']=$l['description'];
								$post['transaction_type']="S-BNB-BEP20";
								$post['transaction_type']="STAKE";
								$this->bep20PostN($post,$chain);		
	}
	
	function unstaked_goatx($local,$chain="BSC") {
			$l=array();
							$l['description']="Unstaked GOATX";
							if($local[0]['tokenTo']=="0x0000000000000000000000000000000000000000") {
								$post=$this->postBasic($local[3],$chain);
								$post['bnbFrom']=$local[3]['bnbFrom'];
								$post['bnbTo']=$local[3]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
								$post['fromAddress']=$local[1]['tokenFrom'];	
								$post['fromTokenSymbol']=$local[1]['tokenSymbol'];				
								$post['fromTokenName']=$local[1]['tokenName'];		
								$post['fromTokenDecimal']=$local[1]['tokenDecimal'];			
								$post['fromValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);													
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];		
								
								$post['toTokenSymbol']=$local[0]['tokenSymbol'];			
								$post['toTokenName']=$local[0]['tokenName'];	
								$post['toTokenDecimal']=$local[0]['tokenDecimal'];	
								$post['toValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);		
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								$post['contractAddress']=$local[1]['contractAddress'];
								$post['description']=$l['description'];
								$post['transaction_type']="US-GOATX-1";
								$post['transaction_type']="UNSTAKE";
								$this->bep20PostN($post,$chain);		

								$post=$this->postBasic($local[3],$chain);
								$post['bnbFrom']=$local[3]['bnbFrom'];
								$post['bnbTo']=$local[3]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
								$post['fromAddress']=$local[2]['tokenFrom'];	
								$post['fromTokenSymbol']=$local[2]['tokenSymbol'];				
								$post['fromTokenName']=$local[2]['tokenName'];		
								$post['fromTokenDecimal']=$local[2]['tokenDecimal'];			
								$post['fromValue']=$this->convertBigNumber($local[2]['tokenValue'],$local[2]['tokenDecimal']);													
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];		
								
								$post['toTokenSymbol']=$local[0]['tokenSymbol'];			
								$post['toTokenName']=$local[0]['tokenName'];	
								$post['toTokenDecimal']=$local[0]['tokenDecimal'];	
								$post['toValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);		
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								$post['contractAddress']=$local[2]['contractAddress'];
								
								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$post['description']=$l['description'];
								$post['transaction_type']="US-GOATX-2";
								$post['transaction_type']="UNSTAKE";								
								$this->bep20PostN($post,$chain);
							}
							
							if($local[1]['tokenTo']=="0x0000000000000000000000000000000000000000") {
								$post=$this->postBasic($local[3],$chain);
								$post['bnbFrom']=$local[3]['bnbFrom'];
								$post['bnbTo']=$local[3]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
								$post['fromAddress']=$local[0]['tokenFrom'];	
								$post['fromTokenSymbol']=$local[0]['tokenSymbol'];				
								$post['fromTokenName']=$local[0]['tokenName'];		
								$post['fromTokenDecimal']=$local[0]['tokenDecimal'];			
								$post['fromValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);													
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];		
								
								$post['toTokenSymbol']=$local[1]['tokenSymbol'];			
								$post['toTokenName']=$local[1]['tokenName'];	
								$post['toTokenDecimal']=$local[1]['tokenDecimal'];	
								$post['toAddress']=$local[1]['tokenTo'];
								$post['toValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);		
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];									
								$post['contractAddress']=$local[0]['contractAddress'];
								
								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$post['description']=$l['description'];
								$post['transaction_type']="US-GOATX-3";
								$post['transaction_type']="UNSTAKE";								
								$this->bep20PostN($post,$chain);

								$post=$this->postBasic($local[3],$chain);
								$post['bnbFrom']=$local[3]['bnbFrom'];
								$post['bnbTo']=$local[3]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
								$post['fromAddress']=$local[2]['tokenFrom'];	
								$post['fromTokenSymbol']=$local[2]['tokenSymbol'];				
								$post['fromTokenName']=$local[2]['tokenName'];		
								$post['fromTokenDecimal']=$local[2]['tokenDecimal'];			
								$post['fromValue']=$this->convertBigNumber($local[2]['tokenValue'],$local[2]['tokenDecimal']);													
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];		
								
								$post['toTokenSymbol']=$local[1]['tokenSymbol'];			
								$post['toTokenName']=$local[1]['tokenName'];	
								$post['toTokenDecimal']=$local[1]['tokenDecimal'];	
								$post['toAddress']=$local[1]['tokenTo'];								
								$post['toValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);		
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								
								$post['contractAddress']=$local[2]['contractAddress'];

								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$post['description']=$l['description'];
								$post['transaction_type']="US-GOATX-4";		
								$post['transaction_type']="UNSTAKE";								
								$this->bep20PostN($post,$chain);
							}	

							if($local[2]['tokenTo']=="0x0000000000000000000000000000000000000000") {
								$post=$this->postBasic($local[3],$chain);
								$post['bnbFrom']=$local[3]['bnbFrom'];
								$post['bnbTo']=$local[3]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
								$post['fromAddress']=$local[0]['tokenFrom'];	
								$post['fromTokenSymbol']=$local[0]['tokenSymbol'];				
								$post['fromTokenName']=$local[0]['tokenName'];		
								$post['fromTokenDecimal']=$local[0]['tokenDecimal'];														
								$post['fromValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);													
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];		
								
								$post['toTokenSymbol']=$local[2]['tokenSymbol'];			
								$post['toTokenName']=$local[2]['tokenName'];	
								$post['toTokenDecimal']=$local[2]['tokenDecimal'];	
								$post['toValue']=$this->convertBigNumber($local[2]['tokenValue'],$local[2]['tokenDecimal']);		
								$post['toAddress']=$local[2]['tokenTo'];
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								$post['contractAddress']=$local[0]['contractAddress'];
								
								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$post['description']=$l['description'];
								$post['transaction_type']="US-GOATX-5";
								$post['transaction_type']="UNSTAKE";
								$this->bep20PostN($post,$chain);

								$post=$this->postBasic($local[3],$chain);
								$post['seq']="1";
								$post['bnbFrom']=$local[3]['bnbFrom'];
								$post['bnbTo']=$local[3]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
								$post['fromAddress']=$local[1]['tokenFrom'];
								$post['fromTokenSymbol']=$local[1]['tokenSymbol'];				
								$post['fromTokenName']=$local[1]['tokenName'];		
								$post['fromTokenDecimal']=$local[1]['tokenDecimal'];			
								$post['fromValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);													
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];		
								
								$post['toTokenSymbol']=$local[2]['tokenSymbol'];			
								$post['toTokenName']=$local[2]['tokenName'];	
								$post['toTokenDecimal']=$local[2]['tokenDecimal'];	
								$post['toAddress']=$local[2]['tokenTo'];								
								$post['toValue']=$this->convertBigNumber($local[2]['tokenValue'],$local[2]['tokenDecimal']);		
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								$post['contractAddress']=$local[1]['contractAddress'];

								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$post['description']=$l['description'];
								$post['transaction_type']="US-GOATX-6";
								$post['transaction_type']="UNSTAKE";
								$this->bep20PostN($post,$chain);
							}
	}
	
	function staked_bep20_and_bep20($local, $chain="BSC") {
							$l=array();
							$l['description']="";
							if($local[0]['tokenFrom']=="0x0000000000000000000000000000000000000000") {
								$l['description']="Staked " . $local[1]['tokenSymbol'] . " and " . $local[2]['tokenSymbol'] . " received " . $local[0]['tokenSymbol'];	
								$post=$this->postBasic($local[3],$chain);
								$post['bnbFrom']=$local[3]['bnbFrom'];
								$post['bnbTo']=$local[3]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
								$post['fromAddress']=$this->address;
								$post['fromTokenSymbol']=$local[1]['tokenSymbol'];				
								$post['fromTokenName']=$local[1]['tokenName'];		
								$post['fromTokenDecimal']=$local[1]['tokenDecimal'];			
								$post['fromValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);													
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];		
								
								$post['toTokenSymbol']=$local[0]['tokenSymbol'];			
								$post['toTokenName']=$local[0]['tokenName'];	
								$post['toTokenDecimal']=$local[0]['tokenDecimal'];
								$post['toValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);		
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								
								$post['contractAddress']=$local[1]['contractAddress'];

								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$post['toAddress']=$local[0]['tokenTo'];
								$post['description']=$l['description'];
								$post['transaction_type']="S-BEP20-1";
								$post['transaction_type']="STAKE";								
								$this->bep20PostN($post,$chain);

								$post=$this->postBasic($local[3],$chain);
								$post['seq']="1";
								$post['bnbFrom']=$local[3]['bnbFrom'];
								$post['bnbTo']=$local[3]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
								$post['fromAddress']=$this->address;
								$post['fromTokenSymbol']=$local[2]['tokenSymbol'];				
								$post['fromTokenName']=$local[2]['tokenName'];		
								$post['fromTokenDecimal']=$local[2]['tokenDecimal'];			
								$post['fromValue']=$this->convertBigNumber($local[2]['tokenValue'],$local[2]['tokenDecimal']);													
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];		
								
								$post['toTokenSymbol']=$local[0]['tokenSymbol'];			
								$post['toTokenName']=$local[0]['tokenName'];	
								$post['toTokenDecimal']=$local[0]['tokenDecimal'];	
								$post['toValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);		
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								
								$post['toAddress']=$local[0]['tokenTo'];
								$post['contractAddress']=$local[1]['contractAddress'];
								
								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];

								
								$post['description']=$l['description'];
								$post['transaction_type']="S-BEP20-2";
								$post['transaction_type']="STAKE";
								$this->bep20PostN($post,$chain);								
								
							}
							if($local[1]['tokenFrom']=="0x0000000000000000000000000000000000000000") {
								$l['description']="Staked " . $local[0]['tokenSymbol'] . " and " . $local[2]['tokenSymbol'] . " received " . $local[1]['tokenSymbol'];	
								$post=$this->postBasic($local[3],$chain);
								$post['bnbFrom']=$local[3]['bnbFrom'];
								$post['bnbTo']=$local[3]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
								$post['fromAddress']=$this->address;
								$post['fromTokenSymbol']=$local[0]['tokenSymbol'];				
								$post['fromTokenName']=$local[0]['tokenName'];		
								$post['fromTokenDecimal']=$local[0]['tokenDecimal'];			
								$post['fromValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);													
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];		
								
								$post['toTokenSymbol']=$local[1]['tokenSymbol'];			
								$post['toTokenName']=$local[1]['tokenName'];	
								$post['toTokenDecimal']=$local[1]['tokenDecimal'];	
								$post['toValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);		
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								
								$post['contractAddress']=$local[0]['contractAddress'];
								$post['toAddress']=$local[1]['tokenTo'];
								$post['description']=$l['description'];
								$post['transaction_type']="S-BEP20-3";
								$post['transaction_type']="STAKE";
								$this->bep20PostN($post,$chain);

								$post=$this->postBasic($local[3],$chain);
								$post['seq']="1";
								$post['bnbFrom']=$local[3]['bnbFrom'];
								$post['bnbTo']=$local[3]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
								$post['fromAddress']=$this->address;
								$post['fromTokenSymbol']=$local[2]['tokenSymbol'];				
								$post['fromTokenName']=$local[2]['tokenName'];		
								$post['fromTokenDecimal']=$local[2]['tokenDecimal'];			
								$post['fromValue']=$this->convertBigNumber($local[2]['tokenValue'],$local[2]['tokenDecimal']);													
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];		
								
								$post['toTokenSymbol']=$local[1]['tokenSymbol'];			
								$post['toTokenName']=$local[1]['tokenName'];	
								$post['toTokenDecimal']=$local[1]['tokenDecimal'];	
								$post['toValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);		
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								
								$post['toAddress']=$local[1]['tokenTo'];
								$post['contractAddress']=$local[2]['contractAddress'];

								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$post['description']=$l['description'];
								$post['transaction_type']="S-BEP20-4";
								$post['transaction_type']="STAKE";
								$this->bep20PostN($post,$chain);										
							}								
							if($local[2]['tokenFrom']=="0x0000000000000000000000000000000000000000") {
								$l['description']="Staked " . $local[0]['tokenSymbol'] . " and " . $local[1]['tokenSymbol'] . " received " . $local[2]['tokenSymbol'];	
								$post=$this->postBasic($local[3],$chain);
								$post['bnbFrom']=$local[3]['bnbFrom'];
								$post['bnbTo']=$local[3]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
								$post['fromAddress']=$this->address;
								$post['fromTokenSymbol']=$local[0]['tokenSymbol'];				
								$post['fromTokenName']=$local[0]['tokenName'];		
								$post['fromTokenDecimal']=$local[0]['tokenDecimal'];			
								$post['fromValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);													
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];		
								
								$post['toTokenSymbol']=$local[2]['tokenSymbol'];			
								$post['toTokenName']=$local[2]['tokenName'];	
								$post['toTokenDecimal']=$local[2]['tokenDecimal'];	
								$post['toValue']=$this->convertBigNumber($local[2]['tokenValue'],$local[2]['tokenDecimal']);		
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								
								$post['contractAddress']=$local[0]['contractAddress'];
								
								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];

								
								$post['toAddress']=$local[1]['tokenTo'];
								$post['description']=$l['description'];
								$post['transaction_type']="S-BEP20-5";
								$post['transaction_type']="STAKE";
								$this->bep20PostN($post,$chain);

								$post=$this->postBasic($local[3],$chain);
								$post['seq']="1";
								$post['bnbFrom']=$local[3]['bnbFrom'];
								$post['bnbTo']=$local[3]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
								$post['fromAddress']=$this->address;
								$post['fromTokenSymbol']=$local[1]['tokenSymbol'];				
								$post['fromTokenName']=$local[1]['tokenName'];		
								$post['fromTokenDecimal']=$local[1]['tokenDecimal'];			
								$post['fromValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);													
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];		
								
								$post['toTokenSymbol']=$local[2]['tokenSymbol'];			
								$post['toTokenName']=$local[2]['tokenName'];	
								$post['toTokenDecimal']=$local[2]['tokenDecimal'];	
								$post['toValue']=$this->convertBigNumber($local[2]['tokenValue'],$local[2]['tokenDecimal']);		
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								$post['toAddress']=$local[2]['tokenTo'];
								$post['contractAddress']=$local[1]['contractAddress'];
								
								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$post['description']=$l['description'];
								$post['transaction_type']="S-BEP20-6";
								$post['transaction_type']="STAKE";
								$this->bep20PostN($post,$chain);	
							}			
					return $l['description'];
	}
	
	function unstaked_bep20_and_bep20($local,$chain="BSC") {
			$l=array();
			$l['description']="";
											    if ($local[0]['tokenFrom']==$this->address) {
									
											$sent=$local[0]['tokenSymbol'];
											$r1=$local[1]['tokenSymbol'];
											$r2=$local[2]['tokenSymbol'];		
											$l['description']="Unstaked " . $r1 . " and " . $r2 . " sent " . $sent;								
											
											$post=$this->postBasic($local[3],$chain);
											$post['bnbFrom']=$local[3]['bnbFrom'];
											$post['bnbTo']=$local[3]['bnbTo'];
											$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
											$post['fromAddress']=$local[0]['tokenFrom'];
											$post['fromTokenSymbol']=$local[0]['tokenSymbol'];				
											$post['fromTokenName']=$local[0]['tokenName'];		
											$post['fromTokenDecimal']=$local[0]['tokenDecimal'];			
											$post['fromValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);													
											$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
											$post['fromPrice']=$zz['price'];
											$post['fromTimestamp']=$zz['timestamp'];
											$post['fromValueUSD']=$zz['total'];		
								
											$post['toTokenSymbol']=$local[1]['tokenSymbol'];			
											$post['toTokenName']=$local[1]['tokenName'];	
											$post['toTokenDecimal']=$local[1]['tokenDecimal'];	
											$post['toValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);		
											$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
											$post['toPrice']=$zz['price'];
											$post['toTimestamp']=$zz['timestamp'];
											$post['toValueUSD']=$zz['total'];	
											$post['contractAddress']=$local[1]['contractAddress'];
											
											$array=$this->get_bep20_contract($post['contractAddress'],$chain);
											$post['contractName']=$array['CONTRACT_NAME'];
											$post['contractType']=$array['CONTRACT_TYPE'];
											$post['contractSymbol']=$array['SYMBOL'];
								
											$post['toAddress']=$local[1]['tokenTo'];
											$post['description']=$l['description'];
											$post['transaction_type']="US-BEP20-1";
											$post['transaction_type']="UNSTAKE";
											
											$this->bep20PostN($post,$chain);

											$post=$this->postBasic($local[3],$chain);
											$post['bnbFrom']=$local[3]['bnbFrom'];
											$post['bnbTo']=$local[3]['bnbTo'];
											$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
											$post['fromAddress']=$local[0]['tokenFrom'];
											$post['fromTokenSymbol']=$local[0]['tokenSymbol'];				
											$post['fromTokenName']=$local[0]['tokenName'];		
											$post['fromTokenDecimal']=$local[0]['tokenDecimal'];			
											$post['fromValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);													
											$post['toTokenSymbol']=$local[2]['tokenSymbol'];			
											$post['toTokenName']=$local[2]['tokenName'];	
											$post['toTokenDecimal']=$local[2]['tokenDecimal'];	
											$post['toValue']=$this->convertBigNumber($local[2]['tokenValue'],$local[2]['tokenDecimal']);		
											$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
											$post['toPrice']=$zz['price'];
											$post['toTimestamp']=$zz['timestamp'];
											$post['toValueUSD']=$zz['total'];	
											$post['contractAddress']=$local[2]['contractAddress'];
											
											$array=$this->get_bep20_contract($post['contractAddress'],$chain);
											$post['contractName']=$array['CONTRACT_NAME'];
											$post['contractType']=$array['CONTRACT_TYPE'];
											$post['contractSymbol']=$array['SYMBOL'];
								
											$post['toAddress']=$local[2]['tokenTo'];
											$post['description']=$l['description'];
											$post['transaction_type']="US-BEP20-2";
											$post['transaction_type']="UNSTAKE";
											$this->bep20PostN($post,$chain);
											
									}
								    if ($local[1]['tokenFrom']==$this->address) {
											$sent=$local[1]['tokenSymbol'];
											$r1=$local[0]['tokenSymbol'];
											$r2=$local[2]['tokenSymbol'];											
											$l['description']="Unstaked " . $r1 . " and " . $r2 . " sent " . $sent;	

											$post=$this->postBasic($local[3],$chain);
											$post['bnbFrom']=$local[3]['bnbFrom'];
											$post['bnbTo']=$local[3]['bnbTo'];
											$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
											$post['fromAddress']=$local[1]['tokenFrom'];
											$post['fromTokenSymbol']=$local[1]['tokenSymbol'];				
											$post['fromTokenName']=$local[1]['tokenName'];		
											$post['fromTokenDecimal']=$local[1]['tokenDecimal'];			
											$post['fromValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);													
											$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
											$post['fromPrice']=$zz['price'];
											$post['fromTimestamp']=$zz['timestamp'];
											$post['fromValueUSD']=$zz['total'];		
								
											$post['toTokenSymbol']=$local[0]['tokenSymbol'];			
											$post['toTokenName']=$local[0]['tokenName'];	
											$post['toTokenDecimal']=$local[0]['tokenDecimal'];	
											$post['toValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);		
											$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
											$post['toPrice']=$zz['price'];
											$post['toTimestamp']=$zz['timestamp'];
											$post['toValueUSD']=$zz['total'];	
											$post['contractAddress']=$local[0]['contractAddress'];
											
											$array=$this->get_bep20_contract($post['contractAddress'],$chain);
											$post['contractName']=$array['CONTRACT_NAME'];
											$post['contractType']=$array['CONTRACT_TYPE'];
											$post['contractSymbol']=$array['SYMBOL'];
								
											$post['toAddress']=$local[0]['tokenTo'];
											$post['description']=$l['description'];
											$post['transaction_type']="US-BEP20-3";
											$post['transaction_type']="UNSTAKE";
											$this->bep20PostN($post,$chain);

											$post=$this->postBasic($local[3],$chain);
											$post['bnbFrom']=$local[3]['bnbFrom'];
											$post['bnbTo']=$local[3]['bnbTo'];
											$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
											$post['fromAddress']=$local[1]['tokenFrom'];
											$post['fromTokenSymbol']=$local[1]['tokenSymbol'];				
											$post['fromTokenName']=$local[1]['tokenName'];		
											$post['fromTokenDecimal']=$local[1]['tokenDecimal'];			
											$post['fromValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);													
											$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
											$post['fromPrice']=$zz['price'];
											$post['fromTimestamp']=$zz['timestamp'];
											$post['fromValueUSD']=$zz['total'];		
								
											$post['toTokenSymbol']=$local[2]['tokenSymbol'];			
											$post['toTokenName']=$local[2]['tokenName'];	
											$post['toTokenDecimal']=$local[2]['tokenDecimal'];	
											$post['toValue']=$this->convertBigNumber($local[2]['tokenValue'],$local[2]['tokenDecimal']);		
											$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
											$post['toPrice']=$zz['price'];
											$post['toTimestamp']=$zz['timestamp'];
											$post['toValueUSD']=$zz['total'];	
											$post['contractAddress']=$local[2]['contractAddress'];
											
											$array=$this->get_bep20_contract($post['contractAddress'],$chain);
											$post['contractName']=$array['CONTRACT_NAME'];
											$post['contractType']=$array['CONTRACT_TYPE'];
											$post['contractSymbol']=$array['SYMBOL'];
								
											$post['toAddress']=$local[2]['tokenTo'];
											$post['description']=$l['description'];
											$post['transaction_type']="US-BEP20-4";
											$post['transaction_type']="UNSTAKE";
											$this->bep20PostN($post,$chain);
											
									}
								    if ($local[2]['tokenFrom']==$this->address) {
											$sent=$local[2]['tokenSymbol'];
											$r1=$local[0]['tokenSymbol'];
											$r2=$local[1]['tokenSymbol'];											
											$l['description']="Unstaked " . $r1 . " and " . $r2 . " sent " . $sent;		

											$post=$this->postBasic($local[3],$chain);
											$post['bnbFrom']=$local[3]['bnbFrom'];
											$post['bnbTo']=$local[3]['bnbTo'];
											$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
											$post['fromAddress']=$local[2]['tokenFrom'];
											$post['fromTokenSymbol']=$local[2]['tokenSymbol'];				
											$post['fromTokenName']=$local[2]['tokenName'];		
											$post['fromTokenDecimal']=$local[2]['tokenDecimal'];			
											$post['fromValue']=$this->convertBigNumber($local[2]['tokenValue'],$local[2]['tokenDecimal']);													
											$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
											$post['fromPrice']=$zz['price'];
											$post['fromTimestamp']=$zz['timestamp'];
											$post['fromValueUSD']=$zz['total'];		
								
											$post['toTokenSymbol']=$local[0]['tokenSymbol'];			
											$post['toTokenName']=$local[0]['tokenName'];	
											$post['toTokenDecimal']=$local[0]['tokenDecimal'];	
											$post['toValue']=$this->convertBigNumber($local[0]['tokenValue'],$local[0]['tokenDecimal']);		
											$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
											$post['toPrice']=$zz['price'];
											$post['toTimestamp']=$zz['timestamp'];
											$post['toValueUSD']=$zz['total'];	
											$post['contractAddress']=$local[0]['contractAddress'];
											
											$array=$this->get_bep20_contract($post['contractAddress'],$chain);
											$post['contractName']=$array['CONTRACT_NAME'];
											$post['contractType']=$array['CONTRACT_TYPE'];
											$post['contractSymbol']=$array['SYMBOL'];
								
											$post['toAddress']=$local[0]['tokenTo'];
											$post['description']=$l['description'];
											$post['transaction_type']="US-BEP20-5";
											$post['transaction_type']="UNSTAKE";
											$this->bep20PostN($post,$chain);

											$post=$this->postBasic($local[3],$chain);
											$post['bnbFrom']=$local[3]['bnbFrom'];
											$post['bnbTo']=$local[3]['bnbTo'];
											$post['bnbValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
											$post['fromAddress']=$local[2]['tokenFrom'];
											$post['fromTokenSymbol']=$local[2]['tokenSymbol'];				
											$post['fromTokenName']=$local[2]['tokenName'];		
											$post['fromTokenDecimal']=$local[2]['tokenDecimal'];			
											$post['fromValue']=$this->convertBigNumber($local[2]['tokenValue'],$local[2]['tokenDecimal']);													
											$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
											$post['fromPrice']=$zz['price'];
											$post['fromTimestamp']=$zz['timestamp'];
											$post['fromValueUSD']=$zz['total'];		
								
											$post['toTokenSymbol']=$local[1]['tokenSymbol'];			
											$post['toTokenName']=$local[1]['tokenName'];	
											$post['toTokenDecimal']=$local[1]['tokenDecimal'];	
											$post['toValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);		
											$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
											$post['toPrice']=$zz['price'];
											$post['toTimestamp']=$zz['timestamp'];
											$post['toValueUSD']=$zz['total'];	
											$post['contractAddress']=$local[1]['contractAddress'];
											
											$array=$this->get_bep20_contract($post['contractAddress'],$chain);
											$post['contractName']=$array['CONTRACT_NAME'];
											$post['contractType']=$array['CONTRACT_TYPE'];
											$post['contractSymbol']=$array['SYMBOL'];
								
											$post['toAddress']=$local[1]['tokenTo'];
											$post['description']=$l['description'];
											$post['transaction_type']="US-BEP20-6";
											$post['transaction_type']="UNSTAKE";
											$this->bep20PostN($post,$chain);											
									}		
		
	}

    function unstaked_bnb_and_bep20_2($local,$chain="BSC") {
		$l=array();
		$l['description']="";
										
								if ($local[0]['bnbValue']!="0"&&$local[3]['bnbValue']=="0"&&$local[0]['bnbTo']==$this->address) {
								if ($local[1]['tokenFrom']==$this->address) {
								if ($chain=="BSC") $l['description']="Unstaked BNB and " . $local[2]['tokenSymbol'] . " sent " . $local[1]['tokenSymbol'];	
								if ($chain=="ETH") $l['description']="Unstaked ETH and " . $local[2]['tokenSymbol'] . " sent " . $local[1]['tokenSymbol'];	
								$post=$this->postBasic($local[0],$chain);
								$post['bnbFrom']=$local[0]['bnbFrom'];
								$post['bnbTo']=$local[0]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[0]['bnbValue'],18);	
								if (isset($local[1]['fromAddress'])) {
                                                                     $post['fromAddress']=$local[1]['fromAddress'];
                                                                } else {
                                                                     $post['fromAddress']="ERROR";
                                                                }
								$post['fromTokenSymbol']=$local[1]['tokenSymbol'];				
								$post['fromTokenName']=$local[1]['tokenName'];
								$post['fromTokenDecimal']=$local[1]['tokenDecimal'];
								$post['fromValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];		
								
								$post['toTokenSymbol']=$local[1]['tokenSymbol'];				
								$post['toTokenName']=$local[1]['tokenName'];
								$post['toTokenDecimal']=$local[1]['tokenDecimal'];	
								$post['toAddress']=$this->address;
								$post['contractAddress']=$local[1]['contractAddress'];

								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$post['toValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);												
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								$post['description']=$l['description'];
								$post['transaction_type']="US2-BNB-BEP20";
								$post['transaction_type']="UNSTAKE";
								$this->bep20PostN($post,$chain);				

								//-- Sequence 1 Receving BNB
								$post=$this->postBasic($local[0],$chain);
								$post['seq']="1";
								$post['bnbFrom']=$local[0]['bnbFrom'];
								$post['bnbTo']=$local[0]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[0]['bnbValue'],18);	
								if (isset($local[0]['fromAddress'])) {
                                                                       $post['fromAddress']=$local[1]['fromAddress'];
                                                                } else {
                                                                       $post['fromAddress']="ERROR";
                                                                }
								$post['fromTokenSymbol']=$local[1]['tokenSymbol'];				
								$post['fromTokenName']=$local[1]['tokenName'];	
								$post['fromTokenDecimal']=18;
								$post['fromValue']=$this->convertBigNumber($local[0]['bnbValue'],18);
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];		
								
								$post['toTokenSymbol']="BNB";				
								$post['toTokenName']="BNB";
								$post['toTokenDecimal']=18;		
								$post['toAddress']=$this->address;
								$post['contractAddress']=$local[1]['contractAddress'];									
								$post['toValue']=$this->convertBigNumber($local[0]['bnbValue'],18);						

								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								$post['description']=$l['description'];
								$post['transaction_type']="US2-BNB-BEP20";
								$post['transaction_type']="UNSTAKE";
								$this->bep20PostN($post,$chain);	
								} else {
									if ($chain=="BSC") $l['description']="Unstaked BNB and " . $local[1]['tokenSymbol'] . " sent " . $local[2]['tokenSymbol'];
									if ($chain=="ETH") $l['description']="Unstaked ETH and " . $local[1]['tokenSymbol'] . " sent " . $local[2]['tokenSymbol'];									
									$post=$this->postBasic($local[0],$chain);
									$post['bnbFrom']=$local[0]['bnbFrom'];
									$post['bnbTo']=$local[0]['bnbTo'];
									$post['bnbValue']=$this->convertBigNumber($local[0]['bnbValue'],18);	
									if (isset($local[2]['fromAddress'])) {
                                                                              $post['fromAddress']=$local[2]['fromAddress'];
                                                                        } else {
                                                                              $post['fromAddress']="ERROR";
                                                                        }
									$post['fromTokenSymbol']=$local[2]['tokenSymbol'];				
									$post['fromTokenName']=$local[2]['tokenName'];
									$post['fromTokenDecimal']=$local[2]['tokenDecimal'];
									$post['fromValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);
									$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
									$post['fromPrice']=$zz['price'];
									$post['fromTimestamp']=$zz['timestamp'];
									$post['fromValueUSD']=$zz['total'];		
								
									$post['toTokenSymbol']=$local[1]['tokenSymbol'];				
									$post['toTokenName']=$local[1]['tokenName'];
									$post['toTokenDecimal']=$local[1]['tokenDecimal'];	
									$post['toAddress']=$this->address;
									$post['contractAddress']=$local[1]['contractAddress'];	
									
									$array=$this->get_bep20_contract($post['contractAddress'],$chain);
									$post['contractName']=$array['CONTRACT_NAME'];
									$post['contractType']=$array['CONTRACT_TYPE'];
									$post['contractSymbol']=$array['SYMBOL'];
								
									$post['toValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);												
									$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
									$post['toPrice']=$zz['price'];
									$post['toTimestamp']=$zz['timestamp'];
									$post['toValueUSD']=$zz['total'];	
									$post['description']=$l['description'];
									$post['transaction_type']="US2-BNB-BEP20";
									$post['transaction_type']="UNSTAKE";
									$this->bep20PostN($post,$chain);				

								//-- Sequence 1 Receving BNB
								$post=$this->postBasic($local[0],$chain);
								$post['seq']="1";
								$post['bnbFrom']=$local[0]['bnbFrom'];
								$post['bnbTo']=$local[0]['bnbTo'];
								$post['bnbValue']=$this->convertBigNumber($local[0]['bnbValue'],18);	
								if (isset($local[2]['fromAddress']))  {
                                                                     $post['fromAddress']=$local[2]['fromAddress'];
                                                                } else {
                                                                     $post['fromAddress']="ERROR";
                                                                }
								$post['fromTokenSymbol']=$local[2]['tokenSymbol'];				
								$post['fromTokenName']=$local[2]['tokenName'];	
								$post['fromTokenDecimal']=18;
								$post['fromValue']=$this->convertBigNumber($local[0]['bnbValue'],18);
								$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
								$post['fromPrice']=$zz['price'];
								$post['fromTimestamp']=$zz['timestamp'];
								$post['fromValueUSD']=$zz['total'];		
								
								if ($chain=="BSC") {
								$post['toTokenSymbol']="BNB";				
								$post['toTokenName']="BNB";
								$post['toTokenDecimal']=18;		
								}
								if ($chain=="ETH") {
								$post['toTokenSymbol']="ETH";				
								$post['toTokenName']="ETH";
								$post['toTokenDecimal']=18;		
								}
								
								$post['toAddress']=$this->address;
								$post['contractAddress']=$local[1]['contractAddress'];									
								
								$array=$this->get_bep20_contract($post['contractAddress'],$chain);
								$post['contractName']=$array['CONTRACT_NAME'];
								$post['contractType']=$array['CONTRACT_TYPE'];
								$post['contractSymbol']=$array['SYMBOL'];
								
								$post['toValue']=$this->convertBigNumber($local[0]['bnbValue'],18);		
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
								$post['description']=$l['description'];
								$post['transaction_type']="US2-BNB-BEP20";
								$post['description']=$l['description'];
								$post['transaction_type']="US2-BNB-BEP20";
								$post['transaction_type']="UNSTAKE";
								$this->bep20PostN($post,$chain);
								}
								} 
	}
	
	function staked_bnb_and_bep20($local,$chain="BSC") {
		$l=array();
		$l['description']="";
		if ($local[1]['tokenTo']==$this->address) {
				if ($chain=="BSC") $l['description']="Staked BNB and " . $local[2]['tokenSymbol'] . " received " . $local[1]['tokenSymbol'];	
				if ($chain=="ETH") $l['description']="Staked ETH and " . $local[2]['tokenSymbol'] . " received " . $local[1]['tokenSymbol'];					
				$post=$this->postBasic($local[0],$chain);
									$post['bnbFrom']=$local[0]['bnbFrom'];
									$post['bnbTo']=$local[0]['bnbTo'];
									$post['bnbValue']=$this->convertBigNumber($local[0]['bnbValue'],18);	
									$post['fromAddress']=$this->address;
									$post['fromTokenSymbol']=$local[2]['tokenSymbol'];				
									$post['fromTokenName']=$local[2]['tokenName'];
									$post['fromTokenDecimal']=$local[2]['tokenDecimal'];
									$post['fromValue']=$this->convertBigNumber($local[2]['tokenValue'],$local[2]['tokenDecimal']);
									$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
									$post['fromPrice']=$zz['price'];
									$post['fromTimestamp']=$zz['timestamp'];
									$post['fromValueUSD']=$zz['total'];											
									$post['toTokenSymbol']=$local[1]['tokenSymbol'];				
									$post['toTokenName']=$local[1]['tokenName'];
									$post['toTokenDecimal']=$local[1]['tokenDecimal'];	
									$post['toAddress']=$this->address;
									$post['contractAddress']=$local[1]['contractAddress'];	

									$array=$this->get_bep20_contract($post['contractAddress'],$chain);
									$post['contractName']=$array['CONTRACT_NAME'];
									$post['contractType']=$array['CONTRACT_TYPE'];
									$post['contractSymbol']=$array['SYMBOL'];
								
									$post['toValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);												
									$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
									$post['toPrice']=$zz['price'];
									$post['toTimestamp']=$zz['timestamp'];
									$post['toValueUSD']=$zz['total'];	
									$post['description']=$l['description'];
									$post['transaction_type']="S2-BNB-BEP20";
									$post['transaction_type']="STAKE";
									$this->bep20PostN($post,$chain);		

									$post=$this->postBasic($local[0],$chain);
									$post['seq']="1";
									$post['bnbFrom']=$local[0]['bnbFrom'];
									$post['bnbTo']=$local[0]['bnbTo'];
									$post['bnbValue']=$this->convertBigNumber($local[0]['bnbValue'],18);	
									$post['fromAddress']=$this->address;
									if ($chain=="BSC") {
										$post['fromTokenSymbol']="BNB";				
										$post['fromTokenName']="BNB";
										$post['fromTokenDecimal']=18;
									} else {
										$post['fromTokenSymbol']="ETH";				
										$post['fromTokenName']="ETH";
										$post['fromTokenDecimal']=18;										
									}
									$post['fromValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
									$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
									$post['fromPrice']=$zz['price'];
									$post['fromTimestamp']=$zz['timestamp'];
									$post['fromValueUSD']=$zz['total'];											
									$post['toTokenSymbol']=$local[1]['tokenSymbol'];				
									$post['toTokenName']=$local[1]['tokenName'];
									$post['toTokenDecimal']=$local[1]['tokenDecimal'];	
									$post['toAddress']=$this->address;
									$post['contractAddress']=$local[1]['contractAddress'];	
									
									$array=$this->get_bep20_contract($post['contractAddress'],$chain);
									$post['contractName']=$array['CONTRACT_NAME'];
									$post['contractType']=$array['CONTRACT_TYPE'];
									$post['contractSymbol']=$array['SYMBOL'];
								
									$post['toValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);												
									$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
									$post['toPrice']=$zz['price'];
									$post['toTimestamp']=$zz['timestamp'];
									$post['toValueUSD']=$zz['total'];	
									$post['description']=$l['description'];
									$post['transaction_type']="S2-BNB-BEP20";
									$post['transaction_type']="STAKE";
									$this->bep20PostN($post,$chain);	
				
								} else {
									if ($chain=="BSC") $l['description']="Staked BNB and " . $local[1]['tokenSymbol'] . " received " . $local[2]['tokenSymbol'];
									if ($chain=="ETH") $l['description']="Staked ETH and " . $local[1]['tokenSymbol'] . " received " . $local[2]['tokenSymbol'];									
									$post=$this->postBasic($local[0],$chain);
									$post['bnbFrom']=$local[0]['bnbFrom'];
									$post['bnbTo']=$local[0]['bnbTo'];
									$post['bnbValue']=$this->convertBigNumber($local[0]['bnbValue'],18);	
									$post['fromAddress']=$this->address;
									$post['fromTokenSymbol']=$local[1]['tokenSymbol'];				
									$post['fromTokenName']=$local[1]['tokenName'];
									$post['fromTokenDecimal']=$local[1]['tokenDecimal'];
									$post['fromValue']=$this->convertBigNumber($local[1]['tokenValue'],$local[1]['tokenDecimal']);
									$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
									$post['fromPrice']=$zz['price'];
									$post['fromTimestamp']=$zz['timestamp'];
									$post['fromValueUSD']=$zz['total'];											
									$post['toTokenSymbol']=$local[2]['tokenSymbol'];				
									$post['toTokenName']=$local[2]['tokenName'];
									$post['toTokenDecimal']=$local[2]['tokenDecimal'];	
									$post['toAddress']=$this->address;
									$post['contractAddress']=$local[2]['contractAddress'];	
									
									$array=$this->get_bep20_contract($post['contractAddress'],$chain);
									$post['contractName']=$array['CONTRACT_NAME'];
									$post['contractType']=$array['CONTRACT_TYPE'];
									$post['contractSymbol']=$array['SYMBOL'];
								
									$post['toValue']=$this->convertBigNumber($local[0]['bnbValue'],18);												
									$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
									$post['toPrice']=$zz['price'];
									$post['toTimestamp']=$zz['timestamp'];
									$post['toValueUSD']=$zz['total'];	
									$post['description']=$l['description'];
									$post['transaction_type']="S2-BNB-BEP20";
									$post['transaction_type']="STAKE";
									$this->bep20PostN($post,$chain);		

									$post=$this->postBasic($local[0],$chain);
									$post['seq']="1";
									$post['bnbFrom']=$local[0]['bnbFrom'];
									$post['bnbTo']=$local[0]['bnbTo'];
									$post['bnbValue']=$this->convertBigNumber($local[0]['bnbValue'],18);	
									$post['fromAddress']=$this->address;
									if ($chain=="BSC") { 
										$post['fromTokenSymbol']="BNB";				
										$post['fromTokenName']="BNB";
										$post['fromTokenDecimal']=18;
									} else {
										$post['fromTokenSymbol']="ETH";				
										$post['fromTokenName']="ETH";
										$post['fromTokenDecimal']=18;										
									}
									$post['fromValue']=$this->convertBigNumber($local[3]['bnbValue'],18);
									$zz=$this->getTokenPrice($post['fromTokenSymbol'],$post['timestamp'],$post['fromValue']);	
									$post['fromPrice']=$zz['price'];
									$post['fromTimestamp']=$zz['timestamp'];
									$post['fromValueUSD']=$zz['total'];		
								
									$post['toTokenSymbol']=$local[2]['tokenSymbol'];				
									$post['toTokenName']=$local[2]['tokenName'];
									$post['toTokenDecimal']=$local[2]['tokenDecimal'];	
									$post['toAddress']=$this->address;
									$post['contractAddress']=$local[2]['contractAddress'];	
									
									$array=$this->get_bep20_contract($post['contractAddress'],$chain);
									$post['contractName']=$array['CONTRACT_NAME'];
									$post['contractType']=$array['CONTRACT_TYPE'];
									$post['contractSymbol']=$array['SYMBOL'];

								
									$post['toValue']=$this->convertBigNumber($local[2]['tokenValue'],$local[2]['tokenDecimal']);												
								$zz=$this->getTokenPrice($post['toTokenSymbol'],$post['timestamp'],$post['toValue']);	
								$post['toPrice']=$zz['price'];
								$post['toTimestamp']=$zz['timestamp'];
								$post['toValueUSD']=$zz['total'];	
									$post['description']=$l['description'];
									$post['transaction_type']="S2-BNB-BEP20";
									$post['transaction_type']="STAKE";
									$this->bep20PostN($post,$chain);										
								}
	}
	
	function process_transactions($address,$chain="BSC", $force="N") {
		
            $this->address=strtolower($address);
			//
			// Populate Background Data 
			//
			$this->populate_lp_list();
			$this->populate_farm_list();
            

            //
			// Query the data backwards in order to get the last timestamp
			//
			if ($force=="N") {
				$sql="SELECT timeStamp FROM BEP20_TRANSACTIONS where ";
				$sql.="wallet_address = '" . strtolower($address) . "' AND CHAIN = '" . $chain . "' ORDER BY timeStamp desc";
				
				$list=$this->sql($sql);	
				if (sizeof($list)>0) {
					$last_timestamp=$list[0]['timeStamp'];
				} else {
					$last_timestamp=0;				
				}
			} else {
			    $last_timestamp=0;	
			}
			
		    $address=strtolower($address);
			
			//
			// Query raw transactions for the wallet.
			//
			
			$sql="SELECT timeStamp, hash, count(*) as C from BEP20_RAW_TRANSACTIONS where ";
			$sql.="walletAddress = '" . strtolower($address) . "' and timeStamp > " . $last_timestamp . " AND CHAIN = '" . $chain . "' GROUP BY timeStamp, hash ORDER BY 1";
		
			$list=$this->sql($sql);
			
			$output=array();
			foreach ($list as $l) {
                $this->t='N';
				$sql="SELECT * FROM BEP20_RAW_TRANSACTIONS where ";
				$sql.="walletAddress = '" . strtolower($address) . "' AND hash='" . $l['hash'] . "' AND CHAIN = '" . $chain . "' order by source";
				$local=$this->sql($sql);
				$counts=$this->spread($local);
				$l['description']="";
				//
				//-- 1 Record
				//
				if ($l['C']==1) {
						if ($counts['txlist']==1) {
							if ($local[0]['bnbTo']==$address&&$local[0]['bnbValue']!="0") $this->receive_bnb_from_another_wallet($local, $chain);
							if ($local[0]['bnbFrom']==$address&&$local[0]['bnbValue']!="0") $this->send_bnb_to_another_wallet($local, $chain);					
							if ($local[0]['bnbFrom']==$address&&$local[0]['bnbValue']=="0") $this->contract_interaction_approval($local, $chain);											
						}						
						if ($counts['tokentx']==1) $this->received_bep20_token_from_another_wallet($local,$chain);
				}
				//
				//-- 2 Records
				//
				if ($l['C']==2) {	
	if ($local[0]['tokenFrom']==$address&&$local[1]['bnbFrom']==$address&&$local[1]['bnbValue']=="0") {
		if ($this->is_staking_contract($local[1]['bnbTo'])) {
			$this->stake_tokens($local,$chain);							
		} else {
			$this->send_bep20($local,$chain);
		}
	} else {
  	    if ($local[0]['tokenTo']==$address&&$local[1]['bnbFrom']==$address&&$local[1]['bnbValue']!="0") {
		$this->send_bnb_receive_bep20($local, $chain);	
	    } else {			
		if (!$this->is_syrup_contract($local[0]['tokenFrom'])) {
		if ($local[0]['tokenTo']==$address&&$local[1]['bnbFrom']==$address&&$local[1]['bnbValue']=="0") $this->unstake_farm_with_no_reward($local);
		if ($local[1]['bnbFrom']==$address&&$local[1]['bnbValue']=="0") {
                        $this->harvest_farm_rewards($local,$chain);
               }
						}
            }
        }
				}
				//
				//-- 3 Records
				//				
				if ($l['C']==3) {
					if ($local[0]['source']=="internal") {
						if ($local[0]['bnbTo']==$address||$local[0]['tokenFrom']==$address) {
							if ($local[1]['tokenFrom']==$address) {
								$this->send_bep20_receive_bnb($local,$chain);
							} else {
								$this->swapped_bnb_for_bep20_token($local,$chain);
							}
						} else {
								$this->unstaked_bnb_and_bep20($local,$chain);
						}
					} else {
						if ($local[2]['bnbFrom']==$address&&$local[2]['bnbValue']=="0") {
							if ($local[2]['bnbTo']==$this->master_chef) {
								$this->staked_goatx_first_time($local,$chain);
							} else {
								$this->swap_bep20_for_bep20($local,$chain);
							}
						} else {
						    $this->lp_staking_with_bnb($local,$chain);
						}
					}
				}
				//
				//-- 4 Records
				//
				if ($l['C']==4) {
					if ($local[3]['bnbValue']=="0"&&$local[3]['source']="txlist") {				
						if($local[0]['tokenTo']=="0x0000000000000000000000000000000000000000"||
						$local[1]['tokenTo']=="0x0000000000000000000000000000000000000000"||
						$local[2]['tokenTo']=="0x0000000000000000000000000000000000000000") {
							$this->unstaked_goatx($local,$chain);					
						} else {
							$l['description']="";
							$l['description']=$this->staked_bep20_and_bep20($local,$chain);
							if ($l['description']=="") {
								if ($local[3]['bnbValue']=="0") {
									$this->unstaked_bep20_and_bep20($local,$chain);	
								}
							}
							if ($l['description']=="") {
								$this->unstaked_bnb_and_bep20_2($local,$chain); 	
							}					
						}							
					} else {
						if ($local[3]['bnbValue']!="0"&&$local[0]['bnbValue']!="0"&&$local[0]['bnbTo']==$address) {
							$this->staked_bnb_and_bep20($local,$chain);
						  
						} else {
							$l['description']="Else 1";
						}
					}
				}		
			}
                       $hash=$address;
                       $sql = "SELECT * FROM BEP20_TRANSACTIONS WHERE WALLET_ADDRESS = '" . $hash . "'";
                       $t=$this->sql($sql);
                       foreach($t as $u) {
                          if ($u['toValue']!=''&&$u['toPrice']!='') {
                             try {
       	                     $toPriceUSD = str_replace(',','',$u['toValue']) * str_replace(',','',$u['toPrice']);
                             $toPriceUSD = number_format($toPriceUSD,4);
} catch(Exception $e) {
  $toPriceUSD=0;
                             $toPriceUSD = number_format($toPriceUSD,4);
}
                             $sql="update BEP20_TRANSACTIONS set toValueUSD = '" . $toPriceUSD . "' WHERE hash = '" . $u['hash'] . "'";
                             $this->execute($sql); 
                          }
                          if ($u['fromValue']!=''&&$u['fromPrice']!='') {
                             try {
	                     $toPriceUSD = str_replace(',','',$u['fromValue']) * str_replace(',','',$u['fromPrice']);
                             $toPriceUSD = number_format($toPriceUSD,4);
                             } 
catch(Exception $e) {
  $toPriceUSD=0;
                             $toPriceUSD = number_format($toPriceUSD,4);
}
                             $sql="update BEP20_TRANSACTIONS set fromValueUSD = '" . $toPriceUSD . "' WHERE hash = '" . $u['hash'] . "'";
                             $this->execute($sql); 
                          }
                        }
			return array();
	}
	
	function populate_lp_list() {
			$sql="SELECT LP_ADDRESS, SYMBOL0, AMOUNT0, SYMBOL1, AMOUNT1 FROM BEP20_TOKEN_PAIR";
			$d=$this->sql($sql);
			$this->lp_list=$d;
	}
	
	function is_lp_address($address) {
			$result=false;
			foreach($this->lp_list as $l) {
				if (strtolower($l['LP_ADDRESS'])==strtolower($address)) $result=true;
			}
			return $result;
	}

	function is_farm_address($address) {
			$result=false;
			foreach($this->farm_list as $l) {
				if (strtolower($l['LP_ADDRESS'])==strtolower($address)) $result=true;
			}
			return $result;
	}
	
	function populate_farm_list() {
			$sql="SELECT FARM_ID, FARM_NAME, LP_ADDRESS FROM BEP20_TOKEN_FARM";
			$d=$this->sql($sql);
			$this->farm_list=$d;
	}
	
	
   function get_token_historical_prices($contract,$symbol,$name, $chain="BSC") {
	   
	  //
	  // Get the most recent timestamp data was loaded or checked.
	  //
      $sql="SELECT last_timestamp from GOATX_TOKEN_CONTRACT_PRICE WHERE contract_address = '" . $contract . "' ORDER by timestamp DESC";
      $t=$this->sql($sql);
      if (sizeof($t)>0) {
            $timestamp=$t[0]['last_timestamp']; 
      } else {
            $timestamp=0;
      } 

      //
      // Get the current timestamp
      //
	  
      $q=time();

      if (($q-$timestamp)>9600) {
            sleep(1); 
			if ($chain=="BSC") {
				$url="https://api.coingecko.com/api/v3/coins/binance-smart-chain/contract/";
			} else {
				$url="https://api.coingecko.com/api/v3/coins/ethereum/contract/";				
			}
			$url.=strtolower($contract);
            $url.="/market_chart/?vs_currency=usd&days=max";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			
            $array=json_decode($d,true);
            echo $d . "\r\n";
			if (isset($array['prices'])) {
                foreach($array['prices'] as $a) {
                    $timestamp=$a[0];
                    $price=$a[1];
                            
                    $sql="select * from GOATX_TOKEN_CONTRACT_PRICE WHERE contract_address = '" . $contract . "' and timestamp = '" . substr($timestamp,0,10) . "'";
                    $z=$this->sql($sql);
                    if (sizeof($z)==0) {     
                        $sql="INSERT INTO GOATX_TOKEN_CONTRACT_PRICE (contract_address, timestamp, token_symbol, token_name, price,last_timestamp) VALUES (";
                        $sql.="'" . $contract . "'," . substr($timestamp,0,10) . ",'" . $symbol . "','" . $name . "','" . $price . "'," . time() . ")";
                        echo $sql;
                        $this->execute($sql);
                    }
                }
			} else {
				$sql="UPDATE GOATX_USER_TOKEN_BALANCES SET EXCLUDE = 'Y' WHERE token_address = '" . $contract . "'";
				$this->execute($sql);
			}
		}
	}

	
	function calculate_token_balances($uid) {
	        // Make the indexes for each wallet in the balances array.
			$balances=array();
			$sql="select WALLET_ADDRESS from GOATX_USER_WALLET WHERE USER_ID = " . $uid;
			$list=$this->sql($sql);
			$balances['ALL']=array();
			foreach ($list as $item) {
				$b=array();
				$balances[$item['WALLET_ADDRESS']]=array();
			}
                    // Get BNB and ETH for each wallet.
                        $total_bnb=0;
                        $total_eth=0;
                        foreach($balances as $name=>$value) {
							  // Get Current BNB Balance 
                              $j=$this->getBNBBalance($name);
                              $jj=json_decode($j,true);
							  if ($jj['message']=="OK") {
									$bnb_val=$this->convertBigNumber($jj['result'],18,18);
							  } else {
									$bnb_val=0;
							  }
                              $total_bnb+=$bnb_val;
							  // Get Current ETH Balance
                              $k=$this->getETHBalance($name);
                              $kk=json_decode($k,true);
							  if ($kk['message']=="OK") {
								$eth_val=$this->convertBigNumber($kk['result'],18,18);
							  } else {
								$eth_val=0;
							  }
                              $total_eth+=$eth_val;
							  
							  //-- Update Table
							  $sql="SELECT * FROM GOATX_USER_TOKEN_BALANCES WHERE uid = " . $uid;
							  $sql.=" AND wallet_address = '" . $name . "' AND token_address = 'BNB Coin'";
							  $z=$this->sql($sql);
							  if (sizeof($z)==0) {
									$sql="INSERT INTO GOATX_USER_TOKEN_BALANCES (uid, wallet_address, token_address, chain) VALUES (";
									$sql.=$uid . ",'" . $name . "','BNB Coin','BSC') ";
									$this->execute($sql);
							  } 
							  $sql="UPDATE GOATX_USER_TOKEN_BALANCES SET timestamp = " . time() . ",";
							  $sql.=" token_balance = '" . $bnb_val . "', ";
							  $sql.=" token_count = '0', ";
							  $sql.=" chain = 'BSC', ";
							  $sql.=" token_symbol = 'BNB', ";
							  $sql.=" token_name = 'Binance Coin' ";
							  $sql.=" WHERE uid = " . $uid; 
							  $sql.=" AND wallet_address = '" . $name . "' AND token_address = 'BNB Coin'";
							  $this->execute($sql);
                              
							  $sql="SELECT * FROM GOATX_USER_TOKEN_BALANCES WHERE uid = " . $uid;
							  $sql.=" AND wallet_address = '" . $name . "' AND token_address = 'Ethereum'";
							  $z=$this->sql($sql);
							  if (sizeof($z)==0) {
								$sql="INSERT INTO GOATX_USER_TOKEN_BALANCES (uid, wallet_address, token_address, chain) VALUES (";
								$sql.=$uid . ",'" . $name . "','Ethereum','ETH') ";
					            $this->execute($sql);
							  } 
							  $sql="UPDATE GOATX_USER_TOKEN_BALANCES SET timestamp = " . time() . ",";
							  $sql.=" token_balance = '" . $eth_val . "', ";
							  $sql.=" token_count = '0', ";
							  $sql.=" chain = 'ETH', ";
							  $sql.=" token_symbol = 'ETH', ";
							  $sql.=" token_name = 'Ethereum' ";
							  $sql.=" WHERE uid = " . $uid; 
							  $sql.=" AND wallet_address = '" . $name . "' AND token_address = 'Ethereum'";
							  $this->execute($sql);
                              
                        }
						$sql="SELECT * FROM GOATX_USER_TOKEN_BALANCES WHERE uid = " . $uid;
						$sql.=" AND wallet_address = 'ALL' AND token_address = 'BNB Coin'";
						$z=$this->sql($sql);
						if (sizeof($z)==0) {
							$sql="INSERT INTO GOATX_USER_TOKEN_BALANCES (uid, wallet_address, token_address, chain) VALUES (";
							$sql.=$uid . ",'ALL','BNB Coin','BSC') ";
							$this->execute($sql);
						} 
						$sql="UPDATE GOATX_USER_TOKEN_BALANCES SET timestamp = " . time() . ",";
						$sql.=" token_balance = '" . $bnb_val . "', ";
						$sql.=" token_count = '0', ";
						$sql.=" chain = 'BSC', ";
						$sql.=" token_symbol = 'BNB', ";
						$sql.=" token_name = 'Binance Coin' ";
						$sql.=" WHERE uid = " . $uid; 
						$sql.=" AND wallet_address = 'ALL' AND token_address = 'BNB Coin'";
						$this->execute($sql);
                              
						$sql="SELECT * FROM GOATX_USER_TOKEN_BALANCES WHERE uid = " . $uid;
						$sql.=" AND wallet_address = 'ALL' AND token_address = 'Ethereum'";
						$z=$this->sql($sql);
						if (sizeof($z)==0) {
							$sql="INSERT INTO GOATX_USER_TOKEN_BALANCES (uid, wallet_address, token_address, chain) VALUES (";
							$sql.=$uid . ",'ALL','Ethereum','ETH') ";
							$this->execute($sql);
						} 
						$sql="UPDATE GOATX_USER_TOKEN_BALANCES SET timestamp = " . time() . ",";
						$sql.=" token_balance = '" . $eth_val . "', ";
						$sql.=" token_count = '0', ";
						$sql.=" chain = 'ETH', ";
						$sql.=" token_symbol = 'ETH', ";
						$sql.=" token_name = 'Ethereum' ";
						$sql.=" WHERE uid = " . $uid; 
						$sql.=" AND wallet_address = 'ALL' AND token_address = 'Ethereum'";
						$this->execute($sql);
						// Make a list of all tokens in all wallets
						$last_contract="";			
							$sql="select walletAddress, contractAddress, tokenValue, tokenSymbol, tokenName, tokenDecimal, CHAIN, ";
							$sql.=" tokenFrom, tokenTo, tokenValue from ";
							$sql.=" BEP20_RAW_TRANSACTIONS WHERE walletAddress IN (SELECT WALLET_ADDRESS FROM ";
							$sql.=" GOATX_USER_WALLET WHERE USER_ID = " . $uid . ") order by contractAddress, tokenSymbol";
							$list=$this->sql($sql);
							foreach ($list as $item) {
								if ($item['contractAddress']!=$last_contract) {
									$last_contract = $item['contractAddress'];
									$f=array();
									$f['token_address']=$item['contractAddress'];
									$f['token_symbol']=$item['tokenSymbol'];
									$f['token_decimal']=$item['tokenDecimal'];	
									$f['token_name']=$item['tokenName'];		
									$f['chain']=$item['CHAIN'];								
									$f['token_balance']='0';	
									$f['token_count']=0;
									foreach ($balances as $name => $value) {
										array_push($balances[$name],$f);
									}										
								}
							//-- Do ALL
                            $i=0;
							foreach($balances['ALL'] as $all) {
								if ($all['token_address']==$item['contractAddress']) {
									$current_balance=$all['token_balance'];
									$cb=floatval($all['token_balance']);						
									$val=$this->convertBigNumber($item['tokenValue'],$item['tokenDecimal'],$item['tokenDecimal']);
									if ($item['walletAddress']==$item['tokenTo']) {
										// add
                                                $all['token_balance']=$cb+floatval($val);
                                                $balances['ALL'][$i]['token_balance']=$cb+floatval($val);
                                                $balances['ALL'][$i]['token_count']++;
									}
									if ($item['walletAddress']==$item['tokenFrom']) {
										// subtract
                                                $all['balance']=$cb-floatval($val);							
                                                $balances['ALL'][$i]['token_balance']=$cb-floatval($val);
                                                $balances['ALL'][$i]['token_count']++;
									}	
									$all['token_count']++;						
								}
                                $i++;
							}
							//-- Do Wallet
                                $i=0;
								foreach($balances[$item['walletAddress']] as $all) {
									if ($all['token_address']==$item['contractAddress']) {
										$current_balance=$all['token_balance'];
										$cb=floatval($all['token_balance']);						
										$val=$this->convertBigNumber($item['tokenValue'],$item['tokenDecimal'],$item['tokenDecimal']);
										if ($item['walletAddress']==$item['tokenTo']) {
											// add
                                                $all['token_balance']=$cb+floatval($val);
                                                $balances[$item['walletAddress']][$i]['token_balance']=$cb+floatval($val);
                                                $balances[$item['walletAddress']][$i]['token_count']++;
										}
									if ($item['walletAddress']==$item['tokenFrom']) {
											// subtract
                                                $all['balance']=$cb-floatval($val);							
                                                $balances[$item['walletAddress']][$i]['token_balance']=$cb-floatval($val);
                                                $balances[$item['walletAddress']][$i]['token_count']++;
										}			
									$all['token_count']++;			
								}
                                $i++;
						}
		} // End Item Loop

		foreach($balances as $name=>$value) {
			foreach ($balances[$name] as $a)
			{
				$sql="SELECT * FROM GOATX_USER_TOKEN_BALANCES WHERE uid = " . $uid;
				$sql.=" AND wallet_address = '" . $name . "' AND token_address = '" . $a['token_address'] . "' AND exclude = 'Y'";
				$z=$this->sql($sql);
                                if (sizeof($z)==0) {
				$sql="SELECT * FROM GOATX_USER_TOKEN_BALANCES WHERE uid = " . $uid;
				$sql.=" AND wallet_address = '" . $name . "' AND token_address = '" . $a['token_address'] . "'";
				$z=$this->sql($sql);
				if (sizeof($z)==0) {
					$sql="INSERT INTO GOATX_USER_TOKEN_BALANCES (uid, wallet_address, token_address, chain) VALUES (";
					$sql.=$uid . ",'" . $name . "','" . $a['token_address'] . "','" . $a['chain'] . "') ";
					$this->execute($sql);
				} 
			$sql="UPDATE GOATX_USER_TOKEN_BALANCES SET timestamp = " . time() . ",";
			$sql.=" token_balance = '" . $a['token_balance'] . "', ";
			$sql.=" token_count = '" . $a['token_count'] . "', ";
			$sql.=" chain = '" . $a['chain'] . "', ";
			$sql.=" token_symbol = '" . $a['token_symbol'] . "', ";
			$sql.=" token_name = '" . $a['token_name'] . "' ";
			$sql.=" WHERE uid = " . $uid; 
			$sql.=" AND wallet_address = '" . $name . "' AND token_address = '" . $a['token_address'] . "'";
			$this->execute($sql);
				// Update Historical Pricess -- REMOVE
				// $this->get_token_historical_prices($a['token_address'],$a['token_symbol'],$a['token_name']);
			}
			}
		}
	}
	function calculate_lp_balances($address) {
			$sql="SELECT * FROM BEP20_WALLET_LP WHERE WALLET_ADDRESS = '" . $address . "'";
			$z=$this->sql($sql);
			foreach ($z as $z0) {
				$array=$this->getBEP20TokenAccountBalance($z0['LP_CONTRACT'], $address);
				$a=json_decode($array,true);
				$balance=$this->convertBigNumber($a['result'],18);
				$sql="SELECT * FROM BEP20_TOKEN_PAIR WHERE LP_ADDRESS = '" . strtolower($z0['LP_CONTRACT']) . "'";
				$u=$this->sql($sql);
                if (sizeof($u)==0) {
					$sql="DELETE FROM BEP20_WALLET_LP WHERE WALLET_ADDRESS = '" . $address . "' AND ";
					$sql.="LP_CONTRACT = '" . strtolower($z0['LP_CONTRACT']) . "'";
					$this->execute($sql);
				} else {
				if ($u[0]['AMOUNT0']=='') $u[0]['AMOUNT0']="0";
				if ($u[0]['AMOUNT1']=='') $u[0]['AMOUNT1']="0";
				$my0=floatval($balance)*floatval($u[0]['AMOUNT0']);
				$my1=floatval($balance)*floatval($u[0]['AMOUNT1']);
				$sql="UPDATE BEP20_WALLET_LP SET LP_AMOUNT = '" . $balance . "' WHERE ID = " . $z0['ID'];
				$this->execute($sql);
				$sql="UPDATE BEP20_WALLET_LP SET TOKEN0_USD = '" . $my0 . "' WHERE ID = " . $z0['ID'];
				$this->execute($sql);
				$sql="UPDATE BEP20_WALLET_LP SET TOKEN1_USD = '" . $my1 . "' WHERE ID = " . $z0['ID'];
				$this->execute($sql);
				}
				usleep(100000);
			}
	}
	//
	//-- GET BALANCE FOR A SINGLE ADDRESS 
	//
	
	function getBNBBalance($address) {
		    //
			//Get BNB Balance for a single Address	
			// tested
			$url="https://api.bscscan.com/api?module=account&action=balance&address=";
			$url.=$address;
			$url.="&tag=latest&apikey=" . $this->bsc_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;
	}
	
	function getETHBalance($address) {
		    //
			//Get ETH Balance for a single Address	
			// tested
			$url="https://api.etherscan.io/api?module=account&action=balance&address=";
			$url.=$address;
			$url.="&tag=latest&apikey=" . $this->eth_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;
	}
	
	function getETHBlock($id) {
                $block_hex=dechex($id);
		$url="https://api.etherscan.io/api?module=proxy&action=eth_getBlockByNumber&tag=0x";
			$url.=$block_hex;
			$url.="&boolean=true&apikey=" . $this->eth_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;
	}
	
    //
	//-- LAST PRICE 
	//
	
	function getBNBLastPrice() {
		    //
			//Get BNB Balance for a single Address	
			//tested
			$url="https://api.bscscan.com/api?module=stats&action=bnbprice";
			$url.="&apikey=" . $this->bsc_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;
	}

	function getETHLastPrice() {
		    //
			//Get ETH Balance for a single Address	
			//tested
			$url="https://api.etherscan.io/api?module=stats&action=ethprice";
			$url.="&apikey=" . $this->eth_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;
	}
	
	//
	//-- GET PRICE HISTORY
	//
	
	function getArmyTokenPrices() {
			$url="https://farm.army/api/v0/prices";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

        
	function getGeckoTokenData($contract) {
			$url="https://api.coingecko.com/api/v3/coins/binance-smart-chain/contract/";
			$url.=strtolower($contract);
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getGeckoAssetPlatforms() {
			$url="https://api.coingecko.com/api/v3/asset_platforms";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	function getGeckoCategories() {
			$url="https://api.coingecko.com/api/v3/coins/categories";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getGeckoExchangesPage1() {
			$url="https://api.coingecko.com/api/v3/exchanges?per_page=250&page=1";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getGeckoTokenMarket() {
			$url="https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=500&page=1&sparkline=true";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getGeckoExchangesPage2() {
			$url="https://api.coingecko.com/api/v3/exchanges?per_page=250&page=2";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getGeckoFinancePlatforms() {
			$url="https://api.coingecko.com/api/v3/finance_platforms";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getGeckoFinanceProducts() {
			$url="https://api.coingecko.com/api/v3/finance_products?per_page=1000";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getGeckoIndexes() {
			$url="https://api.coingecko.com/api/v3/indexes?per_page=1000";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getGeckoEvents() {
			$url="https://api.coingecko.com/api/v3/events";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}


	function getGeckoDerivatives() {
			$url="https://api.coingecko.com/api/v3/derivatives";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getGeckoDerivativeExchanges() {
			$url="https://api.coingecko.com/api/v3/derivatives/exchanges";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}


	function getGeckoTrending() {
			$url="https://api.coingecko.com/api/v3/search/trending";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getGeckoExchangeRates() {
			$url="https://api.coingecko.com/api/v3/exchange_rates";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getGeckoGlobal() {
			$url="https://api.coingecko.com/api/v3/global";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getGeckoGlobalDeFi() {
			$url="https://api.coingecko.com/api/v3/global/decentralized_finance_defi";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getGeckoCompaniesBitcoin() {
			$url="https://api.coingecko.com/api/v3/companies/public_treasury/bitcoin";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getGeckoCompaniesEthereum() {
			$url="https://api.coingecko.com/api/v3/companies/public_treasury/ethereum";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}


	function getGeckoHistoricalPrices($contract) {
			if ($chain=="BSC") {
				$url="https://api.coingecko.com/api/v3/coins/binance-smart-chain/contract/";
			} else {
				$url="https://api.coingecko.com/api/v3/coins/ethereum/contract/";				
			}		
			$url.=strtolower($contract);
			$url.="/market_chart/?vs_currency=usd&days=max";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	function getGecko90Prices($contract,$chain) {
			if ($chain=="BSC") {
				$url="https://api.coingecko.com/api/v3/coins/binance-smart-chain/contract/";
			} else {
				$url="https://api.coingecko.com/api/v3/coins/ethereum/contract/";				
			}		
			$url.=strtolower($contract);
			$url.="/market_chart/?vs_currency=usd&days=90";
                        echo $url;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
}
	function getGeckoDayPrices($contract, $chain="BSC") {
			if ($chain=="BSC") {
				$url="https://api.coingecko.com/api/v3/coins/binance-smart-chain/contract/";
			} else {
				$url="https://api.coingecko.com/api/v3/coins/ethereum/contract/";				
			}		
			$url.=strtolower($contract);
			$url.="/market_chart/?vs_currency=usd&days=1";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	
	function getArmyLPTokens() {
			$url="https://farm.army/api/v0/liquidity-tokens";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getArmyFarms() {
			$url="https://farm.army/api/v0/farms";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	
	function getBNBPriceHistory($startdate, $enddate) {
		    //
			//Get BNB Balance for a single Address	
			//tested
			$url="https://api.bscscan.com/api?module=stats&action=bnbdailyprice";
			$url.="&startdate=" . $startdate;
			$url.="&enddate=" . $enddate;
			$url.="&apikey=" . $this->bsc_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;
	}	

	function getETHPriceHistory($startdate, $enddate) {
		    //
			//Get ETH Balance for a single Address	
			//tested
			$url="https://api.etherscan.io/api?module=stats&action=ethdailyprice";
			$url.="&startdate=" . $startdate;
			$url.="&enddate=" . $enddate;
			$url.="&apikey=" . $this->eth_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;
	}	
	
	//
    //-- HISTORICAL COIN BALANCES 
	//
	
	function getHistoricalBNBBalance($address,$blockno) {
		    //
			//Get Historical BNB Balance for a single Address By BlockNo	
			//tested
			$url="https://api.bscscan.com/api?module=account&action=balancehistory&address=";
			$url.=$address;
			$url.="&blockno=" . $blockno . "&apikey=" . $this->bsc_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getHistoricalETHBalance($address,$blockno) {
		    //
			//Get Historical ETH Balance for a single Address By BlockNo	
			//tested
			$url="https://api.etherscan.io/api?module=account&action=balancehistory&address=";
			$url.=$address;
			$url.="&blockno=" . $blockno . "&apikey=" . $this->eth_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	
	//
	//-- TRANSACTIONS BY ADDRESS
	//
	
	function getBSCTransactionsByAddress($address,$startblock="0", $endblock="99999999", $sort="desc") {
		    //
			//Get a list of 'Normal' Transactions By Address	
			//tested
			$url="https://api.bscscan.com/api?module=account&action=txlist&address=";
			$url.=$address;
			$url.="&startblock=" . $startblock;
            $url.="&endblock=" . $endblock;
            $url.="&sort=" . $sort . "&apikey=" . $this->bsc_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);					
			curl_close($curl);
			return $d;		
	}

	function getBSCInternalTransactionsByAddress($address,$startblock="0", $endblock="99999999", $sort="desc") {
		    //
			//Get a list of 'Normal' Transactions By Address	
			//tested
			$url="https://api.bscscan.com/api?module=account&action=txlistinternal&address=";
			$url.=$address;
			$url.="&startblock=" . $startblock;
            $url.="&endblock=" . $endblock;
            $url.="&sort=" . $sort . "&apikey=" . $this->bsc_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);					
			curl_close($curl);
			return $d;		
	}

	function getETHInternalTransactionsByAddress($address,$startblock="0", $endblock="99999999", $sort="desc") {
		    //
			//Get a list of 'Normal' Transactions By Address	
			//tested
			$url="https://api.etherscan.io/api?module=account&action=txlistinternal&address=";
			$url.=$address;
			$url.="&startblock=" . $startblock;
            $url.="&endblock=" . $endblock;
            $url.="&sort=" . $sort . "&apikey=" . $this->eth_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);					
			curl_close($curl);
			return $d;		
	}
	
	function populateTxList($address,$chain="BSC",$force="N") {

		//--
		//-- DO BSC
		//--
                if ($chain=="BSC") {
			$sql="SELECT blockNumber AS C FROM BEP20_RAW_TRANSACTIONS ";
			$sql.=" WHERE walletAddress = '" . strtolower($address) . "' AND source = 'txlist' AND CHAIN = 'BSC' ORDER BY blockNumber DESC";
			$f=$this->sql($sql);
			if (sizeof($f)>0) {
					$startblock=$f[0]['C']+1;
			} else {
					$startblock=0;
			}

                        if ($force=="Y") $startblock=0;
			$txlist=$this->getBSCTransactionsByAddress($address,$startblock, "99999999", "asc");
			$t=json_decode($txlist,true);
			if (isset($t['result'])) {
                                if (is_array($t['result'])) {
				    foreach($t['result'] as $t0) {
					$this->txlistPostN($t0,$address,"BSC");
				    }
                                } else {
                                  $sql="update GOATX_WALLET set exclude = 'Y' where wallet_address = '" . $address . "'";
                                  $this->execute($sql);
                                }
			}
			
                }
		//--
		//-- DO ETH
		//--
                if ($chain=="ETH") {
			$sql="SELECT blockNumber AS C FROM BEP20_RAW_TRANSACTIONS ";
			$sql.=" WHERE walletAddress = '" . strtolower($address) . "' AND source = 'txlist' AND CHAIN = 'ETH' ORDER BY blockNumber DESC";
			$f=$this->sql($sql);
			if (sizeof($f)>0) {
					$startblock=$f[0]['C']+1;
			} else {
					$startblock=0;
			}
                        if ($force=="Y") $startblock=0;
			$txlist=$this->getETHTransactionsByAddress($address,$startblock, "99999999", "asc");
			$t=json_decode($txlist,true);
			if (isset($t['result'])) {
                             if (is_array($t['result'])) {
				foreach($t['result'] as $t0) {
					$this->txlistPostN($t0,$address,"ETH");
				}
                            }
			}	
                }	
	}

	function populateTxInternalList($address,$chain="BSC",$force="N") {
		
		//--
		//-- DO BSC
		//--
                if ($chain=="BSC") {
			$sql="SELECT blockNumber AS C FROM BEP20_RAW_TRANSACTIONS ";
			$sql.=" WHERE walletAddress = '" . strtolower($address) . "' AND source = 'internal' and CHAIN = 'BSC' ORDER BY blockNumber DESC";
			
			$f=$this->sql($sql);
			if (sizeof($f)>0) {
					$startblock=$f[0]['C']+1;
			} else {
					$startblock=0;
			}
                        if ($force=="Y") $startblock=0;
			$txlist=$this->getBSCInternalTransactionsByAddress($address,$startblock, "99999999", "asc");
			$t=json_decode($txlist,true);
			if (isset($t['result'])) {
                             if (is_array($t['result'])) {
				foreach($t['result'] as $t0) {
					$this->internalPostN($t0,$address,"BSC");
				}
                             }
			}
		}
		//--
		//-- DO ETH
		//--
                if ($chain=="ETH") {
			$sql="SELECT blockNumber AS C FROM BEP20_RAW_TRANSACTIONS ";
			$sql.=" WHERE walletAddress = '" . strtolower($address) . "' AND source = 'internal' and CHAIN = 'ETH' ORDER BY blockNumber DESC";
			
			$f=$this->sql($sql);
			if (sizeof($f)>0) {
					$startblock=$f[0]['C']+1;
			} else {
					$startblock=0;
			}
                        if ($force=="Y") $startblock=0;
			$txlist=$this->getETHInternalTransactionsByAddress($address,$startblock, "99999999", "asc");
			$t=json_decode($txlist,true);
			if (isset($t['result'])) {
				foreach($t['result'] as $t0) {
					$this->internalPostN($t0,$address,"ETH");
				}
			}
                }
	}
	
        function format_amount_with_no_e($amount) {
              $amount = (string)$amount; // cast the number in string
               $pos = stripos($amount, 'E-'); // get the E- position
               $there_is_e = $pos !== false; // E- is found
               if ($there_is_e) {
               $decimals = intval(substr($amount, $pos + 2, strlen($amount))); // extract the decimals
               $amount = number_format($amount, $decimals, '.', ','); // format the number without E-
               }
               $amount=str_replace(',','',$amount);
                return $amount;
        }

        function costTokenTx($record, $address, $chain) {
              $send="X";
              if (strtolower($record['from'])==$address) $send='Y';
              if (strtolower($record['to'])==$address) $send='N';

              //-- get Price
	
              $sql="select timestamp, price from GOATX_TOKEN_CONTRACT_PRICE WHERE contract_address = '" . $record['contractAddress'] . "' ORDER by timestamp DESC";
              $x=$this->sql($sql);
              if (sizeof($x)==0) {
                   $tokenPrice='0';
                   $tokenPriceTimestamp=0;
              } else {
                   $tokenPrice=$x[0]['price'];
                   $tokenPriceTimestamp=$x[0]['timestamp'];
              }
              //-- get Prior Transaction
              $sql="select * from BEP20_RAW_TRANSACTIONS WHERE wallet_address = '" . $address . "' AND contractAddress = '" . $record['contractAddress'] . "' and timeStamp < " . $record['timeStamp'];
              $sql.=" ORDER BY timeStamp DESC";
              $x=$this->sql($sql);
              if (sizeof($x)==0) {
                    $previousHash='';
                    $previousBalance='0';
                    $previousPrice='0';
                    $previousTotalValue='0';
                    $previousTotalCost='0';
                    $previousWeightedTokenCost='0';
                    $previousTotalRealizedGain='0';
                    $previousTotalUnrealizedGain='0';
              } else {
                    $previousHash=$x[0]['hash'];
                    $previousBalance=$x[0]['currentBalance'];
                    $previousPrice=$x[0]['tokenPrice'];
                    //$previousTotalValue=$[0]['totalValue'];
                    $previousTotalCost=$x[0]['totalCost'];
                    $previousWeightedTokenCost=$x[0]['weightedTokenCost'];
                    $previousTotalRealizedGain=$x[0]['totalRealizedGain'];
                    $previousTotalUnrealizedGain=$x[0]['totalUnrealizedGain'];
              }
              //-- Convert Numbers

              //"timeStamp":"1605585978",
              //"hash":"0x7a5abf86c82d3f97a40dd841a9f2089fbe3ac1332157c01bd1a1d89f575c45fc",
              //"from":"0x641414e2a04c8f8ebbf49ed47cc87dccba42bf07",
              //"contractAddress":"0xc9849e6fdb743d08faee3e34dd2d1bc69ea11a51",
              //"to":"0x7bb89460599dbf32ee3aa50798bbceae2a5f7f6a",
              //"value":"27605634758857128698365",
              //"tokenDecimal":"18",

             $convertedValue=$this->convertBigNumber($record['value'],$record['tokenDecimal']);
             $tokenPrice=$this->format_number_with_no_e($tokenPrice);

              //-- Calculate Numbers
              if ($send=='N') {
                   //-- Increase Number of Tokens in Wallet
                   $currentBalance=$previousBalance+$convertedValue;

                   //-- Value of the Transaction.
                   $transactionValue=$convertedValue*$tokenPrice;
 
                   //-- When Receiving total Cost increases by the value.
                   $totalCost=$previousTotalCost+$transactionValue;

                   //-- When Receving the current cost is the value of the transaction.
                   $currentCost=$transactionValue;

                   //-- Total Value is Balance * Price                  
                   $totalValue=$currentBalance*$tokenPrice;

                   //-- There is no realized gain on a receive.
                   $realizedGain=0;
                   $totalRealizedGain=$previousTotalRealizedGain;

                   //-- Current Change in Unrealized Gain is current value - previous Value.
                   $unrealizedGain=($totalValue-$previousTotalValue);
                   $totalUnrealizedGain=($totalValue-$totalCost);
                   
                   //-- Weighted Token Cost
                   $weightedTokenCost=$totalCost / $currentBalance;

              } 
              if ($send=='Y') {
                   //-- Decrease Number of Tokens in Wallet
                   $currentBalance=$previousBalance-$convertedValue;

                   //-- Value of the Transaction (-)
                   $transactionValue=0-($convertedValue*$tokenPrice);

                   //-- When Sending Total Cost is Decreased by the Cost of Tokens Sent.
                   $currentCost = ($previousWeightedTokenCost * $convertedValue);

                   $totalCost = $previousTotalCost - $currentCost;
  

                   $totalValue=$currentBalance*$tokenPrice;

                   //-- Realized Gain
                   $realizedGain = $transactionValue - $currentCost;
                   $totalRealizedGain=$previousTotalRealizedGain+$realizedGain;

                   $unrealizedGain=($totalValue-$previousTotalValue);
                   $totalUnrealizedGain=($totalValue-$totalCost);

                   //-- Weighted Token Cost does not change on send.
                   $weightedTokenCost=$previousWeightedTokenCost;
              }
              //-- Append Data

                    $record['previousHash']=$previousHash;
                    $record['previousBalance']=$previousBalance;
                    $record['previousPrice']=$previousPrice;
                    $record['previousTotalValue']=$previousTotalValue;
                    $record['previousTotalCost']=$previousTotalCost;
                    $record['previousWeightedTokenCost']=$previousWeightedTokenCost;
                    $record['previousTotalRealizedGain']=$previousTotalRealizedGain;
                   // $record['previousTotalUnrealizedGain']=$previousTotalUnrealizedGain'0';
                    $record['currentBalance']=$currentBalance;
                    $record['transactionValue']=$transactionValue;
                    $record['currentCost']=$currentCost;
                    $record['totalCost']=$totalCost;
                    $record['totalValue']=$totalValue;
                    $record['realizedGain']=$realizedGain;
                    $record['unrealizedGain']=$unrealizedGain;
                    $record['totalRealizedGain']=$totalRealizedGain;
                    $record['totalUnrealizedGain']=$totalUnrealizedGain;
                    $record['weightedTotalCost']=$weightedTokenCost;
              //-- Return record.

              return $record;
         
        }
	function populateTokenTxList($address,$chain="BSC",$force="N") {
		
		//--
		//-- DO BSC
		//--
	       if ($chain=="BSC") { 	
			$sql="SELECT blockNumber AS C FROM BEP20_RAW_TRANSACTIONS ";
			$sql.=" WHERE walletAddress = '" . strtolower($address) . "' AND source = 'tokentx' AND CHAIN = 'BSC' ORDER BY blockNumber DESC";
			$f=$this->sql($sql);
			if (sizeof($f)>0) {
					$startblock=$f[0]['C']+1;
			} else {
					$startblock=0;
			}
                        if ($force=="Y") $startblock=0;
                        echo "Startblock is " . $startblock . "\r\n";
			$txlist=$this->getBEP20TransfersByAddress($address,$startblock, "99999999", "asc");
			$t=json_decode($txlist,true);
			if (isset($t['result'])) {
				foreach($t['result'] as $t0) {
					$this->tokentxPostN($t0,$address,"BSC");
				}
			}
                        }
		//--
		//-- DO ETH
		//--
		if ($chain=="ETH") {
			$sql="SELECT blockNumber AS C FROM BEP20_RAW_TRANSACTIONS ";
			$sql.=" WHERE walletAddress = '" . strtolower($address) . "' AND source = 'tokentx' AND CHAIN = 'ETH' ORDER BY blockNumber DESC";
			$f=$this->sql($sql);
			if (sizeof($f)>0) {
					$startblock=$f[0]['C']+1;
			} else {
					$startblock=0;
			}
                        if ($force=="Y") $startblock=0;
			$txlist=$this->getERC20TransfersByAddress($address,$startblock, "99999999", "asc");
			$t=json_decode($txlist,true);
			if (isset($t['result'])) {
				foreach($t['result'] as $t0) {
					$this->tokentxPostN($t0,$address,"ETH");
				}
			}
		}
			
	}
	
	function getETHTransactionsByAddress($address,$startblock="0", $endblock="99999999", $sort="desc") {
		    //
			//Get a list of 'Normal' Transactions By Address	
			//tested
			$url="https://api.etherscan.io/api?module=account&action=txlist&address=";
			$url.=$address;
			$url.="&startblock=" . $startblock;
            $url.="&endblock=" . $endblock;
            $url.="&sort=" . $sort . "&apikey=" . $this->eth_key;
echo $url . "\r\n";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	
	//
	//-- 20 TRANSFERS BY ADDRESS
	//tested
	function getBEP20TransfersByAddress($address,$startblock="0", $endblock="99999999", $sort="desc") {
		    //
			//Get a list of 'Normal' Transactions By Address	
			//
			$url="https://api.bscscan.com/api?module=account&action=tokentx&address=";
			$url.=$address;
			$url.="&startblock=" . $startblock;
            $url.="&endblock=" . $endblock;
            $url.="&sort=" . $sort . "&apikey=" . $this->bsc_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getERC20TransfersByAddress($address,$startblock="0", $endblock="99999999", $sort="desc") {
		    //
			//Get a list of 'Normal' Transactions By Address	
			//
			$url="https://api.etherscan.io/api?module=account&action=tokentx&address=";
			$url.=$address;
			$url.="&startblock=" . $startblock;
            $url.="&endblock=" . $endblock;
            $url.="&sort=" . $sort . "&apikey=" . $this->eth_key;
echo $url . "\r\n";
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	
	//
	//-- 721 TRANSFERS BY ADDRESS
	//tested
	function getBEP721TransfersByAddress($address,$startblock="0", $endblock="99999999", $sort="desc") {
		    //
			//Get a list of 'Normal' Transactions By Address	
			//
			$url="https://api.bscscan.com/api?module=account&action=tokennfttx&address=";
			$url.=$address;
			$url.="&startblock=" . $startblock;
            $url.="&endblock=" . $endblock;
            $url.="&sort=" . $sort . "&apikey=" . $this->bsc_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getERC721TransfersByAddress($address,$startblock="0", $endblock="99999999", $sort="desc") {
		    //
			//Get a list of 'Normal' Transactions By Address	
			//tested
			$url="https://api.etherscan.io/api?module=account&action=tokennfttx&address=";
			$url.=$address;
			$url.="&startblock=" . $startblock;
            $url.="&endblock=" . $endblock;
            $url.="&sort=" . $sort . "&apikey=" . $this->eth_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	
	//
	//-- BLOCK BY TIMESTAMP
	//	
	
	function getBSCBlockByTimestamp($date='2021-06-15',$time='00:00:00') {
		// Determine the closest block number based on date and time.
	   $url="https://api.bscscan.com/api?module=block&action=getblocknobytime&timestamp=";
	   $timestamp=$this->getUnixTimestamp($date,$time);
	   $url.=$timestamp;
	   $url.="&closest=before&apikey=K14YJFYWC3NAWWU7J2T2G7KBXNDV993H85";
	   $ch = curl_init();
	   curl_setopt($ch,CURLOPT_URL,$url);
	   curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	   $output=curl_exec($ch);
	   curl_close($ch);
	   $array=json_decode($output,true);
	   return $array['result'];
	}

	function getETHBlockByTimestamp($date='2021-06-15',$time='00:00:00') {
		// Determine the closest block number based on date and time.
	   $url="https://api.etherscan.io/api?module=block&action=getblocknobytime&timestamp=";
	   $timestamp=$this->getUnixTimestamp($date,$time);
	   $url.=$timestamp;
	   $url.="&closest=before&apikey=" . $this->eth_key;
	   $ch = curl_init();
	   curl_setopt($ch,CURLOPT_URL,$url);
	   curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	   $output=curl_exec($ch);
	   curl_close($ch);
	   $array=json_decode($output,true);
	   return $array['result'];
	}
	
	//
	//-- TOKEN ACCOUNT BALANCE
	//	
	
	function getBEP20TokenAccountBalance($contract, $address) {
		    //
			//Get BEP20-Token Account Balance for TokenContractAddress	
			//tested
			$url="https://api.bscscan.com/api?module=account&action=tokenbalance&contractaddress=";
			$url.=$contract;
			$url.="&address=";
			$url.=$address;
            $url.="&tag=latest&apikey=" . $this->bsc_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getERC20TokenAccountBalance($contract, $address) {
		    //
			//Get ERC20-Token Account Balance for TokenContractAddress	
			//tested
			$url="https://api.etherscan.io/api?module=account&action=tokenbalance&contractaddress=";
			$url.=$contract;
			$url.="&address=";
			$url.=$address;
            $url.="&tag=latest&apikey=" . $this->eth_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	
	//
	// TOKEN HOLDERS BY CONTRACT 
	//
	
	function getBEP20TokenHoldersByContractAddress($address) {
		    //
			//Return the current token holder and number of tokens held	
			//tested
			$url="https://api.bscscan.com/api?module=token&action=tokenholderlist&contractaddress=";
			$url.=$address;
            $url.="&page=1&offset=90000&apikey=" . $this->bsc_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getERC20TokenHoldersByContractAddress($address) {
		    //
			//Return the current token holder and number of tokens held	
			//NOT VALID API
			$url="https://api.etherscan.io/api?module=token&action=tokenholderlist&contractaddress=";
			$url.=$address;
            $url.="&page=1&offset=90000&apikey=" . $this->eth_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	
	//
	//-- HISTORICAL TOTAL SUPPLY
	//
	
	function getHistoricalBEP20TotalSupply($contract, $blockno) {
		    //
			//Get Historical BEP20-Token TotalSupply by ContractAddress & BlockNo 	
			//tested
			$url="https://api.bscscan.com/api?module=stats&action=tokensupplyhistory&contractaddress=";
			$url.=$contract;
            $url.="&blockno=" . $blockno . "&apikey=" . $this->bsc_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	
	function getHistoricalERC20TotalSupply($contract, $blockno) {
		    //
			//Get Historical BEP20-Token TotalSupply by ContractAddress & BlockNo 	
			//tested
			$url="https://api.etherscan.io/api?module=stats&action=tokensupplyhistory&contractaddress=";
			$url.=$contract;
            $url.="&blockno=" . $blockno . "&apikey=" . $this->eth_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	
	//
	//-- HISTORICAL TOKEN ACCOUNT BALANCES
	//
	
	function getHistoricalBEP20AccountBalance($contract, $account, $blockno) {
		    //
			//Get Historical BEP20-Token Account Balance for TokenContractAddress by BlockNo
			//tested
			$url="https://api.bscscan.com/api?module=account&action=tokenbalancehistory&contractaddress=";
			$url.=$contract;
			$url.="&address=";
			$url.=$account;
            $url.="&blockno=" . $blockno . "&apikey=" . $this->bsc_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	
	
	function getHistoricalERC20AccountBalance($contract, $account, $blockno) {
		    //
			//Get Historical BEP20-Token Account Balance for TokenContractAddress by BlockNo
			//tested
			$url="https://api.etherscan.io/api?module=account&action=tokenbalancehistory&contractaddress=";
			$url.=$contract;
			$url.="&address=";
			$url.=$account;
            $url.="&blockno=" . $blockno . "&apikey=" . $this->eth_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	
	//
	//-- TOKEN INFOR
	//
	
	function getBEP20TokenInfo($contract) {
		    //
			//Get Historical BEP20-Token TotalSupply by ContractAddress & BlockNo 	
			//tested
			$url="https://api.bscscan.com/api?module=token&action=tokeninfo&contractaddress=";
			$url.=$contract;
            $url.="&apikey=" . $this->bsc_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	function getERC20TokenInfo($contract) {
		    //
			//Get Historical ETH20-Token TotalSupply by ContractAddress & BlockNo 	
			//tested
			$url="https://api.etherscan.io/api?module=token&action=tokeninfo&contractaddress=";
			$url.=$contract;
            $url.="&apikey=" . $this->eth_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	
	//
	//-- ALL BEP20 TOKEN BALANCES
	//
	
	function getAllBEP20TokenBalances($address) {
		    //
			//Get Historical BEP20-Token TotalSupply by ContractAddress & BlockNo 	
			//tested
			$url="https://api.bscscan.com/api?module=account&action=addresstokenbalance&address=";
			$url.=$address;
            $url.="&apikey=" . $this->bsc_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

    //
	//-- 721 BALANCES
	//
	function getAllBEP721Tokens($address) {
		    //
			//Get Address ERC721 Token Holding
			//tested
			$url="https://api.bscscan.com/api?module=account&action=addresstokennftbalance&address=";
			$url.=$address;
            $url.="&apikey=" . $this->bsc_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	
	function getAllERC721Tokens($address) {
		    //
			//Get Address ERC721 Token Holding
			//not api
			$url="https://api.etherscan.io/api?module=account&action=addresstokennftbalance&address=";
			$url.=$address;
            $url.="&apikey=" . $this->eth_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	
    //
	// 721 TOKEN INVENTORY ADDRESS / CONTRACT
	//
	
	function getAllBEP721TokenInventory($address, $contract) {
		    //
			//Get Address ERC721 Token Inventory By Contract Address
			//
			$url="https://api.bscscan.com/api?module=account&action=addresstokennftinventory&address=";
			$url.=$address;
			$url.="&contractaddress=" . $contract;
            $url.="&apikey=" . $this->bsc_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}
	
	function getAllERC721TokenInventory($address, $contract) {
		    //
			//Get Address ERC721 Token Inventory By Contract Address
			//
			$url="https://api.etherscan.io/api?module=account&action=addresstokennftinventory&address=";
			$url.=$address;
			$url.="&contractaddress=" . $contract;
            $url.="&apikey=" . $this->eth_key;
			$curl=curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			$d=curl_exec($curl);			
			curl_close($curl);
			return $d;		
	}

	//
	function getUnixTimestamp($date='2021-06-15', $time='00:00:00') {
		// Reqiired to query BSC Blocks.
			$str=$date . ' ' . $time;
			return strtotime($str);
	}	

	function convertBigNumber($value,$decimals,$precision=4) {
		$n=0-$decimals;
		if ($decimals==6&&strlen($value)<=6) $value=sprintf('%07d', $value);	
		if ($decimals==7&&strlen($value)<=7) $value=sprintf('%08d', $value);
		if ($decimals==8&&strlen($value)<=8) $value=sprintf('%09d', $value);		
		if ($decimals==9&&strlen($value)<=9) $value=sprintf('%010d', $value);
		if ($decimals==10&&strlen($value)<=10) $value=sprintf('%011d', $value);
		if ($decimals==11&&strlen($value)<=11) $value=sprintf('%012d', $value);
		if ($decimals==12&&strlen($value)<=12) $value=sprintf('%013d', $value);		
		if ($decimals==13&&strlen($value)<=13) $value=sprintf('%014d', $value);
		if ($decimals==14&&strlen($value)<=14) $value=sprintf('%015d', $value);
		if ($decimals==15&&strlen($value)<=15) $value=sprintf('%016d', $value);
		if ($decimals==16&&strlen($value)<=16) $value=sprintf('%017d', $value);
		if ($decimals==17&&strlen($value)<=17) $value=sprintf('%018d', $value);
		if ($decimals==18&&strlen($value)<=18) $value=sprintf('%019d', $value);		
		
		$end=substr($value,$n);
		$begin=substr($value,0,strlen($value)-$decimals);
        $output=$begin . "." . substr($end,0,$precision);
        return $output;
	}
	
	function token_exists($token) {
			$f=0;
			foreach($this->symbol_list as $s) {
					if ($s==$token) {
							$f=1;
					}
			}
			return $f;
	}

	function getTXList($address, $startblock, $direction="desc", $chain="BSC") {
		if ($chain=="BSC") {
			$url="https://api.bscscan.com/api?module=account&action=tokentx&address=";
			$url.=$address;
			$url.="&startblock=" . $startblock . "&endblock=900000000&sort=" . $direction . "&apikey=K14YJFYWC3NAWWU7J2T2G7KBXNDV993H85";
		} else {
			$url="https://api.etherscan.io/api?module=account&action=tokentx&address=";			
			$url.=$address;
			$url.="&startblock=" . $startblock . "&endblock=900000000&sort=" . $direction . "&apikey=QY1P3CGUEF5BMMXB2NFEQGIN5N77RC574E";
		}		
		$curl=curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$d=curl_exec($curl);
		$pendingTx="";
		$array=json_decode($d,true);		
		return $array;
	}
    
	function calculateETHTokenBalances($address) {
		$sql="SELECT * FROM ERC20_WALLET_TOKEN WHERE WALLET_ADDRESS = '" . $address . "'";
		$L=$this->sql($sql);
		foreach($L as $l) {
			if ($l['CONTRACT']!="") {
				$url="https://api.etherscan.io/api?module=account&action=tokenbalance&contractaddress=";
				$url.=$l['CONTRACT'];
				$url.="&address=" . $address . "&tag=latest&apikey=QY1P3CGUEF5BMMXB2NFEQGIN5N77RC574E";
				$curl=curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HEADER, false);
				$d=curl_exec($curl);
				$array=json_decode($d,true);	
				$result=$array['result'];
				$divisor=$l['DIVISOR'];
				$balance=$this->convertBigNumber($result,$divisor);
				$sql="UPDATE ERC20_WALLET_TOKEN SET BALANCE = '" . $balance . "' WHERE WALLET_ADDRESS = '" . $address . "'";
				$this->execute($sql);
			}
		}
	}
	
	function calculateBSCTokenBalances($address) {
		$url="https://api.bscscan.com/api?module=account&action=addresstokenbalance&address=";
		$url.=$address;
		$url.="&page=1&offset=100&apikey=K14YJFYWC3NAWWU7J2T2G7KBXNDV993H85";
		$curl=curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		$d=curl_exec($curl);
		$pendingTx="";
		$array=json_decode($d,true);				
        foreach($array['result'] as $a) {
			$sql="SELECT ID FROM BEP20_WALLET_TOKEN WHERE WALLET_ADDRESS = '" . $address . "'";
			$sql.=" AND SYMBOL = '" . $a['TokenSymbol'] . "'";
			$x=$this->sql($sql);
			$post=array();
			$post['TABLE_NAME']="BEP20_WALLET_TOKEN";
			$post['ACTION']="insert";
			if (sizeof($x)>0) {
					$post['ID']=$x[0]['ID'];
			}
			$post['SYMBOL']=$a['TokenSymbol'];
			$post['NAME']=$a['TokenName'];
			$post['BALANCE']=$this->convertBigNumber($a['TokenQuantity'],$a['TokenDivisor']);
			$post['CONTRACT']=$a['TokenAddress'];
			$post['DIVISOR']=$a['TokenDivisor'];			
			$this->post($post);
		}	
		$this->calculate_portfolio($address);
	}
	
	function add_lp_token($address, $lp, $hash="X") {
		$post=array();
		$post['TABLE_NAME']="BEP20_WALLET_LP";
		$post['ACTION']="insert";
		$sql="SELECT ID FROM BEP20_WALLET_LP WHERE WALLET_ADDRESS = '" . $address . "' ";
		$sql.=" AND LP_CONTRACT = '" . $lp . "'";
		$z=$this->sql($sql);
		if (sizeof($z)>0) {
				$post['ID']=$z[0]['ID'];
		}
		$post['CONTRACT']=$lp;
		$post['WALLET_ADDRESS']=$address;
		$post['TRANSACTION']=$hash;		
		$post['LP_CONTRACT']=$lp;		
		$sql="SELECT SYMBOL0, SYMBOL1, AMOUNT0, AMOUNT1 FROM BEP20_TOKEN_PAIR WHERE LP_ADDRESS = '" . $lp . "'";
		$z=$this->sql($sql);
		$name=$z[0]['SYMBOL0'] . '-' . $z[0]['SYMBOL1'];
		$post['NAME']=$name;
		$post['TOKEN0_SYMBOL']=$z[0]['SYMBOL0'];
		$post['TOKEN0_AMOUNT']=$z[0]['AMOUNT0'];		
		$post['TOKEN1_SYMBOL']=$z[0]['SYMBOL1'];		
		$post['TOKEN1_AMOUNT']=$z[0]['AMOUNT1'];
		$this->post($post);
	}
	function add_farm_token($address, $lp) {
		$post=array();
		$post['TABLE_NAME']="BEP20_WALLET_FARM";
		$post['ACTION']="insert";
		$sql="SELECT ID FROM BEP20_WALLET_FARM WHERE WALLET_ADDRESS = '" . $address . "' ";
		$sql.=" AND LP_CONTRACT = '" . $lp . "'";
		$z=$this->sql($sql);
		if (sizeof($z)>0) {
				$post['ID']=$z[0]['ID'];
		}
		$post['CONTRACT']=$lp;
		$post['WALLET_ADDRESS']=$address;
		$sql="SELECT FARM_ID, FARM_NAME, YIELD_APY, YIELD_DAILY FROM BEP20_TOKEN_FARM WHERE LP_ADDRESS = '" . $lp . "'";
		$z=$this->sql($sql);
		$name=$z[0]['SYMBOL0'] . '-' . $z[0]['SYMBOL1'];
		$post['FARM_ID']=$z[0]['FARM_ID'];		
		$post['FARM_NAME']=$z[0]['FARM_NAME'];
		$post['YIELD_APY']=$z[0]['YIELD_APY'];
		$post['YIELD_DAILY']=$z[0]['YIELD_DAILY'];		
		$this->post($post);		
	}	
	
	function processWalletTransactions($address, $chain="BSC") {

		//--- Make a list of symbols tracked for this wallet;

		
        $this->symbol_list=array();
		if ($chain=="BSC") {
		$sql="SELECT SYMBOL FROM BEP20_WALLET_TOKEN WHERE WALLET_ADDRESS = '" . $address . "'";
		} else {
		$sql="SELECT SYMBOL FROM ERC20_WALLET_TOKEN WHERE WALLET_ADDRESS = '" . $address . "'";			
		}
		$w0=$this->sql($sql);
		foreach($w0 as $w) {
			array_push($this->symbol_list,$w['SYMBOL']);
		}
   		
		//--- Get the latest block loaded for this wallet.
		if ($chain=="BSC") {		
			$sql="SELECT * FROM BEP20_TOKEN_LOAD WHERE address = '" . $address . "'";
		} else {
			$sql="SELECT * FROM ERC20_TOKEN_LOAD WHERE address = '" . $address . "'";			
		}
		$user=$this->sql($sql);
		$lastBlock=0;
		$flag=0;
		if (sizeof($user)>0) {
			$lastBlock=$user[0]['lastBlock'];
			$flag=1;
		}

        //-- Execute the Transaction API
		$array=$this->getTXList($address,$lastBlock, "desc", $chain);
		
		$pendingTx="";	
		$i=0;
		if (isset($array['result'])) {
		foreach ($array['result'] as $r) {
			if ($i==0) {
				$post=array();
				if ($chain=="BSC") {
					$post['TABLE_NAME']="BEP20_TOKEN_LOAD";
				} else {
					$post['TABLE_NAME']="ERC20_TOKEN_LOAD";							
				}
				$sql="SELECT ID, lastBlock FROM " . $post['TABLE_NAME'] . " WHERE address = '" . strtolower($address) . "'";
				$L=$this->sql($sql);
				if (sizeof($L)>0) {
					$post['ID']=$L[0]['ID'];
				} else {
						$aa=array();
						$aa['lastBlock']=0;
						array_push($L,$aa);
				}
				$post['ACTION']="insert";
				$post['lastBlock']=$r['blockNumber'];
				$post['address']=strtolower($address);
				if (intval($r['blockNumber'])>intval($L[0]['lastBlock'])) {
					$this->post($post);
				}
			}
	$i++;
	
	if ((int)$r['blockNumber']>(int)$lastBlock) {
		//-- Check to see if to or from is an LP CONTRACT
//		if ($this->is_lp_address($r['from'])) {
//			$this->add_lp_token($address, $r['from'],$r['hash']);
//		}
//		if ($this->is_lp_address($r['to'])) {
//			$this->add_lp_token($address, $r['to'],$r['hash']);			
//		}
//		if ($this->is_farm_address($r['from'])) {
//			if (!$this->is_lp_address($r['from'])) $this->add_farm_token($address, $r['from']);				
//		}
//		if ($this->is_farm_address($r['to'])) {
//			if (!$this->is_lp_address($r['to'])) $this->add_farm_token($address, $r['to']);				
//		}
		
		//-- If TOKEN does not exist in symbol_list add it.
		$k=$this->token_exists($r['tokenSymbol']);
		if ($k==0) {
				$post=array();
				if ($chain=="BSC") {
					$post['TABLE_NAME']="BEP20_WALLET_TOKEN";
				} else {
					$post['TABLE_NAME']="ERC20_WALLET_TOKEN";					
				}
				$post['ACTION']="insert";
				$post['WALLET_ADDRESS']=$address;
				$post['SYMBOL']=$r['tokenSymbol'];
				$post['NAME']=$r['tokenName'];
				$post['CONTRACT']=$r['contractAddress'];
				$this->post($post);
				array_push($this->symbol_list,$r['tokenSymbol']);
		}
		if (strtolower($r['hash'])!=$pendingTx) {
			//Starting a new Transaction.
			$pendingTx=strtolower($r['hash']);
			$post=array();
			if ($chain=="BSC") {
				$post['TABLE_NAME']="BEP20_TRANSACTIONS";
			} else { 
				$post['TABLE_NAME']="ERC20_TRANSACTIONS";			
			}
			$sql="SELECT ID FROM " . $post['TABLE_NAME'] . " WHERE hash = '" . strtolower($r['hash']) . "'";
			$z=$this->sql($sql);
			if (sizeof($z)>0) {
				$post['ID']=$z[0]['ID'];
			}
			$post['ACTION']="insert";
			$post['hash']=strtolower($r['hash']);
			$post['wallet_address']=$address;			
			$post['contractAddress']=strtolower($r['contractAddress']);
			$post['blockNumber']=$r['blockNumber'];
			$post['timestamp']=$r['timeStamp'];		
			if (strtolower($r['from'])==strtolower($address)) {
					// Sending Transaction
                    // Swapping Tokens
                    // Staking Tokens
					$post['fromAddress']=strtolower($address);
					$post['fromTokenName']=$r['tokenName'];
					$post['fromTokenSymbol']=$r['tokenSymbol'];
					$post['fromTokenDecimal']=$r['tokenDecimal'];
					$post['toAddress']=$r['to'];
					$post['toTokenName']=$r['tokenName'];
					$post['toTokenSymbol']=$r['tokenSymbol'];
					$post['toTokenDecimal']=$r['tokenDecimal'];
                    $post['transaction_type']="SEND";                                        
					$tmp_value=$this->convertBigNumber($r['value'],$r['tokenDecimal']);			
					$post['fromValue']=$tmp_value;	
					$post['toValue']=$tmp_value;						
					$post['description']='Sent ' . $r['tokenName'];
					$ID=$this->post($post);
					$this->getPrices($ID,$r['timeStamp'], $post['fromTokenSymbol'], $post['fromValue'], $post['toTokenSymbol'], $post['toValue'],strtolower($r['contractAddress']));						
				} else {			
					$post['toAddress']=strtolower($address);					
					$post['toTokenName']=$r['tokenName'];
					$post['toTokenSymbol']=$r['tokenSymbol'];
					$post['toTokenDecimal']=$r['tokenDecimal'];						
					$post['fromAddress']=$r['from'];
					$post['fromTokenName']=$r['tokenName'];
					$post['fromTokenSymbol']=$r['tokenSymbol'];
					$post['fromTokenDecimal']=$r['tokenDecimal'];
                    $post['transaction_type']="RECEIVE"; 				
					$tmp_value=$this->convertBigNumber($r['value'],$r['tokenDecimal']);				
					$post['fromValue']=$tmp_value;
					$post['toValue']=$tmp_value;				
					$post['description']='Received ' . $r['tokenName'];					
					$ID=$this->post($post);	
  	     			$this->getPrices($ID,$r['timeStamp'], $post['fromTokenSymbol'], $post['fromValue'], $post['toTokenSymbol'], $post['toValue'],strtolower($r['contractAddress']));							
				}
			} else {
			//Working a Pending Transaction
			$sql="SELECT ID, fromTokenSymbol, toTokenSymbol, contractAddress FROM BEP20_TRANSACTIONS WHERE hash = '" . strtolower($r['hash']) . "'";
			$tx=$this->sql($sql);
			$post=array();
			if ($chain=="BSC") {
				$post['TABLE_NAME']="BEP20_TRANSACTIONS";
			} else {
				$post['TABLE_NAME']="ERC20_TRANSACTIONS";				
			}
			$post['ACTION']="insert";		
	                $from="";
			$to="";
			if (sizeof($tx)>0) {
			   $post['ID']=$tx[0]['ID'];	
			   $from=$tx[0]['fromTokenSymbol'];
			   $to=$tx[0]['toTokenSymbol'];			   
			}
			if (strtolower($r['from'])==strtolower($address)) {					
					$post['fromAddress']=strtolower($address);
					$post['fromTokenName']=$r['tokenName'];
					$post['fromTokenSymbol']=$r['tokenSymbol'];
					$post['fromTokenDecimal']=$r['tokenDecimal'];				
					$tmp_value=$this->convertBigNumber($r['value'],$r['tokenDecimal']);								
					$post['fromValue']=$tmp_value;
					if ($this->is_farm_address(strtolower($r['to']))) {
						$post['description']="Staked " . $r['tokenSymbol'] . " for " . $from;
						$post['transaction_type']="FARM-STAKE";						
					} else {
						$post['description']="Swapped " . $r['tokenSymbol'] . " for " . $from;
						$post['transaction_type']="SWAP";
					}
					$ID=$this->post($post);
					$this->getPrices($ID, $r['timeStamp'], $post['fromTokenSymbol'],  $post['fromValue'], "", "", $r['contractAddress']);					
			} else {
					// Receiving Transaction					
					$post['toAddress']=strtolower($address);					
					$post['toTokenName']=$r['tokenName'];
					$post['toTokenSymbol']=$r['tokenSymbol'];
					$post['toTokenDecimal']=$r['tokenDecimal'];	
					$post['description']="Unstaked " . $to . " sent " . $from;										
					$tmp_value=$r['value'];
					$tmp_value=$this->convertBigNumber($r['value'],$r['tokenDecimal']);	
					$post['transaction_type']="UNSTAKE";
					$post['toValue']=$tmp_value;				
					$ID=$this->post($post);	
					$this->getPrices($ID, $r['timeStamp'], "", "", $post['toTokenSymbol'], $post['toValue'], $r['contractAddress']);
					$sql="SELECT ID, fromAddress, toAddress FROM BEP20_TRANSACTIONS WHERE hash = '" . strtolower($r['hash']) . "'";
					$tx2=$this->sql($sql);
					if ($this->is_lp_address($tx2[0]['fromAddress'])) {
						$this->add_lp_token($address, $tx2[0]['fromAddress'],strtolower($r['hash']));
					}
					if ($this->is_lp_address($tx2[0]['toAddress'])) {
						$this->add_lp_token($address, $tx2[0]['toAddress'],strtolower($r['hash']));			
					}
					if ($this->is_farm_address($tx2[0]['fromAddress'])) {
						if (!$this->is_lp_address($tx2[0]['fromAddress'])) $this->add_farm_token($address, $tx2[0]['fromAddress']);				
					}
					if ($this->is_farm_address($tx2[0]['toAddress'])) {
						if (!$this->is_lp_address($tx2[0]['toAddress'])) $this->add_farm_token($address, $tx2[0]['toAddress']);				
					}					
			}
		} // Working a Pending Transaction
	}  // Block Number > Last Block
 } // For Each Result
	} // ISSET Result
} // End or Function 
	
	function getBSCBlock($date='2021-06-15',$time='00:00:00') {
		// Determine the closest block number based on date and time.
	   $url="https://api.bscscan.com/api?module=block&action=getblocknobytime&timestamp=";
	   $timestamp=$this->getUnixTimestamp($date,$time);
	   $url.=$timestamp;
	   $url.="&closest=before&apikey=K14YJFYWC3NAWWU7J2T2G7KBXNDV993H85";
	   $ch = curl_init();
	   curl_setopt($ch,CURLOPT_URL,$url);
	   curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	   $output=curl_exec($ch);
	   curl_close($ch);
	   $array=json_decode($output,true);
	   return $array['result'];
	}
	
	function getContractBalance(
		// Get the token balance of a contract as of a date and time.
		$contract="0x6D2baf9b208967Bd769Ce71D6c772CD7FeFE4C28",
		$address="0x951F430EebB6865467969Fa1Cf5f1625d717A2d1",
		$date='2021-06-15',
		$time='00:00:00') {
			$block=$this->getBSCBlock($date,$time);
			$url="https://api.bscscan.com/api?module=account&action=tokenbalancehistory&contractaddress=";
			$url.=$contract;
			$url.="&address=" . $address;
			$url.="&blockno=" . $block;
			$url.="&apikey=K14YJFYWC3NAWWU7J2T2G7KBXNDV993H85";
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			$output=curl_exec($ch);
			curl_close($ch);
			$array=json_decode($output,true);
			$j=$array['result'];
			if (strlen($j)<18) { $j=sprintf('%018d', $j); }
			$end=substr($j,-18);
			$begin=substr($j,0,strlen($j)-18);			
			$r=$begin . '.' . $end;
			return $r;			
	}
	
	function calculate_portfolio($wallet) {
			$sql="SELECT * FROM BEP20_WALLET_TOKEN WHERE WALLET_ADDRESS = '" . $wallet . "'";
			$g=$this->sql($sql);
			foreach($g as $h) {
					$post=array();
					$post['TABLE_NAME']="BEP20_WALLET_TOKEN";
					$post['ACTION']="insert";
					$post['ID']=$h['ID'];
					$token=$h['SYMBOL'];
					//-- Count Transactions
					$sql="SELECT COUNT(*) AS C FROM BEP20_TRANSACTIONS WHERE ";
					$sql.=" wallet_address = '" . $wallet . "' AND (";
					$sql.=" fromTokenSymbol = '" . $token . "' OR toTokenSymbol = '" . $token . "')";
					$d=$this->sql($sql);
					$post['TRANSACTIONS']=$d[0]['C'];
					//-- SPEND
					$sql="SELECT toValue, toValueUSD FROM BEP20_TRANSACTIONS WHERE toAddress = '" . $wallet . "' ";
					$sql.="AND toTokenSymbol = '" . $token . "'";
					$d=$this->sql($sql);
					$count=0;
					$value=0;
					foreach($d as $e) {
							$count+=floatval($e['toValue']);
							$value+=floatval($e['toValueUSD']);
					}
					$count=round($count,4);
					$buy_count=$count;
					$value=round($value,4);
					$buy_value=$value;
					if ($value>0) {
						$price=$count / $value;
					} else {
						$price=0;
					}
					$price=round($price,4);
					
					$post['PURCHASED']=number_format($count,4);
					$post['TOTAL_RECEIVED']="$" . number_format($value,4);
					$post['AVG_PURCHASE_PRICE']="$" . number_format($price,4);
					//--SELL
					$sql="SELECT fromValue, fromValueUSD FROM BEP20_TRANSACTIONS WHERE fromAddress = '" . $wallet . "' ";
					$sql.="AND fromTokenSymbol = '" . $token . "'";
					$d=$this->sql($sql);
					$count=0;
					$value=0;
					foreach($d as $e) {
							$count+=floatval($e['fromValue']);
							$value+=floatval($e['fromValueUSD']);
					}
					$count=round($count,4);
				    $sell_count=$count;
					$value=round($value,4);
					$sell_value=$value;
					if ($value>0) {
						$price=$count / $value;
					} else {
						$price=0;
					}
					$price=round($price,4);
					$post['SOLD']=number_format($count,4);
					$post['TOTAL_SENT']="$" . number_format($value,4);
					$post['AVG_SALE_PRICE']="$" . number_format($price,4);
					$sql="SELECT TOKEN_PRICE_USD FROM BEP20_PRICE WHERE SYMBOL = '" . $token . "' ORDER BY ID DESC";
					$z=$this->sql($sql);
					"$" . number_format($value,4);
                                        if (isset($z[0])) $current_price=floatval($z[0]['TOKEN_PRICE_USD']); else $current_price=0;
					$current_price=round($current_price,4);
					$post['CURRENT_PRICE']="$" . number_format($current_price,4);
					$balance=floatval($h['BALANCE']);
					$current_value=$balance*$current_price;
					$current_value=round($current_value,4);
					$basis_value=$buy_value-$sell_value;
					if ($balance > 0) {
						$cost_avg=$basis_value / $balance;
					} else {
						$cost=0;
					}
					$cost_avg=round($cost_avg,4);
					$post['COST_AVG']="$" . number_format($cost_avg,4);
					$post['CURRENT_VALUE']="$" . number_format($current_value,4);
					$this->post($post);
			}
	}
	
	function checkWalletStatus($id) {
		$sql="SELECT ID FROM GOATX_WALLET WHERE wallet_address = '" . strtolower($id) . "'";
		$z=$this->sql($sql);
		if (sizeof($z)==0) {
				$post=array();
				$post['TABLE_NAME']="GOATX_WALLET";
				$post['ACTION']="insert";
				$post['wallet_address']=strtolower($id);
				$this->post($post);
		}
	}
	
	function getToken($address) {
		
	}
	
	function getTokenPrice($symbol,$timestamp,$value) {
                //$this->writelog("begin get token price");
		$output=array();
		$output['price']=0;
		$output['timestamp']=0;
		$output['total']=0;			
                if (strpos($symbol,'\x')) {
				$output['price']=0;
				$output['price']=number_format($output['price'],4);					
				$output['timestamp']=0;
				$output['total']=0;				
				$output['total']=number_format($output['total'],4);					
                                return $output;
                }

		if ($symbol!="GOATX") {
			$sql="select timestamp, price from GECKO_HISTORICAL_PRICES WHERE symbol = '" . $symbol . "' ";
			$sql.="and timestamp <= " . $timestamp . " ORDER BY timestamp DESC";
			$z=$this->sql($sql);
			if (sizeof($z)>0) {
				$output['price']=round($z[0]['price'],5);
				$output['price']=number_format($output['price'],4);						
				$output['timestamp']=$z[0]['timestamp'];
				$output['total']=round(floatval($value) * floatval($output['price']),4);
				$output['total']=number_format($output['total'],4);
            } else {
				$output['price']=0;
				$output['price']=number_format($output['price'],4);						
				$output['timestamp']=0;
				$output['total']=0;
				$output['total']=number_format($output['total'],4);					
			}
		} else {
			$sql="select BLOCK_TIMESTAMP, GOATX_PRICE from GOATX_EXCHANGE_DATA ";
			$sql.="WHERE BLOCK_TIMESTAMP <= " . $timestamp . "  ORDER BY BLOCK_TIMESTAMP  DESC";	
			$z=$this->sql($sql);
			if (sizeof($z)>0) {
				$output['price']=round($z[0]['GOATX_PRICE'],5);
				$output['value']=$value;
				$output['timestamp']=$z[0]['BLOCK_TIMESTAMP'];
				$output['total']=round(floatval($value) * floatval($output['price']),4);	
				$g=floatval(round($z[0]['GOATX_PRICE'],5))*floatval($value);		
				$output['total']=number_format($g,4);		
				$output['price']=number_format($output['price'],4);					
			} else {
				$output['price']=0;
				$output['price']=number_format($output['price'],4);					
				$output['timestamp']=0;
				$output['total']=0;				
				$output['total']=number_format($output['total'],4);					
			}
		}
                //$this->writelog("end get token price");
		return $output;
	}
	
    function getPrices($ID, $timestamp, $fromTokenSymbol, $fromValue, $toTokenSymbol, $toValue, $address) {
		
	if ($fromTokenSymbol!=""&&$fromTokenSymbol!="GOATX") {
			$sql="select timestamp, price from GOATX_TOKEN_CONTRACT_PRICE WHERE contract_address = '" . $address . "' ";
			$sql.="and CONVERT(timestamp,DECIMAL) <= " . $timestamp . " ORDER BY CONVERT(timestamp,DECIMAL) DESC";
			$z=$this->sql($sql);
			if (sizeof($z)>0) {
				$fromPrice=round($z[0]['price'],5);
				$fromTimestamp=$z[0]['timestamp'];
				$fromValueUSD=round(floatval($fromValue) * floatval($fromPrice),4);
				$post=array();
				$post['TABLE_NAME']="BEP20_TRANSACTIONS";
				$post['ACTION']="insert";
				$post['ID']=$ID;
				$post['fromPrice']=$fromPrice;
				$post['fromTimestamp']=$fromTimestamp;
				$post['fromValueUSD']=$fromValueUSD;
				$this->post($post);
			} else {
				$post=array();
				$post['TABLE_NAME']="BEP20_TRANSACTIONS";
				$post['ACTION']="insert";
				$post['ID']=$ID;
				$post['fromPrice']='0.0000';
				$post['fromTimestamp']=$fromTimestamp;
				$post['fromValueUSD']='0.0000';
				$this->post($post);				
			}
	}
	if ($toTokenSymbol!=""&&$toTokenSymbol!="GOATX") {
			$sql="select timestamp, price from GOATX_TOKEN_CONTRACT_PRICE WHERE contract_address = '" . $address . "' ";
			$sql.="and CONVERT(timestamp,DECIMAL) <= " . $timestamp . " ORDER BY CONVERT(timestamp,DECIMAL) DESC";
			$z=$this->sql($sql);
			if (sizeof($z)>0) {
				$toPrice=round($z[0]['price'],5);
				$toTimestamp=$z[0]['timestamp'];
				$toValueUSD=round(floatval($toValue) * floatval($toPrice),4);
				$post=array();
				$post['TABLE_NAME']="BEP20_TRANSACTIONS";
				$post['ACTION']="insert";
				$post['ID']=$ID;
				$post['toPrice']=$toPrice;
				$post['toTimestamp']=$toTimestamp;
				$post['toValueUSD']=$toValueUSD;
				$this->post($post);
			}  else {
				$post=array();
				$post['TABLE_NAME']="BEP20_TRANSACTIONS";
				$post['ACTION']="insert";
				$post['ID']=$ID;
				$post['fromPrice']='0.0000';
				$post['fromTimestamp']=$toTimestamp;
				$post['fromValueUSD']='0.0000';
				$this->post($post);				
			}
	}
	}
	
	function connect() {
		$cs=file_get_contents("/var/www/vault/cs.json");
		$c=json_decode($cs,true);
		$this->dbh = new PDO($c['cs'], $c['un'], $c['pwd']);
		$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
		return $this->dbh;	
	}
	
	function query($table,$where="",$order="") {
		$output=array();
		if ($table=="") {
			return $output;
		} else {
	
			$sql = "SELECT * from " . $table . " where 1 = 1 ";
			if ($where != "") $sql .= " and " . $where;
			if ($order != "") $sql .= " order by " . $order;
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$output = array();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);	
			foreach ($results as $result) {
				$r = array();
				foreach ($result as $name => $value) {
				if ($name!="json") {
					$r[$name]=$value;
				} else {
					if ($value!="") {
						$resultJSON=json_decode($value,true);
						foreach ($resultJSON as $nameJSON => $valueJSON) $r[$nameJSON]=$valueJSON;					
					}
				}
			}
			array_push($output,$r);	
			
			}
			return $output;	
		}
	}
	
	function sql($s="") {
		$output=array();
		if ($s=="") {
			return $output;
		} else {
			$stmt = $this->db->prepare($s);
                        try {
			    $stmt->execute();
                        } catch(Exception $e) {
                          return array();
                        }
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);	
			return $results;	
		}
	}

	function sql0($s="") {
		$output=array();
		if ($s=="") {
			return '0';
		} else {
			$stmt = $this->db->prepare($s);
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);	
			return $results[0];	
		}
	}

	function sqlC($s="") {
		$output=array();
		if ($s=="") {
			return 0;
		} else {
			$stmt = $this->db->prepare($s);
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);	
			return $results[0]['c'];	
		}
	}
	
	function execute($s) {
		$stmt = $this->db->prepare($s);
		$stmt->execute();
	}	

	function execute0($s) {
		$stmt = $this->db->prepare($s);
		$stmt->execute();
	        return $this->db->lastInsertId();
	}	


	function update($s,$p) {
		$stmt = $this->db->prepare($s);
		$stmt->bindParam(1, $p);		
		$stmt->execute();
	}	
	
	function isTableColumn($name,$columns) {
		$result=false;
		foreach ($columns as $column) {
			if ($name==$column['Field']) {
				$result=true;
			}
		}
		return $result;
	}
	
	function post($POST) {	
        if (isset($POST['TABLE_NAME'])) $POST['table_name']=$POST['TABLE_NAME'];
		$output=array();
		if (!isset($POST['action'])) $POST['action']="insert";
		if (!isset($POST['id'])) $POST['id']="";
		if (isset($POST['ID'])) $POST['id']=$POST['ID']; else $POST['ID']="";
		if (!isset($POST['table_name'])) 
		{
			$output['result']='Failed';
        } else
		{
		$sql = "SHOW COLUMNS FROM " . $POST['table_name'];
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);	

		if ($POST['action']!="delete") {
				// If there is not 'id' value, record is inserted
				if ($POST['ID']==""||$POST['ID']=="0") {
					$sql = "insert into " . $POST['table_name'] . " (create_timestamp) values (now())";
					$stmt = $this->db->prepare($sql);
					$stmt->execute();
					$POST['ID'] = $this->db->lastInsertId();
					$output['result']="insert";
				} else {
					$output['result']="update";					
				}

				foreach ($POST as $name => $value) {
					if ($name!="ID"&&$name!="CREATE_TIMESTAMP"&&$name!="table_name"&&$name!="action") {
						if ($this->isTableColumn($name,$columns)) {
							$sql = "update " . $POST['table_name'] . " set " . $name . " = ? where ID = ?";
							$stmt = $this->db->prepare($sql);
					         	$stmt->bindParam(1, $value);
							$stmt->bindParam(2, $POST['ID']);
							$stmt->execute();
						} 
					}
				}
			} 
			else {
				$sql = "delete from " . $POST['table_name'] . " where id = ?";
				$stmt = $this->db->prepare($sql);
				$stmt->bindParam(1, $POST['id']);
				$stmt->execute();
				$output['result']="update";
			}	
		}
		return $POST['id'];
	}
	
	function txlistPostN($POST, $wallet_address, $chain="BSC") {
		
	    $POST['TABLE_NAME']="BEP20_RAW_TRANSACTIONS";
		$POST['walletAddress']=strtolower($wallet_address);
		$POST['source']="txlist";
		$POST['CHAIN']=$chain;
		$output=array();

                $sql="SELECT ID FROM BEP20_RAW_TRANSACTIONS WHERE hash = '" . $POST['hash'];
                $sql.="' AND walletAddress = '" . $POST['walletAddress'] . "' ";
		$sql.="AND source = 'txlist' ";
		$c=$this->sql($sql);
		if (sizeof($c)>0) {
			$POST['ID']=$c[0]['ID'];
		} else {
                        $POST['ID']="";
                }
		
		if ($POST['ID']==""||$POST['ID']=="0") {
                        $top = "INSERT INTO BEP20_RAW_TRANSACTIONS (CREATE_TIMESTAMP, ";
                        $bottom = " VALUES (now(), ";
		        foreach ($POST as $name => $value) {
			    if ($name=="from") $name="bnbFrom";
			    if ($name=="to") $name="bnbTo";
			    if ($name=="value") $name="bnbValue";
		            if ($name!="input"&&
                                $name!="txreceipt_status"&&
                                $name!="isError"&&
                                $name!="transactionIndex"&&
                                $name!="ID"&&
                                $name!="CREATE_DATE"&&
                                $name!="TABLE_NAME"&&$name!="ACTION"&&$name!="source") {
                                $top .= $name . ", ";
                                $bottom .= "'" . $value . "',";
                            }
			}
                        $top .= " source) ";
                        $bottom .= " 'txlist') ";
                        $sql=$top . $bottom;
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$POST['ID'] = $this->db->lastInsertId();
		} else {
                        $top = "UPDATE BEP20_RAW_TRANSACTIONS SET ";
		        foreach ($POST as $name => $value) {
			    if ($name=="from") $name="bnbFrom";
			    if ($name=="to") $name="bnbTo";
			    if ($name=="value") $name="bnbValue";
		            if ($name!="input"&&$name!="txreceipt_status"&&
                                $name!="isError"&&
                                $name!="transactionIndex"&&
                                $name!="ID"&&
                                $name!="CREATE_DATE"&&$name!="TABLE_NAME"&&$name!="ACTION"&&$name!="source") {
                                $top .= $name . " = '" . $value . "', ";
                            }
			}
                        $top .= " source = 'txlist' WHERE ID = " . $POST['ID'];
                        $sql=$top;
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$POST['ID'] = $this->db->lastInsertId();
                }
			
	    $this->writelog("NEW txlist");	
		return $POST['ID'];
	}

	function internalPost($POST, $wallet_address) {
		
	    $POST['TABLE_NAME']="BEP20_RAW_TRANSACTIONS";
		$POST['walletAddress']=strtolower($wallet_address);
		$POST['source']="internal";
		$output=array();

        $sql="SELECT ID FROM BEP20_RAW_TRANSACTIONS WHERE hash = '" . $POST['hash'];
        $sql.="' AND walletAddress = '" . $POST['walletAddress'] . "' ";
		$sql.="AND source = 'internal' ";
		$c=$this->sql($sql);
		if (sizeof($c)>0) {
			$POST['ID']=$c[0]['ID'];
		}
		
		if ($POST['ID']==""||$POST['ID']=="0") {
			$sql = "insert into " . $POST['TABLE_NAME'] . " (CREATE_TIMESTAMP) values (now())";
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$POST['ID'] = $this->db->lastInsertId();
		}

		$sql = "SHOW COLUMNS FROM " . $POST['TABLE_NAME'];
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);	
		
		foreach ($POST as $name => $value) {
			if ($name=="from") $name="bnbFrom";
			if ($name=="to") $name="bnbTo";
			if ($name=="value") $name="bnbValue";
			
			if ($name!="ID"&&$name!="CREATE_DATE") {
				if ($this->isTableColumn($name,$columns)) {
					$sql = "update " . $POST['TABLE_NAME'] . " set " . $name . " = ? where ID = ?";
					$stmt = $this->db->prepare($sql);
					$stmt->bindParam(1, $value);
					$stmt->bindParam(2, $POST['ID']);
					$stmt->execute();
				}
			}
		}
	    $this->writelog("OLD INTERNAL");	
		return $POST['ID'];
	}
	
	function internalPostN($POST, $wallet_address, $chain="BSC") {
		
	    $POST['TABLE_NAME']="BEP20_RAW_TRANSACTIONS";
		$POST['walletAddress']=strtolower($wallet_address);
		$POST['source']="internal";
		$POST['CHAIN']=$chain;
		$output=array();

        $sql="SELECT ID FROM BEP20_RAW_TRANSACTIONS WHERE hash = '" . $POST['hash'];
        $sql.="' AND walletAddress = '" . $POST['walletAddress'] . "' ";
		$sql.="AND source = 'internal' AND CHAIN = '" . $chain . "'";
		$c=$this->sql($sql);
		if (sizeof($c)>0) {
			$POST['ID']=$c[0]['ID'];
		} else {
                        $POST['ID']="";
                }
		
		if ($POST['ID']==""||$POST['ID']=="0") {
                        $top = "INSERT INTO BEP20_RAW_TRANSACTIONS (CREATE_TIMESTAMP, ";
                        $bottom = " VALUES (now(), ";
		        foreach ($POST as $name => $value) {
			    if ($name=="from") $name="bnbFrom";
			    if ($name=="to") $name="bnbTo";
			    if ($name=="value") $name="bnbValue";
		            if ($name!="input"&&$name!="txreceipt_status"&&
                                $name!="isError"&&
                                $name!="type"&&
                                $name!="traceId"&&
                                $name!="errCode"&&
                                $name!="transactionIndex"&&
                                $name!="ID"&&
                                $name!="CREATE_DATE"&&$name!="TABLE_NAME"&&$name!="ACTION"&&$name!="source") {
                                $top .= $name . ", ";
                                $bottom .= "'" . $value . "',";
                            }
			}
                        $top .= " source) ";
                        $bottom .= " 'internal') ";
                        $sql=$top . $bottom;
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$POST['ID'] = $this->db->lastInsertId();
		} else {
                        $top = "UPDATE BEP20_RAW_TRANSACTIONS SET ";
		        foreach ($POST as $name => $value) {
			    if ($name=="from") $name="tokenFrom";
		   	    if ($name=="to") $name="tokenTo";
			    if ($name=="value") $name="tokenValue";
		            if ($name!="input"&&$name!="txreceipt_status"&&
                                $name!="isError"&&
                                $name!="transactionIndex"&&
                                $name!="type"&&
                                $name!="errCode"&&
                                $name!="traceId"&&
                                $name!="transactionIndex"&&
                                $name!="ID"&&
                                $name!="CREATE_DATE"&&$name!="TABLE_NAME"&&$name!="ACTION"&&$name!="source") {
                                $top .= $name . " = '" . $value . "', ";
                            }
			}
                        $top .= " source = 'internal' WHERE ID = " . $POST['ID'];
                        $sql=$top;
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$POST['ID'] = $this->db->lastInsertId();
                }
	    $this->writelog("NEW INTERNAL");	
		return $POST['ID'];
	}
	
	function tokentxPost($POST,$wallet_address) {
		
	    $POST['TABLE_NAME']="BEP20_RAW_TRANSACTIONS";
		$POST['source']="tokentx";
		$POST['walletAddress']=strtolower($wallet_address);
	
		$output=array();

        $sql="SELECT ID FROM BEP20_RAW_TRANSACTIONS WHERE hash = '" . $POST['hash'];
        $sql.="' AND walletAddress = '" . $POST['walletAddress'] . "' ";
		$sql.="AND source = 'tokentx' ";
		$sql.="AND tokenFrom = '" . $POST['from'] . "' ";
		$sql.="AND tokenTo = '" . $POST['to'] . "' ";
		$sql.="AND contractAddress = '" . $POST['contractAddress'] . "'";
		$c=$this->sql($sql);
		if (sizeof($c)>0) {
			$POST['ID']=$c[0]['ID'];
		} else {
                        $POST['ID']="";
                }
		
		if ($POST['ID']==""||$POST['ID']=="0") {
			$sql = "insert into " . $POST['TABLE_NAME'] . " (CREATE_TIMESTAMP) values (now())";
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$POST['ID'] = $this->db->lastInsertId();
		}

		$sql = "SHOW COLUMNS FROM " . $POST['TABLE_NAME'];
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);	
		
		foreach ($POST as $name => $value) {
			if ($name=="from") $name="tokenFrom";
			if ($name=="to") $name="tokenTo";
			if ($name=="value") $name="tokenValue";
			
			if ($name!="ID"&&$name!="CREATE_DATE") {
				if ($this->isTableColumn($name,$columns)) {
					$sql = "update " . $POST['TABLE_NAME'] . " set " . $name . " = ? where ID = ?";
					$stmt = $this->db->prepare($sql);
					$stmt->bindParam(1, $value);
					$stmt->bindParam(2, $POST['ID']);
					$stmt->execute();
				}
			}
		}
	    $this->writelog("OLD tokent");	
		return $POST['ID'];
	}
	
	function tokentxPostN($POST,$wallet_address, $chain="BSC") {
		
	    $POST['TABLE_NAME']="BEP20_RAW_TRANSACTIONS";
		$POST['source']="tokentx";
		$POST['walletAddress']=strtolower($wallet_address);
		$POST['CHAIN']=$chain;
	
		$output=array();
  
                $sql="SELECT ID FROM BEP20_RAW_TRANSACTIONS WHERE hash = '" . $POST['hash'];
                $sql.="' AND walletAddress = '" . $POST['walletAddress'] . "' ";
		$sql.="AND source = 'tokentx' ";
		$sql.="AND tokenFrom = '" . $POST['from'] . "' ";
		$sql.="AND tokenTo = '" . $POST['to'] . "' ";
		$sql.="AND contractAddress = '" . $POST['contractAddress'] . "'";
		$c=$this->sql($sql);
		if (sizeof($c)>0) {
			$POST['ID']=$c[0]['ID'];
		} else {
                        $POST['ID']="";
                }
		
		if ($POST['ID']==""||$POST['ID']=="0") {
                        $top = "INSERT INTO BEP20_RAW_TRANSACTIONS (CREATE_TIMESTAMP, ";
                        $bottom = " VALUES (now(), ";
		        foreach ($POST as $name => $value) {
			    if ($name=="from") $name="tokenFrom";
		   	    if ($name=="to") $name="tokenTo";
			    if ($name=="value") $name="tokenValue";
		            if ($name!="input"&&$name!="txreceipt_status"&&
                                $name!="isError"&&
                                $name!="transactionIndex"&&
                                $name!="ID"&&
                                $name!="CREATE_DATE"&&$name!="TABLE_NAME"&&$name!="ACTION"&&$name!="source") {
                                $top .= $name . ", ";
                                $bottom .= "'" . str_replace("'","",$value) . "',";
                            }
			}
                        $top .= " source) ";
                        $bottom .= " 'tokentx') ";
                        $sql=$top . $bottom;
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$POST['ID'] = $this->db->lastInsertId();
		} else {
                        $top = "UPDATE BEP20_RAW_TRANSACTIONS SET ";
		        foreach ($POST as $name => $value) {
			    if ($name=="from") $name="tokenFrom";
		   	    if ($name=="to") $name="tokenTo";
			    if ($name=="value") $name="tokenValue";
		            if ($name!="input"&&$name!="txreceipt_status"&&
                                $name!="isError"&&
                                $name!="transactionIndex"&&
                                $name!="ID"&&
                                $name!="CREATE_DATE"&&$name!="TABLE_NAME"&&$name!="ACTION"&&$name!="source") {
                                $top .= $name . " = '" . str_replace("'","",$value) . "', ";
                            }
			}
                        $top .= " source = 'tokentx' WHERE ID = " . $POST['ID'];
                        $sql=$top;
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$POST['ID'] = $this->db->lastInsertId();
                }
	    $this->writelog("NEW txlist");	
	}
	
	function teDelete($POST) {
			//--
			//-- delete transaction entries for user id, transaction id, and wallet address.
			//
			$sql="DELETE FROM GOATX_TRANSACTION_ENTRIES WHERE uid = " . $POST['uid'] . " AND transaction_id = '" . $POST['transaction_id'] . "' AND ";
			$sql.=" wallet_address = '" . $_POST['wallet_address'] . "'";
			$this->execute($sql);		
	}
	
	function tePost($POST) {
			//required:
			//-- uid
			//-- transaction_id	
			//-- wallet_address
			//-- timestamp 
			//-- account
			//-- amount 

			//--
			//-- Get Previous Balance
			//--
			$previous_balance='0';			
			$sql="SELECT new_balance FROM GOATX_TRANSACTION_ENTRIES WHERE ";
			$sql.=" uid = " . $POST['uid'] . ", ";
			$sql.=" wallet_address = '" . $POST['wallet_address'] . "', ";
			$sql.=" and account = '" . $POST['account'] . "' AND timestamp <= " . $POST['TIMESTAMP'] . " AND ";
			$sql.=" transaction_id <> '" . $POST['transaction_id'] . "' ORDER BY timestamp DESC";
			$z=$this->sql($sql);
			if (sizeof($z)>0) {
			     $previous_balance=$z[0]['new_balance'];
			}
			$new_balance=floatval($previous_balance)+floatval($POST['amount']);
			
            $sql="INSERT INTO GOATX_TRANSACTION_ENTRIES (";
			$sql.="uid, ";
			$sql.="transaction_id, ";
			$sql.="wallet_address, ";
			$sql.="timestamp, ";
			$sql.="account, ";
			$sql.="amount, ";
			$sql.="previous_balance, ";
			$sql.="new_balance) "; 			
			$sql.=" VALUES (" . $POST['uid'] . ",'";
			$sql.=$POST['transaction_id'] . "','";
			$sql.=$POST['wallet_address'] . "',";
			$sql.=$POST['timestamp'] . ", ";
			$sql.="'" . $POST['account'] . "','";
			$sql.=number_format($POST['amount'],4) . "','";
			$sql.=number_format($previous_balance,4) . "','";
			$sql.=number_format($new_balance,4) . "')";
			$this->execute($sql);
			
	}
	
	function bep20Post($POST) {
		
	    //$this->writelog("begin bep20 post");	
	    $POST['TABLE_NAME']="BEP20_TRANSACTIONS";
		//$POST['walletAddress']=$POST['address'];
		$output=array();
        if (!isset($POST['seq'])) $POST['seq']=0;
        $sql="SELECT ID FROM BEP20_TRANSACTIONS WHERE hash = '" . $POST['hash'];
        $sql.="' AND wallet_address = '" . $POST['wallet_address'] . "' ";
		$sql.="AND seq = " . $POST['seq'] . " ";
		$c=$this->sql($sql);
		if (sizeof($c)>0) {
			$POST['ID']=$c[0]['ID'];
		} else {
                        $POST['ID']="";
                } 
		
		if ($POST['ID']==""||$POST['ID']=="0") {
			$sql = "insert into " . $POST['TABLE_NAME'] . " (CREATE_TIMESTAMP) values (now())";
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$POST['ID'] = $this->db->lastInsertId();
		}

		$sql = "SHOW COLUMNS FROM " . $POST['TABLE_NAME'];
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);	
		
		foreach ($POST as $name => $value) {
			
			if ($name!="ID"&&$name!="CREATE_DATE") {
				if ($this->isTableColumn($name,$columns)) {
					$sql = "update " . $POST['TABLE_NAME'] . " set " . $name . " = ? where ID = ?";
					$stmt = $this->db->prepare($sql);
					$stmt->bindParam(1, $value);
					$stmt->bindParam(2, $POST['ID']);
					$stmt->execute();
				}
			}
		}
	    $this->writelog("bep20 post");	
		return $POST['ID'];
	}
	
	function bep20PostN($POST,$chain) {
		$output=array();
        if (!isset($POST['seq'])) $POST['seq']=0;
		if ($POST['fromTokenDecimal']=="") $POST['fromTokenDecimal']=18;
		if ($POST['toTokenDecimal']=="") $POST['toTokenDecimal']=18;		
	$POST['CHAIN']=$chain;	
        $sql="SELECT ID FROM BEP20_TRANSACTIONS WHERE hash = '" . $POST['hash'];
        $sql.="' AND wallet_address = '" . $POST['wallet_address'] . "' ";
		$sql.="AND seq = " . $POST['seq'] . " ";
		$c=$this->sql($sql);
		if (sizeof($c)>0) {
			$POST['ID']=$c[0]['ID'];
		} else {
                        $POST['ID']="";
                } 
		
		if ($POST['ID']==""||$POST['ID']=="0") {
                        $top = "INSERT INTO BEP20_TRANSACTIONS (CREATE_TIMESTAMP, ";
                        $bottom = " VALUES (now(), ";
		        foreach ($POST as $name => $value) {
		            if ($name!="ID"&&$name!="CREATE_DATE"&&$name!="TABLE_NAME"&&$name!="ACTION"&&$name!="VERSION_ID") {
                                $top .= $name . ", ";
                                $bottom .= "'" . $value . "',";
                            }
			}
                        $top .= " VERSION_ID) ";
                        $bottom .= " " . $this->current_version . ") ";
                        $sql=$top . $bottom;
echo $sql . "\r\n";
			$stmt = $this->db->prepare($sql);
                        try {
			$stmt->execute();
                        } catch(Exception $e) {
                            echo $sql;
                        }
			$POST['ID'] = $this->db->lastInsertId();
		} else {
                        $top = "UPDATE BEP20_TRANSACTIONS SET ";
		        foreach ($POST as $name => $value) {
		            if ($name!="ID"&&$name!="CREATE_DATE"&&$name!="TABLE_NAME"&&$name!="ACTION"&&$name!="VERSION_ID") {
                                $top .= $name . " = '" . $value . "', ";
                            }
			}
                        $top .= " VERSION_ID = " . $this->current_version . " WHERE ID = " . $POST['ID'];
                        $sql=$top;
//echo "A";
echo $sql . "\r\n";
			$stmt = $this->db->prepare($sql);
			$stmt->execute();
			$POST['ID'] = $this->db->lastInsertId();
                }

	    $this->writelog("NEW bep20 post");	
		return $POST['ID'];
	}
	
}

