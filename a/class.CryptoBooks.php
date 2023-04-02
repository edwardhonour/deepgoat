<?php

//------------------------------------------------------------------------------------
// Copyright 2021 - GEX Data Labs Inc.
// Data Processing utilities for BSCScan, Etherscan, CoinGecko API, and 
//     Farm.Army API
// Created for Amazon Aurora Database 
// Author: Edward Honour
// Date:  9/26/2021
//------------------------------------------------------------------------------------


class CryptoBooks {

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
	
        function format_amount_with_no_e($amount) {
        }

	// ACCOUNTS
	//
	// Assets
	//     TOKENS-HELD		Debit
	//     TOKENS-STAKED	Debit
	//
	// Liabilities
    //      None
	//
    // Equity
    //     INVESTMENT 		Credit
    //     EARNINGS         Credit
    //  	
	//
	// Revenue
	//      REVENUE			Credit
	//
    // Expenses
	//		EXCHANGE-FEES	Debit
	//		GAS-FEES		Debit
	//		MINING			Debit
	//

	function clear_transaction($wallet_address, $hash) {
		$sql="DELETE FROM GOATX_TRANSACTION_ENTRIES WHERE ";
        $sql.="wallet_address = '" . $wallet_address . "' AND hash = '" . $hash . "'";
		$this->execute($sql);
        $this->execute("COMMIT");
	}
	
	function post_detail($wallet_address, $account, $hash, $timestamp, $amount) {
		$sql="insert into GOATX_TRANSACTION_ENTRIES (";
        $sql.="wallet_address, transaction_id, timestamp, account, amount) ";
		$sql.="VALUES ";
		$sql.=" ('" . $wallet_address . "','" . $hash . "',";
		$sql.=$timestamp . ",'" . $account . "','" . $amount . "')";
		$this->execute($sql);
        $this->execute("COMMIT");
	}
	
	function receive_tokens_from_exchange_wallet($data) {
		//
		// If user has not entered a cost basis, the entire 
		// value of the tokens is income.
		//
		$this->clear_transaction($l['wallet_address'],$l['hash']);
		
		$cost_basis=$l['USER_COST_BASIS'];
		if ($cost_basis=='') $cost_basis='0';
		
		if ($data['toPrice']=='') {
			$price=$this->get_token_price($l['contractAddress'],$l['timestamp']);
			$usd=floatval($l['toValue'])*floatval($price);
		} else {
			$price=str_replace(",","",$data['toPrice']);
			$usd=str_replace(",","",$data['toValueUSD']);
		}
		$gain=floatval($usd)-floatval($cost_basis);
		$this->post_detail($l['wallet_address'],"INVESTMENT",$l['hash'],$l['timestamp'],$cost_basis);
		$this->post_detail($l['wallet_address'],"TOKENS-HELD",$l['hash'],$l['timestamp'],$usd);
		$this->post_detail($l['wallet_address'],"REVENUE",$l['hash'],$l['timestamp'],$gain);
	}

	function send_tokens_to_exchange_wallet($data) {
		//DR - INVESTMENT --> Reduce investment by amount tokens are worth.
		//CR - TOKENS-HELD --> Reduce tokens held by the value.
		//DR/CR - REVENUE --> Gain or loss in the value of the tokens send.
		//DR/CR - REVENUE --> Additional Gain or loss from the Exchange when cashing out.
	}
	// 1 5 munte
	// 1-90  - Hourly Bars
	
	function receive_tokens_from_bridge_wallet($data) {
		
	}

	function send_tokens_to_bridge_wallet($data) {
		
	}
	
	function receive_tokens_from_external_wallet($data) {
	    
	}
	
	function send_tokens_to_external_wallet($data) {
		
		
	}
	
	
	function contract_interaction_approval($data) {
	
	}

	function swap_tokens($data) {
					
	}
	
	function stake_tokens($data) {
					
	}

	function unstake_stake_tokens($data) {
					
	}
	
