<?php
namespace app\controllers;
use app\models\Blocks;
use app\models\Txs;
use app\extensions\action\Coingreen;
class BlockchainController extends \lithium\action\Controller {

	public function index() {
		$COINGREEN = new COINGREEN('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
  
		$getblockcount = $COINGREEN->getblockcount();

	  $getconnectioncount = $COINGREEN->getconnectioncount();
	  $getblockhash = $COINGREEN->getblockhash($getblockcount);
	  $getblock = $COINGREEN->getblock($getblockhash);
 		$title = "Network connectivity ";		
	  return compact('getblockcount','getconnectioncount','getblock','title');
	}

	public function blocks($blockcount = null){
		$COINGREEN = new COINGREEN('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
		
	  if (!isset($blockcount)){
	  	  $blockcount = $COINGREEN->getblockcount();
	  }else{
	  	$blockcount = intval($blockcount);
	  }
	  if($blockcount<10){$blockcount = 10;}
	  $getblock = array();
	  $getblockhash = array();
	  $j = 0;
	  for($i=$blockcount;$i>$blockcount-10;$i--){
		$getblockhash[$j] = $COINGREEN->getblockhash($i);
		$getblock[$j] = $COINGREEN->getblock($getblockhash[$j]);
		$j++;
	  }
  		$title = "Blocks: ". $blockcount;		
		return compact('getblock','blockcount','title');
	}
	public function peer(){
		$COINGREEN = new COINGREEN('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
		$title = "Peer connection infomration";
		$getpeerinfo = $COINGREEN->getpeerinfo();
		$getconnectioncount = $COINGREEN->getconnectioncount();
		return compact('title','getpeerinfo','getconnectioncount');
	
	}

	public function blockhash($blockhash = null){
		$COINGREEN = new COINGREEN('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
		$blockcount = $COINGREEN->getblockcount();
	if (!isset($blockhash)){
		$blockhash = $COINGREEN->getblockhash($blockcount);		
		$prevblock = $blockcount - 1;
		$prevblockhash = $COINGREEN->getblockhash($prevblock);		
	}else{
		$getblock = $COINGREEN->getblock($blockhash);
		$prevblock = $getblock['height'] - 1;
		$prevblockhash = $COINGREEN->getblockhash($prevblock);		
		if($getblock['height']<>$blockcount ){
			$nextblock = $getblock['height'] + 1;
			$nextblockhash = $COINGREEN->getblockhash($nextblock);		
		
		}
		
	}
	
		$getblock = $COINGREEN->getblock($blockhash);
		$title = "Block hash: ". $blockhash;		
		return compact('getblock','prevblockhash','nextblockhash','prevblock','nextblock','title');
	}

		
	public function transactionhash($transactionhash = null){
		$COINGREEN = new COINGREEN('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
		$getrawtransaction = $COINGREEN->getrawtransaction($transactionhash);
//		print_r($getrawtransaction);
		$decoderawtransaction = $COINGREEN->decoderawtransaction($getrawtransaction);
//		print_r($decoderawtransaction);
		$listsinceblock = $COINGREEN->listsinceblock($transactionhash);
		$title = "Transactions hash: ". $transactionhash;		
		return compact('decoderawtransaction','listsinceblock','title');
	}
	
	public function address($address = null,$json = null){
		$COINGREEN = new COINGREEN('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
		$title = "Transactions done by ". $address;
		$txout = Txs::find('all',array(
			'conditions'=>array('txid.address'=>$address),
		));
		print_r(count($txout));
		$txin = Txs::find('all',array(
			'conditions'=>array('txid.vin.address'=>$address),
		));
		print_r(count($txin));
		if($json == 'json'){
			return $this->render(array('json' =>  
				compact('n_tx','total_received','total_sent','final_balance','txs','address'),
			'status'=> 200));
		}
		return compact('txout','txin','address');
	}


}

?>