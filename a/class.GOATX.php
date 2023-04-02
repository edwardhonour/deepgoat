<?php

//------------------------------------------------------------------------------------
// Copyright 2021 - GEX Data Labs Inc.
// Functions called by API Router 
// Created for Amazon Aurora Database 
// Author: Edward Honour
// Date:  7/18/2021
//------------------------------------------------------------------------------------

require_once('class.BSCScan.php');

class GOATX {

	protected $dbh;
	protected $db;
    protected $B;
	
	function connect() {
		$cs=file_get_contents("/var/www/vault/cs.json");
		$c=json_decode($cs,true);
		$this->dbh = new PDO($c['cs'], $c['un'], $c['pwd']);
		$this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
		return $this->dbh;
	}

    function __construct() {
       $this->B = new BSCScan();
    }
			
    function router($POST) {
                $this->postUserAccess($POST);
		switch ($POST['q']) {
			case 'get.dashboard.counts':
				return $this->getDashboardCounts($POST);
			break;				
			case 'get.user.login':
				return $this->getUserLogin($POST);
			break;				
			case 'post.manual.transaction':
				return $this->postManualTransaction($POST);
			break;				
			case 'post.contract.tag':
				return $this->postContractTag($POST);
			break;				
			case 'post.create.user':
				return $this->postCreateUser($POST);
			break;				
			case 'check.user.username':
				return $this->checkUserUsername($POST);
			break;				
			case 'check.user.email':
				return $this->checkUserEmail($POST);
			break;				
			case 'post.update.contract':
				return $this->postUpdateContract($POST);
			break;				
                        case 'post.transaction.tag':
                                return $this->postTransactionTag($POST);
                        break;
                        case 'delete.transaction.tag':
                                return $this->deleteTransactionTag($POST);
                        break;
			case 'post.update.wallet':
				return $this->postUpdateWallet($POST);
			break;				
			case 'post.add.wallet':
				return $this->postAddWallet($POST);
			break;				
			case 'get.wallet.list':
				return $this->getWalletList($POST);
			break;				
			case 'get.wallet.balances':
				return $this->getWalletBalances($POST);
			break;				
			case 'post.update.contract2':
				return $this->getUpdateContract($POST);
			break;				
			case 'post.flag.contract':
                                $POST['symbol']="";
                                $POST['name']="";
				return $this->postiUpdateContract($POST);
			break;				
			case 'post.goatx.wallet':
				return $this->postGoatXWallet($POST);
			break;				
			case 'get.token.list':
				return $this->getTokenList($POST);
			break;			
			case 'get.exception.list':
				return $this->getExceptionList($POST);
			break;					
			case 'get.one.contract':
				return $this->getOneContract($POST);
			break;					
			case 'get.one.wallet':
				return $this->getOneWallet($POST);
			break;					
			case 'get.contract.list':
				return $this->getContractList($POST);
			break;				
			case 'get.token.assets':
				return $this->getTokenAssets($POST);
			break;
			case 'get.token.data':
				return $this->getTokenData($POST);			
			break;
			case 'get.account.home':
				return $this->getAccountHome($POST);			
			break;
			case 'check.wallet.status':
				return $this->checkWalletStatus($POST);			
			break;
			case 'get.one.transaction':
				return $this->getOneTransaction($POST);			
			break;
			case 'get.token.staking':
				return $this->getTokenStaking($POST);			
			break;
			case 'get.token.portfolio':
				return $this->getTokenPortfolio($POST);
			break;
			case 'get.wallet.tokens':
				return $this->getWalletTokens($POST);			
			break;
			case 'get.token.transactions':
				return $this->getTokenTransactions($POST);			
			break;
			case 'get.token.pairs':
				return $this->getTokenPairs($POST);			
			break;
			case 'post.airdrop':
				return $this->postAirdrop($POST);			
			break;
			case 'post.wallet.token.balance':
				return $this->postWalletTokenBalances($POST);			
			break;
			case 'post.lp.contracts':
				return $this->postLpContracts($POST);			
			break;
			case 'get.background.data':
				return $this->getBackgroundData($POST);			
			break;
			case 'get.airdrop.wallets':
				return $this->getAirdropWallets($POST);			
			break;
			case 'get.one.order':
				return $this->getOneOrder($POST);			
			break;
			case 'get.one.client':
				return $this->getOneClient($POST);			
			break;
			case 'get.client.list':
				return $this->getClientList($POST);			
			break;
			case 'get.token.watchlist':
				return $this->getTokenWatchlist($POST);			
			break;
			case 'get.contract.interactions':
				return $this->getContractInteractions($POST);			
			break;
			case 'get.tagged.wallets':
				return $this->getTaggedWallets($POST);			
			break;
			case 'get.cex.transactions':
				return $this->getCexTransactions($POST);			
			break;
			case 'get.nft.transactions':
				return $this->getNftTransactions($POST);			
			break;
			case 'get.yield.farms':
				return $this->getYieldFarms($POST);			
			break;
			case 'get.eth.transactions':
				return $this->getEthTransactions($POST);			
			break;
			case 'get.token.transactions':
			case 'get.all.token.transactions':
				return $this->getAllTokenTransactions($POST);			
			break;
			case 'get.wallet.transactions':
				return $this->getAllWalletTransactions($POST);			
			break;
			case 'get.add.wallet':
				return $this->getAddWallet($POST);			
			break;
			case 'get.lp.pools':
				return $this->getLpPools($POST);			
			break;
			case 'get.research.home':
				return $this->getResearchHome($POST);			
			break;
			case 'get.research.pools':
				return $this->getResearchPools($POST);			
			break;
			case 'get.research.platforms':
				return $this->getResearchPlatforms($POST);			
			break;
			case 'get.research.lptokens':
				return $this->getResearchLptokens($POST);			
			break;
			case 'get.edit.profile':
				return $this->getEditProfile($POST);			
			break;
			case 'post.edit.profile':
				return $this->postEditProfile($POST);			
			break;
			case 'get.research.tokens':
				return $this->getResearchTokens($POST);			
			break;
			case 'get.research.nfts':
				return $this->getResearchNfts($POST);			
			break;
			case 'get.edit.wallet':
				return $this->getEditWallet($POST);			
			break;
			case 'post.edit.wallet':
				return $this->postEditWallet($POST);			
			break;
			case 'get.research.token.dashboard':
				return $this->getResearchTokenDashboard($POST);			
			break;
			case 'get.research.asset.platforms':
				return $this->getResearchAssetPlatforms($POST);			
			break;
			case 'get.research.token.categories':
				return $this->getResearchTokenCategories($POST);			
			break;
			case 'get.research.exchanges':
				return $this->getResearchExchanges($POST);			
			break;
			case 'get.research.finance.platforms':
				return $this->getResearchFinancePlatforms($POST);			
			break;
			case 'get.research.indexes':
				return $this->getResearchIndexes($POST);			
			break;
			case 'get.research.derivatives':
				return $this->getResearchDerivatives($POST);			
			break;
			case 'get.research.events':
				return $this->getResearchEvents($POST);			
			break;
			case 'get.research.exchange.rates':
				return $this->getResearchExchangeRates($POST);			
			break;
			case 'get.research.trending':
				return $this->getResearchTrending($POST);			
			break;
			case 'get.research.global':
				return $this->getResearchGlobal($POST);			
			break;
			case 'get.research.global.defi':
				return $this->getResearchGlobalDefi($POST);			
			break;
			case 'get.research.companies':
				return $this->getResearchCompanies($POST);			
			break;
			case 'get.research.pool.dashboard':
				return $this->getResearchPoolDashboard($POST);			
			break;
			case 'get.research.platform.dashboard':
				return $this->getResearchPlatformDashboard($POST);			
			break;
			case 'get.research.nft.dashboard':
				return $this->getResearchNftDashboard($POST);			
			break;
			case 'get.research.nft.dashboard':
				return $this->getResearchNftDashboard($POST);			
			break;
			case 'get.wallet.transaction.dashboard':
				return $this->getWalletTransactionDashboard($POST);			
			break;
			case 'get.token.transaction.dashboard':
				return $this->getTokenTransactionDashboard($POST);			
			break;
			case 'get.nft.transaction.dashboard':
				return $this->getNftTransactionDashboard($POST);			
			break;
			case 'get.eth.transaction.dashboard':
				return $this->getEthTransactionDashboard($POST);			
			break;
			case 'get.lp.pool.dashboard':
				return $this->getLpPoolDashboard($POST);			
			break;
			case 'get.yield.farm.dashboard':
				return $this->getYieldFarmDashboard($POST);			
			break;
			case 'get.cex.interaction.dashboard':
				return $this->getCexInteractionDashboard($POST);			
			break;
			case 'get.tagged.wallet.dashboard':
				return $this->getTaggedWalletDashboard($POST);			
			break;
			case 'get.contract.interaction.dashboard':
				return $this->getContractInteractionDashboard($POST);			
			break;
			case 'get.token.watchlist.dashboard':
				return $this->getTokenWatchlistDashboard($POST);			
			break;
			default:
		}
    }
    function postTransactionTag($data) {
         $sql="UPDATE BEP20_TRANSACTIONS SET USER_TRANSACTION_TYPE = '" . $data['user_transaction_type'] . "' WHERE ID = " . $data['id'];
         $this->execute($sql);

         if (isset($data['user_transaction_type_other'])) {
            $sql="UPDATE BEP20_TRANSACTIONS SET USER_TRANSACTION_TYPE_OTHER = '" . $data['user_transaction_type_other'] . "' WHERE ID = " . $data['id'];
            $this->execute($sql);
         }
         if (isset($data['user_tag_exchange'])) {
            $sql="UPDATE BEP20_TRANSACTIONS SET USER_TAG_EXCHANGE = '" . $data['user_tag_exchange'] . "' WHERE ID = " . $data['id'];
            $this->execute($sql);
         }
         if (isset($data['user_tag_exchange_other'])) {
            $sql="UPDATE BEP20_TRANSACTIONS SET USER_TAG_EXCHANGE_OTHER = '" . $data['user_tag_exchange_other'] . "' WHERE ID = " . $data['id'];
            $this->execute($sql);
         }
         if (isset($data['user_cost_basis'])) {
            $sql="UPDATE BEP20_TRANSACTIONS SET USER_COST_BASIS = '" . $data['user_cost_basis'] . "' WHERE ID = " . $data['id'];
            $this->execute($sql);
         }
         if (isset($data['hide_transaction'])) {
             if ($data['hide_transaction']=="") {

            $sql="UPDATE BEP20_TRANSACTIONS SET HIDE_TRANSACTION = 'N' WHERE ID = " . $data['id'];
            $this->execute($sql);
             } else {

            $sql="UPDATE BEP20_TRANSACTIONS SET HIDE_TRANSACTION = 'Y' WHERE ID = " . $data['id'];
            $this->execute($sql);
             }
         }
         if (isset($data['user_tag'])) {
            if ($data['user_tag']=='') {
                if ($data['user_transaction_type']=='PURCHASE') $data['user_tag']="Purchase using Fiat";
                if ($data['user_transaction_type']=="SALE") $data['user_tag']="Sale for Fiat";
                if ($data['user_transaction_type']=="ESEND") $data['user_tag']="Transfer to Exchange Wallet";                                
                if ($data['user_transaction_type']=="ERECEIVE")$data['user_tag']="Transfer from Exchange Wallet";
                if ($data['user_transaction_type']=="BSEND") $data['user_tag']="Transfer to Bridge Wallet";                                 
                if ($data['user_transaction_type']=="BRECEIVE") $data['user_tag']="Transfer from Bridge Wallet";                                        
                if ($data['user_transaction_type']=="TRANSFER") $data['user_tag']="Transfer between Wallets";  
                if ($data['user_transaction_type']=="RECEIVE") $data['user_tag']="Receive Tokens / Coins";
                if ($data['user_transaction_type']=="SEND") $data['user_tag']="Send Tokens / Coins";
                if ($data['user_transaction_type']=="SWAP") $data['user_tag']="Swap Tokens"; 
                if ($data['user_transaction_type']=="STAKE") $data['user_tag']="Stake Tokens"; 
                if ($data['user_transaction_type']=="UNSTAKE") $data['user_tag']="Unstake Tokens";
                if ($data['user_transaction_type']=="HARVEST") $data['user_tag']="Harvest / Earn Rewards";
                if ($data['user_transaction_type']=="MINING") $data['user_tag']="Mining";
                if ($data['user_transaction_type']=="AIRDROP") $data['user_tag']="Receive Airdrop - Requested";
                if ($data['user_transaction_type']=="NAIRDROP") $data['user_tag']="Receive Airdrop - Not Requested";
                if ($data['user_transaction_type']=="CONTRACT") $data['user_tag']="Contract Interaction";
                if ($data['user_transaction_type']=="OTHER") $data['user_tag']="OTHER - Not Listed";    
            }
            $sql="UPDATE BEP20_TRANSACTIONS SET USER_TAG = '" . $data['user_tag'] . "' WHERE ID = " . $data['id'];
            $this->execute($sql);
         } else {
              $data['user_tag']="";
                if ($data['user_transaction_type']=='PURCHASE') $data['user_tag']="Purchase using Fiat";
                if ($data['user_transaction_type']=="SALE") $data['user_tag']="Sale for Fiat";
                if ($data['user_transaction_type']=="ESEND") $data['user_tag']="Transfer to Exchange Wallet";                                
                if ($data['user_transaction_type']=="ERECEIVE")$data['user_tag']="Transfer from Exchange Wallet";
                if ($data['user_transaction_type']=="BSEND") $data['user_tag']="Transfer to Bridge Wallet";                                 
                if ($data['user_transaction_type']=="BRECEIVE") $data['user_tag']="Transfer from Bridge Wallet";                                        
                if ($data['user_transaction_type']=="TRANSFER") $data['user_tag']="Transfer between Wallets";  
                if ($data['user_transaction_type']=="RECEIVE") $data['user_tag']="Receive Tokens / Coins";
                if ($data['user_transaction_type']=="SEND") $data['user_tag']="Send Tokens / Coins";
                if ($data['user_transaction_type']=="SWAP") $data['user_tag']="Swap Tokens"; 
                if ($data['user_transaction_type']=="STAKE") $data['user_tag']="Stake Tokens"; 
                if ($data['user_transaction_type']=="UNSTAKE") $data['user_tag']="Unstake Tokens";
                if ($data['user_transaction_type']=="HARVEST") $data['user_tag']="Harvest / Earn Rewards";
                if ($data['user_transaction_type']=="MINING") $data['user_tag']="Mining";
                if ($data['user_transaction_type']=="AIRDROP") $data['user_tag']="Receive Airdrop - Requested";
                if ($data['user_transaction_type']=="NAIRDROP") $data['user_tag']="Receive Airdrop - Not Requested";
                if ($data['user_transaction_type']=="CONTRACT") $data['user_tag']="Contract Interaction";
                if ($data['user_transaction_type']=="OTHER") $data['user_tag']="OTHER - Not Listed";    
            $sql="UPDATE BEP20_TRANSACTIONS SET USER_TAG = '" . $data['user_tag'] . "' WHERE ID = " . $data['id'];
            $this->execute($sql);
         }


         $output=array();
         $output['error_message']="No Message";
         $output['error_code']=0;       
         $output['error']=0;       
         return $output;
    }