	function harvest_farm_rewards($data) {

	}
	
	
	function post_token_snapshot($post) {
		$sql="SELECT * FROM GOATX_TOKEN_BALANCE_SNAPSHOT WHERE ";
		$sql.=" wallet_address = '" . $post['wallet_address'] . "' AND token_address = '" . $post['token_address'] . "' AND transaction_id = '" . $post['transaction_id'] ."'";
		$z=$this->sql($sql);
		if (sizeof($z)==0) {
			$sql="INSERT INTO GOATX_TOKEN_BALANCE_SNAPSHOT (";
			$sql.=" wallet_address, ";			
			$sql.=" transaction_id, ";			
			$sql.=" timestamp, ";		
			$sql.=" token_address, ";
			$sql.=" transaction_token_count, ";
			$sql.=" transaction_token_price, ";
			$sql.=" transaction_token_price_timestamp, ";		
			$sql.=" transaction_token_usd, ";
			$sql.=" transaction_user_cost, ";
			$sql.=" transaction_token_cost_basis, ";
			$sql.=" transaction_total_gain, ";
			$sql.=" transaction_gain_per_token, ";
			$sql.=" transaction_realized_gain, ";
			$sql.=" transaction_unrealized_gain, ";	
			$sql.=" prior_token_count, ";
			$sql.=" prior_token_price, ";
			$sql.=" prior_token_price_timestamp, ";		
			$sql.=" prior_token_usd, ";
			$sql.=" prior_user_cost, ";
			$sql.=" prior_token_cost_basis, ";
			$sql.=" prior_total_gain, ";
			$sql.=" prior_realized_gain, ";
			$sql.=" prior_unrealized_gain, ";				
			$sql.=" token_count, ";
			$sql.=" token_price, ";
			$sql.=" token_price_timestamp, ";		
			$sql.=" token_usd, ";
			$sql.=" token_user_cost, ";
			$sql.=" token_cost_basis, ";
			$sql.=" total_gain, ";
			$sql.=" realized_gain, ";
                        $sql.=" token_cost_basis_per_token, ";
			$sql.=" unrealized_gain) ";					
			$sql.=" VALUES (";
			$sql.="'" . $post['wallet_address'] . "',";	
			$sql.="'" . $post['transaction_id'] . "',";				
			$sql.="" . $post['timestamp'] . ",";
			$sql.="'" . $post['token_address'] . "',";		
			$sql.="'" . $post['transaction_token_count'] . "',";
			$sql.="'" . $post['transaction_token_price'] . "',";
			$sql.="" . $post['transaction_token_price_timestamp'] . ",";			
			$sql.="'" . $post['transaction_token_usd'] . "',";
			$sql.="'" . $post['transaction_user_cost'] . "',";
			$sql.="'" . $post['transaction_token_cost_basis'] . "',";			
			$sql.="'" . $post['transaction_total_gain'] . "',";	
			$sql.="'" . $post['transaction_gain_per_token'] . "',";	
			$sql.="'" . $post['transaction_realized_gain'] . "',";	
			$sql.="'" . $post['transaction_unrealized_gain'] . "',";				
			$sql.="'" . $post['prior_token_count'] . "',";
			$sql.="'" . $post['prior_token_price'] . "',";
			$sql.="" . $post['prior_token_price_timestamp'] . ",";			
			$sql.="'" . $post['prior_token_usd'] . "',";
			$sql.="'" . $post['prior_user_cost'] . "',";
			$sql.="'" . $post['prior_token_cost_basis'] . "',";				
			$sql.="'" . $post['prior_total_gain'] . "',";	
			$sql.="'" . $post['prior_realized_gain'] . "',";	
			$sql.="'" . $post['prior_unrealized_gain'] . "',";				
			$sql.="'" . $post['token_count'] . "',";
			$sql.="'" . $post['token_price'] . "',";
			$sql.="" . $post['token_price_timestamp'] . ",";			
			$sql.="'" . $post['token_usd'] . "',";
			$sql.="'" . $post['token_user_cost'] . "',";
			$sql.="'" . $post['token_cost_basis'] . "',";				
			$sql.="'" . $post['total_gain'] . "',";	
			$sql.="'" . $post['realized_gain'] . "',";	
			$sql.="'" . $post['token_cost_basis_per_token'] . "',";	
			$sql.="'" . $post['unrealized_gain'] . "')";				
echo $sql;
			$this->execute($sql);
		} else {
		$sql="UPDATE GOATX_TOKEN_BALANCE_SNAPSHOT SET ";
			$sql.=" wallet_address = '" . $post['wallet_address'] . "',";			
			$sql.=" transaction_id = '" . $post['transaction_id'] . "',";			
			$sql.=" timestamp = " . $post['timestamp'] . ",";		
			$sql.=" token_address = '" . $post['token_address'] . "',";
			$sql.=" transaction_token_count = '" . $post['transaction_token_count'] . "',";
			$sql.=" transaction_token_price = '" . $post['transaction_token_price'] . "',";
			$sql.=" transaction_token_price_timestamp = " . $post['transaction_token_price_timestamp'] . ",";
			$sql.=" transaction_token_usd = '" . $post['transaction_token_usd'] . "',";
			$sql.=" transaction_user_cost = '" . $post['transaction_user_cost'] . "',";
			$sql.=" transaction_token_cost_basis = '" . $post['transaction_token_cost_basis'] . "',";
			$sql.=" transaction_total_gain = '" . $post['transaction_total_gain'] . "',";
			$sql.=" transaction_gain_per_token = '" . $post['transaction_gain_per_token'] . "',";
			$sql.=" transaction_realized_gain = '" . $post['transaction_realized_gain'] . "',";
			$sql.=" transaction_unrealized_gain = '" . $post['transaction_unrealized_gain'] . "',";
			$sql.=" prior_token_count = '" . $post['prior_token_count'] . "',";
			$sql.=" prior_token_price = '" . $post['prior_token_price'] . "',";
			$sql.=" prior_token_price_timestamp = " . $post['prior_token_price_timestamp'] . ",";
			$sql.=" prior_token_usd = '" . $post['prior_token_usd'] . "',";
			$sql.=" prior_user_cost = '" . $post['prior_user_cost'] . "',";
			$sql.=" prior_token_cost_basis = '" . $post['prior_token_cost_basis'] . "',";
			$sql.=" prior_total_gain = '" . $post['prior_total_gain'] . "',";
			$sql.=" prior_realized_gain = '" . $post['prior_realized_gain'] . "',";
			$sql.=" prior_unrealized_gain = '" . $post['prior_unrealized_gain'] . "',";
			$sql.=" token_count = '" . $post['token_count'] . "',";
			$sql.=" token_price = '" . $post['token_price'] . "',";
			$sql.=" token_price_timestamp = " . $post['token_price_timestamp'] . ",";
			$sql.=" token_usd = '" . $post['token_usd'] . "',";
			$sql.=" token_user_cost = '" . $post['token_user_cost'] . "',";
			$sql.=" token_cost_basis = '" . $post['token_cost_basis'] . "',";
			$sql.=" total_gain = '" . $post['total_gain'] . "',";
			$sql.=" realized_gain = '" . $post['realized_gain'] . "',";
			$sql.=" token_cost_basis_per_token = '" . $post['token_cost_basis_per_token'] . "',";
			$sql.=" unrealized_gain = '" . $post['unrealized_gain'] . "'";
            $sql.=" WHERE ID = " . $z[0]['ID'];			
echo $sql;
			$this->execute($sql);		
	}
}

