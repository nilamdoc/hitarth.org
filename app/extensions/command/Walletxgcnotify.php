<?php 
namespace app\extensions\command;
use app\models\Transactions;
use app\models\Details;
use app\models\Users;

use app\extensions\action\Coingreen;

class Walletxgcnotify extends \lithium\console\Command {
    public function index($s=null) {
			$coingreen = new Coingreen('http://'.COINGREEN_WALLET_SERVER.':'.COINGREEN_WALLET_PORT,COINGREEN_WALLET_USERNAME,COINGREEN_WALLET_PASSWORD);

		$getrawtransaction = $coingreen->getrawtransaction($s);
		$decoderawtransaction = $coingreen->decoderawtransaction($getrawtransaction);		

			foreach($decoderawtransaction['vout'] as $out){
				foreach($out['scriptPubKey']['addresses'] as $address){
				
					$useremail = $coingreen->getaccount($address);
				
					$Amount = (float)$out['value'];
					if($useremail!=""){
						$Transactions = Transactions::find('first',array(
							'conditions'=>array('TransactionHash' => $s)
						));
						if($Transactions['_id']==""){
							$t = Transactions::create();
							$Amount = $Amount;
							$comment = "Move from User: ".$useremail."; Address: ".$address."; Amount:".$Amount.";";
							$transfer = $coingreen->move($useremail, "NilamDoctor", (float)$Amount,(int)0,$comment);

							if(isset($transfer['error'])){
								$error = $transfer['error']; 
							}else{
								$error = $transfer;
							}
						$user = Users::find('first',array(
							'conditions'=>array('email'=>$useremail)
						));

						$data = array(
							'DateTime' => new \MongoDate(),
							'TransactionHash' => $s,
							'useremail' => $useremail,
							'user_id'=>  (string) $user['_id'],
							'address'=>$address,							
							'Currency'=>'XGC',							
							'Amount'=> (float)$Amount,
							'Added'=>true,
							'Action'=>'Deposit',
							'Transfer'=>$comment,
						);							
						$t->save($data);
		
		
						$details = Details::find('first',
							array('conditions'=>array('user_id'=> (string) $user['_id']))
						);
						$dataDetails = array(
								'balance.XGC' => (float)((float)$details['balance']['XGC'] + (float)$Amount),
								'newaddress' => 'Yes'
							);
						
							$details = Details::find('all',
								array(
										'conditions'=>array('user_id'=>(string)$user['_id'])
									))->save($dataDetails);

						}else{
							$Transactions = Transactions::find('first',array(
								'conditions'=>array('TransactionHash' => $s)
							))->save($data);
						}
					}
				}
			}
		}
} 
?>