    function deleteTransactionTag($data) {
         $sql="UPDATE BEP20_TRANSACTIONS SET USER_TRANSACTION_TYPE = '' WHERE ID = " . $data['id'];
         $this->execute($sql);

         $sql="UPDATE BEP20_TRANSACTIONS SET USER_TRANSACTION_TYPE_OTHER = '' WHERE ID = " . $data['id'];
         $this->execute($sql);
         $sql="UPDATE BEP20_TRANSACTIONS SET USER_TAG_EXCHANGE = '' WHERE ID = " . $data['id'];
         $this->execute($sql);
         $sql="UPDATE BEP20_TRANSACTIONS SET USER_TAG_EXCHANGE_OTHER = '' WHERE ID = " . $data['id'];
        $this->execute($sql);
         $sql="UPDATE BEP20_TRANSACTIONS SET USER_COST_BASIS = '' WHERE ID = " . $data['id'];
         $this->execute($sql);
         $sql="UPDATE BEP20_TRANSACTIONS SET USER_TAG = '' WHERE ID = " . $data['id'];
         $this->execute($sql);

         $output=array();
         $output['error_message']="No Message";
         $output['error_code']=44;       
         return $output;
    }

    function getEditWallet($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function postContractTag($data) {

        $id=$data['id'];
        $uid=$data['uid'];
        $contract_address=$data['contract_address'];

        $sql="SELECT * FROM GOATX_USER_CONTRACT_TAGS WHERE uid = '" . $uid . "' AND contract_address = '";
        $sql.=$data['contract_address'] . "'";
        $z=$this->sql($sql);
        if (sizeof($z)==0) {
           $sql="INSERT INTO GOATX_USER_CONTRACT_TAGS (uid, contract_address, transaction_id) VALUES (";
           $sql.=  $uid . ",'" . strtolower($contract_address) . "'," . $id .")";
           $this->execute($sql);
        }            
        if (isset($data['contract_type'])) {
           $sql="UPDATE GOATX_USER_CONTRACT_TAGS SET contract_type = '" . $data['contract_type'] . "' ";
           $sql.=" WHERE contract_address = '" . strtolower($contract_address) . "' AND uid = " . $uid;
           $this->execute($sql); 
        }
        if (isset($data['contract_type_other'])) {
           $sql="UPDATE GOATX_USER_CONTRACT_TAGS SET contract_type_other = '" . $data['contract_type_other'] . "' ";
           $sql.=" WHERE contract_address = '" . strtolower($contract_address) . "' AND uid = " . $uid;
           $this->execute($sql); 
        }
        if (isset($data['contract_tag'])) {
           $sql="UPDATE GOATX_USER_CONTRACT_TAGS SET contract_tag = '" . $data['contract_tag'] . "' ";
           $sql.=" WHERE contract_address = '" . strtolower($contract_address) . "' AND uid = " . $uid;
           $this->execute($sql); 
        }
        if (isset($data['contract_exchange'])) {
           $sql="UPDATE GOATX_USER_CONTRACT_TAGS SET contract_exchange = '" . $data['contract_exchange'] . "' ";
           $sql.=" WHERE contract_address = '" . strtolower($contract_address) . "' AND uid = " . $uid;
           $this->execute($sql); 
        }
        if (isset($data['contract_exchange_other'])) {
           $sql="UPDATE GOATX_USER_CONTRACT_TAGS SET contract_exchange_other = '" . $data['contract_exchange_other'] . "' ";
           $sql.=" WHERE contract_address = '" . strtolower($contract_address) . "' AND uid = " . $uid;
           $this->execute($sql); 
        }
        if (isset($data['contract_token_symbol'])) {
           $sql="UPDATE GOATX_USER_CONTRACT_TAGS SET contract_token_symbol = '" . $data['contract_token_symbol'] . "' ";
           $sql.=" WHERE contract_address = '" . strtolower($contract_address) . "' AND uid = " . $uid;
           $this->execute($sql); 
        }
        if (isset($data['contract_token_name'])) {
           $sql="UPDATE GOATX_USER_CONTRACT_TAGS SET contract_token_name = '" . $data['contract_token_name'] . "' ";
           $sql.=" WHERE contract_address = '" . strtolower($contract_address) . "' AND uid = " . $uid;
           $this->execute($sql); 
        }
        $output=array();
        $output['error_code']=0;
        $output['error_message']="No Error";
        return $output;
        
    }

    function postManualTransaction($post) {
        $post['table_name']="GOATX_USER_TRANSACTIONS";
        if ($post['transaction_type']=="DEPOSIT") $post['PLATFORM']="EXCHANGE";
        if ($post['transaction_type']=="WITHDRAW") $post['PLATFORM']="WITHDRAW";
        $id=$this->post($post);   
        $output=array();
        $output['id']=$id;
        return $output;
    }

    function postEditWallet($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function postEditProfile($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function postAddWallet($data) {
	$output=array();
        $uid=$data['id'];
	$address=$data['address'];
	$userTag=$data['usertag'];
	$sql="select * FROM GOATX_WALLET WHERE WALLET_ADDRESS = '" . strtolower($address) . "'";
	$x=$this->sql($sql);
        if (sizeof($x)>0) {
                $sql="UPDATE GOATX_WALLET SET last_bep20_timestamp = 0, last_erc20_timestamp = 0, exclude = 'N',  UID = " . $data['uid'] . " WHERE ";
                $sql.=" WALLET_ADDRESS = '" . strtolower($data['address']) . "'";
        } else {
               $sql="INSERT INTO GOATX_WALLET (WALLET_ADDRESS, UID, exclude) VALUES ('" . strtolower($data['address']) . "'," . $data['uid'] . ",'N')";
        }
        $this->execute($sql);

	$sql="select * FROM GOATX_USER_WALLET WHERE USER_ID = " . $uid . " AND WALLET_ADDRESS = '" . strtolower($address) . "'";
	$x=$this->sql($sql);
	if (sizeof($x)==0) {
             $sql="INSERT INTO GOATX_USER_WALLET (CREATE_TIMESTAMP, USER_ID, WALLET_ADDRESS, TAG, PLATFORM) VALUES (now()," . $uid . ",";
	     $sql.="'" . strtolower($address) . "','" . str_replace("'","''",$userTag) . "','" . $data['platform'] . "')";
	     $this->execute($sql);
	     $output=array();
	     $output['error_code']=0;
	     $output['error_message']=$sql;
	} else {
	     $output=array();
	     $output['error_code']=100;
	     $output['error_message']="This wallet already exists on your account!";
	}

        return $output;
    }

    function queryResearchCategories() {
        $sql="SELECT * FROM GOATX_RESEARCH_CATEGORIES ORDER BY name";
        $z=$this->X->sql($sql);
        $output=$z;
        return $output;
    }

    function getResearchTokenCategories($POST) {
        return $this->queryResearchTokenCategories();
    }


    function queryResearchExchanges() {
        $sql="SELECT id, name, image, country, url, trust_score, trust_score_rank, trade_volume_24h_btc, year_established, trade_volume_24h_btc_normalized FROM GOATX_RESEARCH_EXCHANGES ORDER BY name";
        $z=$this->sql($sql);
        $output=$z;
        return $output;
    }

    function getResearchExchanges($POST) {
         return $this->queryResearchExchanges();
    }

    function queryResearchEvents() {
        $sql="SELECT * FROM GOATX_RESEARCH_EVENTS ORDER BY start_date";
        $o=array();
        $z=$this->sql($sql);
        foreach($z as $z0) {
            $z0['description']=str_replace('\r','',$z0['description']);
            $z0['description']=str_replace('\n','',$z0['description']);
            $z0['description']=str_replace("\n",'',$z0['description']);
            $z0['description']=str_replace("\r",'',$z0['description']);
            array_push($o,$z0);  
        }
        return $o;
        $output=$z;
        return $output;
    }

    function getResearchEvents($POST) {
         return $this->queryResearchEvents();
    }


    function queryResearchTrending() {
        $sql="SELECT * FROM GOATX_RESEARCH_TOKEN WHERE symbol IN (SELECT symbol FROM GOATX_RESEARCH_TRENDING) ORDER ";
        $sql.=" BY price_change_percentage_24h";
        $z=$this->sql($sql);
        $output=array();
        foreach($z as $z0) {
            $z0['sparkline']="https://deepgoat.com/api/svg/" . $z0['id'] . ".svg";
            $z0['symbol']=strtoupper($z0['symbol']);
            array_push($output,$z0);
        }
        return $output;
    }
 
    function getResearchTrending($POST) {
         return $this->queryResearchTrending();
    }

    function queryResearchGlobalDefi() {
        $sql="SELECT * FROM GOATX_RESEARCH_GLOBAL_DEFI";
        $z=$this->sql($sql);
        $output=$z;
        return $output;
    }

    function getResearchGlobalDefi($POST) {
         return $this->queryResearchGlobalDefi();
    }


    function queryResearchAssetPlatforms() {
        $sql="SELECT * FROM GOATX_RESEARCH_GLOBAL_DEFI";
        $z=$this->sql($sql);
        $output=$z;
        return $output;
    }

    function queryResearchTokenCategories() {
        $sql="SELECT * FROM GOATX_RESEARCH_CATEGORIES";
        $z=$this->sql($sql);
        $output=$z;
        return $output;
    }
    function getResearchAssetPlatforms($POST) {
        $output=array();
	$output['platforms']=$this->queryResearchAssetPlatforms();
        return $output;
     }

    function queryResearchCompanies() {
        $sql="SELECT * FROM GOATX_RESEARCH_COMPANIES";
        $z=$this->sql($sql);
        $output=$z;
        return $output;
    }

    function getResearchCompanies($POST) {
        return $this->queryResearchCompanies();
    }

    function queryResearchGlobal() {
        $sql="SELECT * FROM GOATX_RESEARCH_GLOBAL";
        $z=$this->sql($sql);
        $output=$z[0];
	$sql="SELECT * FROM GOATX_RESEARCH_GLOBAL_MARKET_CAP ORDER BY currency";
        $z=$this->sql($sql);
        $output['market_cap']=$z;
	$sql="SELECT * FROM GOATX_RESEARCH_GLOBAL_VOLUME ORDER BY currency";
        $z=$this->sql($sql);
        $output['volume']=$z;
	$sql="SELECT * FROM GOATX_RESEARCH_GLOBAL_PCT ORDER BY currency";
        $z=$this->sql($sql);
        $output['pct']=$z;
        return $output;
    }

    function getResearchGlobal($POST){
       return $this->queryResearchGlobal();
    }

    function queryResearchExchangeRates() {
        $sql="SELECT * FROM GOATX_RESEARCH_EXCHANGE_RATES";
        $o=array();
        $z=$this->sql($sql);
        foreach($z as $z0) {
            $z0['symbol']=$z0['id'];
            array_push($o,$z0);  
        }
        return $o;
    }

    function getResearchExchangeRates($POST) {
        $output=array();
	$output=$this->queryResearchExchangeRates();
	return $output;
     }

    function queryResearchFinancePlatforms() {
        $sql="SELECT * FROM GOATX_RESEARCH_FINANCE_PLATFORMS WHERE id <> '' ORDER BY name";
        $o=array();
        $z=$this->sql($sql);
        foreach($z as $z0) {
            $a=hash('sha256',$z0['id']);
            $z0['key']=$a;
            array_push($o,$z0);  
        }
        return $o;
    }


    function queryResearchFinanceProducts() {
        $sql="SELECT * FROM GOATX_RESEARCH_FINANCE_PRODUCTS WHERE id <> '' ORDER BY platform";
        $o=array();
        $z=$this->sql($sql);
        foreach($z as $z0) {
            $a=hash('sha256',strtolower($z0['platform']));
            $z0['key']=$a;
            array_push($o,$z0);  
        }
        return $o;
    }

    function getResearchFinancePlatforms($POST) {
        $output=array();
	$output['products']=$this->queryResearchFinanceProducts();
	$output['platforms']=$this->queryResearchFinancePlatforms();
	return $output;
    }

    function queryResearchDerivativeExchanges() {
        $sql="SELECT * FROM GOATX_RESEARCH_DERIVATIVE_EXCHANGES WHERE id <> ''";
        $o=array();
        $z=$this->sql($sql);
        foreach($z as $z0) {
            $a=hash('sha256',strtolower(strtolower($z0['name'])));
            $z0['key']=$a;
            $z0['description']=str_replace('\r','',$z0['description']);
            $z0['description']=str_replace('\n','',$z0['description']);
            $z0['description']=str_replace("\n",'',$z0['description']);
            $z0['description']=str_replace("\r",'',$z0['description']);
	    $z0['url']=substr($z0['url'],0,45);
            array_push($o,$z0);  
        }
        return $o;
    }

    function queryResearchDerivatives() {
        $sql="SELECT * FROM GOATX_RESEARCH_DERIVATIVES WHERE id <> ''";
        $o=array();
        $z=$this->sql($sql);
        foreach($z as $z0) {
            $a=hash('sha256',strtolower(strtolower($z0['market'])));
            $z0['key']=$a;
            array_push($o,$z0);  
        }
        return $o;
    }

    function getResearchDerivatives($POST) {
        $output=array();
	$output['platforms']=$this->queryResearchDerivativeExchanges();
	$output['products']=$this->queryResearchDerivatives();
	return $output;
    }

    
    function queryResearchIndexes() {
        $sql="SELECT * FROM GOATX_RESEARCH_INDEXES WHERE id <> '' ORDER BY name";
        $z=$this->sql($sql);
        return $z;
    }

    function getResearchIndexes($POST) {
        return $this->queryResearchIndexes();
    }

    function getResearchTokenDashboard($data) {
        $sql="SELECT * FROM GOATX_RESEARCH_TOKEN WHERE id = '" . $data['id'] . "'";
        $z=$this->sql($sql);
	$output=$z[0];
        return $output;
    }

    function getResearchPoolDashboard($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function getResearchPlatformDashboard($data) {
        $sql="SELECT * FROM GOATX_FARM_PROVIDER ORDER BY id";
        $z=$this->sql($sql);
        $output=$z;
        return $output;
    }

    function getResearchNftDashboard($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function getWalletTransactionDashboard($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function getTokenTransactionDashboard($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function getNftTransactionDashboard($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function getEthTransactionDashboard($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function getLpPoolDashboard($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function getYieldFarmDashboard($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function getTaggedWalletDashboard($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function getCexInteractionDashboard($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function getContractInteractionDashboard($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function getTokenWatchlistDashboard($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function getResearchHome($data) {
        $sql="SELECT * FROM GOATX_RESEARCH_TOKEN ORDER BY order_id";
        $output=array();
        $o=array();
        $z=$this->sql($sql);
        foreach($z as $z0) {
            $a=hash('sha256',$z0['id']);
            $z0['key']=$a;
            array_push($o,$z0);  
        }
        $output['tokens']=$o;
        return $output;
    }

    function getResearchPools($data) {

        $sql="SELECT * FROM GOATX_FARM_PROVIDER ORDER BY id";
        $output=array();
        $o=array();
        $z=$this->sql($sql);
        foreach($z as $z0) {
            $a=hash('sha256',$z0['id']);
            $z0['key']=$a;
            $z0['url']=substr($z0['url'],0,55);
            array_push($o,$z0);  
        }
        $sql="SELECT * FROM GOATX_FARMS ORDER BY id";
        $o2=array();
        $z=$this->sql($sql);
        foreach($z as $z0) {
            $a=hash('sha256',$z0['provider_id']);
            $z0['key']=$a;
            array_push($o2,$z0);  
        }
        $output['platforms']=$o;
        $output['farms']=$o2;
        return $output;
    }

    function getResearchNfts($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function getResearchPlatforms($data) {
        $sql="SELECT * FROM GOATX_FARM_PROVIDER ORDER BY id";
        $output=array();
        $o=array();
        $z=$this->sql($sql);
        foreach($z as $z0) {
            $a=hash('sha256',$z0['id']);
            $z0['key']=$a;
            $z0['url']=substr($z0['url'],0,55);
            array_push($o,$z0);  
        }
        $sql="SELECT * FROM GOATX_FARMS ORDER BY id";
        $o2=array();
        $z=$this->sql($sql);
        foreach($z as $z0) {
            $a=hash('sha256',$z0['provider_id']);
            $z0['key']=$a;
            array_push($o2,$z0);  
        }
        $output['platforms']=$o;
        $output['farms']=$o2;
        return $output;
    }

    function getResearchLptokens($data) {
        $output=array();
        $output['bep20']=array();
        $output['eth20']=array();
        return $output;
    }

    function getResearchTokens($data) {
        $sql="SELECT * FROM GOATX_RESEARCH_TOKEN ORDER BY order_id";
        $output=array();
        $o=array();
        $z=$this->sql($sql);
        foreach($z as $z0) {
            $a=hash('sha256',$z0['id']);
            $z0['key']=$a;
	    $end=strpos($z0['image'],"/",43);
	    $rr=substr($z0['image'],42,$end-42);
            $z0['sparkline']="https://deepgoat.com/api/svg/" . $z0['id'] . ".svg";
            array_push($o,$z0);  
        }
        $output['tokens']=$o;
        return $output;
    }

    function getContractInteractions($data) {
        $output=array();
        $output['address']=$data['address'];
        return $output;
    }
    function getTaggedWallets($data) {
        $output=array();
        $output['address']=$data['address'];
        return $output;
    }
    function getCexTransactions($data) {
        $output=array();
        $output['address']=$data['address'];
        return $output;
    }
    function getNftTransactions($data) {
        $output=array();
        $output['address']=$data['address'];
        return $output;
    }
    function getYieldFarms($data) {
        $output=array();
        $output['address']=$data['address'];
        return $output;
    }
    function getEditProfile($data) {
        $output=array();
        $output['address']=$data['address'];
        return $output;
    }
    function getLpPools($data) {
        $output=array();
        $output['address']=$data['address'];
        return $output;
    }
    function getEthTransactions($data) {
        $output=array();
        $output['address']=$data['address'];
        return $output;
    }
    function getAllTokenTransactions($data) {
        $output=array();
        $output['address']=$data['address'];
        return $output;
    }
    function getAllWalletTotals($data) {

	$sql="SELECT ID, wallet_address, hash, description, fromValue, fromValueUSD, transaction_type, toValue, toValueUSD, timestamp, ";
        $sql.=" CHAIN, gasTotal, USER_TAG, USER_COST_BASIS FROM BEP20_TRANSACTIONS WHERE wallet_address IN (SELECT WALLET_ADDRESS FROM ";
	$sql.=" GOATX_USER_WALLET WHERE USER_ID = " . $data['uid'] . ") AND HIDE_TRANSACTION <> 'Y' ORDER BY timestamp";
	$x=$this->sql($sql);
	$o=array();

         
        foreach($o as $oo) {


        }
    }

    function getAllWalletTransactions($data) {
        $output=array();
        // 
        //-- Get All the Wallets for this account.
        //
	$sql="SELECT id, name, trust_score_rank FROM GOATX_RESEARCH_EXCHANGES WHERE CAST(trust_score_rank AS UNSIGNED) > 0 ORDER BY CAST(trust_score_rank AS UNSIGNED)";
        $z=array();
	$x=$this->sql($sql);
        foreach ($x as $x0) {
            array_push($z,$x0);
        }
        $output['exchanges']=$z;
	$sql="SELECT * FROM GOATX_USER_WALLET WHERE USER_ID = " . $data['uid'] . " ORDER BY ID";
	$z=$this->sql($sql);
	$o=array();
	foreach($z as $z0) {
		$z0['wallet_key']=hash('sha256',$z0['WALLET_ADDRESS']);
		if ($z0['TAG']!="") {
                    $z0['label']=$z0['TAG'];
		} else {
                    $z0['label']=substr($z0['WALLET_ADDRESS'],0,6)."...".substr($z0['WALLET_ADDRESS'],-6);
		}
	       array_push($o,$z0);	
	}
	$output['wallets']=$o;
if (!isset($data['id'])) { $data['id']='0'; }
if ($data['id']=='0') {
	$sql="SELECT ID, wallet_address, hash, description, fromValue, fromValueUSD, transaction_type, toValue, toValueUSD, timestamp, ";
        $sql.=" CHAIN, gasTotal, USER_TAG, USER_COST_BASIS FROM BEP20_TRANSACTIONS WHERE wallet_address IN (SELECT WALLET_ADDRESS FROM ";
	$sql.=" GOATX_USER_WALLET WHERE USER_ID = " . $data['uid'] . ") AND HIDE_TRANSACTION <> 'Y' ORDER BY timestamp DESC";
} else {
	$sql="SELECT ID, wallet_address, hash, description, fromValue, fromValueUSD, transaction_type, toValue, toValueUSD, timestamp, ";
        $sql.=" CHAIN, gasTotal, USER_TAG, USER_COST_BASIS FROM BEP20_TRANSACTIONS WHERE wallet_address IN (SELECT WALLET_ADDRESS FROM ";
	$sql.=" GOATX_USER_WALLET WHERE USER_ID = " . $data['uid'] . ") AND HIDE_TRANSACTION <> 'Y' ORDER BY timestamp";
}
	$x=$this->sql($sql);
	$o=array();
	foreach($x as $x0) {
            if ($x0['USER_TAG']!="") $x0['tag_key']="tagged"; else $x0['tag_key']="";
	    $x0['wallet_key']=hash('sha256',$x0['wallet_address']);
	    foreach($output['wallets'] as $oo) {
            if (strtolower($oo['WALLET_ADDRESS'])==strtolower($x0['wallet_address'])) {
                        if ($oo['TAG']!="") {
                            $x0['wallet_tag']=$oo['TAG'];
			} else {
                            $x0['wallet_tag']=substr($x0['wallet_address'],0,6)."...".substr($x0['wallet_address'],-6);
			}
	    } 
	    }
	       array_push($o,$x0);	
	}

        $output['transactions']=$o;
        return $output;
    }
    function getAddWallet($data) {
        $output=array();
	$sql="SELECT id, UPPER(symbol) AS symbol, name FROM GOATX_RESEARCH_TOKEN ORDER BY UPPER(SYMBOL)";
	$x=$this->sql($sql);
        $z=$x;
        $output['tokens']=$z;
	$sql="SELECT id, name, trust_score_rank FROM GOATX_RESEARCH_EXCHANGES WHERE CAST(trust_score_rank AS UNSIGNED) > 10 ORDER BY name";
	$x=$this->sql($sql);
        foreach ($x as $x0) {
            array_push($z,$x0);
        }
        $output['exchanges']=$z;
	$sql="SELECT * FROM GOATX_USER_WALLET WHERE USER_ID = " . $data['uid'] . " ORDER BY ID";
	$z=$this->sql($sql);
	$o=array();
	foreach($z as $z0) {
		$z0['wallet_key']=hash('sha256',$z0['WALLET_ADDRESS']);
		if ($z0['BLOCKCHAIN']!="") {
                    $z0['label']=$z0['BLOCKCHAIN'];
		} else {
                    $z0['label']=substr($z0['WALLET_ADDRESS'],0,6)."...".substr($z0['WALLET_ADDRESS'],-6);
		}
	       array_push($o,$z0);	
	}
	$output['wallets']=$o;
        return $output;
    }

    function getTokenWatchlist($data) {
        $output=array();
        $output['address']=$data['address'];
        return $output;
    }
    function postGoatXWallet($data) {

    }

    function getAccountHome($data) {
        $output=array();
	$sql="SELECT id, name, trust_score_rank FROM GOATX_RESEARCH_EXCHANGES WHERE CAST(trust_score_rank AS UNSIGNED) > 0 ORDER BY CAST(trust_score_rank AS UNSIGNED)";
        $z=array();
	$x=$this->sql($sql);
        foreach ($x as $x0) {
            array_push($z,$x0);
        }
        $output['exchanges']=$z;
	$sql="SELECT * FROM GOATX_USER_WALLET WHERE USER_ID = " . $data['uid'] . " ORDER BY ID";
	$x=$this->sql($sql);
        $output['wallets']=$x;
	$sql="SELECT id, name, trust_score_rank FROM GOATX_RESEARCH_EXCHANGES WHERE CAST(trust_score_rank AS UNSIGNED) <= 10 ORDER BY CAST(trust_score_rank AS UNSIGNED)";
	$x=$this->sql($sql);
        $z=$x;

//	$sql="SELECT id, name, trust_score_rank FROM GOATX_RESEARCH_EXCHANGES WHERE CAST(trust_score_rank AS UNSIGNED) > 10 ORDER BY name";
//	$x=$this->sql($sql);
  //      foreach ($x as $x0) {
//            array_push($z,$x0);
//        }
//
//        $output['exchanges']=$z;
	$sql="SELECT * FROM BEP20_TRANSACTIONS WHERE wallet_address IN (SELECT WALLET_ADDRESS FROM ";
	$sql.=" GOATX_USER_WALLET WHERE USER_ID = " . $data['uid'] . ") ORDER BY timestamp DESC LIMIT 10";
	$x=$this->sql($sql);
	$o=array();
	foreach($x as $x0) {
	    $x0['wallet_key']=hash('sha256',$x0['wallet_address']);
	    foreach($output['wallets'] as $oo) {
                if ($oo['WALLET_ADDRESS']==$x0['wallet_address']) {
                        if ($oo['BLOCKCHAIN']!="") {
                            $x0['wallet_tag']=$oo['BLOCKCHAIN'];
			} else {
                            $x0['wallet_tag']=substr($x0['wallet_address'],0,6)."...".substr($x0['wallet_address'],-6);
			}
	    } 
	    }
	       array_push($o,$x0);	
	}

        $output['transactions']=$o;
        $o=array();
        $output['fiat_transactions']=$o;
        $d=array();
        $d['address']="0x951f430eebb6865467969fa1cf5f1625d717a2d1";
        $balances=array();
        $balances['ALL']=array();
        $totals=array();
        $totals['ALL']=0;
        $sql="SELECT chain, token_address, token_symbol, token_name, token_balance, token_address, token_price, token_total_usd from ";
        $sql.=" GOATX_USER_TOKEN_BALANCES WHERE wallet_address = 'ALL' and uid = " . $data['uid']; 
        $sql.=" ORDER BY token_symbol, token_name";
            $out=array();
            $u=$this->sql($sql);
            foreach($u as $v) {
                   if ($v['token_name']=='') {
                       $v['token_name']=$v['token_address'];
                   }
                   if (strpos($v['token_balance'],'E')>0) $v['token_balance']='0.0000'; else $v['token_balance']=number_format($v['token_balance'],4);
                   $v['token_price']=number_format(floatval(str_replace(",","",$v['token_price'])),4);
                   try {
                   $v['token_total_usd']=number_format(str_replace(",","",$v['token_total_usd']),4);
                   } 
                      catch(Exception $e) {
                      $v['token_total_usd']='0.0000';
                   }
                   $totals['ALL']=$totals['ALL']+floatval(str_replace(",","",$v['token_total_usd']));
                   array_push($out,$v);
            }
        $totals['ALL']=number_format($totals['ALL'],4);
        $balances['ALL']=$out;
 
        $sql="select WALLET_ADDRESS FROM GOATX_USER_WALLET WHERE USER_ID = " . $data['uid'];
        $v=$this->sql($sql);
        foreach($v as $q) {
            $totals[$q['WALLET_ADDRESS']]=0;
            $sql="SELECT chain, token_address, token_symbol, token_name, token_balance, token_address, token_price, token_total_usd from ";
            $sql.=" GOATX_USER_TOKEN_BALANCES WHERE wallet_address = '" . $q['WALLET_ADDRESS'] . "' and uid = " . $data['uid']; 
            $sql.=" ORDER BY token_symbol, token_name";
            $out=array();
            $u=$this->sql($sql);
            foreach($u as $v) {
                   if ($v['token_name']=='') {
                       $v['token_name']=$v['token_address'];
                   }
                   if (strpos($v['token_balance'],'E')>0) $v['token_balance']='0.0000'; else $v['token_balance']=number_format($v['token_balance'],4);
                   $v['token_price']=number_format(str_replace(",","",$v['token_price']),4);
                   try {
                   $v['token_total_usd']=number_format(str_replace(",","",$v['token_total_usd']),4);
                   } 
                      catch(Exception $e) {
                      $v['token_total_usd']='0.0000';
                   }
                   $totals[$q['WALLET_ADDRESS']]=$totals[$q['WALLET_ADDRESS']]+str_replace(",","",$v['token_total_usd']);
                   array_push($out,$v);
            }
            $balances[$q['WALLET_ADDRESS']]=$out;
        }
        $output['balances']=$balances;
        $output['totals']=$totals;
        $o=array();
        $output['pools']=$o;
        $output['nfts']=$o;
        $o=array();
        $output['tagged_wallets']=$o;
  
        return $output;
    }

        function saveAccessData($data) {
            $sql="insert into GOATX_USER_SESSION (uid,email,timestamp,ip_address, location_name, ";
            $sql.=" token, http_referer, query_string, server_name, mobile, desktop, tablet, os, os_version, ";
            $sql.=" browser, browser_version, user_agent, orientation, provider_type, status) values (";
            if (isset($data['uid'])) {
                 $sql.=$data['uid'] . ", ";      //uid
            } else {
                 $sql.="0, ";
            }
            if (isset($data['email'])) {
                 $sql.="'" . $data['email'] . "', ";     //email
            } else {
                 $sql.="'', ";
            }
            $timestamp=time();
            $sql.=$timestamp . ", ";             //timestamp
            if (isset($_SERVER['REMOTE_ADDR'])) {
		$sql .= "'" . $_SERVER['REMOTE_ADDR'] . "',";
            } else {
		$sql .= "'',";
            }
            $sql.="'',";                         // Location Name 
            $ip = rand(100000,999999);
            $token=hash('sha256',$ip."GOAT".$timestamp."X");
            $sql.="'".$token."',";               // Token

            if (isset($_SERVER['HTTP_REFERER'])) {
		$sql .= "'" . $_SERVER['HTTP_REFERER'] . "',";    //http_referer
            } else {
		$sql .= "'',";
            }

            if (isset($_SERVER['QUERY_STRING'])) {
		$sql .= "'" . $_SERVER['QUERY_STRING'] . "',";    //query_string
            } else {
		$sql .= "'',";
            }
            if (isset($_SERVER['SERVER_NAME'])) {          // server_name
		$sql .= "'" . $_SERVER['SERVER_NAME'] . "',";  
            } else {
		$sql .= "'',";
            }
            if (isset($data['mobile'])) {
		$sql.="'" . $data['mobile'] . "',";
            } else {
		$sql .= "'',";
            }
            if (isset($data['desktop'])) {
		$sql.="'" . $data['desktop'] . "',";
            } else {
		$sql .= "'',";
            }
            if (isset($data['tablet'])) {
		$sql.="'" . $data['tablet'] . "',";        // tablet
            } else {
		$sql .= "'',";
            }
            if (isset($data['os'])) {
		$sql.="'" . $data['os'] . "',";        // os
            } else {
		$sql .= "'',";
            }
            if (isset($data['os_version'])) {
		$sql.="'" . $data['os_version'] . "',";        // os_version
            } else {
		$sql .= "'',";
            }
            if (isset($data['browser'])) {
		$sql.="'" . $data['browser'] . "',";        // browser
            } else {
		$sql .= "'',";
            }
            if (isset($data['browser_version'])) {
		$sql.="'" . $data['browser_version'] . "',";        // browser_version
            } else {
		$sql .= "'',";
            }
            if (isset($data['user_agent'])) {
		$sql.="'" . $data['user_agent'] . "',";        // user_agent
            } else {
		$sql .= "'',";
            }
            if (isset($data['orientation'])) {                // orientation
		$sql.="'" . $data['orientation'] . "',";        // user_agent
            } else {
		$sql .= "'',";
            }
	    $sql .= "'',";                                    // provider_type
            if (isset($data['status'])) {
		$sql.="'" . $data['status'] . "')";
            } else {
		$sql .= "'')";
            }
            $this->execute($sql);
            return $token;
        }

        function postUserAccess($data) {
            $sql="insert into GOATX_USER_ACCESS (uid,email,timestamp,query, ip_address, location_name, ";
            $sql.=" token, http_referer, query_string, server_name, mobile, desktop, tablet, os, os_version, ";
            $sql.=" browser, browser_version, user_agent, orientation, provider_type, id, address, status) values (";
            if (isset($data['uid'])) {
                 $sql.=$data['uid'] . ", ";      //uid
            } else {
                 $sql.="0, ";
            }
            if (isset($data['email'])) {
                 $sql.="'" . $data['email'] . "', ";     //email
            } else {
                 $sql.="'', ";
            }
            $timestamp=time();
            $sql.=$timestamp . ", ";             //timestamp
            if (isset($data['q'])) {
                 $sql.="'" . $data['q'] . "', ";     //email
            } else {
                 $sql.="'', ";
            }
            if (isset($_SERVER['REMOTE_ADDR'])) {
		$sql .= "'" . $_SERVER['REMOTE_ADDR'] . "',";
            } else {
		$sql .= "'',";
            }
            $sql.="'',";                         // Location Name 

            if (isset($data['token'])) {
                 $sql.="'" . $data['token'] . "', ";     //token
            } else {
                 $sql.="'', ";
            }
            if (isset($_SERVER['HTTP_REFERER'])) {
		$sql .= "'" . $_SERVER['HTTP_REFERER'] . "',";    //http_referer
            } else {
		$sql .= "'',";
            }

            if (isset($_SERVER['QUERY_STRING'])) {
		$sql .= "'" . $_SERVER['QUERY_STRING'] . "',";    //query_string
            } else {
		$sql .= "'',";
            }
            if (isset($_SERVER['SERVER_NAME'])) {          // server_name
		$sql .= "'" . $_SERVER['SERVER_NAME'] . "',";  
            } else {
		$sql .= "'',";
            }
            if (isset($data['mobile'])) {
		$sql.="'" . $data['mobile'] . "',";
            } else {
		$sql .= "'',";
            }
            if (isset($data['desktop'])) {
		$sql.="'" . $data['desktop'] . "',";
            } else {
		$sql .= "'',";
            }
            if (isset($data['tablet'])) {
		$sql.="'" . $data['tablet'] . "',";        // tablet
            } else {
		$sql .= "'',";
            }
            if (isset($data['os'])) {
		$sql.="'" . $data['os'] . "',";        // os
            } else {
		$sql .= "'',";
            }
            if (isset($data['os_version'])) {
		$sql.="'" . $data['os_version'] . "',";        // os_version
            } else {
		$sql .= "'',";
            }
            if (isset($data['browser'])) {
		$sql.="'" . $data['browser'] . "',";        // browser
            } else {
		$sql .= "'',";
            }
            if (isset($data['browser_version'])) {
		$sql.="'" . $data['browser_version'] . "',";        // browser_version
            } else {
		$sql .= "'',";
            }
            if (isset($data['user_agent'])) {
		$sql.="'" . $data['user_agent'] . "',";        // user_agent
            } else {
		$sql .= "'',";
            }
            if (isset($data['orientation'])) {                // orientation
		$sql.="'" . $data['orientation'] . "',";        // user_agent
            } else {
		$sql .= "'',";
            }
	    $sql .= "'',";                                    // provider_type
            if (isset($data['id'])) {                // orientation
		$sql.="'" . $data['id'] . "',";        // user_agent
            } else {
		$sql .= "'',";
            }
            if (isset($data['address'])) {                // orientation
		$sql.="'" . $data['address'] . "',";        // user_agent
            } else {
		$sql .= "'',";
            }
            if (isset($data['status'])) {
		$sql.="'" . $data['status'] . "')";
            } else {
		$sql .= "'')";
            }
            //$this->execute($sql);
        }

        function getUserLogin($data) {
              $sql="select ID, CREATE_TIMESTAMP, pwd, verified, first_name, last_name, email FROM GOATX_USER where email = '" . strtolower($data['email']) . "'";
              $t=$this->sql($sql);
              $output=array();
              if (sizeof($t)==0) {
                  $output['error']="100";
                  $output['uid']=0;
                  $output['first_name']="";
                  $output['last_name']="";
                  $output['message']="Invalid Login - Please Try Again!";
                  $data['uid']=0;
                  $data['status']="FAILED-NO ACCOUNT";
                  $this->saveAccessData($data);
              } else {
                     $pwdin=hash('sha256',$data['pwd'] . $t[0]['CREATE_TIMESTAMP']);
                     if ($pwdin!=$t[0]['pwd']) {
                         $output['error']="101";
                         $output['uid']=0;
                         $output['first_name']="";
                         $output['last_name']="";
                         $output['email']="";
                         $output['message']="Invalid Login - Please Try Again";
                         $data['uid']=0;
                         $data['status']="FAILED-INVALID PWD";
                         $this->saveAccessData($data);
                     } else {
                         $output['error']="0";
                         $output['uid']=$t[0]['ID'];
                         $output['first_name']=$t[0]['first_name'];
                         $output['last_name']=$t[0]['last_name'];
                         $output['email']=$t[0]['email'];
                         $output['message']="";
                         $data['uid']=$t[0]['ID'];
                         $data['status']="SUCCESS";
                         $token=$this->saveAccessData($data);
                         $output['token']=$token;
                     }
              }
              return $output;
        }

        function checkUserEmail($data) {

              $sql="SELECT COUNT(*) AS C FROM GOATX_USER WHERE email = '" . strtolower($data['email']) . "'";
              $t=$this->sql($sql);
              $output=array();
              $output['count']=$t[0]['C'];
              return $output;
        }
        function checkUserUsername($data) {

              $sql="SELECT COUNT(*) AS C FROM GOATX_USER WHERE lower(user) = '" . strtolower($data['user']) . "'";
              $t=$this->sql($sql);
              $output=array();
              $output['count']=$t[0]['C'];
              return $output;
        }

        function postCreateUser($data) {
              $sql="SELECT COUNT(*) AS C FROM GOATX_USER WHERE email = '" . strtolower($data['email']) . "'";
              $t=$this->sql($sql);
              $output=array();
              if ($t[0]['C']>0) {
                 $output['uid']=0;
                 $output['error']="200"; 
                 $output['message']="This EMAIL already used.";
              } else {
                 $post=array();
                 $post['TABLE_NAME']="GOATX_USER";
                 $post['email']=$data['email'];
                 $post['ip_address']=$_SERVER['REMOTE_ADDR'];
                 if (isset($data['fname'])) $post['first_name']=$data['fname'];
                 if (isset($data['lname'])) $post['last_name']=$data['lname'];
                 if (isset($data['user'])) $post['user']=$data['user'];

                 $id=$this->post($post);
                 $sql="select CREATE_TIMESTAMP, pwd, verified, first_name, last_name FROM GOATX_USER where email = '" . strtolower($data['email']) . "'";
                 $t=$this->sql($sql);
                 $pwdin=hash('sha256',$data['pwd'] . $t[0]['CREATE_TIMESTAMP']);
                 $d=array();
                 $d['email']=$data['email'];
                 $d['fname']=$data['fname'];
                 $d['lname']=$data['lname'];
                 $this->syncMailchimp($d);
                 $sql="UPDATE GOATX_USER SET pwd = '" . $pwdin . "' WHERE ID = " . $id;
                 $this->execute($sql);
                 $output['uid']=$id;
                 $output['error']="0";
                 $output['message']="";
              }
              return $output;
        }

	function getExceptionList($data) {
		$sql="SELECT ADDRESS AS contractAddress, CHAIN, C FROM GOATX_CONTRACT_EXCEPTION ORDER BY C DESC";
		$z=$this->sql($sql);
		return $z;
	}
	function getWalletList($data) {
		$sql="SELECT * FROM GOATX_WALLET WHERE tag <> '' OR exclude = 'Y' ORDER BY ID DESC";
		$z=$this->sql($sql);
		return $z;
	}

    function postUpdateWallet($data) {
             $sql="SELECT * FROM GOATX_WALLET WHERE wallet_address = '" . strtolower($data['wallet_address']) . "'";
             $z=$this->sql($sql);
             $post=array();
             if (sizeof($z)>0) {
                 $post['ID']=$z[0]['ID'];
             }
             $post['TABLE_NAME']="GOATX_WALLET";
             $post['ACTION']="insert";
             $post['wallet_address']=strtolower($data['wallet_address']);
             $post['exclude']=strtoupper($data['exclude']);
             $post['tag']=$data['tag'];
             $post['source_id']=$data['source_id'];
             $post['timestamp']=time();
             $id=$this->post2($post);
             $sql="COMMIT";
             $this->execute($sql);
             $output=array();
             $output['error']='0';
             $output['records']=10;
             return $output;
    }

    function postUpdateContract($data) {
              $sql="SELECT * FROM BEP20_CONTRACT WHERE LOWER(CONTRACT_ADDRESS) = '" . strtolower($data['address']) . "'";
              $z=$this->sql($sql);
              $post=array();
              if (sizeof($z)>0) {
                  $post['ID']=$z[0]['ID'];
              }
                   $post['TABLE_NAME']="BEP20_CONTRACT";
                   $post['ACTION']="insert";
                   $post['CONTRACT_ADDRESS']=strtolower($data['address']);
                   $post['CONTRACT_TYPE']=strtoupper($data['ctype']);
                   $post['PLATFORM']=$data['chain'];
                   if (isset($data['name'])) {
                         $post['CONTRACT_NAME']=$data['name'];
                   } else { 
                         $post['CONTRACT_NAME']="";
                         $data['name']='.';
                   }
                   if (isset($data['symbol'])) {
                       $post['SYMBOL']=strtoupper($data['symbol']);
                   } else {
                       $post['SYMBOL']="";
                       $data['symbol']="";
                   }
                   $id=$this->post($post);
             $sql="DELETE FROM GOATX_CONTRACT_EXCEPTION WHERE ADDRESS = '" . strtolower($data['address']) . "'";
             $this->execute($sql);
             $output=array();
             $output['error']='0';
             $output['records']=10;
             return $output;
        }

    function postUpdateContract2($data) {
              $sql="SELECT * FROM BEP20_CONTRACT WHERE LOWER(CONTRACT_ADDRESS) = '" . strtolower($data['address']) . "'";
              $z=$this->sql($sql);
              if (sizeof($z)==0) {
                   $post=array();
                   $post['TABLE_NAME']="BEP20_CONTRACT";
                   $post['ACTION']="insert";
                   $post['CONTRACT_ADDRESS']=strtolower($data['address']);
                   $post['CONTRACT_TYPE']=strtoupper($data['ctype']);
                   $post['PLATFORM']=$data['chain'];
                   if (isset($data['name'])) {
                         $post['CONTRACT_NAME']=$data['name'];
                   } else { 
                         $post['CONTRACT_NAME']="";
                         $data['name']='.';
                   }
                   if (isset($data['symbol'])) {
                       $post['SYMBOL']=strtoupper($data['symbol']);
                   } else {
                       $post['SYMBOL']="";
                       $data['symbol']="";
                   }
                   if ($data['ctype']!="FLAGGED") {
                       $id=$this->post($post);
                   }
               } else {
                   $data['address']=strtolower($z[0]['CONTRACT_ADDRESS']);
                   $data['name']=$z[0]['CONTRACT_NAME'];
                   $data['ctype']=$z[0]['CONTRACT_TYPE'];
                   $data['symbol']=$z[0]['SYMBOL'];
                   
               }
               $sql="select count(*) as C FROM BEP20_TRANSACTIONS WHERE lower(contractAddress) = '" . strtolower($data['address']) . "' ";
               $x=$this->sql($sql);
               $output=array();
               $output['error']=0;
                   $output['records']=$x[0]['C'];
                   if ($data['ctype']!="FLAGGED") {
                        $sql="UPDATE BEP20_TRANSACTIONS SET contractName = '" . $data['name'] . "', contractSymbol = '" . $data['symbol'] . "', ";
                        $sql.=" contractType='" . $data['ctype'] . "' where lower(contractAddress) = '" . strtolower($data['address']) . "' ";
                        $this->execute($sql);
                   } else {
                        $sql="UPDATE BEP20_TRANSACTIONS SET contractType = 'FLAGGED'";
                        $sql.=" where contractAddress = '" . strtolower($data['address']) . "' ";
                        $this->execute($sql);
                   }

                   return $output;
        }

function getDashboardCounts($data) {
		
		$sql="SELECT COUNT(*) AS C FROM BEP20_CONTRACT";
		$z=$this->sql($sql);
		$output=array();
		$output['contracts']=number_format($z[0]['C']);

			$sql="SELECT COUNT(*) AS C FROM BEP20_CONTRACT WHERE CONTRACT_TYPE IN ('farm-BEP20','farm-ERC20')";
			$z=$this->sql($sql);
			$output['farms']=number_format($z[0]['C']);
			
			$sql="SELECT COUNT(*) AS C FROM GOATX_TOKEN_200";
			$z=$this->sql($sql);
			$output['tokens']=number_format($z[0]['C']);

			$sql="SELECT COUNT(*) AS C FROM GOATX_WALLET";
			$z=$this->sql($sql);
			$output['wallets']=number_format($z[0]['C']);

			$sql="SELECT COUNT(*) AS C FROM BEP20_TRANSACTIONS";
			$z=$this->sql($sql);
			$output['transactions']=number_format($z[0]['C']);
			
			$sql="SELECT COUNT(*) AS C FROM BEP20_TRANSACTIONS WHERE contractAddress <> '' AND contractName = ''";
			$z=$this->sql($sql);
			$output['exceptions']=number_format($z[0]['C']);			

			$sql="SELECT COUNT(*) AS C FROM BEP20_TOKEN_PAIR";
			$z=$this->sql($sql);
			$output['pools']=number_format($z[0]['C']);	
			
			return $output;
	}
	
	function getTokenList($data) {
		$sql="SELECT * FROM GOATX_TOKEN_200 ORDER BY symbol, CHAIN";
		$z=$this->sql($sql);
		return $z;
	}
//	function getExceptionList($data) {
//		$sql="SELECT * FROM BEP20_TRANSACTIONS WHERE contractAddress <> '' AND contractName = '' ORDER BY timestamp LIMIT 500";
//		$z=$this->sql($sql);
//		return $z;
//	}
	function getContractList($data) {
		$sql="SELECT * FROM BEP20_CONTRACT ORDER BY CONTRACT_NAME";
		$z=$this->sql($sql);
		return $z;
	}
	function getOneContract($data) {
		$sql="SELECT * FROM BEP20_CONTRACT WHERE ID = "  . $data['address'];
		$z=$this->sql($sql);
		return $z[0];
	}

	function getOneWallet($data) {
		$sql="SELECT * FROM BEP20_CONTRACT WHERE wallet_address = '"  . $data['address'] . "'";
		$z=$this->sql($sql);
		return $z[0];
	}

//	function getWalletList($data) {
//		$sql="SELECT * FROM GOATX_WALLET ORDER BY ID";
//		$z=$this->sql($sql);
//		return $z;
//	}
	
	function getTokenAssets($data){
		
		$address=strtolower($data['address']);
//		$this->B->calculateBSCTokenBalances($address);
//		$this->B->populate_raw_transactions($address);
//		$this->B->process_transactions($address);

		$bsc_balances=array();
		$eth_balances=array();
		$sql="select ID, SYMBOL, NAME, BALANCE, CONTRACT FROM BEP20_WALLET_TOKEN WHERE WALLET_ADDRESS = '" . strtolower($address) . "' ORDER BY SYMBOL";
		$z=$this->sql($sql);
		$output=array();

		$bep20_value=0;
		$erc20_value=0;
		$lp_value=0;
		$lp_erc_value=0;

		foreach($z as $a) {
				$o=array();
				$o['ID']=$a['ID'];
				$o['SYMBOL']=$a['SYMBOL'];
				$o['NAME']=$a['NAME'];
				$o['BALANCE']=round(floatval($a['BALANCE']),4);
				$sql="SELECT ID, TOKEN_PRICE_USD FROM BEP20_PRICE WHERE SYMBOL = '" . $a['SYMBOL'] . "' ORDER BY TIMESTAMP DESC";
				//$b=$this->sql($sql);
				//if (sizeof($b)>0) {
				//		$usd=round(floatval($b[0]['TOKEN_PRICE_USD']),2);
				//		$value=round(floatval($a['BALANCE']*$usd),2);
				//} else {
				//		$usd="0.00";
				//		$value="0.00";
				//}
				//$o['USD']='$'.$usd;
				//$bep20_value += $value;
				$o['USD']="0";
				//$o['VALUE']='$' . $value;
                                $o['VALUE']="$0.00";
				array_push($bsc_balances,$o);
		}

		$lp=array();
		$sql="SELECT NAME, LP_AMOUNT, TOKEN0_USD, TOKEN1_USD FROM BEP20_WALLET_LP WHERE WALLET_ADDRESS = '" . strtolower($address) . "' ORDER BY NAME";
		$z=$this->sql($sql);
		foreach($z as $a) {
				$o=array();
				$o['ID']=$a['ID'];
				$o['NAME']=$a['NAME'];
				$o['LP_AMOUNT']=$a['LP_AMOUNT'];
				$g=round(floatval($a['TOKEN0_USD']),2);
				$o['B1']='$' . $g;
				$h=round(floatval($a['TOKEN1_USD']),2);
				$o['B2']='$' . $h;
				$o['TOTAL']='$' . ($g+$h);
				$lp_value+=$g+$h;
				array_push($lp,$o);
		}

		$sql="select ID, SYMBOL, NAME, BALANCE FROM ERC20_WALLET_TOKEN WHERE WALLET_ADDRESS = '" . strtolower($address) . "' ORDER BY SYMBOL";
		$z=$this->sql($sql);
		$output=array();
		foreach($z as $a) {
				$o=array();
				$o['ID']=$a['ID'];
				$o['SYMBOL']=$a['SYMBOL'];
				$o['NAME']=$a['NAME'];
				$o['BALANCE']=round(floatval($a['BALANCE']),4);
				array_push($eth_balances,$o);
		}

		$sql="select ID, timestamp, hash, fromAddress, toAddress, fromTokenSymbol, toTokenSymbol, toTokenDecimal, fromTokenDecimal, fromValue, toValue, ";
		$sql.="description, transaction_type from BEP20_TRANSACTIONS where wallet_address = '" . strtolower($address) . "' ORDER BY timestamp desc";
		$z=$this->sql($sql);
		$i=0;
		$bsc_transactions=array();

		foreach($z as $a) {
		
				$i++;
				if ($i<50) {
					
				$o=array();
				$o['ID']=$a['ID'];
				$o['DATE']=date('m/d/Y',$a['timestamp']);
				$o['timestamp']=$a['timestamp'];
				if ($a['fromAddress']==$address) {
					$o['address']=substr($a['toAddress'],0,10) . "..." . substr($a['toAddress'],-10);
								$o['full_address']=$a['toAddress'];
				} else {
					$o['address']=substr($a['fromAddress'],0,10) . "..." . substr($a['fromAddress'],-10);
								$o['full_address']=$a['fromAddress'];
				}
				$o['description']=$a['description'];
				$o['fromValue']=$a['fromValue'];
				$o['toValue']=$a['toValue'];
				$o['fromToken']=$a['fromTokenSymbol'];
				$o['toToken']=$a['toTokenSymbol'];
				$o['hash']=substr($a['hash'],0,10) . "..." . substr($a['hash'],-10);
                $o['transaction_type']=$a['transaction_type'];
				array_push($bsc_transactions,$o);
			}	
		}

		$sql="select ID, timestamp, hash, fromAddress, toAddress, fromTokenSymbol, toTokenSymbol, toTokenDecimal, fromTokenDecimal, fromValue, toValue, ";
		$sql.="description, transaction_type from ERC20_TRANSACTIONS where wallet_address = '" . strtolower($address) . "' ORDER BY timestamp desc";
		$z=$this->sql($sql);
		$i=0;
		$eth_transactions=array();
		
		foreach($z as $a) {
				$i++;
				if ($i<50) {			
					$o=array();
					$o['ID']=$a['ID'];
					$o['DATE']=date('m/d/Y',$a['timestamp']);
					$o['timestamp']=$a['timestamp'];
					if ($a['fromAddress']==$address) {
						$o['address']=substr($a['toAddress'],0,10) . "..." . substr($a['toAddress'],-10);
						$o['full_address']=$a['toAddress'];
					} else {	
						$o['address']=substr($a['fromAddress'],0,10) . "..." . substr($a['fromAddress'],-10);
						$o['full_address']=$a['fromAddress'];
					}
				$o['description']=$a['description'];
				$o['fromValue']=$a['fromValue'];
				$o['toValue']=$a['toValue'];
				$o['fromToken']=$a['fromTokenSymbol'];
				$o['toToken']=$a['toTokenSymbol'];
                $o['hash']=substr($a['hash'],0,10) . "..." . substr($a['hash'],-10);
                $o['transaction_type']=$a['transaction_type'];
				array_push($eth_transactions,$o);
				}
		}
		$output['bep20_value']='$' . round($bep20_value,2);
		$output['erc20_value']='$' . round($erc20_value,2);
		$output['lp_value']='$' . round($lp_value,2);
		$output['lp_erc_value']='$' . round($lp_erc_value,2);
		$output['v']=round($bep20_value,2)+round($erc20_value,2)+round($lp_value,2)+round($lp_erc_value,2);
		$output['vd']='$' . (round($bep20_value,2)+round($erc20_value,2)+round($lp_value,2)+round($lp_erc_value,2));
		$output['erc20_v']=round($erc20_value,2);
		$output['lp_v']=round($lp_value,2);
		$output['lp_erc_v']=round($lp_erc_value,2);
		$output['lp']=$lp;
		$output['bsc_balances']=$bsc_balances;
		$output['bsc_transactions']=$bsc_transactions;
		$output['eth_balances']=$eth_balances;
		$output['eth_transactions']=$eth_transactions;
        return $output;		
	}
	
	function getTokenData($data) {
		$address=strtolower($data['address']);
		$token_symbol=strtoupper($data['token']);
		$sql="SELECT * FROM BEP20_WALLET_TOKEN WHERE UPPER(SYMBOL) = '" . $token_symbol . "' ";
		$sql.=" AND WALLET_ADDRESS = '" . strtolower($address) . "'";
		$z=$this->sql($sql);
		if (sizeof($z)>0) {
				$balance=$z[0]['BALANCE'];
		} else {
				$balance="0";
		}
		$sql="select * from BEP20_TOKEN WHERE UPPER(SYMBOL) = '" . strtoupper($token_symbol) . "'";
		$t=$this->sql($sql);
		$output=array();
		if (sizeof($t)>0) {
			$contract_address=strtolower($t[0]['CONTRACT_ADDRESS']);
			$token_data=$this->B->getGeckoTokenData($contract_address);
			$token_data=str_replace('null','""',$token_data);
			$token_data=stripcslashes($token_data);
			$token_array=json_decode($token_data,true);
			if (!isset($token_array['error'])) {
					//--
					//-- Gecko Token Returned valid data.
					//--
					$token=array();
					$token['token_id']=$token_array['id'];
					$token['symbol']=$token_array['symbol'];
					$token['name']=$token_array['name'];
					if (isset($token_array['description']['en'])) {
						$token['description']=$token_array['description']['en'];
					} else {
						$token['description']="";	
					}

			if (isset($token_array['market_data']['current_price']['usd'])) {
				$token['price']=$token_array['market_data']['current_price']['usd'];
			} else {
				$token['price']="0";
			}
			
            $token['contract_address']=$contract_address;
			
			if (isset($token_array['market_data']['total_supply'])) {
				$token['total_supply']=$token_array['market_data']['total_supply'];			
			} else {
				$token['total_supply']="0";
			}

			if (isset($token_array['market_cap_rank'])) {			
			$token['market_cap_rank']=$token_array['market_cap_rank'];
			} else {
			$token['market_cap_rank']="";				
			}
			
			if (isset($token_array['market_data']['market_cap']['usd'])) {			
			$token['market_cap']=$token_array['market_data']['market_cap']['usd'];	
			} else {
			$token['market_cap']="";				
			}
		
			if (isset($token_array['market_data']['high_24h']['usd'])) {			
			$token['high_24h']=$token_array['market_data']['high_24h']['usd'];		
			} else {
			$token['high_24h']="";				
			}
			
			if (isset($token_array['market_data']['low_24h']['usd'])) {			
			$token['low_24h']=$token_array['market_data']['low_24h']['usd'];
			} else {
			$token['low_24h']="";				
			}
			
			if (isset($token_array['market_data']['max_supply'])) {				
			$token['max_supply']=$token_array['market_data']['max_supply'];			
			} else {
			$token['max_supply']="";				
			}
			
			if (isset($token_array['market_data']['circulating_supply'])) {			
			$token['circulating_supply']=$token_array['market_data']['circulating_supply'];	
			} else {
			$token['circulating_supply']="";				
			}
			
			if (isset($token_array['market_data']['fully_diluted_valuation']['usd'])){
				$token['fully_diluted_valuation']=$token_array['market_data']['fully_diluted_valuation']['usd'];		
			} else {
				$token['fully_diluted_valuation']="No Data";		
			}
			
			if (isset($token_array['market_data']['total_value_locked']['usd'])) {			
			$token['total_value_locked']=$token_array['market_data']['total_value_locked']['usd'];		
			} else {
			$token['total_value_locked']="";				
			}
			
            $token_volume=0;
			$token_volume_usd=0;
			foreach($token_array['tickers'] as $t) {
				$token_volume+=$t['volume'];
				$token_volume_usd+=$t['converted_volume']['usd'];
			}
			$token['volume_24h']=$token_volume;
			$token['value_24h']=$token_volume_usd;
			
			if (isset($token_array['links']['homepage'][0])) {			
			$token['website']=$token_array['links']['homepage'][0];
			} else {
			$token['website']="";				
			}
				
			if (isset($token_array['links']['official_forum_url'][0])) {			
			$token['forum']=$token_array['links']['official_forum_url'][0];
			} else {
			$token['forum']="";				
			}
			
			if (isset($token_array['links']['chat_url'][0])) {			
			$token['discord']=$token_array['links']['chat_url'][0];
			} else {
			$token['discord']="";				
			}
			
			if (isset($token_array['links']['twitter_screen_name'])) {			
			$token['twitter']=$token_array['links']['twitter_screen_name'];
			} else {
			$token['twitter']="";				
			}
			
			if (isset($token_array['links']['telegram_channel_identifier'])) {			
			$token['telegram']=$token_array['links']['telegram_channel_identifier'];
			} else {
			$token['telegram']="";				
			}
			
			if (isset($token_array['links']['subreddit_url'])) {			
			$token['reddit']=$token_array['links']['subreddit_url'];
			} else {
			$token['reddit']="";				
			}
			
			if (isset($token_array['links']['repos_url']['github'][0])) {
			$token['github']=$token_array['links']['repos_url']['github'][0];
			} else {
			$token['github']="";				
			}
			
			if (isset($token_array['image']['thumb'])) {			
			$token['img_thumb']=$token_array['image']['thumb'];
			} else {
			$token['img_thumb']="";				
			}
			
			if (isset($token_array['image']['small'])) {			
			$token['img_small']=$token_array['image']['small'];
			} else {
			$token['img_small']="";				
			}
						
			if (isset($token_array['image']['large'])) {			
			$token['img_large']=$token_array['image']['large'];
			} else {
			$token['img_large']="";				
			}
			
			if (isset($token_array['market_data']['mcap_to_tvl_ratio'])) {			
			$token['mcap_to_tvl_ratio']=$token_array['market_data']['mcap_to_tvl_ratio'];			
			} else {
			$token['mcap_to_tvl_ratio']="";				
			}
			
			if (isset($token_array['market_data']['ath']['usd'])) {			
			$token['all_time_high']=$token_array['market_data']['ath']['usd'];		
			} else {
			$token['all_time_high']="";				
			}
			
			if (isset($token_array['market_data']['atl']['usd'])) {			
			$token['all_time_low']=$token_array['market_data']['atl']['usd'];		
			} else {
			$token['all_time_low']="";				
			}
			
			if (isset($token_array['market_data']['total_volume']['usd'])) {			
			$token['total_volume']=$token_array['market_data']['total_volume']['usd'];					
			} else {
			$token['total_volume']="";				
			}
			
			if (isset($token_array['market_data']['price_change_24h'])) {			
			$token['price_change_24h']=$token_array['market_data']['price_change_24h'];
			} else {
			$token['price_change_24h']="";				
			}
			
			if (isset($token_array['market_data']['price_change_percentage_24h'])) {			
			$token['price_change_percentage_24h']=$token_array['market_data']['price_change_percentage_24h'];
			} else {
			$token['price_change_percentage_24h']="";				
			}
			
			if (isset($token_array['market_data']['price_change_percentage_7d'])) {			
			$token['price_change_percentage_7d']=$token_array['market_data']['price_change_percentage_7d'];
			} else {
			$token['price_change_percentage_7d']="";				
			}
			
			if (isset($token_array['market_data']['price_change_percentage_14d'])) {			
			$token['price_change_percentage_14d']=$token_array['market_data']['price_change_percentage_14d'];
			} else {
			$token['price_change_percentage_14d']="";				
			}
			
			if (isset($token_array['market_data']['price_change_percentage_30d'])) {			
			$token['price_change_percentage_30d']=$token_array['market_data']['price_change_percentage_30d'];
			} else {
			$token['price_change_percentage_30d']="";				
			}
			
			if (isset($token_array['market_data']['price_change_percentage_60d'])) {			
			$token['price_change_percentage_60d']=$token_array['market_data']['price_change_percentage_60d'];
			} else {
			$token['price_change_percentage_60d']="";				
			}
			
			if (isset($token_array['market_data']['price_change_percentage_200d'])) {			
			$token['price_change_percentage_200d']=$token_array['market_data']['price_change_percentage_200d'];
			} else {
			$token['price_change_percentage_200d']="";				
			}
			
			if (isset($token_array['market_data']['price_change_percentage_1y'])) {			
			$token['price_change_percentage_1y']=$token_array['market_data']['price_change_percentage_1y'];
			} else {
			$token['price_change_percentage_1y']="";				
			}
			
			if (isset($token_array['market_data']['market_cap_change_24h'])) {			
			$token['market_cap_change_24h']=$token_array['market_data']['market_cap_change_24h'];
			} else {
			$token['market_cap_change_24h']="";				
			}
			
			
			$token['timestamp']=time();
			$output['token']=$token;
			$token['TABLE_NAME']="GECKO_TOKEN";
			$token['ACTION']="insert";
			$sql="SELECT ID FROM GECKO_TOKEN WHERE contract_address = '" . $contract_address . "'";
			$s=$this->sql($sql);
			if (sizeof($s)>0) {
					$token['ID']=$s[0]['ID'];
			}
			$this->post($token);
	
	} else {
		//--
		//-- Gecko Token Returned Error;
		//--
		// 1. Check for data.
		$sql="SELECT * FROM GECKO_TOKEN WHERE SYMBOL = '" . strtoupper($token_symbol) . "'";
		$x=$this->sql($sql);
		if (sizeof($x)>0) {
				// There is stored data
				$token=$x[0];
		} else {
			$sql="SELECT * FROM BEP20_TOKEN WHERE LOWER(CONTRACT_ADDRESS) = '" . $contract_address . "'";
			$x=$this->sql($sql);
			if (sizeof($x)>0) {
				$token=array();
				$token['token_id']=$x[0]['SYMBOL'];
				$token['symbol']=$x[0]['SYMBOL'];
				$token['name']=$x[0]['TOKEN_NAME'];
				$token['description']=$x[0]['DESCRIPTION'];
				$token['price']=$x[0]['TOKEN_PRICE_USD'];
				$token['contract_address']=$contract_address;
				$token['total_supply']=$x[0]['TOTAL_SUPPLY'];			
				$token['market_cap_rank']="N/A";
				$token['market_cap']=floatval($x[0]['TOTAL_SUPPLY'])*floatval($x[0]['TOKEN_PRICE_USD']);	
				$token['high_24h']="No Data";		
				$token['low_24h']="No Data";
				$token['max_supply']="No Data";
				$token['circulating_supply']="No Data";			
				$token['fully_diluted_valuation']="No Data";			
				$token['total_value_locked']="No Data";			
				$token['volume_24h']="No Data";
				$token['value_24h']="No Data";
				$token['website']=$x[0]['WEBSITE'];
				$token['forum']=$x[0]['BLOG'];
				$token['discord']=$x[0]['DISCORD'];
				$token['twitter']=$x[0]['TWITTER'];
				$token['telegram']=$x[0]['TELEGRAM'];
				$token['reddit']=$x[0]['REDDIT'];
				$token['github']=$x[0]['GITHUB'];
				$token['img_thumb']=$x[0]['LOGO_URI'];
				$token['img_small']=$x[0]['LOGO_URI'];
				$token['img_large']=$x[0]['LOGO_URI'];
				$token['mcap_to_tvl_ratio']="No Data";			
				$token['all_time_high']="No Data";		
				$token['all_time_low']="No Data";		
				$token['total_volume']="No Data";					
				$token['price_change_24h']="No Data";
				$token['price_change_percentage_24h']="No Data";
				$token['price_change_percentage_7d']="No Data";
				$token['price_change_percentage_14d']="No Data";
				$token['price_change_percentage_30d']="No Data";
				$token['price_change_percentage_60d']="No Data";
				$token['price_change_percentage_200d']="No Data";
				$token['price_change_percentage_1y']="No Data";
				$token['market_cap_change_24h']="No Data";
			} else {
			
				}
			}
		}
		} else {
				$token=array();
				$token['token_id']=$x[0]['SYMBOL'];
				$token['symbol']=$x[0]['SYMBOL'];
				$token['name']="No Data";
				$token['description']="No Data";
				$token['price']="0";
				$token['contract_address']="";
				$token['total_supply']="";			
				$token['market_cap_change_24h_rank']="N/A";
				$token['market_cap_change_24h']="0";	
				$token['high_24h']="No Data";		
				$token['low_24h']="No Data";
				$token['max_supply']="No Data";
				$token['circulating_supply']="No Data";			
				$token['fully_diluted_valuation']="No Data";			
				$token['total_value_locked']="No Data";			
				$token['volume_24h']="No Data";
				$token['value_24h']="No Data";
				$token['website']="No Data";
				$token['forum']="No Data";
				$token['discord']="No Data";
				$token['twitter']="No Data";
				$token['telegram']="No Data";
				$token['reddit']="No Data";
				$token['github']="No Data";
				$token['img_thumb']="No Data";
				$token['img_small']="No Data";
				$token['img_large']="No Data";
				$token['mcap_to_tvl_ratio']="No Data";			
				$token['all_time_high']="No Data";		
				$token['all_time_low']="No Data";		
				$token['total_volume']="No Data";					
				$token['price_change_24h']="No Data";
				$token['price_change_percentage_24h']="No Data";
				$token['price_change_percentage_7d']="No Data";
				$token['price_change_percentage_14d']="No Data";
				$token['price_change_percentage_30d']="No Data";
				$token['price_change_percentage_60d']="No Data";
				$token['price_change_percentage_200d']="No Data";
				$token['price_change_percentage_1y']="No Data";
				$token['market_cap_change_24h']="No Data";	
		}

		if ($token['price']!=""&&$token['price']!="No Data") $token['price']="$" . number_format($token['price'],2);
		if ($token['high_24h']!=""&&$token['high_24h']!="No Data") $token['high_24h']="$" . number_format($token['high_24h'],5);
		if ($token['low_24h']!=""&&$token['low_24h']!="No Data") $token['low_24h']="$" . number_format($token['low_24h'],5);
		if ($token['market_cap']!=""&&$token['market_cap']!="No Data") $token['market_cap']="$" . number_format($token['market_cap'],0);
		if ($token['market_cap_change_24h']!=""&&$token['market_cap_change_24h']!="No Data") $token['market_cap_change_24h']="$" . number_format($token['market_cap_change_24h'],0);
		if ($token['total_supply']!=""&&$token['total_supply']!="No Data") $token['total_supply']=number_format($token['total_supply'],0);
		if ($token['max_supply']!=""&&$token['max_supply']!="No Data") $token['max_supply']=number_format($token['max_supply'],0);
		if ($token['circulating_supply']!=""&&$token['circulating_supply']!="No Data") $token['circulating_supply']=number_format($token['circulating_supply'],0);
		if ($token['total_value_locked']!=""&&$token['total_value_locked']!="No Data") $token['total_value_locked']="$" . number_format($token['total_value_locked'],0);
		if ($token['volume_24h']!=""&&$token['volume_24h']!="No Data") $token['volume_24h']="$" . number_format($token['volume_24h'],2);
		if ($token['value_24h']!=""&&$token['value_24h']!="No Data") $token['value_24h']="$" . number_format($token['value_24h'],2);
		if ($token['mcap_to_tvl_ratio']!=""&&$token['mcap_to_tvl_ratio']!="No Data") $token['mcap_to_tvl_ratio']=number_format($token['mcap_to_tvl_ratio'],6);
		if ($token['all_time_high']!=""&&$token['all_time_high']!="No Data") $token['all_time_high']="$" . number_format($token['all_time_high'],4);
		if ($token['all_time_low']!=""&&$token['all_time_low']!="No Data") $token['all_time_low']="$" . number_format($token['all_time_low'],4);
		if ($token['total_volume']!=""&&$token['total_volume']!="No Data") $token['total_volume']="$" . number_format($token['total_volume'],0);
		if ($token['price_change_24h']!=""&&$token['price_change_24h']!="No Data") $token['price_change_24h']="$" . number_format($token['price_change_24h'],4);
		if ($token['price_change_percentage_24h']!=""&&$token['price_change_percentage_24h']!="No Data") $token['price_change_percentage_24h']=number_format($token['price_change_percentage_24h'],4) . "%";
		if ($token['price_change_percentage_7d']!=""&&$token['price_change_percentage_7d']!="No Data") $token['price_change_percentage_7d']=number_format($token['price_change_percentage_7d'],4) . "%";
		if ($token['price_change_percentage_14d']!=""&&$token['price_change_percentage_14d']!="No Data") $token['price_change_percentage_14d']=number_format($token['price_change_percentage_14d'],4) . "%";
		if ($token['price_change_percentage_30d']!=""&&$token['price_change_percentage_30d']!="No Data") $token['price_change_percentage_30d']=number_format($token['price_change_percentage_30d'],4) . "%";
		if ($token['price_change_percentage_60d']!=""&&$token['price_change_percentage_60d']!="No Data") $token['price_change_percentage_60d']=number_format($token['price_change_percentage_60d'],4) . "%";
		if ($token['price_change_percentage_200d']!=""&&$token['price_change_percentage_200d']!="No Data") $token['price_change_percentage_200d']=number_format($token['price_change_percentage_200d'],4) . "%";
		if ($token['price_change_percentage_1y']!=""&&$token['price_change_percentage_1y']!="No Data") $token['price_change_percentage_1y']=number_format($token['price_change_percentage_1y'],4) . "%";

		$output['token']=$token;
		$output['balance']=$balance;

		$o=json_encode($output);
		$o=stripcslashes($o);
		return $output;
		
	}	
	
	function checkWalletStatus($data) {
		if (!isset($data['id'])) return array();
		$id=$data['id'];
		$this->B->checkWalletStatus($id);
		return array();
	}
	
	function getOneTransaction($data) {
		$id=strtolower($data['id']);
		$sql="select * ";
		$sql.=" from BEP20_TRANSACTIONS where ID = " . $id;
		$z=$this->sql($sql);
if ($z[0]['USER_TRANSACTION_TYPE']=="PURCHASE") $z[0]['USER_TRANSACTION_TYPE_LONG']="Purchase using Fiat";   
if ($z[0]['USER_TRANSACTION_TYPE']=="SALE") $z[0]['USER_TRANSACTION_TYPE_LONG']="Sale for Fiat";   
if ($z[0]['USER_TRANSACTION_TYPE']=="ESEND") $z[0]['USER_TRANSACTION_TYPE_LONG']="Transfer to Exchange Wallet";   
if ($z[0]['USER_TRANSACTION_TYPE']=="ERECEIVE") $z[0]['USER_TRANSACTION_TYPE_LONG']="Transfer from Exchange Wallet";   
if ($z[0]['USER_TRANSACTION_TYPE']=="BSEND") $z[0]['USER_TRANSACTION_TYPE_LONG']="Transfer to Bridge Wallet";   
if ($z[0]['USER_TRANSACTION_TYPE']=="BRECEIVE") $z[0]['USER_TRANSACTION_TYPE_LONG']="Transfer from Exchange Wallet";   
if ($z[0]['USER_TRANSACTION_TYPE']=="TRANSFER") $z[0]['USER_TRANSACTION_TYPE_LONG']="Transfer between Wallets";   
if ($z[0]['USER_TRANSACTION_TYPE']=="RECEIVE") $z[0]['USER_TRANSACTION_TYPE_LONG']="Receive Tokens / Coins";   
if ($z[0]['USER_TRANSACTION_TYPE']=="SEND") $z[0]['USER_TRANSACTION_TYPE_LONG']="Send Tokens / Coins";   
if ($z[0]['USER_TRANSACTION_TYPE']=="SWAP") $z[0]['USER_TRANSACTION_TYPE_LONG']="Swap Tokens / Coins";   
if ($z[0]['USER_TRANSACTION_TYPE']=="STAKE") $z[0]['USER_TRANSACTION_TYPE_LONG']="Stake Tokens / Coins";   
if ($z[0]['USER_TRANSACTION_TYPE']=="UNSTAKE") $z[0]['USER_TRANSACTION_TYPE_LONG']="Unstake Tokens / Coins";   
if ($z[0]['USER_TRANSACTION_TYPE']=="HARVEST") $z[0]['USER_TRANSACTION_TYPE_LONG']="Harvest / Earn Rewards";   
if ($z[0]['USER_TRANSACTION_TYPE']=="MINING") $z[0]['USER_TRANSACTION_TYPE_LONG']="Mining";   
if ($z[0]['USER_TRANSACTION_TYPE']=="AIRDROP") $z[0]['USER_TRANSACTION_TYPE_LONG']="Receive Airdrop - Requested";   
if ($z[0]['USER_TRANSACTION_TYPE']=="NAIRDROP") $z[0]['USER_TRANSACTION_TYPE_LONG']="Receive Airdrop - Not Requested";   
if ($z[0]['USER_TRANSACTION_TYPE']=="CONTRACT") $z[0]['USER_TRANSACTION_TYPE_LONG']="Contract Interaction";   
		$output=array();
		$output['transaction']=$z[0];
	$sql="SELECT id, name, trust_score_rank FROM GOATX_RESEARCH_EXCHANGES  ORDER BY name";
	$x=$this->sql($sql);
        foreach ($x as $x0) {
            array_push($z,$x0);
        }
        $output['exchanges']=$z;
		return $output;
	}
	
	function getTokenStaking($data) {
		
		$address=strtolower($data['address']);
		
		$this->B->processWalletTransactions($address,"BSC");
		$this->B->processWalletTransactions($address,"ETH");
		$this->B->calculate_lp_balances($address);

		$bsc_balances=array();
		$eth_balances=array();

		$bep20_value=0;
		$erc20_value=0;
		$lp_value=0;
		$lp_erc_value=0;
		
		$lp=array();
		$sql="SELECT NAME, LP_AMOUNT, TOKEN0_USD, TOKEN1_USD FROM BEP20_WALLET_LP WHERE WALLET_ADDRESS = '" . strtolower($address) . "' ORDER BY NAME";
		$z=$this->sql($sql);
		foreach($z as $a) {
				$o=array();
				$o['ID']=$a['ID'];
				$o['NAME']=$a['NAME'];
				$o['LP_AMOUNT']=$a['LP_AMOUNT'];
				$g=round(floatval($a['TOKEN0_USD']),2);
				$o['B1']='$' . $g;
				$h=round(floatval($a['TOKEN1_USD']),2);
				$o['B2']='$' . $h;
				$o['TOTAL']='$' . ($g+$h);
				$lp_value+=$g+$h;
				array_push($lp,$o);
		}

		$output['bep20_value']='$' . round($bep20_value,2);
		$output['erc20_value']='$' . round($erc20_value,2);
		$output['lp_value']='$' . round($lp_value,2);
		$output['lp_erc_value']='$' . round($lp_erc_value,2);
		$output['v']=round($erc20_value,2)+round($erc20_value,2)+round($lp_value,2)+round($lp_erc_value,2);
		$output['vd']='$' . (round($erc20_value,2)+round($erc20_value,2)+round($lp_value,2)+round($lp_erc_value,2));
		$output['erc20_v']=round($erc20_value,2);
		$output['lp_v']=round($lp_value,2);
		$output['lp_erc_v']=round($lp_erc_value,2);
		$output['lp']=$lp;
		
		return $output;
		
	}
	
	function getTokenPortfolio($data) {
	
		$address=strtolower($data['address']);

	//	$this->B->calculateBSCTokenBalances($address);
	//	$this->B->calculateETHTokenBalances($address);
                $bep20_value=0;
		$bsc_balances=array();
		$eth_balances=array();
		$sql="select * FROM BEP20_WALLET_TOKEN WHERE WALLET_ADDRESS = '" . strtolower($address) . "' ORDER BY SYMBOL";
		$z=$this->sql($sql);
		$output=array();
		foreach($z as $a) {
				$o=array();
				$o['ID']=$a['ID'];
				$o['SYMBOL']=$a['SYMBOL'];
				$o['NAME']=$a['NAME'];
				$o['BALANCE']=round(floatval($a['BALANCE']),4);
				$sql="SELECT ID, TOKEN_PRICE_USD FROM BEP20_PRICE WHERE SYMBOL = '" . $a['SYMBOL'] . "' ORDER BY TIMESTAMP DESC";
				$b=$this->sql($sql);
				if (sizeof($b)>0) {
						$usd=round(floatval($b[0]['TOKEN_PRICE_USD']),2);
						$value=round(floatval($a['BALANCE']*$usd),2);
				} else {
						$usd="0.00";
						$value="0.00";
				}
				$o['USD']='$'.$usd;
				$bep20_value += $value;
			
				$o['VALUE']='$' . $value;
				array_push($bsc_balances,$a);
		}

		$sql="select ID, SYMBOL, NAME, BALANCE FROM ERC20_WALLET_TOKEN WHERE WALLET_ADDRESS = '" . strtolower($address) . "' ORDER BY SYMBOL";
		$z=$this->sql($sql);
		$output=array();
		foreach($z as $a) {
				$o=array();
				$o['ID']=$a['ID'];
				$o['SYMBOL']=$a['SYMBOL'];
				$o['NAME']=$a['NAME'];
				$o['BALANCE']=round(floatval($a['BALANCE']),4);
				array_push($eth_balances,$o);
		}

		$output['bsc_balances']=$bsc_balances;
		$output['eth_balances']=$eth_balances;
		
		return $output;

	}

        function getWalletBalances($data) {
		$address=strtolower($data['address']);
                $l=$this->B->getAllBEP20TokenBalances($address);
                $a=json_decode($l,true);
                $output=array();
                $bsc=array();
if ($a['message']!="NOTOK") {
                foreach($a['result'] as $b) {
		    $q=array();
		    $q['symbol']=$b['TokenSymbol'];
                    $q['name']=$b['TokenName'];
                    $q['qty']=$this->B->convertBigNumber($b['TokenQuantity'],$b['TokenDivisor']);
                    array_push($bsc,$q);
                }
}
                $output['bsc']=$bsc;
                return $output;
        }	

	function getWalletTokens($data) {
		$address=strtolower($data['address']);
		$this->B->populate_raw_transactions($address);
		$this->B->process_transactions($address);

		$sql="select ID, timestamp, hash, fromAddress, toAddress, fromTokenSymbol, toTokenSymbol, toTokenDecimal, fromTokenDecimal, fromValue, fromValueUSD, toValue, ";
		$sql.="toValueUSD, description, transaction_type, gasTotal from BEP20_TRANSACTIONS where wallet_address = '" . strtolower($address) . "' ORDER BY timestamp desc";
		$z=$this->sql($sql);
		$output=array();
		foreach($z as $a) {
				$o=array();
				$o['ID']=$a['ID'];
				$o['timestamp']=$a['timestamp'];
				if ($a['fromAddress']==$address) {
					$o['address']=substr($a['toAddress'],0,10) . "..." . substr($a['toAddress'],-10);
                    $o['full_address']=$a['toAddress'];
				} else {
					$o['address']=substr($a['fromAddress'],0,10) . "..." . substr($a['fromAddress'],-10);
                    $o['full_address']=$a['fromAddress'];
				}
				$o['description']=$a['description'];
				$o['fromValue']=$a['fromValue'];
				$o['fromValueUSD']=$a['fromValueUSD'];
				$o['toValue']=$a['toValue'];
				$o['toValueUSD']=$a['toValueUSD'];		
				$o['gasTotal']=$a['gasTotal'];
				$o['fromToken']=$a['fromTokenSymbol'];
				$o['toToken']=$a['toTokenSymbol'];
                $o['hash']=substr($a['hash'],0,10) . "..." . substr($a['hash'],-10);
                $o['transaction_type']=$a['transaction_type'];
				array_push($output,$o);
		}
		return $output;		
	}
	
	function getTokenTransactions($data) {
		
		$address=strtolower($data['address']);
		$token=strtoupper($data['token']);
		$this->B->processWalletTransactions($address);

		$sql="select ID, timestamp, hash, fromAddress, toAddress, fromTokenSymbol, toTokenSymbol, ";
		$sql.=" toTokenDecimal, fromTokenDecimal, fromValue, fromPrice, fromValueUSD, toValue, toPrice, toValueUSD, ";
		$sql.="description, transaction_type from BEP20_TRANSACTIONS where ";
		$sql.=" wallet_address = '" . strtolower($address) . "' ";
		$sql.=" and (UPPER(fromTokenSymbol) = '";
		$sql.= $token;
		$sql.= "' OR UPPER(toTokenSymbol) = '" . $token . "') ORDER BY timestamp";

		$z=$this->sql($sql);
		$final=array();
		$output=array();
		foreach($z as $a) {
				$o=array();
				$o['ID']=$a['ID'];
				$o['timestamp']=date("Y-m-d", $a['timestamp']);
				if ($a['fromAddress']==$address) {
					$o['address']=substr($a['toAddress'],0,10) . "..." . substr($a['toAddress'],-10);
								$o['full_address']=$a['toAddress'];
				} else {
					$o['address']=substr($a['fromAddress'],0,10) . "..." . substr($a['fromAddress'],-10);
								$o['full_address']=$a['fromAddress'];
				}
				$o['description']=$a['description'];
				$o['fromValue']=$a['fromValue'];
				$o['fromPrice']=$a['fromPrice'];		
				$o['fromValueUSD']=$a['fromValueUSD'];		
				$o['toValue']=$a['toValue'];
				$o['toPrice']=$a['toPrice'];		
				$o['toValueUSD']=$a['toValueUSD'];		
				$o['fromToken']=$a['fromTokenSymbol'];
				$o['toToken']=$a['toTokenSymbol'];
                $o['hash']=substr($a['hash'],0,10) . "..." . substr($a['hash'],-10);
                $o['transaction_type']=$a['transaction_type'];
				array_push($output,$o);
		}
		$final['transactions']=$output;
		$sql="SELECT * FROM BEP20_TOKEN WHERE SYMBOL = '" . $token . "'";
		$z=$this->sql($sql);
		$final['token']=$z[0];
		$sql="SELECT BALANCE FROM BEP20_WALLET_TOKEN WHERE SYMBOL = '" . $token . "'";
		$sql.=" AND WALLET_ADDRESS = '" . strtolower($address) . "' ";
		$z=$this->sql($sql);
		if (sizeof($z)==0) {
			$final['balance']="0.00";
		} else {
			$final['balance']=$z[0]['BALANCE'];
		}
		return $final;		
	}
	
	function getTokenPairs($data) {

		$sql="SELECT * FROM GOATX_TOKEN_PAIR WHERE lp_address = '' ORDER BY symbol0";
		$z=$this->sql($sql);
		$output=array();
		$a=0;
		foreach($z as $k) {
			$a++;
			array_push($output,$k);
		}
		return $output;
	}
	
	function postAirdrop($data) {
		$id=$data['id'];
		$sql="UPDATE GOATX_WALLET_DATA SET AIRDROP = 'Y' WHERE WALLET_ADDRESS = '" . $id . "'";
		$z=$this->execute($sql);
		$post=array();
		$post['TABLE_NAME']="GOATX_WALLET";
		$post['ACTION']="insert";
		$post['wallet_address']=$id;
		$this->post($post);
	}
	
	function postWalletTokenBalances($data) {
		
		$address=$data['address'];
		$token=$data['symbol'];
		$name=$data['name'];
		$balance=$data['balance'];
		if ($address=="") die('["NO ADDRESS"]');

		$sql="SELECT ID FROM BEP20_WALLET_TOKEN WHERE WALLET_ADDRESS = '" . $address . "' AND SYMBOL = '" . $token . "'";
		$z=$this->sql($sql);
		$post=array();
		$post['TABLE_NAME']="BEP20_WALLET_TOKEN";
		$post['ACTION']="INSERT";
		if (sizeof($z)>0) {
			$post['ID']=$z[0]['ID'];
		} else {
			$sql="SELECT TOKEN_NAME FROM BEP20_TOKEN WHERE SYMBOL = '" . $token . "'";
			$f=$this->sql($sql);
			if (sizeof($f)>0) {
				$post['NAME']=$f[0]['TOKEN_NAME'];
			} else {
				$post['NAME']=$token;
			}
		}
		$post['WALLET_ADDRESS']=$address;
		$post['SYMBOL']=$token;
		$post['TOKEN_COUNT']=$balance;
		if (!isset($post['ID'])&&$balance=='0.00000') die('["ZERO BALANCE"]');
		$this->post($post);
		
	}
	
	function postLpContracts($data) {
		
		$id=$data['id'];

		$sql="UPDATE GOATX_TOKEN_PAIR SET lp_address = '" . $data['lp'] . "' WHERE ID = " . $id;
		$z=$this->execute($sql);
		$output=array();
		$output['lp']=$data['lp'];
		return $output;
	}	
	
	function getBackgroundData($data) {

		$sql="SELECT MAX(ID) AS C FROM GOATX_EXCHANGE_DATA WHERE BUSD_POOL_GOATX_COUNT <> '0'";
		$z=$this->sql($sql);
		$id=$z[0]['C'];
		$sql="SELECT GOATX_PRICE, TOTAL_SUPPLY, MARKET_CAP FROM GOATX_EXCHANGE_DATA WHERE ID = " . $id;
		$z=$this->sql($sql);
		setlocale(LC_MONETARY,"en_US");
		$p=round(floatval($z[0]['GOATX_PRICE']),2);
		$new_price=$z[0]['GOATX_PRICE'];
		$price = number_format($new_price,2);

		$s=round(floatval($z[0]['TOTAL_SUPPLY']),0);
		$total_supply=number_format($s,0);

		$m=round(floatval($z[0]['MARKET_CAP']),2);
		$market_cap=number_format($m,2);

		$id=$id-96;
		if ($id<0) $id=20;
		$sql="SELECT GOATX_PRICE, TOTAL_SUPPLY, MARKET_CAP FROM GOATX_EXCHANGE_DATA WHERE ID >= " . $id. " ORDER BY ID";
		$z=$this->sql($sql);
		$prev_price=$z[0]['GOATX_PRICE'];
		$price_change=$new_price-$prev_price;
		$c=($price_change / $prev_price) * 100;
		$pct_change=number_format($c,3);
		if ($c!=0) {
			if ($c>0) {
				$pct_change = "+" . $pct_change . "%";
			} else {
				$pct_change = $pct_change . "%";
			}
		}

		$output=array();
		$output['price']=$price;
		$output['market_cap']=$market_cap;
		$output['total_supply']=$total_supply;
		$output['pct_change']=$pct_change;
		return $output;
		
	}
	
	function getAirdropWallets($data) {

		$sql="SELECT * FROM GOATX_WALLET_DATA WHERE AIRDROP = 'N' AND BNB_BALANCE = '0' ORDER BY ID";
		$z=$this->sql($sql);
		$output=array();
		$i=0;
		foreach ($z as $z0) {
			$i++;
			$o=array();
			$o['WALLET_ADDRESS']=$z0['WALLET_ADDRESS'];
			$o['AIRDROP_AMOUNT']="1" . rand(0,3) . rand(0,9) . rand(0,9) . rand(0,9) . rand(0,9) . "0000000000000";
			$o['ID']=$i;
			array_push($output,$o);
		}
		return $output;
		
	}
	function getOneOrder($POST) {
		return array();
	}
	
	function getOneClient($POST) {
		return array();		
	}
	
	function getClientList($POST) {
		return array();		
	}
	
	function query($table,$where="",$order="") {
		$db=$this->connect();
		$output=array();
		if ($table=="") {
			return $output;
		} else {
	
			$sql = "SELECT * from " . $table . " where 1 = 1 ";
			if ($where != "") $sql .= " and " . $where;
			if ($order != "") $sql .= " order by " . $order;
			$stmt = $db->prepare($sql);
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
		$db=$this->connect();
		$output=array();
		if ($s=="") {
			return $output;
		} else {
			$stmt = $db->prepare($s);
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);	
			return $results;	
		}
	}

	function sql0($s="") {
		$db=$this->connect();
		$output=array();
		if ($s=="") {
			return '0';
		} else {
			$stmt = $db->prepare($s);
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);	
			return $results[0];	
		}
	}

	function sqlC($s="") {
		$db=$this->connect();
		$output=array();
		if ($s=="") {
			return 0;
		} else {
			$stmt = $db->prepare($s);
			$stmt->execute();
			$results = $stmt->fetchAll(PDO::FETCH_ASSOC);	
			return $results[0]['c'];	
		}
	}
	
	function execute($s) {
		$db=$this->connect();
		$stmt = $db->prepare($s);
		$stmt->execute();
	}	

	function update($s,$p) {
		$db=$this->connect();
		$stmt = $db->prepare($s);
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
		$db=$this->connect();
		$output=array();
		if (!isset($POST['action'])) $POST['action']="insert";
		if (!isset($POST['id'])) $POST['id']="";
		if (isset($POST['ID'])) $POST['id']=$POST['ID'];
		if (!isset($POST['table_name'])) 
		{
			$output['result']='Failed';
        } else
		{
		$sql = "SHOW COLUMNS FROM " . $POST['table_name'];
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);	

		if ($POST['action']!="delete") {
				// If there is not 'id' value, record is inserted
				if ($POST['id']==""||$POST['id']=="0") {
					//-- all tables have id and create_date as the minimum columns
					$sql = "insert into " . $POST['table_name'] . " (create_timestamp) values (now())";
					$stmt = $db->prepare($sql);
					$stmt->execute();				
					//-- put the id in $_POST['id'] so it can be used to process the rest of the columns
					$POST['id'] = $db->lastInsertId();
					$output['result']="insert";
				} else {
					$output['result']="update";					
				}

				$json=array();	

				foreach ($POST as $name => $value) {
					if ($name!="id"&&$name!="create_date"&&$name!="table_name"&&$name!="action") {
						//-- if column is in the table update it, otherwise add it to the $json array.
						if ($this->isTableColumn($name,$columns)) {
							if ($name=="event_date"||$name=="target_start_date"||$name=="target_end_date") {
									$sql = "update " . $POST['table_name'] . " set " . $name . " = STR_TO_DATE(?, '%m/%d/%Y') where id = ?";	
									$stmt = $db->prepare($sql);
									$stmt->bindParam(1, $value);
									$stmt->bindParam(2, $POST['id']);
									$stmt->execute();
							} else {
								if ($value=="now()") {
									$sql = "update " . $POST['table_name'] . " set " . $name . " = now() where id = ?";
									$stmt = $db->prepare($sql);
									$stmt->bindParam(1, $POST['id']);
									$stmt->execute();
								} else {
										$sql = "update " . $POST['table_name'] . " set " . $name . " = ? where id = ?";
										$stmt = $db->prepare($sql);
										$stmt->bindParam(1, $value);
										$stmt->bindParam(2, $POST['id']);
										$stmt->execute();
							    }
							}
					

						} else $json[$name] = $value;
					}
				}	
			} 
			else {
				$sql = "delete from " . $POST['table_name'] . " where id = ?";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(1, $POST['id']);
				$stmt->execute();
				$output['result']="update";
			}	
		}
		return $POST['id'];
	}

	function post2($POST) {	
        if (isset($POST['TABLE_NAME'])) $POST['table_name']=$POST['TABLE_NAME'];
		$db=$this->connect();
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
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);	

		if ($POST['action']!="delete") {
				// If there is not 'id' value, record is inserted
				if ($POST['ID']==""||$POST['ID']=="0") {
					//-- all tables have id and create_date as the minimum columns
					$sql = "insert into " . $POST['table_name'] . " (create_timestamp) values (now())";
					$stmt = $db->prepare($sql);
					$stmt->execute();				
					//-- put the id in $_POST['id'] so it can be used to process the rest of the columns
					$POST['ID'] = $db->lastInsertId();
					$output['result']="insert";
				} else {
					$output['result']="update";					
				}

				$json=array();	

				foreach ($POST as $name => $value) {
					if ($name!="ID"&&$name!="create_date"&&$name!="table_name"&&$name!="action") {
						//-- if column is in the table update it, otherwise add it to the $json array.
						if ($this->isTableColumn($name,$columns)) {
							if ($name=="event_date"||$name=="target_start_date"||$name=="target_end_date") {
									$sql = "update " . $POST['table_name'] . " set " . $name . " = STR_TO_DATE(?, '%m/%d/%Y') where ID = ?";	
									$stmt = $db->prepare($sql);
									$stmt->bindParam(1, $value);
									$stmt->bindParam(2, $POST['ID']);
									$stmt->execute();
							} else {
								if ($value=="now()") {
									$sql = "update " . $POST['table_name'] . " set " . $name . " = now() where id = ?";
									$stmt = $db->prepare($sql);
									$stmt->bindParam(1, $POST['ID']);
									$stmt->execute();
								} else {
										$sql = "update " . $POST['table_name'] . " set " . $name . " = ? where id = ?";
										$stmt = $db->prepare($sql);
										$stmt->bindParam(1, $value);
										$stmt->bindParam(2, $POST['ID']);
										$stmt->execute();
							    }
							}
					

						} else $json[$name] = $value;
					}
				}	
			} 
			else {
				$sql = "delete from " . $POST['table_name'] . " where id = ?";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(1, $POST['id']);
				$stmt->execute();
				$output['result']="update";
			}	
		}
		return $POST['id'];
	}

function syncMailchimp($data) {
    $apiKey = '3249d8eb57b916ab9bd2a0b7e00fc693-us5';
    $listId = '8f21d0cdaa';

    $memberId = md5(strtolower($data['email']));
    $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
    $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

    $json = json_encode([
        'email_address' => $data['email'],
        'status'        => "pending",
        'merge_fields'  => [
            'FNAME'     => $data['fname'],
            'LNAME'     => $data['lname']
        ]
    ]);

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                                                                 

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode;
}
}