	function calculate_token_wallet_snapshot($wallet_address, $token_address, $transaction_id, $timestamp) {
		
	}
	
	function process_transaction_list($wallet_address='0x4056087801950f51e814b772b3e11f99147df1d4') {
			$sql="SELECT * FROM BEP20_TRANSACTIONS WHERE wallet_address = '" . $wallet_address . "' ORDER BY timestamp";
			$x=$this->sql($sql);
			foreach($x as $y) {
					if ($y['CHAIN']=='BSC') {
						if ($y['fromTokenSymbol']=='BNB'||$y['toTokenSymbol']=='BNB') {
							$this->calculate_wallet_coin_snapshot($y['wallet_address'], 'BNB Coin', $y['hash'], $y['timestamp']);
						} 
						$sql="select contractAddress from BEP20_RAW_TRANSACTIONS WHERE hash = '" . $y['hash'] . "' ";
						$sql.=" AND source = 'tokentx' AND walletAddress = '" . $wallet_address . "'";
						$z0=$this->sql($sql);
						foreach ($z0 as $z) {
							$this->calculate_wallet_coin_snapshot($wallet_address, $z['contractAddress'], $y['hash'], $y['timestamp']);							
						}
					}
					if ($y['CHAIN']=='ETH') {
						if ($y['fromTokenSymbol']=='ETH'||$y['toTokenSymbol']=='ETH') {
							$this->calculate_wallet_coin_snapshot($y['wallet_address'], 'ETH Coin', $y['hash'], $y['timestamp']);
						}
						$sql="select contractAddress from BEP20_RAW_TRANSACTIONS WHERE hash = '" . $y['hash'] . "' ";
						$sql.=" AND source = 'tokentx' AND walletAddress = '" . $wallet_address . "'";
						$z0=$this->sql($sql);
						foreach ($z0 as $z) {
							$this->calculate_wallet_coin_snapshot($wallet_address, $z['contractAddress'], $y['hash'], $y['timeStamp']);							
						}						
					}
			}
	}
	
	
	function calculate_wallet_coin_snapshot($wallet_address, $token_address, $transaction_id, $timestamp) {
		
		//-- 
		//-- Get Prior Values
		//--
		
		$sql="select token_count, token_price, token_price_timestamp, token_usd, token_user_cost, token_cost_basis, ";
                $sql.=" total_gain, realized_gain, unrealized_gain, token_cost_basis_per_token from GOATX_TOKEN_BALANCE_SNAPSHOT WHERE wallet_address = '" . $wallet_address . "' AND ";
		$sql.=" timestamp <= " . $timestamp . " AND transaction_id <> '" . $transaction_id . "' ";
		$sql.=" AND token_address = '" . $token_address . "' ORDER BY timestamp";
		$post=array();
		$post['wallet_address']=$wallet_address;
		$post['token_address']=$token_address;
		$post['transaction_id']=$transaction_id;
		$post['timestamp']=$timestamp;		
                $post['token_count']="Z";
                $post['token_price']="Z";
                $post['token_price_timestamp']="Z";
                $post['token_usd']="Z";
                $post['token_user_cost']="Z";
                $post['token_cost_basis']="Z";
                $post['total_gain']="Z";
                $post['realized_gain']="Z";
                $post['unrealized_gain']="Z";
                $post['token_cost_basis_per_token']="Z";
		$z=$this->sql($sql);
		if (sizeof($z)==0) {
			$post['prior_token_count']=floatval('0');		 		
			$post['prior_token_usd']=floatval('0');
			$post['prior_token_price']=floatval('0');					
			$post['prior_token_price_timestamp']=floatval('0');				
			$post['prior_user_cost']=floatval('0');	
			$post['prior_token_cost_basis']=floatval('0');			
			$post['prior_total_gain']=floatval('0');
			$post['prior_realized_gain']=floatval('0');
			$post['prior_unrealized_gain']=floatval('0');			
                        $post['prior_token_cost_basis_per_token']=floatval('0');
		} else {
			$post['prior_token_count']=floatval($z[0]['token_count']);
			$post['prior_token_usd']=floatval($z[0]['token_usd']);
			$post['prior_token_price']=floatval($z[0]['token_price']);					
			$post['prior_token_price_timestamp']=floatval($z[0]['token_price_timestamp']);				
			$post['prior_user_cost']=floatval($z[0]['token_count']);	
			$post['prior_token_cost_basis']=floatval($z[0]['token_cost_basis']);
			$post['prior_total_gain']=floatval($z[0]['total_gain']);
			$post['prior_realized_gain']=floatval($z[0]['realized_gain']);
			$post['prior_unrealized_gain']=floatval($z[0]['unrealized_gain']);					
                        $post['prior_token_cost_basis_per_token']=floatval($z[0]['token_cost_basis_per_token']);
		}
        //--
        //-- Get Current Transaction
        //--
		
		$sql="select * from BEP20_TRANSACTIONS WHERE wallet_address = '" . $wallet_address . "' AND ";
		$sql.=" hash = '" . $transaction_id . "'";
		$z=$this->sql($sql);
		if (sizeof($z)==0) {
			$post['transaction_token_count']=floatval('0');
			$post['transaction_token_price']=floatval('0');
			$post['transaction_token_price_timestamp']=floatval('0');			
			$post['transaction_token_usd']=floatval('0');
			$post['transaction_user_cost']=floatval('0');
			$post['transaction_token_cost_basis']=floatval('0');			
			$post['transaction_total_gain']=floatval('0');
			$post['transaction_realized_gain']=floatval('0');
			$post['transaction_unrealized_gain']=floatval('0');						
		} else {
			if ($z[0]['fromAddress']==$wallet_address) {
				//Send
				$post['transaction_token_count']=0-floatval(str_replace(",","",$z[0]['fromValue']));
				$post['transaction_token_price']=floatval(str_replace(",","",$z[0]['fromPrice']));				
				$post['transaction_token_price_timestamp']=floatval($z[0]['fromTimestamp']);					
				$post['transaction_token_usd']=0-floatval(str_replace(",","",$z[0]['fromValueUSD']));												
				if ($z[0]['USER_COST_BASIS']=='') $z[0]['USER_COST_BASIS']=$post['prior_token_cost_basis'];
				$post['transaction_user_cost']=0-floatval($z[0]['USER_COST_BASIS']);				
				$post['transaction_token_cost_basis']=floatval($z[0]['USER_COST_BASIS']) / floatval(str_replace(",","",$z[0]['fromValue']));
				$post['beginning_token_usd']=$post['prior_token_count']*$post['transaction_token_price'];
				$post['transaction_total_gain']=$post['beginning_token_usd']-$post['prior_token_usd'];
				$post['transaction_gain_per_token']=$post['transaction_total_gain'] / $post['prior_token_count'];
				$post['transaction_realized_gain']=floatval($z[0]['fromValue'])*$post['transaction_gain_per_token'];
				$post['token_count']=$post['prior_token_count']+$post['transaction_token_count'];
				$post['transaction_unrealized_gain']=$post['token_count']*$post['transaction_gain_per_token'];
				$post['total_gain']=$post['prior_total_gain']+$post['transaction_total_gain'];
				$post['realized_gain']=$post['prior_realized_gain']+$post['transaction_realized_gain'];
				$post['unrealized_gain']=$post['prior_unrealized_gain']+$post['transaction_unrealized_gain'];				
				$post['token_cost_basis']=$post['prior_token_cost_basis'];
				$post['token_cost_basis_per_token']=$post['prior_token_cost_basis_per_token'];
                                $post['token_user_cost']=$post['token_cost_basis_per_token']*$post['token_count'];
			} else {
				//Received
				$post['transaction_token_count']=floatval(str_replace(",","",$z[0]['toValue']));
				$post['transaction_token_price']=floatval(str_replace(",","",$z[0]['toPrice']));				
				$post['transaction_token_price_timestamp']=floatval($z[0]['toTimestamp']);					
				$post['transaction_token_usd']=floatval(str_replace(",","",$z[0]['toValueUSD']));												
				if ($z[0]['USER_COST_BASIS']=='') $z[0]['USER_COST_BASIS']="0";
				$post['transaction_user_cost']=floatval($z[0]['USER_COST_BASIS']);				
				$post['transaction_token_cost_basis']=floatval($z[0]['USER_COST_BASIS']) / floatval(str_replace(",","",$z[0]['toValue']));
				$post['beginning_token_usd']=$post['prior_token_count']*$post['transaction_token_price'];
                                if ($z[0]['USER_TRANSACTION_TYPE']=='BRECEIVE') {
                                     $post['transaction_user_cost']=$post['transaction_token_usd'];
                                }  
				$post['transaction_total_gain']=$post['transaction_token_usd']-$post['transaction_user_cost'];
				$post['token_count']=$post['prior_token_count']+$post['transaction_token_count'];
				$post['transaction_gain_per_token']=$post['transaction_total_gain'] / $post['token_count'];
				$post['transaction_realized_gain']=$post['transaction_token_usd']-$post['transaction_user_cost'];
				$post['transaction_unrealized_gain']=($post['token_count']*$post['transaction_gain_per_token'])-$post['transaction_realized_gain'];
				$post['total_gain']=$post['prior_total_gain']+$post['transaction_total_gain'];
				$post['realized_gain']=$post['prior_realized_gain']+$post['transaction_realized_gain'];
				$post['unrealized_gain']=$post['prior_unrealized_gain'];
                                $post['token_user_cost']=$post['prior_user_cost']+$post['transaction_user_cost'];
                                if ($post['transaction_token_price']!=0) {
                                    $post['token_cost_basis']=$post['token_count']/$post['transaction_token_price'];
                                } else {
                                    $post['token_cost_basis']=0;
                                }
                        if ($post['token_count']!=0) {
                            $post['token_cost_basis_per_token']=$post['token_user_cost'] / $post['token_count'];
                        } else {
                            $post['token_cost_basis_per_token']=0;
                        }

                        $post['token_price']=$post['transaction_token_price'];
                        $post['token_price_timestamp']=$post['transaction_token_price_timestamp'];
                        $post['token_usd']=$post['token_count']*$post['token_price'];
			$post['realized_gain']=$post['prior_realized_gain']+$post['transaction_realized_gain'];					
			$post['unrealized_gain']=$post['prior_unrealized_gain']+$post['transaction_unrealized_gain'];					
			print_r($post);
			$this->post_token_snapshot($post);
                     }
		}        		
	}
	
	function calculate_token_balances($uid) {
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
	
	
	function processWalletTransactions($address, $chain="BSC") {

	} 

	
    function getPrices($ID, $timestamp, $fromTokenSymbol, $fromValue, $toTokenSymbol, $toValue, $address) {
		
		$sql="select timestamp, price from GOATX_TOKEN_CONTRACT_PRICE WHERE contract_address = '" . $address . "' ";
		$sql.="and CONVERT(timestamp,DECIMAL) <= " . $timestamp . " ORDER BY CONVERT(timestamp,DECIMAL) DESC";
		$z=$this->sql($sql);
		if (sizeof($z)>0) {
			$post=array();
			$post['price']=round($z[0]['price'],5);
			$post['timestamp']=$z[0]['timestamp'];
		} else {
			$post=array();
			$post['price']='0.0000';
			$post['timestamp']=0;
		}
		return $post;					
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
	
}  // End of class

