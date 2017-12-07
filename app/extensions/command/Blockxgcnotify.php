<?php 
namespace app\extensions\command;
use app\models\Transactions;
use app\models\Details;
use app\models\Users;
use app\models\Blocks;

use app\extensions\action\Coingreen;

class Blockxgcnotify extends \lithium\console\Command {
    public function index() {
			$coingreen = new Coingreen('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);
	$getblockcount = $coingreen->getblockcount();
	$height = Blocks::find('first',array(
		'order' => array('height'=>'DESC')
	));
	
	$h = (int)$height['height'] + 1;

		for($i = $h;$i<=$h+999;$i++)	{
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$pagestarttime = $mtime; 

			$data = array();
			if($i <= $getblockcount){
				$getblockhash = $coingreen->getblockhash($i);
				$getblock = $coingreen->getblock($getblockhash);

				$data = array(
					'confirmations' => $getblock['confirmations'],
					'height' => $getblock['height'],
					'version' => $getblock['version'],
					'time' => new \MongoDate ($getblock['time']),
					'difficulty' => $getblock['difficulty'],
				);

				$txid = 0;					
				foreach($getblock['tx'] as $txx){
					if($txx!=""){
						$getrawtransaction = $coingreen->getrawtransaction((string)$txx);
						$decoderawtransaction = $coingreen->decoderawtransaction($getrawtransaction);
						$txvin = 0;
						$data['txid'][$txid]['version'] = $decoderawtransaction['version'];
						$data['txid'][$txid]['txid'] = $decoderawtransaction['txid'];
						$data['txid'][$txid]['locktime'] = $decoderawtransaction['locktime'];						
						if($decoderawtransaction['vin']){
							foreach($decoderawtransaction['vin'] as $vin){
							if($vin['coinbase']){
								$data['txid'][$txid]['vin'][$txvin]['coinbase'] = $vin['coinbase'];
							}
							if($vin['vout']){
								$data['txid'][$txid]['vin'][$txvin]['vout'] = $vin['vout'];														
							}
							if( $vin['scriptSig']['asm']){
								$data['txid'][$txid]['vin'][$txvin]['scriptSig']['asm'] = $vin['scriptSig']['asm'];														
							}
							if( $vin['scriptSig']['hex']){
								$data['txid'][$txid]['vin'][$txvin]['scriptSig']['hex'] = $vin['scriptSig']['hex'];																					
							}
							if($vin['sequence']){
								$data['txid'][$txid]['vin'][$txvin]['sequence'] = $vin['sequence'];						
							}
									$txvin ++;
							}	
						}else{print_r("No vin\n");}
						$txvout = 0;				
						if($decoderawtransaction['vout']){
							foreach($decoderawtransaction['vout'] as $vout){
								$data['txid'][$txid]['vout'][$txvout]['value'] = $vout['value'];
								$data['txid'][$txid]['vout'][$txvout]['n'] = $vout['n'];
								$data['txid'][$txid]['vout'][$txvout]['scriptPubKey']['addresses'] = $vout['scriptPubKey']['addresses'];						
								$data['txid'][$txid]['vout'][$txvout]['scriptPubKey']['asm'] = $vout['scriptPubKey']['asm'];													
								$data['txid'][$txid]['vout'][$txvout]['scriptPubKey']['hex'] = $vout['scriptPubKey']['hex'];																				
								$data['txid'][$txid]['vout'][$txvout]['scriptPubKey']['reqSigs'] = $vout['scriptPubKey']['reqSigs'];																				
								$data['txid'][$txid]['vout'][$txvout]['scriptPubKey']['type'] = $vout['scriptPubKey']['type'];													
								$txvout++;			
							}
						}else{print_r("No vout\n");}
					}
				$txid ++;											
				}
			
				Blocks::create()->save($data);
				
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$pageendtime = $mtime;
	$pagetotaltime = ($pageendtime - $pagestarttime);
	print_r($pagetotaltime."-".$getblock['height'])	;
	print_r("\n");
			}
		}


		}
} 
?>